<?php

if (!defined('IN_CRONLITE')) {
    define('IN_CRONLITE', true);
}

if (!isset($conf)) {
    $conf = [];
}

if (!isset($GLOBALS['conf'])) {
    $GLOBALS['conf'] = &$conf;
}

function siteurl($type = 0, $mode = 1)
{
    $scheme = 'http';
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $scheme = strtolower(trim(explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO'])[0]));
    } elseif (!empty($_SERVER['REQUEST_SCHEME'])) {
        $scheme = strtolower($_SERVER['REQUEST_SCHEME']);
    } elseif (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $scheme = 'https';
    }

    if (!in_array($scheme, ['http', 'https'], true)) {
        $scheme = 'http';
    }

    $host = '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $host = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_HOST'])[0]);
    } elseif (!empty($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
    } elseif (!empty($_SERVER['SERVER_NAME'])) {
        $host = $_SERVER['SERVER_NAME'];
    }

    $host = preg_replace('/[^A-Za-z0-9\.\-:\[\]]/', '', $host);
    if ($host === '') {
        $host = 'localhost';
    }

    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_NAME']) : '';
    $scriptDir = trim(dirname($scriptName), '/');
    $rootParts = $scriptDir === '' || $scriptDir === '.' ? [] : explode('/', $scriptDir);

    $knownEntryDirs = [
        defined('ADMIN_PATH') ? ADMIN_PATH : 'admin',
        'admin',
        'apply',
        'about',
        'site',
        'pwd',
        'install'
    ];

    if (!empty($rootParts) && in_array(end($rootParts), $knownEntryDirs, true)) {
        array_pop($rootParts);
    }

    $rootPath = empty($rootParts) ? '' : '/' . implode('/', $rootParts);

    if ((int) $type === 1) {
        return $rootPath === '' ? '/' : $rootPath;
    }

    if ((int) $type === 2) {
        return rtrim($host . $rootPath, '/');
    }

    return rtrim($scheme . '://' . $host . $rootPath, '/');
}

function theme_file($file)
{
    $file = ltrim(str_replace('\\', '/', (string) $file), '/');
    if ($file === '' || strpos($file, '..') !== false) {
        $file = '404.php';
    }

    $root = defined('ROOT') ? ROOT : dirname(__DIR__) . DIRECTORY_SEPARATOR;
    $template = isset($GLOBALS['conf']['template']) && $GLOBALS['conf']['template'] !== '' ? $GLOBALS['conf']['template'] : 'default';
    $template = preg_replace('/[^A-Za-z0-9_\.\-]/', '', $template);
    if ($template === '') {
        $template = 'default';
    }

    $candidates = [
        $root . 'template/' . $template . '/' . $file,
        $root . 'site/template/' . $file,
        $root . 'template/default/' . $file
    ];

    foreach ($candidates as $path) {
        if (is_file($path) && is_readable($path)) {
            return $path;
        }
    }

    return $root . 'site/template/404.php';
}
