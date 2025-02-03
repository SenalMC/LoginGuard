LoginGuard
LoginGuard 是一个用于保护 BungeeCord 服务器管理员账户的工具，旨在防止恶意登录。

使用方法
请参考 LoginGuard.md 文件进行详细的使用说明。

安装说明
系统环境要求
PHP 7 或以上版本（推荐 PHP 8 及以上版本）
部署步骤
网页端部署：

在服务器上创建项目文件夹并上传网页文件。
通过访问 http://yourdomain.com/admin_login.html 来访问登录界面。
修改关键信息：

邮件发送地址： 修改 admin_auth.php 文件中 sendVerificationEmail($email, $code) 方法的 34 至 55 行，配置邮件发送地址。

修改后台密码： 可在 admin.php 中自定义验证方法，或者使用服务器托管商（例如宝塔面板）提供的加密访问方法。为增强安全性，强烈建议修改文件名以防止恶意爆破。

修改前端信息：

前端页面：修改 admin_login.html 文件，或根据需要将其重命名为 index.html，并配置为系统的默认文件。
邮件信息： 修改 admin_auth.php 中 34 至 55 行的邮件相关内容。

