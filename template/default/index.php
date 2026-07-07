<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($conf['title'], ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="keywords" content="<?php echo htmlspecialchars($conf['keywords'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($conf['description'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="author" content="LyLme">
    <link rel="icon" href="<?php echo htmlspecialchars($conf['logo'], ENT_QUOTES, 'UTF-8'); ?>" type="image/x-icon">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="full-screen" content="yes">
    <meta name="browsermode" content="application">
    <meta name="x5-fullscreen" content="true">
    <meta name="x5-page-mode" content="app">
    <meta name="lsvn" content="<?php echo base64_encode($conf['version']); ?>">
    <link rel="stylesheet" href="<?php echo $templatepath; ?>/css/style.css?v=20260707" type="text/css">
</head>

<body>
    <?php
    $rel = $conf["mode"] == 2 ? 'noopener noreferrer' : 'nofollow noopener noreferrer';
    $sessionList = isset($_SESSION['list']) && is_array($_SESSION['list']) ? $_SESSION['list'] : [];
    $backgroundUrl = !empty(background()) ? background() : './assets/img/background.jpg';

    function navtabs_e($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    function navtabs_search_url($row)
    {
        if (checkmobile() && !empty($row['sou_waplink'])) {
            return $row['sou_waplink'];
        }
        return $row['sou_link'];
    }

    $searchEngines = [];
    $soulists = $site->getSou();
    while ($sou = $DB->fetch($soulists)) {
        $searchEngines[] = $sou;
    }
    $firstEngine = isset($searchEngines[0]) ? $searchEngines[0] : null;

    $groupsData = [];
    $groups = $site->getGroups();
    while ($group = $DB->fetch($groups)) {
        $groupId = (int)$group['group_id'];
        $groupPwd = isset($group['group_pwd']) ? $group['group_pwd'] : '';
        $links = [];
        $groupLinks = $site->getCategoryLinks($groupId);

        while ($link = $DB->fetch($groupLinks)) {
            $linkPwd = isset($link['link_pwd']) ? $link['link_pwd'] : '';
            $linkStatus = isset($link['link_status']) ? (int)$link['link_status'] : 1;
            $linkAllowed = true;

            if (empty($groupPwd) && !empty($linkPwd) && !in_array((int)$linkPwd, $sessionList, true)) {
                $linkAllowed = false;
            }

            if ($linkStatus && $linkAllowed) {
                $links[] = $link;
            }
        }

        $groupsData[] = [
            'group' => $group,
            'links' => $links
        ];
    }
    ?>

    <div class="background-image" style="background-image: url('<?php echo navtabs_e($backgroundUrl); ?>');"></div>

    <div class="top-links" aria-label="站点导航">
        <?php
        $tagslists = $site->getTags();
        while ($tag = $DB->fetch($tagslists)) {
            echo '<a href="' . navtabs_e($tag['tag_link']) . '"';
            if ((int)$tag['tag_target'] === 1) {
                echo ' target="_blank" rel="noopener noreferrer"';
            }
            echo '>' . navtabs_e($tag['tag_name']) . '</a>';
        }
        ?>
    </div>

    <main class="container">
        <section class="clock-container" aria-label="当前时间">
            <div class="time" id="currentTime">00:00:00</div>
            <div class="date" id="currentDate">0000年0月0日 星期一</div>
        </section>

        <section class="search-container" aria-label="搜索">
            <form id="navSearchForm" class="search-form" action="#" method="get" target="_blank">
                <div class="search-box">
                    <button class="search-engine-selector" id="searchEngineToggle" type="button" aria-label="选择搜索引擎">
                        <span class="search-engine-icon" id="searchIcon">
                            <?php echo $firstEngine ? $firstEngine['sou_icon'] : ''; ?>
                        </span>
                        <span class="dropdown-arrow">▾</span>
                    </button>
                    <input
                        type="text"
                        class="search-input"
                        id="searchInput"
                        autocomplete="off"
                        data-default-url="<?php echo $firstEngine ? navtabs_e(navtabs_search_url($firstEngine)) : 'https://www.baidu.com/s?wd='; ?>"
                        placeholder="<?php echo $firstEngine ? navtabs_e($firstEngine['sou_hint']) : '搜索...'; ?>">
                </div>

                <div class="search-engines-dropdown" id="searchEngines">
                    <?php foreach ($searchEngines as $index => $engine) { ?>
                        <button
                            type="button"
                            class="search-engine-option<?php echo $index === 0 ? ' active' : ''; ?>"
                            data-url="<?php echo navtabs_e(navtabs_search_url($engine)); ?>"
                            data-placeholder="<?php echo navtabs_e($engine['sou_hint']); ?>"
                            data-icon="<?php echo navtabs_e($engine['sou_icon']); ?>">
                            <span class="option-icon"><?php echo $engine['sou_icon']; ?></span>
                            <span><?php echo navtabs_e($engine['sou_name']); ?></span>
                        </button>
                    <?php } ?>
                </div>
            </form>
        </section>

        <nav class="tabs-nav" aria-label="分类">
            <div class="tab-buttons" id="tabButtons">
                <?php foreach ($groupsData as $index => $item) {
                    $group = $item['group'];
                    $groupId = (int)$group['group_id'];
                    echo '<button class="tab-button' . ($index === 0 ? ' active' : '') . '" type="button" data-target="group_' . $groupId . '">' . navtabs_e($group['group_name']) . '</button>';
                } ?>
            </div>
        </nav>

        <section class="main-content">
            <?php foreach ($groupsData as $index => $item) {
                $group = $item['group'];
                $groupId = (int)$group['group_id'];
                $links = $item['links'];
            ?>
                <div class="sites-panel<?php echo $index === 0 ? ' active' : ''; ?>" id="group_<?php echo $groupId; ?>" data-panel>
                    <div class="sites-container">
                        <?php foreach ($links as $link) { ?>
                            <a class="site-card" rel="<?php echo $rel; ?>" href="<?php echo navtabs_e($link['url']); ?>" title="<?php echo navtabs_e($link['name']); ?>" target="_blank">
                                <span class="site-card-bg-icon" aria-hidden="true"><?php echo $link['icon']; ?></span>
                                <span class="site-card-content">
                                    <span class="site-icon"><?php echo $link['icon']; ?></span>
                                    <span class="site-title"><?php echo navtabs_e($link['name']); ?></span>
                                </span>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </section>

        <?php include "footer.php"; ?>
    </main>

    <button class="ai-chat-trigger show" id="aiChatTrigger" type="button" aria-label="打开AI对话">
        <span class="ai-chat-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" focusable="false">
                <path d="M12 2.75l1.76 5.36 5.64.01-4.56 3.31 1.73 5.38L12 13.48l-4.57 3.33 1.73-5.38L4.6 8.12l5.64-.01L12 2.75z"></path>
                <path d="M5.8 17.2l.7 2.05 2.15.01-1.73 1.26.65 2.08-1.77-1.28-1.77 1.28.65-2.08-1.73-1.26 2.15-.01.7-2.05z"></path>
            </svg>
        </span>
        <span>AI对话</span>
    </button>

    <div class="ai-chat-modal" id="aiChatModal" aria-hidden="true">
        <section class="ai-chat-container" id="aiChatContainer" role="dialog" aria-modal="true" aria-label="AI对话">
            <header class="ai-chat-header">
                <h3>AI对话</h3>
                <div class="ai-chat-controls">
                    <button class="ai-chat-maximize" id="aiChatMaximize" type="button" title="最大化" aria-label="最大化">□</button>
                    <button class="ai-chat-close" id="aiChatClose" type="button" title="关闭" aria-label="关闭">×</button>
                </div>
            </header>
            <div class="ai-chat-content">
                <iframe id="aiChatFrame" data-src="https://ai.7li7li.cn" src="about:blank" title="AI对话" allowfullscreen></iframe>
            </div>
            <div class="ai-chat-resize-handle" aria-hidden="true"></div>
        </section>
    </div>
</body>

</html>
