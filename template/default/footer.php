<?php if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("Location:/");
} ?>

<footer class="footer">
    <?php
    if (!empty(theme_config('gonganbei', ""))) {
        preg_match_all('/\d+/', theme_config('gonganbei'), $gab);
        echo '<a class="icp" href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=' . $gab[0][0] . '" target="_blank" rel="nofollow noopener">
            <img src="/assets/img/icp.png" alt="公安网备" width="16" height="16">' . theme_config('gonganbei') . '</a>';
    }
    ?>
    <?php if ($conf['icp'] != null) {
        echo '<a href="http://beian.miit.gov.cn/" class="icp" target="_blank" rel="nofollow noopener">' . $conf['icp'] . '</a>';
    } ?>
    <p><?php echo $conf['copyright']; ?></p>
    <?php if ($conf['wztj'] != null) {
        echo $conf["wztj"];
    } ?>
</footer>

<script src="<?php echo $templatepath; ?>/js/script.js?v=20260707"></script>
<script src="<?php echo $cdnpublic ?>/assets/js/svg.js"></script>
