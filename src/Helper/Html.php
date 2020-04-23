<?php

class Html
{

    /**
     * URLを取得する
     *
     * @param  string  $relative_url  html.urlからの相対URL
     * @return  string
     */
    public static function url($relative_url = null) : string
    {
        if (Lang::getCurrent() == Lang::getDefault()) {
            return rtrim(Config::get('html.url'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ($relative_url ?? $relative_url ?? '');
        } else {
        return rtrim(Config::get('html.url'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . Lang::getCurrent() . DIRECTORY_SEPARATOR . ($relative_url ?? $relative_url ?? '');
        }
    }

    /**
     * cssのURLを取得する
      *
     * @param  string  $relative_url  html.cssからの相対URL
     * @return  string
     */
    public static function css($relative_url = null) : string
    {
        return rtrim(Config::get('html.css'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ($relative_url ?? $relative_url ?? '');
    }

    /**
     * jsのURLを取得する
     *
     * @param  string  $relative_url  html.jsからの相対URL
     * @return  string
     */
    public static function js($relative_url = null) : string
    {
        return rtrim(Config::get('html.js'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ($relative_url ?? $relative_url ?? '');
    }

    /**
     * imgのURLを取得する
       *
     * @param  string  $relative_url  html.imgからの相対URL
     * @return  string
     */
    public static function img($relative_url = null) : string
    {
        return rtrim(Config::get('html.img'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ($relative_url ?? $relative_url ?? '');
    }

    /**
     * 現在のURLを取得する
     *
     * @return  string
     */
    public static function current() : string
    {
        return $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    }

    /**
     * リダイレクトを行う
     *
     * @param  string  $url  リダイレクト先URL
     */
    public static function redirect($url) : void
    {
        header('Location: '. $url);
        exit;
    }

}
