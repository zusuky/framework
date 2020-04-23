<?php

class Model
{

    /**
     * インスタンス生成
     *
     * @return  Model
     */
    public static function forge() : Model
    {
        return new static();
    }

    /** @ver  string  SQLファイル格納ディレクトリ名 */
    protected string $sql_dirname = '';

    /**
     * コンストラクタ
     * 継承先以外はコンストラクタの呼び出し不可
     */
    protected function __construct()
    {
    }

    /**
     * SmartyテンプレートファイルからSQLを取得
     *
     * @param  string        $sql_template  SQLテンプレート名
     * @param  SQLParameter  $sql_param     SQLパラメータ
     * @throws  SystemException
     * @return  string
     */
    protected function getSqlFromTemplate($sql_template, SQLParameter $sql_param) : string
    {
        debugLog(__METHOD__ . '----start');

        $sql_file = rtrim(Config::get('app_dir.model_sql'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->sql_dirname . '/' . $sql_template;
        $sql = '';

        infoLog('Load SQL. SQL file = ' . $sql_file);
        if (!is_file($sql_file)) {
            throw new SystemException('SQL file does not exist. [' . $sql_file . ']');
        }

        $smarty = new Smarty();
        $smarty->template_dir = rtrim(Config::get('app_dir.model_sql'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $smarty->compile_dir = rtrim(Config::get('app_dir.model_sql_c'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $smarty->config_dir = '';
        $smarty->cache_dir = '';

        $params = $sql_param->get();
        foreach ($params as $key => $value) {
            $smarty->assign($key, $value->param_value);
        }

        $assign_params = $sql_param->getAssign();
        foreach ($assign_params as $key => $value) {
            $smarty->assign($key, $value->param_value);
        }

        $sql = $smarty->fetch($sql_file);

        debugLog(__METHOD__ . '----end');
        return $sql;
    }

    /**
     * SELECTを発行するのに使用
     * SQLを直接引数に渡して実行し、取得結果の全件を二次元配列にて返却する
     *
     * @param  string        $sql    SQL
     * @param  SQLParameter  $param  SQLパラメータ
     * @return  array
     */
    public function query($sql, SQLParameter $param) : array
    {
        debugLog(__METHOD__ . '----start');

        debugLog('Execute SQL = ' . $sql);
        debugLog('Parameter = ' . print_r($param, true));
        $result = DB::execute($sql, $param);
        $rtn = [];
        if (!empty((array)$result)) {
            $rtn = $result;
        }
        debugLog('SQL execution end');

        debugLog(__METHOD__ . '----end');
        return $rtn;
    }

    /**
     * SELECTを発行するのに使用
     * SQLを直接引数に渡して実行し、取得結果の最初の1行のみを配列にて返却する
     *
     * @param  string        $sql    SQL
     * @param  SQLParameter  $param  SQLパラメータ
     * @return  array
     */
    public function queryFirst($sql, SQLParameter $param) : array
    {
        debugLog(__METHOD__ . '----start');

        $result = $this->query($sql, $param);
        $rtn = [];
        if (!empty((array)$result)) {
            $rtn = $result[0];
        }

        debugLog(__METHOD__ . '----end');
        return $rtn;
    }

    /**
     * INSERT/UPDATE/DELETEを発行するのに使用
     * SQLを直接引数に渡して実行し、作用した件数を返却する
     *
     * @param  string        $sql    SQL
     * @param  SQLParameter  $param  SQLパラメータ
     * @return  int
     */
    public function nonQuery($sql, SQLParameter $param) : int
    {
        debugLog(__METHOD__ . '----start');

        debugLog('Execute SQL = ' . $sql);
        debugLog('Parameter = ' . print_r($param, true));
        $rtn = DB::executeNonQuery($sql, $param);
        debugLog('SQL execution end');

        debugLog(__METHOD__ . '----end');
        return $rtn;
    }

    /**
     * SELECTを発行するのに使用
     * SQLテンプレートからSQLを取得して実行し、取得結果の全件を二次元配列にて返却する
     *
     * @param  string        $sql_template  SQLファイル名
     * @param  SQLParameter  $param         SQLパラメータ
     * @return  array
     */
    public function queryByFile($sql_template, SQLParameter $param) : array
    {
        debugLog(__METHOD__ . '----start');

        $sql = $this->getSqlFromTemplate($sql_template, $param);
        $rtn = $this->query($sql, $param);

        debugLog(__METHOD__ . '----end');
        return $rtn;
    }

    /**
     * SELECTを発行するのに使用
     * SQLテンプレートからSQLを取得して実行し、取得結果の最初の1行のみを返却する
     *
     * @param  string        $sql_template  SQLファイル名
     * @param  SQLParameter  $param         SQLパラメータ
     * @return  array
     */
    public function queryFirstByFile($sql_template, SQLParameter $param) : array
    {
        debugLog(__METHOD__ . '----start');

        $sql = $this->getSqlFromTemplate($sql_template, $param);
        $rtn = $this->queryFirst($sql, $param);

        debugLog(__METHOD__ . '----end');
        return $rtn;
    }

    /**
     * INSERT/UPDATE/DELETEを発行するのに使用
     * SQLテンプレートからSQLを取得して実行し、作用した件数を返却する
     *
     * @param  string        $sql_template  SQLファイル名
     * @param  SQLParameter  $param         SQLパラメータ
     * @return  int
     */
    public function nonQueryByFile($sql_filename, SQLParameter $param) : int
    {
        debugLog(__METHOD__ . '----start');

        $sql = $this->getSqlFromTemplate($sql_filename, $param);
        $rtn = $this->nonQuery($sql, $param);

        debugLog(__METHOD__ . '----end');
        return $rtn;
    }

}
