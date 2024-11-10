<?php
$_this_forder = explode('/',str_replace('\\', '/', dirname(__FILE__)));
array_pop ($_this_forder);
$inc_folder_path = implode('/' , $_this_forder)."/_INCLUDE/";
$config_file = $inc_folder_path . 'config.inc';
$function_file = $inc_folder_path . 'func.inc';

if (file_exists($config_file)) {
	include_once($config_file);
	include_once($function_file);
}

require 'vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

register_shutdown_function( "fatal_handler" );

function fatal_handler() {

	global $_POST;

	$errfile = "unknown file";
	$errstr  = "shutdown";
	$errno   = E_CORE_ERROR;
	$errline = 0;

	$error = error_get_last();

	if( $error !== NULL) {
		$errno   = $error["type"];
		$errfile = $error["file"];
		$errline = $error["line"];
		$errstr  = $error["message"];

		if($errno == 1 || $errno == 16 || $errno == 64) {

			//로그 포맷
			$loggerFormat = "%datetime% [[^^]] %level_name% [[^^]] %message% [[^^]] %context% [[^^]] %extra%\n";
			$loggerTimeFormat = "Y-m-d H:i:s";
			$formatter = new LineFormatter($loggerFormat, $loggerTimeFormat);

			// 로거 채널 생성
			$log = new Logger('Error.Log');

			// 경로
			$log_path = DY_LOG_PATH;
			//error_mail(format_error( $errno, $errstr, $errfile, $errline));
			// log/your.log 파일에 로그 생성. 로그 레벨은 Info
			$loggerHandler = new StreamHandler($log_path . "/PHP_" . date('Y-m-d') . ".log", Logger::ERROR);
			$loggerHandler->setFormatter($formatter);
			$log->pushHandler($loggerHandler);

			$error_ary = array();
			$error_ary["no"] = $errno;
			$error_ary["str"] = $errstr;
			$error_ary["file"] = $errfile;
			$error_ary["line"] = $errline;
			$error_ary["querystring"] = $_SERVER["QUERY_STRING"];
			$error_ary["postdata"] = $_POST;

			$log->addError($errstr, $error_ary);

			//$log->addError('=================================================');
			//$log->addError('Error No : ' . $errno);
			//$log->addError('Error Str : ' . $errstr);
			//$log->addError('Error File : ' . $errfile);
			//$log->addError('Error Line : ' . $errline);
			//$log->addError('=================================================');
		}
	}
}

function classAutoloader($path)
{
	//$path = str_replace("DYFLUX\\", DY_CLASS_PATH . "/", $path);
	$path = DY_CLASS_PATH . "/" . $path;
	$path = $path.'.php';
//	var_dump($path);
	require_once $path;
}
spl_autoload_register('classAutoloader');


/**
 * 권한 체크
 * 페이지 상단 _init_php 로그 하기전
 * $pagePermissionIdx 변수가 선언되어 있으면
 * 해당 메뉴 IDX 로 권한을 체크한다.
 */
include_once DY_INCLUDE_PATH . "/_include_check_permission.php";
?>
