# GitHub Actions Secrets Configuration Guide

## Required Secrets

Set these secrets in your GitHub repository:
**Settings → Secrets and variables → Actions → New repository secret**

### Database Secrets
```
DB_USER=your_database_username
DB_PASS=your_database_password
STAGING_DB_USER=staging_database_username
STAGING_DB_PASS=staging_database_password
```

### Server Access Secrets
```
STAGING_SSH_KEY=-----BEGIN OPENSSH PRIVATE KEY-----
your_staging_server_private_key_here
-----END OPENSSH PRIVATE KEY-----

PRODUCTION_SSH_KEY=-----BEGIN OPENSSH PRIVATE KEY-----
your_production_server_private_key_here
-----END OPENSSH PRIVATE KEY-----

STAGING_HOST=staging.yourdomain.com
PRODUCTION_HOST=yourdomain.com
```

### Notification Secrets (Optional)
```
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK
DISCORD_WEBHOOK_URL=https://discord.com/api/webhooks/YOUR/DISCORD/WEBHOOK
```

## Environment Variables

Set these in GitHub repository variables:
**Settings → Secrets and variables → Actions → Variables tab**

```
STAGING_USER=www-data
PRODUCTION_USER=www-data
STAGING_PATH=/var/www/staging/jpsme-event
PRODUCTION_PATH=/var/www/html/jpsme-event
```

## Server Setup Requirements

### 1. SSH Key Setup
```bash
# On your server
mkdir -p ~/.ssh
echo "your_public_key_here" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh
```

### 2. Server Dependencies
```bash
# Install required packages
sudo apt update
sudo apt install -y apache2 mysql-server php php-mysql php-gd php-mbstring

# Enable Apache modules
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 3. Directory Permissions
```bash
# Create and setup web directory
sudo mkdir -p /var/www/html/jpsme-event
sudo chown -R www-data:www-data /var/www/html/jpsme-event
sudo chmod -R 755 /var/www/html/jpsme-event
```

### 4. Database Setup
```bash
# Create database and user
mysql -u root -p
CREATE DATABASE jpsme_event;
CREATE USER 'jpsme_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON jpsme_event.* TO 'jpsme_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## GitHub Environments

Create these environments in your repository:
**Settings → Environments**

### Staging Environment
- **Environment name**: `staging`
- **Required reviewers**: (optional)
- **Environment secrets**: Add staging-specific secrets

### Production Environment  
- **Environment name**: `production`
- **Required reviewers**: Add team members who should approve production deployments
- **Environment secrets**: Add production-specific secrets

## Webhook Setup (Optional)

### Slack Webhook
1. Go to your Slack workspace
2. Create a new app: https://api.slack.com/apps
3. Add incoming webhook
4. Copy webhook URL to `SLACK_WEBHOOK_URL` secret

### Discord Webhook
1. Go to your Discord server
2. Server Settings → Integrations → Webhooks
3. Create webhook
4. Copy webhook URL to `DISCORD_WEBHOOK_URL` secret
