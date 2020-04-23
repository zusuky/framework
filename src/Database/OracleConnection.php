<?php

class OracleConnection implements DBConnectionInterface
{

    /**
     * 接続取得（インスタンス生成）
     *
     * @return  DBConnectionInterface
     */
    public static function getConnection() : DBConnectionInterface
    {
        return new static();
    }

    /** @var  object  DB接続 */
    protected $conn = null;

    /**
     * コンストラクタ
     * 継承先以外はコンストラクタの呼び出し不可
     */
    protected function __construct()
    {
        debugLog(__METHOD__ . '----start');

        $this->connect();

        debugLog(__METHOD__ . '----end');
    }

    /**
     * DBに接続
     *
     * @throws  DatabaseException
     */
    private function connect()
    {
        debugLog(__METHOD__ . '----start');
        if(is_null($this->conn)){
            $this->conn= oci_connect(Config::get('datasource.oracle.user'),
                                        Config::get('datasource.oracle.pass'),
                                        Config::get('datasource.oracle.sid'));
            if ($this->conn=== false) {
                $error = oci_error();
                $errMessage = "connection failed: code={$error['code']}, message={$error['message']}, offset={$error['offset']}";
                throw new DatabaseException($errMessage);
            }
        }
        debugLog(__METHOD__ . '----end');
    }

    /**
     * トランザクション開始
     */
    public function beginTransaction() : void
    {
        // OCIでは、最初にINSERT/UPDATE/DELETEが発行された時に自動的にトランザクションを開始させるため、特になにもしない
        return;
    }

    /**
     * SELECT実行
     * SQLを実行し、取得結果を配列で返却する
     *
     * @param  string        $sql    SQL
     * @param  SQLParameter  $param  SQLパラメータ
     * @return  array
     */
    public function execute($sql, SQLParameter $param) : array
    {
        debugLog(__METHOD__ . '----start');

        // 結果
        $rtn = [];

        try {

            $stmt = oci_parse($this->conn, $sql);

            if (!is_null($param)) {
                $params = $param->get();
                foreach ((array)$params as $key => &$value) {
                    oci_bind_by_name($stmt, ":{$key}", $value->paramValue);
                }
            }

            // 自動コミットはOFF
            $flg = oci_execute($stmt, OCI_NO_AUTO_COMMIT);
            if ($flg === false) {
                $error = oci_error($stmt);
                $error_message = "sql_execute() failed: code={$error['code']}, message={$error['message']}, offset={$error['offset']}, sqltext={$error['sqltext']}";
                throw new DatabaseException($error_message, $error['code']);
            }

            // 結果取得
            while ($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)) {
                $rtn[] = $row;
            }

            // ステートメントの解放
            oci_free_statement($stmt);

        } catch (Exception $e){
            errorLog('An error occurred during an SQL query.');
            errorLog('SQL =' . $sql);
            errorLog('Parameter =' . print_r((array)$param, true));
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        debugLog(__METHOD__ . '----end');
        return $rtn;
    }

    /**
     * INSERT/UPDATE/DELETE実行
     * SQLを実行し、作用した件数を返却する
     *
     * @param  string        $sql    SQL
     * @param  SQLParameter  $param  SQLパラメータ
     * @return  int
     */
    public function executeNonQuery($sql, SQLParameter $param) : int
    {
        debugLog(__METHOD__ . '----start');

        // 結果
        $rtn = 0;

        try {

            $stmt = oci_parse($this->conn, $sql);

            if (!is_null($param)) {
                $params = $param->get();
                foreach ((array)$params as $key => &$value) {
                    oci_bind_by_name($stmt, ":{$key}", $value->paramValue);
                }
            }

            // 自動コミットはOFF
            $flg = oci_execute($stmt, OCI_NO_AUTO_COMMIT);
            if ($flg === false) {
                $error = oci_error($stmt);
                $error_message = "sql_execute() failed: code={$error['code']}, message={$error['message']}, offset={$error['offset']}, sqltext={$error['sqltext']}";
                throw new DatabaseException($error_message, $error['code']);
            }

            // 結果取得
            $rtn = oci_num_rows($stmt);

            // ステートメントの解放
            oci_free_statement($stmt);

        } catch (Exception $e) {
            errorLog('An error occurred during an SQL query.');
            errorLog('SQL =' . $sql);
            errorLog('Parameter =' . print_r((array)$param, true));
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        debugLog(__METHOD__ . '----end');
        return $rtn;
    }

    /**
     * コミット
     */
    public function commit() : void
    {
        debugLog(__METHOD__ . '----start');

        if (!is_null($this->conn)) {
            $r = oci_commit($this->conn);
            if($r === false){
                $error = oci_error($this->conn);
                $error_message = "commit() failed: code={$error['code']}, message={$error['message']}, offset={$error['offset']}";
                throw new DatabaseException($error_message, $error['code']);
            }
        }

        debugLog(__METHOD__ . '----end');
        return $r;
    }

    /**
     * ロールバック
     */
    public function rollback() : void
    {
        debugLog(__METHOD__ . '----start');

        if (!is_null($this->conn)) {
            oci_rollback($this->conn);
        }

        debugLog(__METHOD__ . '----end');
    }

    /**
     * 接続切断
     */
    public function close() : void
    {
        debugLog(__METHOD__ . '----start');

        if (!is_null($this->conn)) {
            oci_close($this->conn);
            $this->conn = null;
        }

        debugLog(__METHOD__ . '----end');
    }

    /**
     * デストラクタ
     */
    public function __destruct()
    {
        debugLog(__METHOD__ . '----start');

        if (!is_null($this->conn)) {
            $this->close();
        }

        debugLog(__METHOD__ . '----end');
    }

}
