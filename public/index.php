<?php

// ライブラリ読み込み
require_once dirname (__DIR__) . '/vendor/autoload.php';

/*
 * configの読み込み定義
 * uri別に、読み込むconfigを指定する
 *   - キー：uri（スラッシュで分割した一つ目）
 *   - バリュー：読み込むconfigファイル
 * キーにアスタリスクを設定すると、それがデフォルトのconfigとなる
 * マッチしなかった場合は、デフォルトのconfigを読み込むことになる
 */
$configs = [
    'admin' => dirname(__DIR__) . '/config/config.admin.php',
    '*'     => dirname(__DIR__) . '/config/config.front.php',
];

// Zusukyに魂を注入する
Zusuky::bootstrap($configs);