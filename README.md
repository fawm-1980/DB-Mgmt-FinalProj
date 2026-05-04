# 🌌 Galactic Blog Terminal  
**Secure PHP + MySQL Blog Platform with Multi-Factor Authentication (MFA)**

---

## 📌 Overview

Galactic Blog Terminal is a full-stack blog platform built using **PHP and MySQL**, with a focus on:

- Secure authentication and authorization
- Database design and normalization
- Real-world deployment on a live server
- Responsive UI for both desktop and mobile

This project demonstrates how a traditional LAMP-style application can be hardened using modern security practices, including **Multi-Factor Authentication (MFA)**.

---

## 🚀 Key Features

### 📝 Core Functionality
- User registration and login
- Create, view, and search blog posts
- Comment system
- Category and tag management
- Responsive UI (desktop + mobile)

---

### 🔐 Security Features
- Password hashing (`password_hash`, `password_verify`)
- CSRF protection on all forms
- XSS protection via output escaping
- Session hardening (cookies, strict mode, SameSite)
- Account lockout for repeated login attempts
- Role-based access control (admin vs standard users)
- Content Security Policy (CSP)

---

### 🔑 Multi-Factor Authentication (MFA)
- Google Authenticator compatible
- QR code onboarding (no manual typing required)
- Base32 secret generation
- 30-second rotating TOTP codes
- ±1 time window tolerance

---

## 📂 Project Structure

```
/var/www/html
│
├── index.php                # Homepage (recent posts)
├── nav.php                  # Navigation bar (session-aware)
├── db_connect.php           # DB connection wrapper
├── db_config.php            # DB credentials (NOT in repo)
│
├── includes/
│   └── security.php         # Security logic (CSRF, MFA, sessions)
│
├── js/
│   ├── qrcode.min.js        # QR code library (local copy)
│   └── mfa_qr.js            # QR rendering logic
│
├── styles.css               # Global + responsive styles
│
├── login_form.php
├── login.php
├── register_form.php
├── register.php
│
├── insert_post_form.php
├── insert_post.php
├── get_posts.php
├── insert_comment.php
│
├── post_search_form.php
├── post_search.php
│
├── manage_categories.php
├── manage_tags.php
│
├── mfa_setup.php
├── mfa_verify.php
├── mfa_verify_process.php
├── mfa_reset.php (placeholder)
│
└── images/
```

---

## ⚙️ Local Setup (Development)

### 1. Clone the Repository

```bash
git clone https://github.com/fawm-1980/DB-Mgmt-FinalProj.git
cd DB-Mgmt-FinalProj
```

---

### 2. Configure Database Connection

Create a file named:

```
db_config.php
```

Add your database credentials:

```php
<?php
$DB_HOST = 'localhost';
$DB_USER = 'your_user';
$DB_PASS = 'your_password';
$DB_NAME = 'BlogDB';
```

👉 This file is not included in the repo to protect sensitive credentials.

---

### 3. Start Local Server

```bash
php -S localhost:8000
```

Then open:

```
http://localhost:8000
```

---

## 🌐 Deployment (Live Server)

This project is deployed to a Linux server using Apache.

### SSH into server:

```bash
ssh setup@143.110.234.42 -p 22006
```

---

### Navigate to project directory:

```bash
cd /var/www/html
```

---

### Pull latest changes:

```bash
git pull
```

---

### Fix permissions (if needed):

```bash
sudo chown -R setup:setup /var/www/html
```

---

### Access live site:

```
http://143.110.234.42:8006/
```

---

## 👤 How to Use the Application

### 1. Register
- Create a new account
- Set a secure password

---

### 2. Login
- Enter credentials
- If MFA is enabled, you will be prompted for a code

---

### 3. Setup MFA
- Navigate to MFA setup page
- Scan QR code using Google Authenticator
- Enter the generated 6-digit code

---

### 4. Create Content
- Create blog posts
- Add comments
- Search posts using filters

---

## 🔐 Security Design

### Authentication
- Passwords stored securely using hashing
- Verified using `password_verify()`

---

### MFA
- Custom TOTP implementation
- Compatible with standard authenticator apps
- QR-based onboarding improves usability

---

### Input Protection
- CSRF tokens required for all POST requests
- Output escaped to prevent XSS

---

### Database Security
- All queries use prepared statements
- Foreign keys enforce data integrity

---

### Session Security
- HttpOnly cookies
- SameSite protection
- Strict session mode

---

### Headers

```
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'
```

---

## 🏗️ Architecture

The application follows a traditional 3-tier web architecture:

### 1. Client Layer (Presentation)
- Web browser (desktop or mobile)
- Handles user interaction
- Sends HTTP requests to server
- Renders HTML/CSS/JS responses

---

### 2. Application Layer (Server / PHP)
- Apache web server
- PHP scripts handle:
  - Authentication
  - Session management
  - MFA verification
  - Business logic (posts, comments, search)
- Security enforced via:
  - CSRF validation
  - Output escaping
  - Prepared SQL statements

---

### 3. Data Layer (Database)
- MySQL database
- Stores:
  - Users
  - Blog posts
  - Comments
  - Categories & tags
  - MFA secrets
- Enforces:
  - Foreign key relationships
  - Data integrity

---

### 🔄 Request Flow Example

1. User submits login form
2. PHP validates credentials (`password_verify`)
3. If MFA enabled → user prompted for TOTP code
4. `verify_totp_code()` validates code
5. Session is updated
6. User gains access to protected pages

---

### 🔐 Security Flow

- Input → validated + sanitized
- Output → escaped (`escape_html`)
- Queries → prepared statements
- Requests → CSRF protected
- Sessions → hardened
- Access → role + MFA enforced

---

## ⚠️ Known Limitations

- No UI for MFA reset (SQL required)
- Backup codes not fully implemented
- MFA secrets stored in plaintext
- No HTTPS enforced (HTTP only)
- No MFA rate limiting
- Limited DB user privilege control

---

## 🧠 Development Notes

- No Composer used (manual dependency management)
- QR code library stored locally (no CDN dependency)
- CSP enforced without inline scripts
- Responsive UI implemented with CSS media queries

---

## 🔄 Git Workflow

Local:

```bash
git add .
git commit -m "message"
git push
```

Server:

```bash
git pull
```

---

## 📌 Summary

This project demonstrates:

- Secure full-stack PHP development
- Multi-factor authentication without external libraries
- Real-world deployment and debugging
- Responsive UI design
- Practical security hardening techniques

---

## 🏁 Final Notes

This application is fully functional and deployed.  
All major security features are implemented and tested.

Future improvements could include:
- MFA reset interface
- Backup code support
- HTTPS deployment
- Admin management UI
