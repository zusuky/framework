<?php

use Ramsey\Uuid\Uuid;

class UuidCreator
{

	/**
	 * UIID生成
	 *
	 * @return  string
	 */
	public static function create() : string
	{
		return Uuid::uuid4();
	}
}
