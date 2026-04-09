LoginGuard

LoginGuard is a tool designed to protect BungeeCord server administrator accounts and prevent malicious logins.
LoginGuard 是一个用于保护 BungeeCord 服务器管理员账户的工具，旨在防止恶意登录。

⸻

Usage | 使用方法

Please refer to the LoginGuard.md file for detailed usage instructions.
请参考 LoginGuard.md 文件进行详细的使用说明。

⸻

Installation Guide | 安装说明

System Requirements | 系统环境要求

PHP 7 or above (PHP 8 or higher is recommended).
PHP 7 或以上版本（推荐 PHP 8 及以上版本）

⸻

Deployment Steps | 部署步骤

Web Deployment | 网页端部署
Create a project folder on your server and upload the web files.
在服务器上创建项目文件夹并上传网页文件。

Access the login interface via:
http://yourdomain.com/admin_login.html
通过访问：http://yourdomain.com/admin_login.html 来访问登录界面。

⸻

Modify Key Information | 修改关键信息
Email sender configuration:
Edit lines 34 to 55 of the sendVerificationEmail($email, $code) function in admin_auth.php to configure the email sender address.
邮件发送地址：修改 admin_auth.php 文件中 sendVerificationEmail($email, $code) 方法的 34 至 55 行，配置邮件发送地址。

⸻

Modify backend password:
You can customize the verification method in admin.php, or use encrypted access methods provided by your hosting panel (such as 宝塔面板). For better security, it is strongly recommended to rename the file to prevent brute-force attacks.
修改后台密码：可在 admin.php 中自定义验证方法，或者使用服务器托管商（例如宝塔面板）提供的加密访问方法。为增强安全性，强烈建议修改文件名以防止恶意爆破。

⸻

Modify Frontend Information | 修改前端信息
Frontend page:
Modify the admin_login.html file, or rename it to index.html and set it as the system default page if needed.
前端页面：修改 admin_login.html 文件，或根据需要将其重命名为 index.html，并配置为系统的默认文件。

⸻

Email content:
Edit the email-related content in lines 34 to 55 of admin_auth.php.
邮件信息：修改 admin_auth.php 中 34 至 55 行的邮件相关内容。
