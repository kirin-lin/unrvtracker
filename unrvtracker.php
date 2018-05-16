<?php
require_once 'vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
$debug = 0;
$program_name = "UNRV 活動通知";
if($debug) echo "編碼方式：".mb_detect_encoding($program_name)."\n";

date_default_timezone_set('Asia/Taipei');
$now =  date('Ymd H:i:s');

// 準備紀錄器
$log = new Logger('unrvtracker');
$log->pushHandler(new StreamHandler(__DIR__.'/log/develogment.log', Logger::DEBUG));
$log->pushHandler(new StreamHandler(__DIR__.'/log/production.log', Logger::WARNING));

$ch = curl_init();
$url = "http://www.unrv.com.tw/i167.asp";
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 1);
//curl_setopt($ch, CURLOPT_HEADER, false);

$html = curl_exec($ch);
//echo print_r(curl_getinfo($ch));
//echo "HTTP CODE: ".curl_getinfo($ch, CURLINFO_HTTP_CODE)."\n";
$http_code = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));
curl_close($ch);

if( $http_code == 404 )  {
    $msg = "尚未有活動";
    if($debug) echo "$msg\n";
    $log->info($msg);
} else {
    $msg = "有活動！！";
    $log->info($msg);
    if($debug) {
        echo "$msg\n";
    }
    // 以 mail 通知
    $to ="chilin.lin@gmail.com, nn0724@gmail.com"; //收件者
    //$to ="chilin.lin@gmail.com"; //收件者
    $subject = "環球露營已有新活動"; //信件標題
    $mail_body = "
        kirin.idv.tw 於 $now 發送\n
        最新活動連結\n
        $url \n
        請至UNRV粉絲團查看何時開放報名\n
        https://www.facebook.com/702802493166729/ \n";//信件內容
    $headers = "From: kirin@kirin.idv.tw"; //寄件者
    if(mail("$to", "$subject", "$mail_body", "$headers")) {
        $msg = "UNRV活動通知信件已經發送成功。";
        $log->info($msg);
        if( $debug ) echo "$msg \n";//寄信成功就會顯示的提示訊息
    } else {
        $msg = "UNRV活動通知信件發送失敗！";
        echo "$msg\n";//寄信失敗顯示的錯誤訊息
        $log->warning($msg);
    }

    // 以簡訊通知 -- 未完成，無法發送
    /*
    $ch = curl_init();
    $msg = 'TEST message';
    $aryData = array(
        'username' => '27985615',
        'password' => '3468weiwei',
        'dstaddr' => '0922217092',
    //    'encoding' => 'UTF8',
        'dlvtime' => '',
        'smbody' => $msg,
        );
    $get_param = '';
    foreach($aryData as $key => $value) $get_param .= $key.'='.$value.'&';
    $get_param = trim($get_param,'&');
   
    $url = "http://smexpress.mitake.com.tw:9600/SmSendGet.asp".'?'.$get_param; 
    echo $url."\n";
    curl_setopt($ch, CURLOPT_URL, $url); //Url together with parameters
    //curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
    //curl_setopt($ch, CURLOPT_HEADER, 1);

    $result = curl_exec($ch);
    //echo $result."\n";
    curl_close($ch);
    */
    
    /*
    if(curl_errno($ch))  //catch if curl error exists and show it
        echo 'Curl error: ' . curl_error($ch);
    else
        echo $result;
        */


}

?>
