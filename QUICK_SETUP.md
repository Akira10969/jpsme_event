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

## 🔍 How to Know If It's Working

### Step 1: Check GitHub Actions Tab
1. Go to: `https://github.com/Akira10969/jpsme_event/actions`
2. You should see workflow runs with these statuses:
   - 🟡 **Yellow dot** = Running
   - ✅ **Green checkmark** = Success  
   - ❌ **Red X** = Failed

### Step 2: Click on a Workflow Run
- Click on any workflow run to see details
- You'll see jobs like:
  - ✅ `Code Quality Check`
  - ✅ `Database Schema Test`  
  - ✅ `Security Vulnerability Scan`
  - ✅ `Build Application`

### Step 3: Check Individual Jobs
Click on each job to see:
- ✅ **Green checkmarks** = Steps passed
- ❌ **Red X** = Steps failed
- 📋 **Logs** = Detailed output

### Step 4: Success Indicators
**✅ Everything Working:**
```
✅ CI/CD Pipeline - JPSME Event Registration
  ✅ Code Quality Check (2m 30s)
  ✅ Database Schema Test (1m 45s)  
  ✅ Security Vulnerability Scan (3m 10s)
  ✅ Build Application (1m 20s)
```

**❌ Something Wrong:**
```
❌ CI/CD Pipeline - JPSME Event Registration
  ❌ Code Quality Check (0m 45s) - Failed
  ⚠️ Database Schema Test - Skipped
  ⚠️ Security Vulnerability Scan - Skipped
  ⚠️ Build Application - Skipped
```

### Step 5: Common Success Messages
Look for these in the logs:
- ✅ `"PHP syntax check passed!"`
- ✅ `"Database connection: SUCCESS"`
- ✅ `"Database schema imported successfully!"`
- ✅ `"Build completed successfully"`

### Step 6: If Something Fails
- Click on the ❌ failed job
- Read the error message in red
- Common issues:
  - Missing GitHub secrets
  - Database connection errors
  - PHP syntax errors

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
