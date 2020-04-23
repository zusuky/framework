<?php

return [

    // 環境定義（local|testing|staging|production）
    'env' => 'local',

    'lang' => [
        // デフォルト言語
        'default' => 'ja',

        // 選択可能言語
        'selectables' => ['ja'],
    ],

    // appディレクトリ定義
    'app_dir' => [
        // appのrootとなるディレクトリ
        'root'             => '/var/www/app/Admin/',

        // modelのディレクトリ
        'model_sql'        => '/var/www/app/Admin/Model/sql/',
        'model_sql_c'      => '/var/www/app/Admin/Model/sql_c/',

        // viewのディレクトリ
        'view_cache'       => '/var/www/app/Admin/view/cache/',
        'view_templates'   => '/var/www/app/Admin/view/templates/',
        'view_templates_c' => '/var/www/app/Admin/view/templates_c/',

        // メッセージリソース用ディレクトリ
        'resources_messages' => '/var/www/app/Admin/resources/messages/',

        // sassリソース用ディレクトリ
        'resources_sass' => '/var/www/app/Admin/resources/sass/',

        // jsリソース用ディレクトリ
        'resources_js' => '/var/www/app/Admin/resources/js/',
    ],

    // HTML定義
    'html' => [
        // アプリケーションのベースURL
        'url'  => 'http://localhost/admin/',

        // cssのベースURL
        'css'  => 'http://localhost/assets/admin/css/',

        // jsのベースURL
        'js'   => 'http://localhost/assets/admin/js/',

        // imgのベースURL
        'img'  => 'http://localhost/assets/admin/img/',
    ],

    // URI定義
    'uri' => [
        // HOME画面のURI（URIが無い場合は、このHOME画面のURIが指定されたものとして動作する）
        'home' => '/home/index/',
     ],

    // データソース定義
    'datasource' => [
        // 接続対象のDBMS（mysql|oracle)
        'connection' => 'mysql',

        // mysql（PDO）接続定義
        'mysql' => [
            'host'     => 'mysql',
            'port'     => '3306',
            'database' => 'mydb',
            'user'     => 'root',
            'pass'     => 'pass',
        ],

        // oracle（OCI8）接続定義
        'oracle' => [
            'sid'  => '',
            'user' => '',
            'pass' => '',
            'env'  => [
                'nls_lang'        => '',
                'oracle_home'     => '',
                'ld_library_path' => '',
            ],
        ],
    ],

    // log4php定義
    'log4php' => [
        // log4phpプロパティファイルのパス
        'properties' => __DIR__ . '/log4php.properties',

        // logger
        'logger' => 'adminLogger',
    ],

    // セッション定義
    'session' => [
        'save_handler'   => 'files',
        'save_path'      => '/tmp',
        'save_name'      => 'zusukyadminsid',
        'gc_maxlifetime' => 3600,
        'gc_probability' => 1,
        'gc_divisor'     => 1,
        'cookie_secure'  => 0,
    ],

    // 暗号化定義
    'crypt' => [
        'key'    => 'xxxxxxxxxxxxxxxx',
        'method' => 'AES-256-CBC',
    ],

    // ベーシック認証定義
    'basic_auth' => [
        'user' => '',
        'pass' => '',
    ],

];
