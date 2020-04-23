<?php

class MySQLConnection implements DBConnectionInterface
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

    /** @var  PDO  DB接続 */
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
    private function connect() : void
    {
        debugLog(__METHOD__ . '----start');

        try {
            $conn1 = new PDO('mysql:dbname=' . Config::get('datasource.mysql.database') . ';' .
                                    'host=' . Config::get('datasource.mysql.host') . ';' .
                                    'port=' . Config::get('datasource.mysql.port'),
                                Config::get('datasource.mysql.user'),
                                Config::get('datasource.mysql.pass'));
            $conn1->query('SET NAMES utf8mb4;');
            $conn1->query("SET time_zone = 'Asia/Tokyo'");
            $conn1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn1->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            $conn1->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->conn = $conn1;
        } catch (Exception $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        debugLog(__METHOD__ . '----end');
    }

    /**
     * トランザクション開始
     */
    public function beginTransaction() : void
    {
        debugLog(__METHOD__ . '----start');

        try {
            if (!$this->conn->query('set session transaction isolation level READ COMMITTED')) {
                throw new DatabaseException('Changing the transaction isolation level failed.');
            }
            if (!$this->conn->beginTransaction()) {
                throw new DatabaseException('Failed to start transaction.');
            }
        } catch (Exception $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        debugLog(__METHOD__ . '----end');
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

            // prepare
            $stmt = $this->conn->prepare($sql);

            // bind
            if (!is_null($param)) {
                $params = $param->get();
                foreach ((array)$params as $key => $value) {
                    if (is_null($value->param_value) || $value->param_type == SQLParameter::PARAM_TYPE_NULL) {
                        $stmt->bindValue(':' . $key, null, PDO::PARAM_NULL);
                    } elseif($value->param_type == SQLParameter::PARAM_TYPE_INT) {
                        $stmt->bindValue(':' . $key, $value->param_value, PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue(':' . $key, $value->param_value, PDO::PARAM_STR);
                    }
                }
            }

            // SQL実行
            if (!$stmt->execute()) {
                $error = $stmt->errorInfo();
                throw new PDOException($error[2], $error[1]);
            }

            // 結果取得
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rtn[] = $row;
            }

            // カーソルをクローズしておく。（ => 次のSQLが実行可能な状態になる）
            $stmt->closeCursor();

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

            // prepare
            $stmt = $this->conn->prepare($sql);

            // bind
            if (!is_null($param)) {
                $params = $param->get();
                foreach ((array)$params as $key => $value) {
                    if (is_null($value->param_value) || $value->param_type == SQLParameter::PARAM_TYPE_NULL) {
                        $stmt->bindValue(':' . $key, null, PDO::PARAM_NULL);
                    } elseif($value->param_type == SQLParameter::PARAM_TYPE_INT) {
                        $stmt->bindValue(':' . $key, $value->param_value, PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue(':' . $key, $value->param_value, PDO::PARAM_STR);
                    }
                }
            }

            // SQL実行
            if (!$stmt->execute()) {
                $error = $stmt->errorInfo();
                throw new PDOException($error[2], $error[1]);
            }

            // 結果取得
            $rtn = $stmt->rowCount();

            // カーソルをクローズしておく。（ => 次のSQLが実行可能な状態になる）
            $stmt->closeCursor();

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
            try {
                if(!$this->conn->commit()){
                    throw new DatabaseException('Commit failed.');
                }
            } catch (Exception $e) {
                throw new DatabaseException($e->getMessage(), $e->getCode());
            }
        }

        debugLog(__METHOD__ . '----end');
    }

    /**
     * ロールバック
     */
    public function rollback() : void
    {
        debugLog(__METHOD__ . '----start');

        if (!is_null($this->conn) && $this->conn->inTransaction()) {
            try {
                if(!$this->conn->rollBack()){
                    throw new DatabaseException('Rollback failed.');
                }
            } catch (Exception $e) {
                throw new DatabaseException($e->getMessage(), $e->getCode());
            }
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
            if ($this->conn->inTransaction()) {
                $this->rollback();
            }
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

