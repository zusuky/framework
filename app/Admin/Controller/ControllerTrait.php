<?php

trait ControllerTrait
{

    /**
     * 前処理
     */
    public function before() : void
    {
    }

    /**
     * 後処理
     */
    public function after() : void
    {
    }

    /**
     * エラーページ表示
     *
     * @param  string  $messageId      メッセージID
     * @param  mixed   $messageParams  メッセージパラメータ（省略時はnull）
     */
    public function showError(string $messageId, $messageParams = null) : void
    {
        $message =  Message::get($messageId, (array)$messageParams);
        infoLog('エラーページ表示。 メッセージ=' . $message);
        $view = View::forge();
        $view->assign('error_message', $message);
        $view->display('errors/index.tpl');
    }

    /**
     * セッションタイムアウトエラーページ表示
     */
    public function showSessionTimeoutError() : void
    {
        self::showError('errorSessionTimeout');
    }

    /**
     * 二重送信エラーページ表示
     */
    public function showDoubleTransmissionError() : void
    {
        self::showError('errorDoubleTransmission');
    }

    /**
     * 不正な画面操作エラーページ表示
     */
    public function showInvalidOperationError() : void
    {
        self::showError('errorInvalidOperation');
    }

    /**
     * 無効なURLエラーページ表示
     */
    public function showInvalidUrlError() : void
    {
        self::showError('errorInvalidUrl');
    }

    /**
     * システムエラーページ表示
     */
    public function showSystemError() : void
    {
        self::showError('errorSystem');
    }

}
