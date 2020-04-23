<?php

class Arr
{

    /**
     * キー存在チェック
     *
     * @param  array   $arr  配列
     * @param  string  $key  キー
     * @return  boolean
     */
    public static function keyExists($arr, $key) : bool
    {
        $keys = explode('.', $key);
        foreach ($keys as $keyPart){
            if (!array_key_exists($keyPart, $arr)) {
                return false;
            }
            $arr = $arr[$keyPart];
        }
        return true;
    }

    /**
     * 取得
     *
     * @param  array   $arr      配列
     * @param  string  $key      キー
     * @param  mixed   $default  キーがない場合の返却値（省略時は空文字）
     * @return  mixed
     */
    public static function get($arr, $key, $default = '')
    {
        $rtnVal = $arr;
        $keys = explode('.', $key);
        foreach ($keys as $keyPart){
            if (array_key_exists($keyPart, $rtnVal)) {
                $rtnVal = $rtnVal[$keyPart];
            } else {
                return $default;
            }
        }
        return $rtnVal;
    }

    /**
     * 設定
     *
     * @param  array   $arr    配列（参照渡し）
     * @param  string  $key    キー
     * @param  mixed   $value  値
     */
    public static function set(&$arr, $key, $value) : void
    {
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!array_key_exists($key, $arr) || !is_array($arr[$key])) {
                $arr[$key] = [];
            }
            $arr = $arr[$key];
        }
        $arr[array_shift($keys)] = $value;
    }

    /**
     * 削除
     *
     * @param  array   $arr    配列（参照渡し）
     * @param  string  $key    キー
     */
    public static function delete(&$arr, $key)
    {
        if (!is_array($arr)) {
            return;
        }
        if (is_null($key) || strlen(trim($key)) === 0) {
            return;
        }
        $keys = explode('.', $key);
        $thisKey = array_shift($keys);
        if (empty($keys)) {
            unset($arr[$thisKey]);
        } elseif (array_key_exists($thisKey, $arr)) {
            $nextKey = implode('.', $keys);
            return static::delete($arr[$thisKey], $nextKey);
        }
    }

}