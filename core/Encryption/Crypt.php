<?php

class Crypt
{

    /**
     * 暗号化
     * ※opensslを有効化する必要あり
     *
     * @param  string   $val          暗号化対象文字列
     * @param  boolean  $is_url_safe  URLセーフな暗号文字列を生成する。（省略時はfalse）
     * @return  string
     */
    public static function encrypt($val, $is_url_safe = false) : string
    {
        $iv = self::createIv();
        $str = $val;
        if ($is_url_safe) {
            $str = str_replace(array('=', '+'), array('-', '.'), $str); // URLに使用できない文字列を置き換え
        }
        $enc = openssl_encrypt($str, Config::get('crypt.method'), Config::get('crypt.key'), false, $iv);
        $rtn = self::jumble($enc, $iv);
        return $rtn;
    }

    /**
     * 複合化
     * ※opensslを有効化する必要あり
     *
     * @param  string  $val          複合化対象文字列
     * @param  boolean  $is_url_safe  URLセーフな暗号文字列を複合する。（省略時はfalse）
     * @return  string
     */
    public static function decrypt($val, $is_url_safe = false) : string
    {
        $result = self::parse($val);
        $enc = $result->enc;
        $iv = $result->iv;
        if ($is_url_safe) {
            $enc = str_replace(array('-', '.'), array('=', '+'), $enc); // URLに使用できない文字列を置き換えたのを、元に戻す
        }
        $rtn = openssl_decrypt($enc, Config::get('crypt.method'), Config::get('crypt.key'), false, $iv);
        return $rtn;
    }

    /**
     * IV作成
     *
     * @return  string
     */
    private static function createIv() : string
    {
        $strArr = array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9'));
        $arrlen = count($strArr) - 1;
        $rtn = null;
        $ivlen = openssl_cipher_iv_length(Config::get('crypt.method'));
        for ($i = 0; $i < $ivlen; $i++) {
            $rtn .= $strArr[mt_rand(0, $arrlen)];
        }
        return $rtn;
    }

    /**
     * 暗号化後の文字列とIVをごちゃ混ぜにする
     *
     * @param  string  $enc  暗号化後の文字列
     * @param  string  $iv   IV
     * @return  string
     */
    private static function jumble($enc, $iv) : string
    {
        $ret = $enc . $iv;
        return $ret;
    }

    /**
     * 解析（IVと暗号化後文字列を取り出す）
     *
     * @param  string  $val  複合化対象文字列
     * @return  StdClass
     */
    private static function parse($val) : StdClass
    {
        $ivlen = openssl_cipher_iv_length(Config::get('crypt.method'));
        $iv = substr($val, (-1) * $ivlen);
        $enc = substr($val, 0, strlen($val) - $ivlen);
        $ret = new StdClass();
        $ret->enc = $enc;
        $ret->iv = $iv;
        return $ret;
    }

}