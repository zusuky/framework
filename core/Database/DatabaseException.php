<?php

class DatabaseException extends Exception
{

	/**
	 * コンストラクタ
	 *
	 * @param  string  $message  message
	 * @param  int     $code     code（省略時はnull）
	 */
    public function __construct($message, $code = null)
    {
        parent::__construct($message, (int)$code, null);
    }

}

