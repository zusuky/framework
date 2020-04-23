<?php

class Config
{

    /** @var  array  config */
    private static ?array $config = null;

    /**
     * configファイルをロードする
     *
     * @param  string  $configFile  configファイルパス
     */
    public static function load($configFile) : void
    {
        self::$config = require_once $configFile;
    }

    /**
     * configの値を取得する
     *
     * @param  string  $key  キー
     * @return  string|array
     */
    public static function get($key)
    {
        return Arr::get(self::$config, $key);
    }

}