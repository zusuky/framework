<?php

class Message
{

    /** @var  Message  インスタンス */
    protected static ?Message $instance = null;

    /** @var  array  メッセージ配列 */
    protected array $messages = [];

    /**
     * コンストラクタ
     * 継承先以外はコンストラクタの呼び出し不可
     */
    protected function __construct()
    {
        $smarty = new Smarty();
        $smarty->template_dir = '';
        $smarty->compile_dir = rtrim(Config::get('app_dir.view_templates_c'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $smarty->config_dir = rtrim(Config::get('app_dir.resources_messages'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . Lang::getCurrent() . DIRECTORY_SEPARATOR;
        $smarty->cache_dir = '';
        $confs =  array_filter(
            glob(rtrim(Config::get('app_dir.resources_messages'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . Lang::getCurrent() . DIRECTORY_SEPARATOR .'*'),
            'is_file',
        );
        foreach ($confs as $conf) {
            $this->messages = array_merge($this->messages, $smarty->configLoad($conf)->getConfigVars());
        }
    }

    /**
     * メッセージIDに対応するメッセージ取得
     *
     * @param  string        $msg_id  メッセージID
     * @param  string|array  $params  メッセージパラメータ（省略時はnull）
     * @return  string
     */
    public static function get($msg_id, $params = null) : string
    {
        $message = $msg_id;
        if (is_null(self::$instance)) {
            self::$instance = new Message();
        }

        if (array_key_exists($msg_id, self::$instance->messages)) {
            $message = self::$instance->messages[$msg_id];
        }
        $message = vsprintf($message, (array)$params);
        return $message;
    }

}
