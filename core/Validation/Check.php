<?php

class Check
{

    /**
     * 空文字チェック
     *
     * @param  mixed    $value    判定値
     * @param  boolean  $do_trim  トリムして判定するかどうか（省略時はtrue）
     *
     * @return  boolean
     */
    public static function isEmpty($value, $do_trim = true) : bool
    {
        if (is_null($value)) {
            return true;
        }
        if (is_array($value)) {
            if (count($value) == 0) {
                return true;
            }
        } else {
            if ($do_trim) {
                if (strlen(trim($value)) == 0) {
                    return true;
                }
            } else {
                if (strlen($value) == 0) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 半角数値チェック
     *
     * @param  string|int  $value  判定値
     * @return  boolean
     */
    public static function isNumber($value) : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }
        if (preg_match('/^[0-9]+$/', $value)) {
            return true;
        }
        return false;
    }

    /**
     * 半角英字チェック
     *
     * @param  string|int  $value  判定値
     * @return  boolean
     */
    public static function isAlpha($value) : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }
        if(preg_match('/^[a-zA-Z]+$/', $value)) {
            return true;
        }
        return false;
    }

    /**
     * 半角英数字チェック
     *
     * @param  string|int  $value  判定値
     * @return  boolean
     */
    public static function isAlphaNumber($value) : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }
        if(preg_match('/^[0-9a-zA-Z]+$/', $value)) {
            return true;
        }
        return false;
    }

    /**
     * 全角チェック
     *
     * @param  string|int  $value  判定値
     * @return  boolean
     */
    public static function isZenkaku($value) : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }
        $pattern = '/(?:\xEF\xBD[\xA1-\xBF]|\xEF\xBE[\x80-\x9F])|[\x20-\x7E]/';
        if(!preg_match($pattern, $value)) {
            return true;
        }
        return false;
    }

    /**
     * 全角カナチェック
     *
     * @param  string|int  $value  判定値
     * @return  boolean
     */
    public static function isZenkakuKana($value) : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }
        $pattern = '^[ア-ン゛゜ァ-ォャ-ョー―－‐ヴヵヶ]+$';
        mb_regex_encoding("UTF-8");
        if (mb_ereg($pattern, $value)) {
            return true;
        }
        return false;
    }

    /**
     * メールアドレス形式チェック
     *
     * @param  string|int  $value  判定値
     * @return  boolean
     */
    public static function isEmail($value) : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * 日付形式チェック
     *
     * @param  string|int  $value  判定値
     * @param  string      $delim  日付の区切り文字列（省略時は空文字）
     * @return  boolean
     */
    public static function isDate($value, $delim = '') : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }

        $year = -1;
        $month = -1;
        $day = -1;

        if (strlen($delim) == 0) {

            if (strlen(trim($value)) != 8) {
                return false;
            }
            if (!preg_match('/^[0-9]+$/', $value)) {
                return false;
            }
            $year = (int)substr($value, 0, 4);
            $month = (int)substr($value, 4, 2);
            $day = (int)substr($value, 6, 2);

        }else{

            $arr = explode($delim, $value);
            if (count($arr) != 3) {
                return false;
            }
            if (!preg_match('/^[0-9]+$/', $arr[0])) {
                return false;
            } elseif (strlen(trim($arr[0])) != 4) {
                return false;
            }
            if (!preg_match('/^[0-9]+$/', $arr[1])) {
                return false;
            } elseif (strlen(trim($arr[1])) != 2) {
                return false;
            }
            if (!preg_match('/^[0-9]+$/', $arr[2])) {
                return false;
            } elseif (strlen(trim($arr[2])) != 2) {
                return false;
            }
            $year = (int)$arr[0];
            $month = (int)$arr[1];
            $day = (int)$arr[2];

        }

        return checkdate($month, $day, $year);
    }

    /**
     * 時刻（時分）形式チェック
     *
     * @param  string|int  $value  判定値
     * @param  string      $delim  時刻の区切り文字列（省略時は空文字）
     * @return  boolean
     */
    public static function isTime($value, $delim = '') : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }

        $hour = -1;
        $minute = -1;

        if (strlen($delim) == 0) {

            if (strlen($value) != 4) {
                return false;
            }
            if (!self::isNumber($value)) {
                return false;
            }
            $hour = (int)substr($value, 0, 2);
            $minute = (int)substr($value, 2, 2);

        }else{

            $arr = explode($delim, $value);
            if (count($arr) != 2) {
                return false;
            }
            if (!preg_match('/^[0-9]+$/', $arr[0])) {
                return false;
            } elseif (strlen(trim($arr[0])) != 2) {
                return false;
            }
            if (!preg_match('/^[0-9]+$/', $arr[1])) {
                return false;
            } elseif (strlen(trim($arr[1])) != 2) {
                return false;
            }
            $hour = (int)$arr[0];
            $minute = (int)$arr[1];

        }

        if ($hour < 0 || $hour > 23) {
            return false;
        }
        if ($minute < 0 || $minute > 59) {
            return false;
        }

        return true;
    }

    /**
     * 最少文字数チェック
     *
     * @param  string|int  $value    判定値
     * @param  int         $min_num  最少文字数
     * @return  boolean
     */
    public static function isMinLength($value, $min_num) : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }
        $len = mb_strlen(trim($value));
        if ($len >= (int)$min_num) {
            return true;
        }
        return false;
    }

    /**
     * 最大文字数チェック
     *
     * @param  string|int  $value    判定値
     * @param  int         $max_num  最大文字数
     * @return  boolean
     */
    public static function isMaxLength($value, $max_num) : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }
        $len = mb_strlen(trim($value));
        if ($len <= (int)$max_num) {
            return true;
        }
        return false;
    }

    /**
     * 文字数範囲チェック
     *
     * @param  string|int  $value    判定値
     * @param  int         $min_num  最少文字数
     * @param  int         $max_num  最大文字数
     * @return  boolean
     */
    public static function isRangeLength($value, $min_num, $max_num) : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }
        $len = mb_strlen(trim($value));
        if ($len >= (int)$min_num && $len <= (int)$max_num) {
            return true;
        }
        return false;
    }

    /**
     * 文字数一致チェック
     *
     * @param  string|int  $value  判定値
     * @param  int         $num    文字数
     * @return  boolean
     */
    public static function isExactLength($value, $num) : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }
        $len = mb_strlen(trim($value));
        if ($len == (int)$num) {
            return true;
        }
        return false;
    }

    /**
     * 最少数値チェック
     *
     * @param  string|int  $value    判定値
     * @param  int         $min_num  最少数値
     * @return  boolean
     */
    public static function isMinNumber($value, $min_num) : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }
        if ((int)$value >= (int)$min_num) {
            return true;
        }
        return false;
    }

    /**
     * 最大数値チェック
     *
     * @param  string|int  $value    判定値
     * @param  int         $max_num  最大数値
     * @return  boolean
     */
    public static function isMaxNumber($value, $max_num) : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }
        if ((int)$value <= (int)$max_num) {
            return true;
        }
        return false;
    }

    /**
     * 数値範囲チェック
     *
     * @param  string|int  $value    判定値
     * @param  int         $min_num  最少数値
     * @param  int         $max_num  最大数値
     * @return  boolean
     */
    public static function isRangeNumber($value, $min_num, $max_num) : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }
        if ((int)$value >= (int)$min_num && (int)$value <= (int)$max_num) {
            return true;
        }
        return false;
    }

    /**
     * 値一致チェック
     *
     * @param  string|int  $value          判定値
     * @param  string|int  $value_compare  比較対象
     * @param  boolean     $is_strict      厳格に判定するかどうか（省略時はtrue）
     * @return  boolean
     */
    public static function isExact($value, $value_compare, $is_strict = true) : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return true;
        }
        if (is_null($value_compare) || strlen(trim($value_compare)) == 0) {
            return true;
        }
        if ($is_strict) {
            if ($value === $value_compare) {
                return true;
            } else {
                return false;
            }
        } else {
            if ($value == $value_compare) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * SJISで扱える文字列かチェック
     *
     * @param  string|int  $value  判定値
     * @return  boolean
     */
    public static function isSjis($value) : bool
    {
        // SJISへ変換
        $wkSjis = mb_convert_encoding($value, 'SJIS', 'UTF-8');
        // UTF-8へ戻す
        $chkUtf8 = mb_convert_encoding($wkSjis, 'UTF-8', 'SJIS');
        // 元に戻らない場合は環境依存文字
        if ((string)$value !== (string)$chkUtf8) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 名前・住所系の項目で使用する全角記号のハイフン系以外が含まれているかチェック
     *
     * @param  string|int  $value  判定値
     * @return  boolean
     */
    public static function isContainSymbol($value) : bool
    {
        if (is_null($value) || strlen(trim($value)) == 0) {
            return false;
        }
        $pattern = '[、。，．：；？！゛゜´｀¨＾￣＿ヽヾゝゞ〃仝〆〇／＼～〜∥｜…‥‘’“”（）〔〕［］｛｝〈〉《》「」『』【】＋±×÷＝≠＜＞≦≧∞∴♂♀°′″℃￥＄￠￡％＃＆＊＠§☆★○●◎◇◆□■△▲▽▼※〒→←↑↓〓∈∋⊆⊇⊂⊃∪∩∧∨￢⇒⇔∀∃∠⊥⌒∂∇≡≒≪≫√∽∝∵∫∬Å‰♯♭♪†‡¶◯ΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩαβγδεζηθικλμνξοπρστυφχψωАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгдеёжзийклмнопрстуфхцчшщъыьэюя─│┌┐┘└├┬┤┴┼━┃┏┓┛┗┣┳┫┻╋┠┯┨┷┿┝┰┥┸╂①②③④⑤⑥⑦⑧⑨⑩⑪⑫⑬⑭⑮⑯⑰⑱⑲⑳ⅠⅡⅢⅣⅤⅥⅦⅧⅨⅩ㍉㌔㌢㍍㌘㌧㌃㌶㍑㍗㌍㌦㌣㌫㍊㌻㎜㎝㎞㎎㎏㏄㎡〝〟№㏍℡㊤㊥㊦㊧㊨㈱㈲㈹㍾㍽㍼㍻≒≡∫∮∑√⊥∠∟⊿∵∩∪]+';
        mb_regex_encoding("UTF-8");
        if (mb_ereg($pattern, $value)) {
            return true;
        }
        return false;
    }

    /**
     * javascript文字列を含むかチェック
     * チェック対象文字列
     * ・javascript
     * ・script
     * ・alert
     * ・cookie
     *
     * @param  string|int  $value  判定値
     * @return  boolean
     */
    public static function isContainScript($value) : bool
    {
        if (Check::isEmpty($value)) {
            return false;
        }
        $search = [
                'javascript',
                'script',
                'alert',
                'cookie',
        ];
        if (str_ireplace($search, "", $value) == $value) {
            return false;
        } else {
            return true;
        }
    }

}
