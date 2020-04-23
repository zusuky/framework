<?php

class Lang
{

    /**
     * デフォルト言語を取得
     *
     * @return  string  デフォルト言語
     */
    public static function getDefault() : string
    {
       return Config::get('lang.default');
    }

    /**
     * 選択可能な言語を取得
     *
     * @return  array  選択可能な言語
     */
    public static function getSelectables() : array
    {
       return Config::get('lang.selectables');
    }

    /**
     * 使用する言語を設定
     *
     * @param  array  $lang  使用する言語
     */
    public static function setCurrent($lang) : void
    {
        Cookie::set('currentLang', $lang);
    }

    /**
     * 現在使用されている言語を取得
     *
     * @return  string
     */
    public static function getCurrent() : string
    {
    	return Cookie::get('currentLang');
    }

}