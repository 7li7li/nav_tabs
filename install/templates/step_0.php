<?php if (!defined('IN_INSTALL')) {
    exit('Request Error!');
} ?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>安装向导 - 安装说明</title>
    <link href="templates/style/install.css" type="text/css" rel="stylesheet"/>
    <script type="text/javascript" src="templates/js/jquery.min.js"></script>
</head>
<body>
<div class="header"></div>
<div class="mainBody">
    <div class="text">
        <h3>安装说明</h3>
        <div class="hr_8"></div>
        <p>安装前请确认服务器已正确配置 PHP、数据库和必要扩展。</p>
        <p>继续安装后，向导会写入数据库结构和默认配置。若已存在旧数据，请先自行备份。</p>
        <p>安装完成后请删除安装目录或保留安装锁文件，并立即修改后台默认账号密码。</p>
        <div class="hr_8"></div>
        <h4>默认后台信息</h4>
        <p>后台路径：<code>/admin</code></p>
        <p>默认账号：<code>admin</code></p>
        <p>默认密码：<code>123456</code></p>
    </div>
    <div class="footer"><span class="step"></span>
        <span class="formSubBtn">
            <a href="javascript:void(0);" onclick="window.close();return false;" class="back">取消</a>
            <a href="?s=1" class="submit">继续</a>
        </span>
    </div>
</div>
</body>
</html>
