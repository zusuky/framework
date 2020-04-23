<?php

interface DBConnectionInterface
{

    /**
     * 接続取得（インスタンス生成）
     *
     * @return  DBConnectionInterface
     */
    public static function getConnection() : DBConnectionInterface;

    /**
     * トランザクション開始
     */
    public function beginTransaction() : void;

    /**
     * SELECT実行
     * SQLを実行し、取得結果を配列で返却する
     *
     * @param  string        $sql    SQL
     * @param  SQLParameter  $param  SQLパラメータ
     * @return  array
     */
    public function execute($sql, SQLParameter $param) : array;

    /**
     * INSERT/UPDATE/DELETE実行
     * SQLを実行し、作用した件数を返却する
     *
     * @param  string        $sql    SQL
     * @param  SQLParameter  $param  SQLパラメータ
     * @return  int
     */
    public function executeNonQuery($sql, SQLParameter $param) : int;

    /**
     * コミット
     */
    public function commit() : void;

    /**
     * ロールバック
     */
    public function rollback() : void;

    /**
     * 接続切断
     */
    public function close() : void;

}
