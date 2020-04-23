<?php

class Session
{

    /**
     * セッション開始
     */
    public static function start() : void
    {
        if (Config::get('session.save_handler') != '') { ini_set('session.save_handler', Config::get('session.save_handler')); }
        if (Config::get('session.save_path') != '') { ini_set('session.save_path', Config::get('session.save_path')); }
        if (Config::get('session.session_name') != '') { session_name(Config::get('session.session_name')); }
        if (Config::get('session.gc_maxlifetime') != '') { ini_set('session.gc_maxlifetime', Config::get('session.gc_maxlifetime')); }
        if (Config::get('session.gc_probability') != '') { ini_set('session.gc_probability', Config::get('session.gc_probability')); }
        if (Config::get('session.gc_divisor') != '') { ini_set('session.gc_divisor', Config::get('session.gc_divisor')); }
        if (Config::get('session.cookie_secure') != '') { ini_set('session.cookie_secure', Config::get('session.cookie_secure')); }
        session_start();
        infoLog("Session start [session id=" . self::getId() . ']');
    }

    /**
     * キー存在チェック
     *
     * @param  string  $key  キー
     * @return  boolean
     */
    public static function keyExists($key) : bool
    {
        return Arr::keyExists($_SESSION, $key);
    }

    /**
     * 取得
     * キーを指定しない場合は、全てのSESSION情報を返却する
     *
     * @param  string  $key      キー（省略時はnull）
     * @param  string  $default  キーがない場合の返却値（省略時は空文字）
     * @return  string|array
     */
    public static function get($key = null, $default = '')
    {
        if (func_num_args() === 0) {
            return $_SESSION;
        }
        return Arr::get($_SESSION, $key, $default);
    }

    /**
     * 設定
     *
     * @param  string   $key    キー
     * @param  string   $value  値
     */
    public static function set($key, $value) : void
    {
        Arr::set($_SESSION, $key, $value);
    }

    /**
     * 削除
     *
     * @param  string   $key  キー
     */
    public static function delete($key)
    {
        Arr::delete($_SESSION, $key);
    }

    /**
     * セッション完全破棄
     */
    public static function destroy() : void
    {
        infoLog("Session destroy [session id=" . self::getId() . ']');
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }
        session_destroy();
    }

    /**
     * セッションID取得
     *
     * @return  string
     */
    public static function getId() : string
    {
        return session_id();
    }

    /**
     * セッションID変更
     */
    public static function regenerateId() : void
    {
        infoLog("Change session ID [before change=" . self::getId() . ']');
        session_regenerate_id(true);
        infoLog("Change session ID [after change =" . self::getId() . ']');
    }

}