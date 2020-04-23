<?php
/******************************************************************************
 * SQLパラメータクラス
 ******************************************************************************/

class SQLParameter
{

    /** @var  string  パラメータタイプ : str */
    const PARAM_TYPE_STR  = 'str';

    /** @var  string  パラメータタイプ : int */
    const PARAM_TYPE_INT  = 'int';

    /** @var  string  パラメータタイプ : null */
    const PARAM_TYPE_NULL = 'null';

    /**
     * インスタンス生成
     *
     * @return  SQLParameter
     */
    public static function forge()
    {
        return new static();
    }

    /** @var  array  パラメータ配列 */
    protected array $params = [];

    /** @var  array  アサインパラメータ配列 */
    protected array $assign_params = [];

    /**
     * コンストラクタ
     * 継承先以外はコンストラクタの呼び出し不可
     */
    protected function __construct()
    {
    }

    /**
     * パラメータ追加
     *
     * @param  string  $param_name   パラメータ名
     * @param  string  $param_value  パラメータ値（省略時はnull）
     * @param  string  $param_type   パラメータタイプ（省略時はPARAM_TYPE_STR）
     * @throws  SystemException
     * @return  SQLParameter|self
     */
    public function add($param_name, $param_value = null, $param_type = self::PARAM_TYPE_STR) : SQLParameter
    {
        if (is_null($param_name) || strlen(trim($param_name)) == 0) {
            throw new SystemException('[' . __METHOD__ . '] Parameter is invalid. Parameter = ' . print_r(func_get_args(), true));
        }
        $param = new stdClass;
        $param->param_value = $param_value;
        $param->param_type = $param_type;
        $this->params[$param_name] = $param;
        return $this;
    }

    /**
     * アサインパラメータ追加
     *
     * @param  string  $param_name   パラメータ名
     * @param  string  $param_value  パラメータ値
     * @throws  SystemException
     * @return  SQLParameter|self
     */
    public function addAssign($param_name, $param_value) : SQLParameter
    {
        if (is_null($param_name) || strlen(trim($param_name)) == 0) {
            throw new SystemException('[' . __METHOD__ . '] Parameter is invalid. Parameter = ' . print_r(func_get_args(), true));
        }
        $param = new stdClass;
        $param->param_value = $param_value;
        $this->assign_params[$param_name] = $param;
        return $this;
    }

    /**
     * パラメータ取得
     *
     * @return  array
     */
    public function get() : array
    {
        return $this->params;
    }


    /**
     * アサインパラメータ取得
     *
     * @return  array
     */
    public function getAssign() : array
    {
        return $this->assign_params;
    }

    /**
     * パラメータクリア
     */
    public function clear() : void
    {
        $this->params = [];
    }

    /**
     * アサインパラメータクリア
     */
    public function clearAssign() : void
    {
        $this->assign_params = [];
    }

}
