<?php

class AutoLoader
{

    /** @var  array  オートロード対象のディレクトリ */
    public static array $dirs = [];

    /**
     * オートロード対象の追加
     *
     * @param  string  $dir  ディレクトリ
     */
    public static function add($dir)
    {
        self::addRecursively($dir);
        spl_autoload_register(function($class)
        {
            foreach (self::$dirs as $dir) {
                $file = rtrim($dir, DIRECTORY_SEPARATOR) . '/' . $class . '.php';
                if (is_file($file)) {
                    require_once $file;
                }
            }
        });
    }

    /**
     * オートロード対象のディレクトリの追加（サブディレクトリ込み）
     *
     * @param  string  $dir  ディレクトリ
     */
    private static function addRecursively($dir)
    {
        if (!isset(self::$dirs[$dir])) {
            self::$dirs[$dir] = $dir;
        }
        $subdirs = glob($dir.'/*', GLOB_ONLYDIR);
        foreach ($subdirs as $subdir) {
           self::addRecursively($subdir);
        }
    }

}

AutoLoader::add(__DIR__);
