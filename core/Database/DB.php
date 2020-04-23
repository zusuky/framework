<?php

class DB
{

    /** @var  object  接続 */
    private static ?DBConnectionInterface $connection = null;

    /**
     * 接続オープン
     *
     * @throws  SystemException
     */
    public static function open() : void
    {
        if (is_null(self::$connection)) {
            $connection = Config::get('datasource.connection');
            switch ($connection) {
                case 'mysql':
                    self::$connection = MySQLConnection::getConnection();
                    break;
                case 'oracle':
                    if (!is_array($env)) {
                        if (Config::get('datasource.oracle.env.nls_lang') != '') {
                            putenv("NLS_LANG=" . Config::get('datasource.oracle.env.nls_lang'));
                        }
                        if (Config::get('datasource.oracle.env.oracle_home') != '') {
                            putenv("ORACLE_HOME=" . Config::get('datasource.oracle.env.oracle_home'));
                        }
                        if (Config::get('datasource.oracle.env.ld_library_path') != '') {
                            putenv("LD_LIBRARY_PATH=" . Config::get('datasource.oracle.env.ld_library_path'));
                        }
                    }
                    self::$connection = OracleConnection::getConnection();
                    break;
                case 'pgsql':
                default:
                    throw new SystemException("Unsupported DBMS.");
            }
        }
    }

    /**
     * トランザクション開始
     */
    public static function beginTransaction() : void
    {
        if (is_null(self::$connection)) {
            self::open();
        }
        self::$connection->beginTransaction();
    }

    /**
     * SELECT実行
     * SQLを実行し、取得結果を配列で返却する
     *
     * @param  string        $sql    SQL
     * @param  SQLParameter  $param  SQLパラメータ
     * @return  array
     */
    public static function execute($sql, SQLParameter $param) : array
    {
        if (is_null(self::$connection)) {
            self::open();
        }
        return self::$connection->execute($sql, $param);
    }

    /**
     * INSERT/UPDATE/DELETE実行
     * SQLを実行し、作用した件数を返却する
     *
     * @param  string        $sql    SQL
     * @param  SQLParameter  $param  SQLパラメータ
     * @return  int
     */
    public static function executeNonQuery($sql, SQLParameter $param) : int
    {
        if (is_null(self::$connection)) {
            self::open();
        }
        return self::$connection->executeNonQuery($sql, $param);
    }

    /**
     * コミット
     */
    public static function commit() : void
    {
        self::$connection->commit();
    }

    /**
     * ロールバック
     */
    public static function rollback() : void
    {
        if (!is_null(self::$connection)) {
            self::$connection->rollback();
        }
    }

    /**
     * 接続切断
     */
    public static function close() : void
    {
        if (!is_null(self::$connection)) {
            self::$connection->close();
            self::$connection = null;
        }
    }

    /**
     * コンストラクタ
     * 外部からのコンストラクタ呼び出しは不可
     */
    private function __construct()
    {
    }

}
