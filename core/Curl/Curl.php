<?php

class Curl
{

    /**
     * インスタンス生成
     *
     * @return  Curl
     */
    public static function forge() : Curl
    {
        return new static();
    }

    /** @var  string  URL */
    protected string $url = '';

    /** @var  array  ヘッダー */
    protected array $header = [];

    /** @var  array  データ */
    protected array $data = [];

    /** @var  boolean  JSON形式かどうか */
    protected boolean $isJson = false;

    /** @var  int  タイムアウト（秒数） */
    protected int $timeout = 90;

    /** @var  int  HTTP status code */
    protected int $httpCode = 500;

    /** @var  string  レスポンス */
    protected string $response = '';

    /**
     * コンストラクタ
     * 継承先以外はコンストラクタの呼び出し不可
     */
    protected function __construct()
    {
    }

    /**
     * URL設定
     *
     * @param  string  $url  URL
     */
    public function setUrl(string $url) : void
    {
        $this->url = $url;
    }

    /**
     * ヘッダ設定
     *
     * @param  array  $header  ヘッダ
     */
    public function setHeader(array $header) : void
    {
        $this->$header = $header;
    }

    /**
    * データ設定
     *
     * @param  array  $data  データ
     */
    public function setData(array $data) : void
    {
        $this->$data = $data;
    }

    /**
     * JSON形式かどうか設定
     *
     * @param  boolean  $isJson  JSON形式かどうか
     */
    public function setIsJson(boolean $isJson) : void
    {
        $this->isJson = $isJson;
    }

    /**
     * タイムアウト（秒数）設定
     *
     * @param  integer  $timeout  タイムアウト（秒数）
     */
    public function setTimeout(int $timeout) : void
    {
        $this->timeout = $timeout;
    }

    /**
     * HTTP status code取得
     *
     * @return  int
     */
    public function getHttpCode() : int
    {
        return $this->httpCode;
    }

    /**
     * レスポンス取得
     *
     * @return  string
     */
    public function getResponse() : string
    {
        return $this->response;
    }

    /**
     * POSTリクエストする
     *
     * @return  boolean  リクエストが成功した場合：true, リクエストが失敗した場合：false
     */
    public function post() : bool
    {
        InfoLog(__METHOD__ . '----start');

        $url = self::$url;
        $method = 'POST';

        $ret = $this->request($url, $method);

        infoLog(__METHOD__ . '----end');
        return $ret;
    }

    /**
     * PUTリクエストする
     *
     * @return  boolean  リクエストが成功した場合：true, リクエストが失敗した場合：false
     **/
    public function put() : bool
    {
        InfoLog(__METHOD__ . '----start');

        $url = self::$url;
        $method = 'PUT';

        $ret = $this->request($url, $method);

        infoLog(__METHOD__ . '----end');
        return $ret;
    }

    /**
     * PATCHリクエストする
     *
     * @return  boolean  リクエストが成功した場合：true, リクエストが失敗した場合：false
     **/
    public function patch() : bool
    {
        InfoLog(__METHOD__ . '----start');

        $url = self::$url;
        $method = 'PATCH';

        $ret = $this->request($url, $method);

        infoLog(__METHOD__ . '----end');
        return $ret;
    }

    /**
     * GETリクエストする
     *
     * @return  boolean  リクエストが成功した場合：true, リクエストが失敗した場合：false
     **/
    public function get() : bool
    {
        infoLog(__METHOD__ . '----start');

        $url = self::$url;
        $method = 'GET';

        if (count(self::$data) > 0){
            $url .= '?' . http_build_query(self::$data);
        }

        $ret = $this->request($url, $method);

        infoLog(__METHOD__ . '----end');
        return $ret;
    }

    /**
     * DELETEリクエストする
     *
     * @return  boolean  リクエストが成功した場合：true, リクエストが失敗した場合：false
     **/
    public function delete() : bool
    {
        infoLog(__METHOD__ . '----start');

        $url = self::$url;
        $method = 'DELETE';

        if (count(self::$data) > 0){
            $url .= '?' . http_build_query(self::$data);
        }

        $ret = $this->request($url, $method);

        infoLog(__METHOD__ . '----end');
        return $ret;
    }

    /**
     * リクエスト処理
     *
     * @param  string  $url     URL
     * @param  string  $method  リクエストメソッド
     * @return  boolean  リクエストが成功した場合：true、リクエストが失敗した場合：false
     */
    protected function request(string $url, string $method) : bool
    {
        debugLog(__METHOD__ . '----start');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::timeout);

        if (count((array)self::$data) > 0) {
            if (self::$isJson) {
                $data = json_encode(self::$data);
                infoLog('json data = ' . $data);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                self::$header[] = 'Content-type: application/json';
            } else {
                $data = self::$data;
                infoLog('post data = ' . print_r($data, true));
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(self::$data));
            }
        }

        if (count((array)self::$header) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, (array)self::$header);
        }

        // リクエスト実行
        $response = curl_exec($ch);

        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $this->httpCode = $httpCode;
        $this->response = $response;

        infoLog('URL = ' . $url);
        infoLog('HTTP status code = ' . $this->httpCode);
        infoLog('Response = ' . $this->response);

        $errno = curl_errno($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($errno !== CURLE_OK) {
            errorLog('A curl connection error has occurred. error = ' . $error);
            return false;
        }

        debugLog(__METHOD__ . '----end');
        return true;
    }

}