<?php

class Zusuky
{

    /** @var array uri（uriをスラッシュで分割した配列） */
    private static $uri = [];

    /**
     * Zusukyに魂を注入する
     */
    public static function bootstrap()
    {
        try {

            self::initialize();       // 初期処理
            self::identifyLang();     // 言語の特定
            self::loadConfig();       // configの読み込み
            self::registerHandler();  // ハンドラーの登録
            self::dispatch();         // 処理をコントローラーに割り当て

        } catch (Throwable $e) {
            error_log (print_r($e, true));
        }
    }

    /**
     * ベーシック認証
     *
     * @param      string  $authUser  ユーザ
     * @param      string  $authPass  パス
     */
    public static function basicAuth($authUser, $authPass) : void
    {
        if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == $authUser) {
            if ($_SERVER['PHP_AUTH_PW'] == $authPass){
                return;
            }
        }
        self::abort401();
    }

    /**
     * 401画面表示
     * @return void
     */
    public static function abort401() {
        $html = <<<HTML
<span style="font-size: 1.8em;">Authorization Required</span><br/>
<br/>
This server could not verify that you are authorized to access the document requested.<br/>
Either you supplied the wrong credentials (e.g., bad password), or your browser doesn't understand how to supply the credentials required.
HTML;
        header('WWW-Authenticate: Basic realm="Secret Zone"');
        header('HTTP/1.0 401 Unauthorized');
        header('Content-type: text/html; charset=' . mb_internal_encoding());
        echo $html;
        exit;
    }

    /**
     * 404画面表示
     * @return void
     */
    public static function abort404() {
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<title>404 Not Found</title>
</head>
<body>
<h1>Not Found</h1>
<p>
The page you are looking for could not be found.
</p>
<h2>Error 404</h2>
</body>
</html>
HTML;
        header("HTTP/1.0 404 Not Found");
        header('Content-type: text/html; charset=' . mb_internal_encoding());
        echo $html;
        exit;
    }

    /**
     * 500画面表示
     * @return void
     */
    public static function abort500() {
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
<title>500 Internal Server Error</title>
</head>
<body>
<h1>Internal Server Error</h1>
<p>
A failure occurred inside the server and the request could not be fulfilled.
The server is overloaded or the CGI script has errors.
</p>
<h2>Error 500</h2>
</body>
</html>
HTML;
        header("HTTP/1.1 500 Internal Server Error");
        header('Content-type: text/html; charset=' . mb_internal_encoding());
        echo $html;
        exit;
    }

    /**
     * Zuskyの初期処理
     */
    private static function initialize()
    {
        ini_set('default_charset','UTF-8');
        mb_internal_encoding("UTF-8");
        date_default_timezone_set('Asia/Tokyo');
        ini_set('display_errors', 0);

        // uriの保持
        if (isset($_SERVER['REQUEST_URI'])) {
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if (isset($path) && trim($path) !== '') {
                $path = trim($path, '/');
                if (trim($path) !== '') {
                    self::$uri = (array)explode('/', $path);
                }
            }
        }
    }

    /**
     * 言語の特定
     */
    private static function identifyLang() : void
    {
        // uriから言語を特定
        $lang = Lang::getDefault();
        if (isset(self::$uri[0])) {
            $selectable_langs = Lang::getSelectables();
            if (isset($selectable_langs[self::$uri[0]])) {
                $lang = self::$uri[0];
                array_shift(self::$uri);
            }
        }

        // 言語をcookieに保持
        Lang::setCurrent($lang);
    }

    /**
     * configの読み込み
     */
    private static function loadConfig() : void
    {
        // configの定義
        // uri別に、読み込むconfigを指定する
        //   - キー：uri
        //   - バリュー：読み込むconfigファイル
        // キーにアスタリスクを設定すると、それがデフォルトのconfigとなる
        // uriにマッチしなかった場合は、デフォルトのconfigを読み込むことになる
        $configs = [
            'admin' => dirname(__DIR__) . '/config/config.admin.php',
            'api'   => dirname(__DIR__) . '/config/confiapi.api.php',
            '*'     => dirname(__DIR__) . '/config/config.front.php',
        ];

        // uriからconfigファイル特定
        $config_file = '';
        if (isset(self::$uri[0])) {
            if (isset($configs[self::$uri[0]])) {
                if (is_file($configs[self::$uri[0]])) {
                    $config_file = $configs[self::$uri[0]];
                    array_shift(self::$uri);
                }
            }
        }
        if ($config_file === '') {
            if (isset($configs['*'])) {
                if (is_file($configs['*'])) {
                    $config_file = $configs['*'];
                }
            }
        }
        if ($config_file === '') {
            error_log ('The config file could not be identified.');
            error_log ('$_GET =' . print_r($_GET, true));
            error_log ('$_SERVER =' . print_r($_SERVER, true));
            self::abort404();
            exit;
        }

        // config読み込み
        Config::load($config_file);

        // ベーシック認証が定義されていれば実施
        if (Config::get('basic_auth.user') != '' && Config::get('basic_auth.pass')) {
            self::basicAuth(Config::get('basic_auth.user'), Config::get('basic_auth.pass'));
        }
    }

    /**
     *  ハンドラーの登録
     */
    private static function registerHandler() : void
    {
        self::registerErrorHandler();      // エラーハンドラーの登録
        self::registerExceptionHandler();  // 例外ハンドラーの登録
        self::registerShutdownHandler();   // シャットダウンハンドラーの登録
    }

    /**
     *  エラーハンドラーの登録
     */
    private static function registerErrorHandler() : void
    {
        // エラーハンドラ
        set_error_handler(function($errno, $errstr, $errfile, $errline)
        {
            //エラーレベル
            $errorlevel_arr = [
                    E_ERROR             => "E_ERROR",
                    E_WARNING           => "E_WARNING",
                    E_PARSE             => "E_PARSE",
                    E_NOTICE            => "E_NOTICE",
                    E_CORE_ERROR        => "E_CORE_ERROR",
                    E_CORE_WARNING      => "E_CORE_WARNING",
                    E_COMPILE_ERROR     => "E_COMPILE_ERROR",
                    E_COMPILE_WARNING   => "E_COMPILE_WARNING",
                    E_USER_ERROR        => "E_USER_ERROR",
                    E_USER_WARNING      => "E_USER_WARNING",
                    E_USER_NOTICE       => "E_USER_NOTICE",
                    E_STRICT            => "E_STRICT",
                    E_RECOVERABLE_ERROR => "E_RECOVERABLE_ERROR",
                    E_DEPRECATED        => "E_DEPRECATED",
                    E_USER_DEPRECATED   => "E_USER_DEPRECATED",
                    E_ALL               => "E_ALL",
            ];
            // エラー情報
            $errorInfo = [
                    'errlvl' => $errorlevel_arr[$errno],
                    'errstr' => $errstr,
                    'errfile' => $errfile,
                    'errline' => $errline,
            ];
            // phpのワーニングエラーを例外としてスローし、catch可能にする。
            if($errno == E_WARNING ||
                    $errno == E_CORE_WARNING ||
                    $errno == E_COMPILE_WARNING ||
                    $errno == E_USER_WARNING) {
                throw new ErrorException($errstr, $errorlevel_arr[$errno], 0, $errfile, $errline);
            } else {
                if ($errno == E_NOTICE &&
                    strpos($errfile, '.tpl.php') !== false &&
                    (strpos(strtolower($errstr), 'undefined index') !== false  || strpos(strtolower($errstr), 'trying to get property \'value\' of non-object') !== false)) {
                } else {
                    warnLog('A php error (other than a warning) has occurred. Error details =' . print_r($errorInfo, true));
                }
            }
        }, E_ALL);
    }

    /**
     *  例外ハンドラーの登録
     */
    private static function registerExceptionHandler() : void
    {
        // 例外ハンドラ（catchされていない例外のハンドリング）
        set_exception_handler(function($exception)
        {
            fatalLog('Uncaught exception.', $exception);
            DB::close();
            self::abort500();
        });
    }

    /**
     *  シャットダウンハンドラーの登録
     */
    private static function registerShutdownHandler() : void
    {
        // シャットダウンハンドラ
        register_shutdown_function(function()
        {
            DB::close();
            // エラーハンドラにて捕らえることができない致命的なphpエラーをハンドリングする。
            $error = error_get_last();
            if (isset($error['type'])) {
                if ($error['type'] == E_ERROR ||
                        $error['type'] == E_PARSE ||
                        $error['type'] == E_CORE_ERROR ||
                        $error['type'] == E_COMPILE_ERROR ||
                        $error['type'] == E_USER_ERROR) {
                    fatalLog('A fatal php error has occurred.', $error);
                    self::abort500();
                }
            }
        });
    }

    /**
     * コントローラーへの処理の割り当て
     */
    private static function dispatch() : void
    {
        try {

            // loggerだけはクラスじゃないので、個別に読み込む。
            require_once __DIR__ . '/Log/logger.php';

            // appのrootとなるディレクトリをオートローダーに登録
            AutoLoader::add(Config::get('app_dir.root'));

            infoLog('==========' . $_SERVER['SCRIPT_NAME'] . ' start ==========');

            // セッション開始
            Session::start();

            $sourceip = [];
            if (isset($_SERVER['REMOTE_ADDR'])) { $sourceip[] = $_SERVER['REMOTE_ADDR']; }
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) { $sourceip[] = $_SERVER['HTTP_X_FORWARDED_FOR']; }
            infoLog('[REQUEST URL] ' . Html::current());
            infoLog('[SOURCE IP] ' . print_r($sourceip, true));
            infoLog('[REFERER] ' . Arr::get($_SERVER, 'HTTP_REFERER', 'NON'));
            infoLog('[USER_AGENT] ' . Arr::get($_SERVER, 'HTTP_USER_AGENT'));
            infoLog('[SESSION ID] ' . Session::getId());
            infoLog('[LANG] ' . Lang::getCurrent());
            infoLog('[POST PARAMETER] ' . print_r($_POST, true));
            infoLog('[GET PARAMETER] ' . print_r($_GET, true));

            if (count(self::$uri) === 0) {
                // uriが空の場合は、homeのuriに従う
                self::$uri = (array)explode('/', trim(Config::get('uri.home'), '/'));
            }

            // uriからコントローラー名、アクション名を取得
            $controller = 'Controller_' . self::$uri[0];
            $action = 'action_index';
            if (count(self::$uri) > 1) {
                $action = 'action_' . self::$uri[1];
            }

            infoLog('[CONTROLLER] ' . $controller);
            infoLog('[ACTION] ' . $action);

            try {

                // 実行
                $c = new $controller;
                if ($c instanceof Controller) {
                    $c->before();
                    infoLog($controller . '->' . $action . ' start');
                    $c->$action();
                    infoLog($controller . '->' . $action . ' end');
                    $c->after();
                } else {
                    fatalLog('Does not extends Controller class.');
                    self::abort500();
                }

            } catch (Throwable $e) {
                errorLog('Class or method does not exist.', $e);
                self::abort404();
            }

        } catch (Throwable $e) {
            fatalLog('An unexpected exception has occurred.', $e);
            self::abort500();
        } finally {
            infoLog('==========' . $_SERVER['SCRIPT_NAME'] . ' end ==========');
        }
    }

}
