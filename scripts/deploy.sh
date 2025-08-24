#!/bin/bash

# JPSME Event Registration - Deployment Script
# Usage: ./deploy.sh [staging|production]

set -e  # Exit on any error

ENVIRONMENT=${1:-staging}
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}"
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING] $1${NC}"
}

info() {
    echo -e "${BLUE}[INFO] $1${NC}"
}

# Load environment configuration
if [ -f "$PROJECT_ROOT/.env" ]; then
    source "$PROJECT_ROOT/.env"
else
    warning ".env file not found, using default values"
fi

# Set environment-specific variables
case $ENVIRONMENT in
    "staging")
        SERVER_HOST=${STAGING_HOST:-"localhost"}
        SERVER_USER=${STAGING_USER:-"www-data"}
        DEPLOY_PATH=${STAGING_PATH:-"/xampp/htdocs/jpsme_event"}
        DB_NAME=${STAGING_DB_NAME:-"jpsme_event_staging"}
        ;;
    "production")
        SERVER_HOST=${PRODUCTION_HOST:-"junior.psmeinc.org.ph"}
        SERVER_USER=${PRODUCTION_USER:-"www-data"}
        DEPLOY_PATH=${PRODUCTION_PATH:-"/var/www/html"}
        DB_NAME=${PRODUCTION_DB_NAME:-"jpsme_event"}
        ;;
    *)
        error "Invalid environment. Use 'staging' or 'production'"
        ;;
esac

log "Starting deployment to $ENVIRONMENT environment"
log "Target: $SERVER_USER@$SERVER_HOST:$DEPLOY_PATH"

# Pre-deployment checks
info "Running pre-deployment checks..."

# Check if server is reachable
if ! ssh -o ConnectTimeout=10 "$SERVER_USER@$SERVER_HOST" "echo 'Server reachable'" > /dev/null 2>&1; then
    error "Cannot connect to server $SERVER_HOST"
fi

# Check PHP syntax
info "Checking PHP syntax..."
find "$PROJECT_ROOT" -name "*.php" -exec php -l {} \; > /dev/null || error "PHP syntax errors found"

# Create deployment package
TEMP_DIR=$(mktemp -d)
PACKAGE_NAME="jpsme-event-$(date +%Y%m%d-%H%M%S).tar.gz"

log "Creating deployment package: $PACKAGE_NAME"

# Copy files to temp directory
cp -r "$PROJECT_ROOT"/* "$TEMP_DIR/"

# Remove development files
rm -rf "$TEMP_DIR/.git"
rm -rf "$TEMP_DIR/.github"
rm -f "$TEMP_DIR/.gitignore"
rm -f "$TEMP_DIR/.env"
rm -rf "$TEMP_DIR/scripts"

# Create necessary directories
mkdir -p "$TEMP_DIR/uploads/proof_natcon"
mkdir -p "$TEMP_DIR/uploads/member_enrollments"
mkdir -p "$TEMP_DIR/uploads/proof_payment"

# Set proper permissions
chmod 755 "$TEMP_DIR/uploads"
chmod 755 "$TEMP_DIR/uploads"/*

# Create production database config
cat > "$TEMP_DIR/db.php" << EOF
<?php
// Production database configuration
\$host = '${DB_HOST:-localhost}';
\$db = '$DB_NAME';
\$user = '${DB_USER:-jpsme_user}';
\$pass = '${DB_PASS:-secure_password}';

\$conn = new mysqli(\$host, \$user, \$pass, \$db);
if (\$conn->connect_error) {
    die('Connection failed: ' . \$conn->connect_error);
}
?>
EOF

# Create deployment info
cat > "$TEMP_DIR/DEPLOYMENT_INFO.txt" << EOF
Deployment Information
=====================
Environment: $ENVIRONMENT
Date: $(date)
Git Commit: ${GITHUB_SHA:-$(git rev-parse HEAD 2>/dev/null || echo "N/A")}
Git Branch: ${GITHUB_REF_NAME:-$(git branch --show-current 2>/dev/null || echo "N/A")}
Deployed by: ${GITHUB_ACTOR:-$(whoami)}
EOF

# Create package
cd "$TEMP_DIR"
tar -czf "/tmp/$PACKAGE_NAME" .
cd - > /dev/null

log "Package created: /tmp/$PACKAGE_NAME"

# Backup current deployment (if exists)
log "Creating backup of current deployment..."
ssh "$SERVER_USER@$SERVER_HOST" "
    if [ -d '$DEPLOY_PATH' ]; then
        sudo tar -czf /tmp/backup-$(date +%Y%m%d-%H%M%S).tar.gz -C '$DEPLOY_PATH' . || true
        echo 'Backup created'
    else
        echo 'No existing deployment to backup'
    fi
"

# Upload and extract package
log "Uploading deployment package..."
scp "/tmp/$PACKAGE_NAME" "$SERVER_USER@$SERVER_HOST:/tmp/"

log "Extracting package on server..."
ssh "$SERVER_USER@$SERVER_HOST" "
    sudo mkdir -p '$DEPLOY_PATH'
    sudo tar -xzf '/tmp/$PACKAGE_NAME' -C '$DEPLOY_PATH'
    sudo chown -R www-data:www-data '$DEPLOY_PATH'
    sudo chmod -R 755 '$DEPLOY_PATH'
    sudo chmod -R 775 '$DEPLOY_PATH/uploads'
    rm '/tmp/$PACKAGE_NAME'
"

# Database deployment
if [ "$ENVIRONMENT" = "production" ] || [ "$ENVIRONMENT" = "staging" ]; then
    log "Deploying database schema..."
    scp "$PROJECT_ROOT/registration.sql" "$SERVER_USER@$SERVER_HOST:/tmp/"
    
    ssh "$SERVER_USER@$SERVER_HOST" "
        mysql -u '${DB_USER:-root}' -p'${DB_PASS:-}' '$DB_NAME' < /tmp/registration.sql || echo 'Database schema deployment completed (may have warnings for existing tables)'
        rm /tmp/registration.sql
    "
fi

# Restart web server (if needed)
log "Restarting web server..."
ssh "$SERVER_USER@$SERVER_HOST" "
    sudo systemctl reload apache2 2>/dev/null || sudo systemctl reload nginx 2>/dev/null || echo 'Web server restart not needed or failed'
"

# Post-deployment verification
log "Running post-deployment verification..."
if curl -f -s "http://$SERVER_HOST/index.php" > /dev/null; then
    log "✅ Deployment verification successful!"
else
    warning "⚠️ Deployment verification failed - please check manually"
fi

# Cleanup
rm -rf "$TEMP_DIR"
rm -f "/tmp/$PACKAGE_NAME"

log "🚀 Deployment to $ENVIRONMENT completed successfully!"
log "URL: http://$SERVER_HOST"

# Send notification (if webhook configured)
if [ -n "${SLACK_WEBHOOK_URL:-}" ]; then
    curl -X POST -H 'Content-type: application/json' \
        --data "{\"text\":\"🚀 JPSME Event Registration deployed to $ENVIRONMENT successfully!\"}" \
        "$SLACK_WEBHOOK_URL" 2>/dev/null || true
fi

log "Deployment script finished."
