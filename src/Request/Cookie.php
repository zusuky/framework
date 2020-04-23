<?php

class Cookie
{

    /**
     * キー存在チェック
     *
     * @param  string  $key  キー
     * @return  boolean
     */
    public static function keyExists($key) : bool
    {
        return Arr::keyExists($_COOKIE, $key);
    }

    /**
     * 取得
     * キーを指定しない場合は、全てのCOOKIEを返却する
     *
     * @param  string  $key      キー（省略時はnull）
     * @param  string  $default  キーがない場合の返却値（省略時は空文字）
     * @return  string|array
     */
    public static function get($key = null, $default = '')
    {
        if (func_num_args() === 0) {
            return $_COOKIE;
        }
        return Arr::get($_COOKIE, $key, $default);
    }

    /**
     * 設定
     *
     * @param  string   $key            キー
     * @param  string   $value          値
     * @param  boolean  $isImmediately  クッキーに即反映させるかどうか（省略時はtrue）
     * @param  int      $second         クッキー有効秒数（省略時は2592000）
     */
    public static function set($key, $value, $isImmediately = true, $second = 2592000) : void
    {
        setcookie($key, '', time() -1800, '/');
        setcookie($key, $value, time() + $second, '/');
        if ($isImmediately) {
            Arr::set($_COOKIE, $key, $value);
        }
    }

    /**
     * 削除
     *
     * @param  string   $key            キー
     * @param  boolean  $isImmediately  クッキーに即反映させるかどうか（省略時はtrue）
     */
    public static function delete($key, $isImmediately = true) : void
    {
        setcookie($key, '', time() -1800, '/');
        if ($isImmediately) {
            Arr::delete($_COOKIE, $key);
        }
    }

}