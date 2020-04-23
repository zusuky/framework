<?php

/**
 * デバッグログ出力
 *
 * @param  string  $message  出力メッセージ
 */
function debugLog($message)
{
    $logRow = $message;
    getLogger()->debug($logRow);
}

/**
 * インフォログ出力
 *
 * @param  string  $message  出力メッセージ
 */
function infoLog($message)
{
    $logRow = $message;
    getLogger()->info($logRow);
}

/**
 * ワーニングログ出力
 *
 * @param  string  $message  出力メッセージ
 */
function warnLog($message)
{
    $logRow = $message;
    getLogger()->warn($logRow);
}

/**
 * エラーログ出力
 *
 * @param  string        $message       出力メッセージ
 * @param  object|array  $error_detail  エラー詳細（Objectまたは配列）
 */
function errorLog($message, $error_detail = null)
{
    $logRow = $message;
    if (!is_null($error_detail) && (is_object($error_detail) || is_array($error_detail))) {
        addErrorDetail($logRow, $error_detail);
    }
    getLogger()->error($logRow);
}

/**
 * フェイタルログ出力
 *
 * @param  string        $message       出力メッセージ
 * @param  object|array  $error_detail  エラー詳細（Objectまたは配列）
 */
function fatalLog($message, $error_detail = null)
{
    $logRow = $message;
    if (!is_null($error_detail) && (is_object($error_detail) || is_array($error_detail))) {
        addErrorDetail($logRow, $error_detail);
    }
    getLogger()->fatal($logRow);
}

/**
 * ロガー取得
 *
 * @return Logger
 */
function getLogger()
{
    $logger = Config::get('log4php.logger');
    if (!array_key_exists($logger, $GLOBALS) || is_null($GLOBALS[$logger])) {
        Logger::configure(Config::get('log4php.properties'));
        $GLOBALS[$logger] = Logger::getLogger($logger);
    }
    return $GLOBALS[$logger];
}

/**
 * メッセージへのエラー詳細付加
 *
 * @param  string        $logRow        出力メッセージ（参照渡し）
 * @param  object|array  $error_detail  エラー詳細（Objectまたは配列）
 */
function addErrorDetail(&$logRow, $error_detail)
{
    $logRow .= ' Error details =' . mb_substr(print_r($error_detail, true), 0, 4000);
}
