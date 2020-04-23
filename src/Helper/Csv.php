<?php

class Csv
{

    /** @var  string  エンコード : SJIS-WIN */
    const ENC_SJISW = 'SJIS-WIN';

    /** @var  string  エンコード : UTF-8 */
    const ENC_UTF8  = 'UTF-8';

    /** @var  string  区切り文字 */
    private string $delimiter = ',';

    /** @var  string  囲み文字 */
    private string $enclosure = '"';

    /** @var  boolean  BOM付きかどうか */
    private boolean $withbom = false;

    /** @var  array  CSVデータ */
    private array $data = [];

    /** @var  int  行ポインタ */
    private int $pointer = 0;

    /**
     * インスタンス生成
     *
     * @return  Csv
     */
    public static function forge() : Csv
    {
        return new static();
    }

    /**
     * コンストラクタ
     * 継承先以外はコンストラクタの呼び出し不可
     */
    protected function __construct() : void
    {
    }

    /**
     * 1レコード分のデータを追加する
     * @param  array  $data  1レコード分のデータ
     */
    public function addRow(array $data) : void
    {
        $this->data[] = $this->enclosure .
                            implode($this->enclosure . $this->delimiter . $this->enclosure, $data) .
                            $this->enclosure;
    }

    /**
     * ファイルをブラウザに出力する
     *
     * @param  string  $fileName      The file name
     * @param  string  $encodingFrom  The encoding from
     * @param  string  $encodingTo    The encoding to
     * @throws  SystemException
     */
    public function output($fileName, $encodingFrom = self::ENC_UTF8, $encodingTo = self::ENC_UTF8) : void
    {
        header("Content-disposition: attachment; filename=" . $fileName);
        header("Content-type: plain/text; " . $fileName);
        $fo = fopen('php://output', 'w');
        if ($fo === FALSE) {
            throw new SystemException('Could not open file.');
        }
        if ($this->withbom) {
            fwrite($fo, pack('C*',0xEF,0xBB,0xBF));
        }
        foreach ($this->data as $k => $v) {
            fwrite($fo, mb_convert_encoding($v, $encodingTo, $encodingFrom) . "\r\n");
        }
        fclose($fo);
        $this->data = [];
    }

    /**
     * ファイルを保存する
     *
     * @param  string  $fileName      The file name
     * @param  string  $encodingFrom  The encoding from
     * @param  string  $encodingTo    The encoding to
     * @throws  SystemException  (description)
     */
    public function save($filePath, $encodingFrom = self::ENC_UTF8, $encodingTo = self::ENC_UTF8) : void
    {
        $fo = fopen($filePath, 'w');
        if ($fo === FALSE) {
            throw new SystemException('Could not open file.');
        }
        if ($this->withbom) {
            fwrite($fo, pack('C*',0xEF,0xBB,0xBF));
        }
        foreach ($this->data as $k => $v) {
            fwrite($fo, mb_convert_encoding($v, $encodingTo, $encodingFrom). "\r\n");
        }
        fclose($fo);
        $this->data = [];
    }

    /**
     * ファイルを読み込む
     *
     * @param  string  $fileName      The file name
     * @param  string  $encodingFrom  The encoding from
     * @param  string  $encodingTo    The encoding to
     * @throws  SystemException  (description)
     */
    public function load($filePath, $encodingFrom = self::ENC_UTF8, $encodingTo = self::ENC_UTF8) : void
    {
        $fp = fopen($filePath, "r");
        if ($fp === FALSE) {
            throw new SystemException('Could not open file.');
        }
        while ($row = $this->myFgetcsv($fp, $this->delimiter, $this->enclosure, $encodingFrom, $encodingTo)) {
            $this->data[] = $row;
        }
        fclose($fp);
    }

    /**
     * 現在の行ポインタのデータを取得する
     *
     * @return  array|false  1レコード分のデータ
     */
    public function getRow()
    {
        if (isset($this->data[$this->pointer])) {
            $rtn = $this->data[$this->pointer];
            $this->pointer++;
            return $rtn;
        }
        return false;
    }

    /**
     * 行ポインターを先頭に戻す
     */
    public function repoint() : void
    {
        $this->pointer = 0;
    }

    /**
     * csvレコード → 配列変換
     *
     * @param  resource  $handle        The handle
     * @param  string    $d             The delimiter
     * @param  string    $e             The enclosure
     * @param  string    $encodingFrom  The encoding from
     * @param  string    $encodingTo    The encoding to
     * @return  array|false  1レコード分のデータ
     */
    private function myFgetcsv(resource &$handle, string $d, string $e, string $encodingFrom, string $encodingTo)
    {
        $d = preg_quote($d);
        $e = preg_quote($e);
        $line = "";
        $eof = false;
        while (($eof != true)and(!feof($handle))) {
            $line .= fgets($handle);
            $itemcnt = preg_match_all('/'.$e.'/', $line, $dummy);
            if ($itemcnt % 2 == 0) $eof = true;
        }
        $line = mb_convert_encoding($line, $encodingTo, $encodingFrom);
        $csv_line = preg_replace('/(?:\\r\\n|[\\r\\n])?$/', $d, trim($line));
        $csv_pattern = '/('.$e.'[^'.$e.']*(?:'.$e.$e.'[^'.$e.']*)*'.$e.'|[^'.$d.']*)'.$d.'/';
        preg_match_all($csv_pattern, $csv_line, $_csv_matches);
        $csv_data = $_csv_matches[1];
        for ($_csv_i=0;$_csv_i<count($csv_data);$_csv_i++) {
            $csv_data[$_csv_i]=preg_replace('/^'.$e.'(.*)'.$e.'$/s','$1',$csv_data[$_csv_i]);
            $csv_data[$_csv_i]=str_replace($e.$e, $e, $csv_data[$_csv_i]);
        }
        return empty($line) ? false : $csv_data;
    }

}