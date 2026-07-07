<?php
/* 
 * @Description: 关于页面
 * @Date: 2024-01-23 12:25:35
 * @LastEditTime: 2026-03-22 18:10:13
 */
$title = '关于页面设置';
include './head.php';
$set = isset($_GET['set'])?$_GET['set']:"";
$aboutContent = isset($conf['about_content']) ? $conf['about_content'] : '';
$aboutUrl = siteurl() . '/about';
if ($set== 'conf_submit') {
    $about = isset($_POST['about']) ? $_POST['about'] : '';
    if (!saveSetting('about_content', $about)) {
        echo '<script>alert("修改失败，请检查数据库配置！");history.go(-1);</script>';
        exit();
    }
    echo '<script>alert("修改成功！");window.location.href="./about.php";</script>';
    exit();
}
if ($set == 'default') {
    $defaultAboutContent = "<h3>关于本站</h3>\r\n<p>本站致力于提供简洁高效的上网导航和搜索入口。</p>\r\n<hr>\r\n<h3>本站承诺</h3>\r\n<p><strong>不会主动收集用户隐私信息。</strong></p>\r\n<p>本站链接直接跳转目标站点，不会记录点击、访问或搜索内容。</p>\r\n<hr>\r\n<h3>申请收录</h3>\r\n<p>请访问 <a href=\"../apply\" target=\"_blank\">收录申请</a> 页面提交。</p>";

    if (!saveSetting('about_content', $defaultAboutContent)) {
        echo '<script>alert("恢复默认失败，请检查数据库配置！");history.go(-1);</script>';
        exit();
    }
    echo '<script>alert("恢复默认成功！");window.location.href="./about.php";</script>';
    exit();
}
?>
<main class="lyear-layout-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4>关于页面设置</h4>
                        <div class="panel-body">
                            <form action="./about.php?set=conf_submit" method="POST">
                                <div class="form-group">
                                    <label class="btn-block">关于页面地址</label>
                                    <p><code><?php echo htmlspecialchars($aboutUrl, ENT_QUOTES, 'UTF-8'); ?></code></p>
                                    <a class="btn btn-cyan" href="<?php echo htmlspecialchars($aboutUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">访问关于页面</a>
                                    <a class="btn btn-danger" href="./about.php?set=default" onclick="return confirm('确定将关于页面内容恢复默认？\n注意：该操作不可逆');">恢复默认内容</a>
                                </div>
                                <div class="form-group">
                                    <label for="about_content">关于页内容</label>
                                    <textarea id="about_content" rows="20" class="form-control" name="about" placeholder="显示在关于页面的内容" spellcheck="false" style="min-height:420px;resize:vertical;"><?php echo htmlspecialchars($aboutContent, ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    <small class="help-block">显示在关于页面的内容<code>使用HTML代码编写</code></small>
                                </div>
                                <div class="form-about">
                                    <input type="submit" class="btn btn-primary btn-block" value="保存">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include './footer.php';
?>
