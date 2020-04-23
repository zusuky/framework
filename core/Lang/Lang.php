<?php

class Lang
{

    /** @var  array  選択可能な言語 */
    private static array $selectables = [
        'ja' => '日本語',
        'en' => '英語',
    ];

    /** @var  string  デフォルト言語 */
    private static string $default = 'ja';

    /**
     * 選択可能な言語を取得
     *
     * @return  array  選択可能な言語
     */
    public static function getSelectables() : array
    {
       return self::$selectables;
    }

    /**
     * デフォルト言語を取得
     *
     * @return  string  デフォルト言語
     */
    public static function getDefault() : string
    {
       return self::$default;
    }

    /**
     * 使用する言語を設定
     *
     * @param  string  $current  現在使用されている言語
     */
    public static function setCurrent($current) : void
    {
     	Cookie::set('currentLang', $current);
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