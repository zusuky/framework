<?php

class Validation
{

    /**
     * インスタンス生成
     *
     * @return  Validation
     */
    public static function forge() : Validation
    {
        return new static();
    }

    /** @var  array  Validation項目配列 */
    protected array $items = [];

    /**
     * コンストラクタ
     * 継承先以外はコンストラクタの呼び出し不可
     */
    protected function __construct()
    {
    }

    /**
     * チェック項目追加
     *
     * @param  string  $key    項目キー
     * @param  string  $value  項目値
     * @return  ValidationItem
     */
    public function addItem($key, $value = null) : ValidationItem
    {
        $item = ValidationItem::forge();
        $item->value = $value;
        $item->rules = [];
        $this->items[$key] = $item;
        return $item;
    }

    /**
     * チェック項目取得
     * キーを指定しない場合は、全てのチェック項目を返却する
     *
     * @param  string  $key  項目キー（省略時はnull）
     * @throws  SystemException
     * @return  string|array
     */
    public function getItem($key = null)
    {
        if (is_null($key)) {
            return $this->items;
        }
        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        } else {
            throw new SystemException('[' . __METHOD__ . '] The check item corresponding to the item key does not exist. Key = ' . $key);
        }
    }

    /**
     * validation実行
     *
     * @return  array
     */
    public function run() : array
    {
        $errors = [];
        foreach ((array)$this->items as $key => $value) {
            $rtnVal = $value->validation();
            if ($rtnVal->result != 'OK') {
                $errors[$key] = $rtnVal->message;
            }
        }
        return $errors;
    }

    /**
     * チェック項目クリア
     */
    public function clearItem() : void
    {
        $this->items = [];
    }

}

