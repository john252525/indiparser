<?php
require_once __DIR__ . '/lib/db/safemysql.class.php';
require_once __DIR__ . '/.config.php';
$config = [
    'db' => [
        'user' => _DB_LOGIN_,
        'pass' => _DB_PASSWORD_,
        'charset' => 'utf8mb4',
        'db' => _DB_DATABASE_,
    ],
];
$db = new SafeMySQL($config['db']);

$nl = php_sapi_name() === 'cli'
    ? "\n"
    : '<br>';


function dataEcho($data, $caption=null) {
    $json = json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    if (php_sapi_name() === 'cli') {
        echo ($caption ? $caption . ': ' : '')
             . $json . "\n";
    } else {
        echo '<pre style="white-space: pre-wrap;">'
             . ($caption ? htmlspecialchars($caption, ENT_QUOTES) . ': ' : '')
             . htmlspecialchars($json, ENT_QUOTES)
             . '</pre>';
    }
}


function guidv4($data = null) {
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
