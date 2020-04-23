<?php

class View
{

    /**
     * インスタンス生成
     *
     * @param   boolean  $is_allow_php  テンプレートでPHPタグの利用を許可するか（省略時はfalse）
     * @return   View
     */
    public static function forge($is_allow_php = false) : View
    {
        return new static($is_allow_php);
    }

    /** @var  Smarty|SmartyBC  smarty */
    protected $smarty = null;

    /**
     * コンストラクタ
     * 継承先以外はコンストラクタの呼び出し不可
     *
     * @param  boolean  $is_allow_php  テンプレートでPHPタグの利用を許可するか（省略時はfalse）
     */
    protected function __construct($is_allow_php = false)
    {
        $smarty = null;
        if ($is_allow_php) {
            $smarty = new SmartyBC();
            $smarty->php_handling = SmartyBC::PHP_ALLOW;
        } else {
            $smarty = new Smarty();
        }
        $smarty->template_dir = rtrim(Config::get('app_dir.view_templates'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $smarty->compile_dir = rtrim(Config::get('app_dir.view_templates_c'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $smarty->config_dir = rtrim(Config::get('app_dir.resources_messages'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . Lang::getCurrent() . DIRECTORY_SEPARATOR;
        $smarty->cache_dir = rtrim(Config::get('app_dir.view_cache'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $smarty->escape_html = true;
        $this->smarty = $smarty;
    }

    /**
     * 変数のアサイン
     *
     * @param  string   $key      キー
     * @param  string   $value    値（省略時はnull）
     * @param  boolean  $nocache  ノーキャッシュ（省略時はfalse）
     */
    public function assign($key, $value = null, $nocache = false) : void
    {
        $this->smarty->assign($key, $value, $nocache);
    }

    /**
     * 配列による変数のアサイン
     *
     * @param  array    $array    配列
     * @param  boolean  $nocache  ノーキャッシュ（省略時はfalse）
     */
    public function assignArr($array, $nocache = false) : void
    {
        foreach ((array)$array as $key => $val) {
            $this->assign($key, $val, $nocache);
        }
    }

    /**
     * エスケープ設定
     *
     * @param  bool  $escape  エクケープするかどうか
     */
    public function setEscape($escape) : void
    {
        $this->smarty->escape_html = $escape;
    }

    /**
    * 画面表示
     *
     * @param  string  $template  テンプレート
     */
    public function display($template) : void
    {
        $this->smarty->display($template);
    }

    /**
     * フェッチ
     *
     * @param  string  $template  テンプレート
     * @return  string
     */
    public function fetch($template) : string
    {
        return $this->smarty->fetch($template);
    }

    /**
      * アサインパラメータクリア
     */
    public function clearAssign() : void
    {
        $this->smarty->clear_all_assign();
    }

}