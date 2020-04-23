<?php

namespace Zusuky;

class GetParam
{

    /**
     * キー存在チェック
     *
     * @param  string  $key  キー
     * @return  boolean
     */
    public static function keyExists($key) : bool
    {
        return Arr::keyExists($_GET, $key);
    }

    /**
     * 取得
     * キーを指定しない場合は、全てのGETパラメータを返却する
     *
     * @param  string  $key      キー（省略時はnull）
     * @param  string  $default  キーがない場合の返却値（省略時は空文字）
     * @return  string|array
     */
    public static function get($key = null, $default = '')
    {
        if (func_num_args() === 0) {
            return $_GET;
        }
        return Arr::get($_GET, $key, $default);
    }

    /**
     * 設定
     *
     * @param  string  $key    キー
     * @param  string  $value  値
     */
    public static function set($key, $value) : void
    {
        Arr::set($_GET, $key, $value);
    }

}