# Step-by-Step Guide: Setting Up GitHub Secrets

## 🔐 How to Configure GitHub Secrets for CI/CD

### Step 1: Access Your GitHub Repository
1. Go to your GitHub repository: `https://github.com/Akira10969/jpsme_event`
2. Click on the **"Settings"** tab (near the top right of the repository page)
3. In the left sidebar, click **"Secrets and variables"**
4. Click **"Actions"**

### Step 2: Add Repository Secrets
For each secret below, click **"New repository secret"** and fill in:

---

#### 🗄️ Database Secrets

**Secret Name:** `DB_USER`
**Secret Value:** 
```
root
```
*Note: This is your MySQL username (usually 'root' for XAMPP)*

---

**Secret Name:** `DB_PASS`  
**Secret Value:**
```
your_mysql_password_here
```
*Note: This is your MySQL password. If using XAMPP, it might be empty (leave blank) or set to your custom password*

---

#### 🖥️ Server Configuration Secrets

**Secret Name:** `STAGING_HOST`
**Secret Value:**
```
staging.yourdomain.com
```
*Example: `staging.example.com` or `test.mywebsite.com`*

---

**Secret Name:** `PRODUCTION_HOST`  
**Secret Value:**
```
yourdomain.com
```
*Example: `mywebsite.com` or `jpsme-event.com`*

---

#### 🔑 SSH Key Secrets (For Server Deployment)

**Secret Name:** `STAGING_SSH_KEY`
**Secret Value:**
```
-----BEGIN OPENSSH PRIVATE KEY-----
b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAAAFwAAAAdzc2gtcn
NhAAAAAwEAAQAAAQEAxxx...your_private_key_content_here...xxxxx
-----END OPENSSH PRIVATE KEY-----
```

**Secret Name:** `PRODUCTION_SSH_KEY`
**Secret Value:**
```
-----BEGIN OPENSSH PRIVATE KEY-----  
b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAAAFwAAAAdzc2gtcn
NhAAAAAwEAAQAAAQEAxxx...your_private_key_content_here...xxxxx
-----END OPENSSH PRIVATE KEY-----
```

---

## 🛠️ For Local Development (XAMPP Setup)

Since you're using XAMPP locally, here's what you should use:

### Database Configuration:
```
DB_USER=root
DB_PASS=
```
*(Leave password empty if you haven't set one in XAMPP)*

### For Local Testing (Skip SSH for now):
```
STAGING_HOST=localhost
PRODUCTION_HOST=localhost  
```

---

## 📱 Visual Step-by-Step Screenshots

### Step 1: Go to Repository Settings
```
GitHub Repository → Settings Tab → Secrets and variables → Actions
```

### Step 2: Click "New repository secret"
```
[New repository secret] button (green button on the right)
```

### Step 3: Fill in Secret Details
```
Name: DB_USER
Secret: root

[Add secret] button
```

### Step 4: Repeat for All Secrets
Create these secrets one by one:
- ✅ `DB_USER` → `root`
- ✅ `DB_PASS` → ` ` (empty for XAMPP default)
- ✅ `STAGING_HOST` → `localhost` (for testing)
- ✅ `PRODUCTION_HOST` → `localhost` (for testing)

---

## 🚀 Quick Start for Local Testing

If you want to test the CI/CD pipeline without real servers, use these values:

```bash
# In GitHub Secrets:
DB_USER=root
DB_PASS=(leave empty)
STAGING_HOST=localhost  
PRODUCTION_HOST=localhost
STAGING_SSH_KEY=(skip for now)
PRODUCTION_SSH_KEY=(skip for now)
```

---

## 🔍 How to Check if Secrets are Set Correctly

1. Go to your repository's **Actions** tab
2. Push any change to trigger the workflow
3. Click on the running workflow
4. Check if the "Code Quality Check" job passes
5. Look for any error messages about missing secrets

---

## ⚠️ Important Notes

### For Database Secrets:
- **XAMPP Default**: Username is `root`, password is usually empty
- **Custom Setup**: Use your actual MySQL credentials

### For SSH Keys (Advanced):
- Only needed for real server deployment
- Can skip initially for local testing
- Generate with: `ssh-keygen -t rsa -b 4096 -C "your_email@example.com"`

### For Hostnames:
- **Testing**: Use `localhost`
- **Real Deployment**: Use your actual domain names

---

## 🎯 What to Do Right Now

1. **Go to GitHub**: `https://github.com/Akira10969/jpsme_event/settings/secrets/actions`
2. **Add these 4 secrets first**:
   - `DB_USER` → `root`
   - `DB_PASS` → (empty)
   - `STAGING_HOST` → `localhost`
   - `PRODUCTION_HOST` → `localhost`
3. **Test the pipeline**: Make any small change and push to GitHub
4. **Check Actions tab**: See if workflows run successfully

You can always update these secrets later when you have real servers!
