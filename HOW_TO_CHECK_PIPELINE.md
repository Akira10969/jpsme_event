# 🔍 How to Check If Your CI/CD Pipeline Is Working

## 📍 Step-by-Step Verification Guide

### 1. 🚀 Trigger the Pipeline
First, make a small change and push it:
```bash
# Add a comment to any PHP file
echo "// Testing CI/CD pipeline" >> index.php

# Commit and push
git add .
git commit -m "test: trigger CI/CD pipeline"
git push origin main
```

### 2. 🏃‍♂️ Go to GitHub Actions
Visit: `https://github.com/Akira10969/jpsme_event/actions`

### 3. 👀 What You Should See

#### ✅ **SUCCESS - Everything Working:**
```
🟢 CI/CD Pipeline - JPSME Event Registration  
   ⏱️ 8m 32s ago
   ✅ Code Quality Check         (2m 15s)
   ✅ Database Schema Test       (1m 45s) 
   ✅ Security Vulnerability Scan (3m 02s)
   ✅ Build Application          (1m 30s)
   ✅ Deploy to Staging          (2m 10s)
```

#### ❌ **FAILURE - Something Wrong:**
```
🔴 CI/CD Pipeline - JPSME Event Registration
   ⏱️ 3m 15s ago  
   ❌ Code Quality Check         (0m 45s) - FAILED
   ⚠️ Database Schema Test       - SKIPPED
   ⚠️ Security Vulnerability Scan - SKIPPED  
   ⚠️ Build Application          - SKIPPED
```

### 4. 🔍 Check Job Details
Click on any job to see detailed logs:

#### ✅ **Successful Job Logs:**
```
✅ Checkout code
✅ Setup PHP  
✅ PHP Syntax Check
   🔍 Checking PHP syntax...
   ✅ PHP syntax check passed!
✅ Check file permissions
   🔒 Checking file permissions...
   ✅ File permissions check passed!
✅ Database connection test
   Database connection: SUCCESS
```

#### ❌ **Failed Job Logs:**
```
❌ Setup PHP
✅ Checkout code
❌ Database connection test
   Error: Access denied for user 'root'@'localhost'
   ❌ Database connection failed!
```

### 5. 📧 Notification Indicators

#### **Email Notifications:**
- ✅ Success: "Workflow run succeeded"
- ❌ Failure: "Workflow run failed"

#### **GitHub Interface:**
- Green checkmark (✅) next to commit = Success
- Red X (❌) next to commit = Failure

### 6. 🎯 Quick Health Check

#### **Manual Test URLs** (after deployment):
```bash
# Test if your site loads
curl -I http://localhost/jpsme_event/index.php
# Should return: HTTP/1.1 200 OK

# Test registration forms
curl -I http://localhost/jpsme_event/machine_design_registration.php
# Should return: HTTP/1.1 200 OK
```

### 7. 🚨 Troubleshooting Common Issues

#### **❌ "Secrets not found" Error:**
- Go to Settings → Secrets and variables → Actions
- Verify all 4 secrets are added:
  - ✅ `DB_USER`
  - ✅ `DB_PASS` 
  - ✅ `STAGING_HOST`
  - ✅ `PRODUCTION_HOST`

#### **❌ "Database connection failed":**
- Check if XAMPP MySQL is running
- Verify `DB_USER=root` and `DB_PASS` is empty
- Make sure database `jpsme_event` exists

#### **❌ "PHP syntax errors":**
- Check for missing semicolons, brackets
- Run locally: `php -l filename.php`

### 8. 🎉 Success Confirmation

**You'll know it's working when you see:**

1. **GitHub Actions Tab:**
   - ✅ All jobs completed successfully
   - 🟢 Green status badges

2. **Email/Notifications:**
   - "Workflow run succeeded" message

3. **Commit History:**
   - ✅ Green checkmarks next to your commits

4. **Build Artifacts:**
   - Deployment packages created
   - No error messages in logs

### 9. 🔄 Ongoing Monitoring

**The pipeline runs automatically on:**
- ✅ Every push to `main` branch
- ✅ Every pull request  
- ✅ Daily at 2 AM UTC (health checks)

**Check periodically:**
- Actions tab for any failed runs
- Email for failure notifications
- Repository badges for status

---

## 🆘 Need Help?

If you see failures:
1. **Click the ❌ failed job**
2. **Read the error message**  
3. **Check the troubleshooting section above**
4. **Verify your GitHub secrets**
5. **Ensure XAMPP is running**

The pipeline is working perfectly when you see all ✅ green checkmarks! 🎉
