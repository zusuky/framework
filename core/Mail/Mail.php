<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{

    //------------------------------
    // 定数
    //------------------------------

    const MAIL_DIV_INFO  = 'info';  // メール区分:お知らせ
    const MAIL_DIV_ADMIN = 'admin'; // メール区分:管理者向け通知

    //------------------------------
    // プロパティ
    //------------------------------

    /** @var Smarty smarty */
    private $smarty = null;

    /** @var array メール区分別のアカウント情報 */
    private $accounInfo = null;

    //------------------------------
    // コンストラクタ
    //------------------------------

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $smarty = new Smarty();
        $smarty->template_dir = DIR_APP_VIEW_TEMPLATES;
        $smarty->compile_dir = DIR_APP_VIEW_TEMPLATES_C;
        $smarty->config_dir = DIR_APP_VIEW_CONFIGS;
        $smarty->cache_dir = DIR_APP_VIEW_CACHE;
        $this->smarty = $smarty;

        $this->accounInfo = array(
            self::MAIL_DIV_INFO => array(
                'Host'       => 'XXXXXXXXXXX',
                'Port'       => '587',
                'Username'   => 'XXXXXXXXXXX',
                'Password'   => 'XXXXXXXXXXX',
                'SMTPAuth'   => '1',
                'From'       => 'XXXXXXXXXXX',
                'FromName'   => 'XXXXXXXXXXX',
                'Sender'     => 'XXXXXXXXXXX',
                'SenderName' => 'XXXXXXXXXXX',
            ),
            self::MAIL_DIV_ADMIN => array(
                'Host'       => 'XXXXXXXXXXX',
                'Port'       => '587',
                'Username'   => 'XXXXXXXXXXX',
                'Password'   => 'XXXXXXXXXXX',
                'SMTPAuth'   => '1',
                'From'       => 'XXXXXXXXXXX',
                'FromName'   => 'XXXXXXXXXXX',
                'Sender'     => 'XXXXXXXXXXX',
                'SenderName' => 'XXXXXXXXXXX',
            ),
        );
    }

    //------------------------------
    // メソッド
    //------------------------------

    /**
     * 変数のアサイン
     * @param string  $key     キー
     * @param string  $value   値（省略時はnull）
     * @param boolean $nocache ノーキャッシュ（省略時はfalse）
     */
    public function assign($key, $value = null, $nocache = false)
    {
        $this->smarty->assign($key, $value, $nocache);
    }

    /**
     * アサインされた変数の破棄
     */
    public function clear_all_assign()
    {
        $this->smarty->clear_all_assign();
    }

    /**
     * メール送信
     * @param string  $mailDiv    メール区分
     * @param string  $toAddress  送信先メールアドレス
     * @param string  $subject    件名
     * @param string  $template   メールテンプレート
     * @param boolean $isTemplate true:テンプレートを使う/false:$templateを本文として扱う
     */
    public function send($mailDiv, $toAddress, $subject, $template, $isTemplate = true)
    {
        infoLog(__METHOD__ . '----start');

        if($isTemplate){
            $mailBody = $this->smarty->fetch($template);
        }else{
            $mailBody = $template;
        }

        $ai = $this->accounInfo[$mailDiv];

        mb_language('japanese');

        $mail = new PHPMailer();
        $mail->CharSet  = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isSMTP();
        $mail->Host     = $ai['Host'];
        $mail->Port     = (int)$ai['Port'];
        $mail->SMTPAuth = $ai['SMTPAuth'];
        $mail->Username = $ai['Username'];
        $mail->Password = $ai['Password'];
        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));

        // Fromのメールアドレス
        $mail->From     = $ai['From'];
        $mail->SetFrom($ai['From'], mb_encode_mimeheader($ai['FromName']));

        //エンベロープFROMの設定
        $mail->Sender   = $ai['Sender'];
        $mail->AddReplyTo($ai['Sender'], mb_encode_mimeheader($ai['SenderName']));

        $mail->Subject  = mb_encode_mimeheader($subject, 'UTF-8');
        $mail->Body     = mb_convert_encoding($mailBody, 'UTF-8');

        $addrs = explode(';', $toAddress);

        foreach((array)$addrs as $add){
            if (strlen(trim($add)) > 0) {
                $mail->AddAddress($add, '');
            }
        }

        try {
            $return_flg = $mail->Send();

            if ($return_flg) {
                infoLog('メール送信に成功しました。' . PHP_EOL . 'toAddress=' . $toAddress . PHP_EOL . 'subject=' . $subject . PHP_EOL . 'mailBody=' . $mailBody);
            } else {
                fatalLog('メール送信に失敗しました。' . PHP_EOL . 'toAddress=' . $toAddress . PHP_EOL . 'subject=' . $subject . PHP_EOL . 'mailBody=' . $mailBody);
            }

        } catch (Exception $e) {
            fatalLog('メール送信に失敗しました。（例外発生）' . PHP_EOL . 'toAddress=' . $toAddress . PHP_EOL . 'subject=' . $subject . PHP_EOL . 'mailBody=' . $mailBody, $e);
            $return_flg = false;
        }

        infoLog(__METHOD__ . '----end');
        return $return_flg;
    }

    /**
     * HTMLメール送信
     * @param string  $mailDiv    メール区分
     * @param string  $toAddress  送信先メールアドレス
     * @param string  $subject    件名
     * @param string  $template   メールテンプレート
     * @param boolean $isTemplate true:テンプレートを使う/false:$templateを本文として扱う
     *
     * <p>
     * sendメソッドと[$mail->isHTML(true)の部分以外は変わらない<br>
     * HTMLメールは現状、券発報告以外で使用しないので一旦この形で対応
     * </p>
     *
     */
    public function sendAsHTML($mailDiv, $toAddress, $subject, $template, $isTemplate = true)
    {
        infoLog(__METHOD__ . '----start');

        if($isTemplate){
            $mailBody = $this->smarty->fetch($template);
        }else{
            $mailBody = $template;
        }

        $ai = $this->accounInfo[$mailDiv];

        mb_language('japanese');

        $mail = new PHPMailer();
        $mail->CharSet  = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isSMTP();
        $mail->isHTML(true);
        $mail->Host     = $ai['Host'];
        $mail->Port     = (int)$ai['Port'];
        $mail->SMTPAuth = $ai['SMTPAuth'];
        $mail->Username = $ai['Username'];
        $mail->Password = $ai['Password'];
        $mail->SMTPOptions = array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true));

        // Fromのメールアドレス
        $mail->From     = $ai['From'];
        $mail->SetFrom($ai['From'], mb_encode_mimeheader($ai['FromName']));

        //エンベロープFROMの設定
        $mail->Sender   = $ai['Sender'];
        $mail->AddReplyTo($ai['Sender'], mb_encode_mimeheader($ai['SenderName']));

        $mail->Subject  = mb_encode_mimeheader($subject, 'UTF-8');
        $mail->Body     = mb_convert_encoding($mailBody, 'UTF-8');

        $addrs = explode(';', $toAddress);

        foreach((array)$addrs as $add){
            if (strlen(trim($add)) > 0) {
                $mail->AddAddress($add, '');
            }
        }

        try {
            $return_flg = $mail->Send();

            if ($return_flg) {
                infoLog('メール送信に成功しました。' . PHP_EOL . 'toAddress=' . $toAddress . PHP_EOL . 'subject=' . $subject . PHP_EOL . 'mailBody=' . $mailBody);
            } else {
                fatalLog('メール送信に失敗しました。' . PHP_EOL . 'toAddress=' . $toAddress . PHP_EOL . 'subject=' . $subject . PHP_EOL . 'mailBody=' . $mailBody);
            }

        } catch (Exception $e) {
            fatalLog('メール送信に失敗しました。（例外発生）' . PHP_EOL . 'toAddress=' . $toAddress . PHP_EOL . 'subject=' . $subject . PHP_EOL . 'mailBody=' . $mailBody, $e);
            $return_flg = false;
        }

        infoLog(__METHOD__ . '----end');
        return $return_flg;
    }

}