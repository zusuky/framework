<?php

class Rest
{

    /** @var  string  レスポンスタイプ : json */
    const RESPONSE_TYPE_JSON = 'json';

    /** @var  string  レスポンスタイプ : html */
    const RESPONSE_TYPE_HTML = 'html';

    /** @var  string  レスポンスタイプ : text */
    const RESPONSE_TYPE_TEXT = 'text';

    /** @var  array  リクエストデータ */
    protected static array $request_data = null;

    /** @var  int  HTTP status code */
    protected static int $status_code = 200;

    /** @var  string  レスポンスタイプ */
    protected static string $response_type = self::RESPONSE_TYPE_JSON;

    /** @var  string|array レスポンスデータ */
    protected static $response_data = '';

    /**
     * リクエストメソッド取得
     *
     * @return  string
     */
    public static function getMethod() : string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * リクエストコンテントタイプ取得
     *
     * @return  string
     */
    public static function getContentType() : string
    {
        $headers = apache_request_headers();
        return isset($headers['Content-Type']) ? $headers['Content-Type'] : '';
    }

    /**
     * リクエストパラメータ取得
     * キーを指定しない場合は、全てのパラメータを返却する
     *
     * @param  string        $key      キー（省略時はnull）
     * @param  string|array  $default  キーがない場合の返却値（省略時は空文字）
     * @return  string|array
     */
    public static function getParameter($key = null, $default = '')
    {
        if (is_null(self::$request_data)) {
            $method = self::getMethod();
            $contentType = self::getContentType();
            switch ($method) {
                case 'GET':
                case 'DELETE':
                    self::$request_data = $_GET;
                    break;
                case 'POST':
                case 'PUT':
                case 'PATCH':
                    if ($contentType !== '' && strpos($contentType, 'application/json') !== false) {
                        $request = json_decode(file_get_contents('php://input'), true);
                    } else {
                         $request = $_POST;
                    }
                    self::$request_data = $request;
                    break;
                default:
                    self::$request_data = [];
                    break;
            }
            infoLog('Request method = ' . $method);
            infoLog('Request content type = ' . $contentType);
            infoLog('Request data =' . print_r(self::$request_data, true));
        }
        if (func_num_args() === 0) {
            return self::$request_data;
        }
        return Arr::get(self::$request_data, $key, $default);
    }

    /**
     * HTTP status code設定
     *
     * @param  int  $status_code  HTTP status code
     */
    public static function setStatusCode($status_code) : void
    {
        $this->status_code = $status_code;
    }

    /**
     * レスポンスタイプ設定
     *
     * @param  string  $response_type  レスポンスタイプ
     */
    public static function setResponseType($response_type) : void
    {
        $this->response_type = $response_type;
    }

    /**
     * レスポンスデータ設定
     * - レスポンスタイプがjsonの場合 : 配列データを設定する必要あり
     * - レスポンスタイプがjson以外の場合 : 文字列を設定
     *
     * @param  string|array  $response_data  レスポンスデータ
     */
    public static function setResponseData($response_data) : void
    {
        $this->response_data = $response_data;
    }

    /**
     * レスポンス処理
     */
    public static function response() : void
    {
        switch ($this->response_type) {
            case self::RESPONSE_TYPE_JSON:
                header("HTTP/1.1 " . $this->status_code . " " . $this->getStatusMessage());
                header("Content-Type: application/json; charset=UTF-8");
                $response_data = json_encode((array)$this->response_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
                break;
            case self::RESPONSE_TYPE_HTML:
                header("HTTP/1.1 " . $this->status_code . " " . $this->getStatusMessage());
                header('Content-type: text/html; charset=utf-8');
                $response_data = $this->response_data;
                break;
            case self::RESPONSE_TYPE_TEXT:
            default:
                header("HTTP/1.1 " . $this->status_code . " " . $this->getStatusMessage());
                header('Content-type: text/plain; charset=utf-8');
                $response_data = $this->response_data;
                break;

        }
        echo $response_data;
        infoLog('Response type =' . $this->response_type);
        infoLog('HTTP status code =' . $this->status_code);
        infoLog('Response data =' . $response_data);
    }

    /**
     * HTTPステータスメッセージ取得
     *
     * @return  string
     */
    private static function getStatusMessage() : string
    {
        $status = array(
                100 => 'Continue',
                101 => 'Switching Protocols',
                200 => 'OK',
                201 => 'Created',
                202 => 'Accepted',
                203 => 'Non-Authoritative Information',
                204 => 'No Content',
                205 => 'Reset Content',
                206 => 'Partial Content',
                300 => 'Multiple Choices',
                301 => 'Moved Permanently',
                302 => 'Found',
                303 => 'See Other',
                304 => 'Not Modified',
                305 => 'Use Proxy',
                306 => '(Unused)',
                307 => 'Temporary Redirect',
                400 => 'Bad Request',
                401 => 'Unauthorized',
                402 => 'Payment Required',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                406 => 'Not Acceptable',
                407 => 'Proxy Authentication Required',
                408 => 'Request Timeout',
                409 => 'Conflict',
                410 => 'Gone',
                411 => 'Length Required',
                412 => 'Precondition Failed',
                413 => 'Request Entity Too Large',
                414 => 'Request-URI Too Long',
                415 => 'Unsupported Media Type',
                416 => 'Requested Range Not Satisfiable',
                417 => 'Expectation Failed',
                500 => 'Internal Server Error',
                501 => 'Not Implemented',
                502 => 'Bad Gateway',
                503 => 'Service Unavailable',
                504 => 'Gateway Timeout',
                505 => 'HTTP Version Not Supported',
        );
        return isset($status[$this->status_code]) ? $status[$this->status_code] : $status[500];
    }

}