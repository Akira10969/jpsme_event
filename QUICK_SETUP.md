# Quick Setup for XAMPP Users

## 🎯 Immediate Setup (5 minutes)

Since you're using XAMPP locally, here's the **quickest way** to get your CI/CD working:

### Step 1: Go to GitHub Secrets
```
1. Visit: https://github.com/Akira10969/jpsme_event/settings/secrets/actions
2. Click "New repository secret"
```

### Step 2: Add These 4 Secrets

#### Secret 1:
- **Name:** `DB_USER`
- **Value:** `root`

#### Secret 2:
- **Name:** `DB_PASS`  
- **Value:** ' '

#### Secret 3:
- **Name:** `STAGING_HOST`
- **Value:** `localhost`

#### Secret 4:
- **Name:** `PRODUCTION_HOST`
- **Value:** `localhost`

### Step 3: Test It Works
1. Make any small change to your code (add a comment)
2. Commit and push:
   ```bash
   git add .
   git commit -m "test ci/cd pipeline"
   git push origin main
   ```
3. Go to your repository's **Actions** tab
4. Watch the workflow run!

---

## 🚨 Skip These for Now (Advanced Setup Later)

You can skip these SSH secrets initially:
- ❌ `STAGING_SSH_KEY` (not needed for local testing)
- ❌ `PRODUCTION_SSH_KEY` (not needed for local testing)

Add them later when you have real servers to deploy to.

---

## ✅ What This Will Enable

With just these 4 secrets, your CI/CD pipeline will:
- ✅ Run code quality checks
- ✅ Test database connections
- ✅ Validate your PHP code
- ✅ Check security vulnerabilities
- ✅ Run automated tests

---

## 🔧 Your XAMPP Database Info

For reference, your local XAMPP setup typically uses:
- **Host:** `localhost`
- **Username:** `root` 
- **Password:** (empty/blank)
- **Database:** `jpsme_event`

This matches what we're setting in the GitHub secrets!
