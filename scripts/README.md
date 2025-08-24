# Deployment Scripts for JPSME Event Registration System

This directory contains deployment automation scripts for the CI/CD pipeline.

## Scripts

### `deploy.sh`
Main deployment script for staging and production environments.

**Usage:**
```bash
./scripts/deploy.sh staging
./scripts/deploy.sh production
```

### `backup.sh`
Database and file backup script.

**Usage:**
```bash
./scripts/backup.sh
```

### `rollback.sh`
Rollback script for quick recovery.

**Usage:**
```bash
./scripts/rollback.sh
```

## Environment Setup

1. Copy `.env.example` to `.env`
2. Configure your server details
3. Set up SSH keys for passwordless deployment
4. Test connection: `ssh user@your-server.com`

## Security Notes

- Never commit real credentials to the repository
- Use GitHub Secrets for sensitive information
- Ensure proper file permissions on deployment servers
- Regularly update and patch deployment servers
