<?php
include_once "../_init_.php";

require '../vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;


// 로거 채널 생성
$log = new Logger('Error.Log');

// 경로
$log_path = DY_LOG_PATH;
//error_mail(format_error( $errno, $errstr, $errfile, $errline));
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$log->pushHandler(new StreamHandler($log_path . "/" . date('Y-m-d') . ".log", Logger::ERROR));

//$log->addError('=================================================');
$log->addError('=================================================');
?>