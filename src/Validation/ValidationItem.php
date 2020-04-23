<?php

class ValidationItem
{

    /**
     * インスタンス生成
     * @return ValidationItem
     */
    public static function forge()
    {
        return new static();
    }

    /** @var  string  項目キー */
    public ?string $key = null;

    /** @var  string  項目値 */
    public ?string $value = null;

    /** @var  array  ルール配列 */
    public array $rules = [];

    /**
     * コンストラクタ
     * 継承先以外はコンストラクタの呼び出し不可
     */
    protected function __construct()
    {
    }

    /**
     * チェックルール追加
     *
     * @param  string  $rule     ルール
     * @param  string  $message  メッセージ（エラーになった際のエラーメッセージ）
     * @return  ValidationItem|self
     */
    public function addRule($rule, $message) : ValidationItem
    {
        $ruleObj = new stdClass;
        $ruleObj->rule = $rule;
        $ruleObj->message = $message;
        $this->rules[] = $ruleObj;
        return $this;
    }

    /**
     * 項目についてのvalidation実行
     *
     * @throws  SystemException
     * @return  stdClass
     */
    public function validation() : stdClass
    {
        $rtnVal = new stdClass;
        $rtnVal->result = 'OK';
        $rtnVal->message = '';
        foreach ((array)$this->rules as $rule) {
            $replaceRule = str_replace(array('[', ']'), array('#', ''), $rule->rule);
            $splitRule = explode('#', $replaceRule);
            switch ($splitRule[0]) {
                case 'required':
                    if (Check::isEmpty($this->value)) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'number':
                    if (!Check::isNumber($this->value)) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;					}
                    break;
               case 'alpha':
                    if (!Check::isAlpha($this->value)) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'alphanumber':
                    if (!Check::isAlphaNumber($this->value)) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'zenkaku':
                    if (!Check::isZenkaku($this->value)) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'zenkakuKana':
                    if (!Check::isZenkakuKana($this->value)) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'email':
                    if (!Check::isEmail($this->value)) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'date':
                    if (!Check::isDate($this->value, $splitRule[1])) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'time':
                    if (!Check::isTime($this->value, $splitRule[1])) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'minLength':
                    if (!Check::isMinLength($this->value, $splitRule[1])) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'maxLength':
                    if (!Check::isMaxLength($this->value, $splitRule[1])) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'rangeLength':
                    if (!Check::isRangeLength($this->value, $splitRule[1], $splitRule[2])) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'exactLength':
                    if (!Check::isExactLength($this->value, $splitRule[1])) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'minNumber':
                    if (!Check::isMinNumber($this->value, $splitRule[1])) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'maxNumber':
                    if (!Check::isMaxNumber($this->value, $splitRule[1])) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'rangeNumber':
                    if (!Check::isRangeNumber($this->value, $splitRule[1], $splitRule[2])) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'exact':
                    $len = count($splitRule);
                    $isOk = false;
                    for ($i = 1; $i < $len; $i++) {
                        if (Check::isExact($this->value, $splitRule[$i])) {
                            $isOk = true;
                            break;
                        }
                    }
                    if (!$isOk) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'sjis':
                    if (!Check::isSjis($this->value)) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'noneSymbol':
                    if (Check::isContainSymbol($this->value)) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                case 'noneScript':
                    if (Check::isContainScript($this->value)) {
                        $rtnVal->result = 'NG';
                        $rtnVal->message = $rule->message;
                        return $rtnVal;
                    }
                    break;
                default:
                    throw new SystemException('[' . __METHOD__ . '] An incorrect rule has been set. Rule = ' . $splitRule[0]);
                    break;
            }
        }
        return $rtnVal;
    }

}
