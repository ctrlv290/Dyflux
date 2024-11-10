<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);
ini_set("display_errors", 1);

//echo $_SERVER["REMOTE_ADDR"];
$_dev_ip_list = array(
	"222.110.5.230",
);
if(in_array($_SERVER["REMOTE_ADDR"], $_dev_ip_list) || $_SERVER["REMOTE_ADDR"] == "127.0.0.1" || $_SERVER["REMOTE_ADDR"] == "::1")
{
	define('DY_DEV_ING',      true);
}else{
	define('DY_DEV_ING',      false);
}


function dy_path()
{
	$_this_forder = explode('/',str_replace('\\', '/', dirname(__FILE__)));
	array_pop($_this_forder);
	$result['document_home'] = implode('/' , $_this_forder);
	$result['path'] = implode('/' , $_this_forder)."/www";
	$tilde_remove = preg_replace('/^\/\~[^\/]+(.*)$/', '$1', $_SERVER['SCRIPT_NAME']);
	$document_root = str_replace($tilde_remove, '', $_SERVER['SCRIPT_FILENAME']);
	$root = str_replace($document_root, '', $result['path']);
	$port = $_SERVER['SERVER_PORT'] != 80 ? ':'.$_SERVER['SERVER_PORT'] : '';
	$http = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 's' : '') . '://';
	$user = str_replace(str_replace($document_root, '', $_SERVER['SCRIPT_FILENAME']), '', $_SERVER['SCRIPT_NAME']);
	$result['url'] = $http.$_SERVER['SERVER_NAME'].$port.$user.$root;
	return $result;
}

$dy_path = dy_path();

////////////////////////////////////////////////////////////////
//# include
////////////////////////////////////////////////////////////////
if(isset($GL_Enable_EUCKR) && $GL_Enable_EUCKR) {
	include_once dirname(__FILE__) . '/no_cache_euckr.inc';
}else{
	include_once dirname(__FILE__) . '/no_cache.inc';
}

include_once dirname(__FILE__) . '/site_config.inc';	    //사이트 관련 설정 파일 (사이트에 맞게 수정 필요!!)
include_once dirname(__FILE__) . '/const.inc';			//일반 상수 정의 파일

//SSL Check
//SSL 강제 적용 설정이 되어 있으면 실행
if(DY_FORCED_SSL == "Y"){
	//localhost 로 실행 중일 때는 제외
	if($_SERVER["REMOTE_ADDR"] != "127.0.0.1" && $_SERVER["REMOTE_ADDR"] != "::1") {
		if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
			$location = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: ' . $location);
			exit;
		}
	}
}

unset($dy_path);


// multi-dimensional array에 사용자지정 함수적용
function array_map_deep($fn, $array)
{
	if(is_array($array)) {
		foreach($array as $key => $value) {
			if(is_array($value)) {
				$array[$key] = array_map_deep($fn, $value);
			} else {
				$array[$key] = call_user_func($fn, $value);
			}
		}
	} else {
		$array = call_user_func($fn, $array);
	}

	return $array;
}


// SQL Injection 대응 문자열 필터링
function sql_escape_string($str)
{
	if(defined('DY_ESCAPE_PATTERN') && defined('DY_ESCAPE_REPLACE')) {
		$pattern = DY_ESCAPE_PATTERN;
		$replace = DY_ESCAPE_REPLACE;

		if($pattern)
			$str = preg_replace($pattern, $replace, $str);
	}

	//$str = call_user_func('addslashes', $str);
	$str = call_user_func('ms_escape_string', $str);

	return $str;
}

function ms_escape_string($data) {
	if ( !isset($data) ) return '';
	if ( is_numeric($data) ) return $data;
	if ( empty($data)  ) return '';

	$non_displayables = array(
		'/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
		'/%1[0-9a-f]/',             // url encoded 16-31
		'/[\x00-\x08]/',            // 00-08
		'/\x0b/',                   // 11
		'/\x0c/',                   // 12
		'/[\x0e-\x1f]/'             // 14-31
	);
	foreach ( $non_displayables as $regex )
		$data = preg_replace( $regex, '', $data );
	$data = str_replace("'", "''", $data );
	return $data;
}

//==============================================================================
// SQL Injection 등으로 부터 보호를 위해 sql_escape_string() 적용
//------------------------------------------------------------------------------
// magic_quotes_gpc 에 의한 backslashes 제거
if (get_magic_quotes_gpc()) {
	$_POST    = array_map_deep('stripslashes',  $_POST);
	$_GET     = array_map_deep('stripslashes',  $_GET);
	$_COOKIE  = array_map_deep('stripslashes',  $_COOKIE);
	$_REQUEST = array_map_deep('stripslashes',  $_REQUEST);
}

// sql_escape_string 적용
$_POST    = array_map_deep(DY_ESCAPE_FUNCTION,  $_POST);
$_GET     = array_map_deep(DY_ESCAPE_FUNCTION,  $_GET);
$_COOKIE  = array_map_deep(DY_ESCAPE_FUNCTION,  $_COOKIE);
$_REQUEST = array_map_deep(DY_ESCAPE_FUNCTION,  $_REQUEST);
//==============================================================================

// PHP 4.1.0 부터 지원됨
// php.ini 의 register_globals=off 일 경우
@extract($_GET);
@extract($_POST);
@extract($_SERVER);

//==============================================================================
// SESSION 설정
//------------------------------------------------------------------------------
@ini_set("session.use_trans_sid", 0);    // PHPSESSID를 자동으로 넘기지 않음
@ini_set("url_rewriter.tags",""); // 링크에 PHPSESSID가 따라다니는것을 무력화함

if (!file_exists(DY_SESSION_PATH)) {
    mkdir(DY_SESSION_PATH, 0777);
}
session_save_path(DY_SESSION_PATH);

if (isset($SESSION_CACHE_LIMITER))
	@session_cache_limiter($SESSION_CACHE_LIMITER);
else
	@session_cache_limiter("no-cache, must-revalidate");

ini_set("session.cache_expire", 180); // 세션 캐쉬 보관시간 (분)
ini_set("session.gc_maxlifetime", 10800); // session data의 garbage collection 존재 기간을 지정 (초)
ini_set("session.gc_probability", 1); // session.gc_probability는 session.gc_divisor와 연계하여 gc(쓰레기 수거) 루틴의 시작 확률을 관리합니다. 기본값은 1입니다. 자세한 내용은 session.gc_divisor를 참고하십시오.
ini_set("session.gc_divisor", 100); // session.gc_divisor는 session.gc_probability와 결합하여 각 세션 초기화 시에 gc(쓰레기 수거) 프로세스를 시작할 확률을 정의합니다. 확률은 gc_probability/gc_divisor를 사용하여 계산합니다. 즉, 1/100은 각 요청시에 GC 프로세스를 시작할 확률이 1%입니다. session.gc_divisor의 기본값은 100입니다.

session_set_cookie_params(0, '/');
ini_set("session.cookie_domain", DY_COOKIE_DOMAIN);

@session_start();


// 4.00.03 : [보안관련] PHPSESSID 가 틀리면 로그아웃한다.
if (isset($_REQUEST['PHPSESSID']) && $_REQUEST['PHPSESSID'] != session_id())
	goto_url(DY_URL.'/logout.php');


////////////////////////////////////////////////////////////////
//# local function
////////////////////////////////////////////////////////////////
$_dev_query_table = "";
$_const_info = "";
if (DY_DEV_ING) {
	function _show_global () {
		$str = "<table border='1' width='100%'>";
		$str .= "<colgroup><col style='width: 200px;' /><col /></colgroup>";
		$tmpGL = $GLOBALS;
		ksort($tmpGL);
		foreach ($tmpGL as $k => $v) {
			if (substr($k,0,3) == 'GL_')
				$str .= '<tr><td>$' . $k . '</td><td>' . print_r($v, TRUE) . '</td></tr>';
		}
		$str .= "</table>";
		return $str;
	}

	function _const_list($prefix = 'DY_')
	{
		foreach (get_defined_constants() as $key=>$value)
			if (substr($key,0,strlen($prefix))==$prefix)  $dump[$key] = $value;
		ksort($dump);

		$str = "<table border='1' width='100%'>";
		$str .= "<colgroup><col style='width: 200px;' /><col /></colgroup>";
		foreach ($dump as $k => $v) {
			$str .= '<tr><td>' . $k . '</td><td>' . print_r($v, TRUE) . '</td></tr>';
		}
		$str .= "</table>";

		if(empty($dump)) { return "Error: No Constants found with prefix '".$prefix."'"; }
		else { return $str; }
	}

	$_const_info = _const_list ();


	//include_once 'dev_script.inc';
}


if($_SERVER["REMOTE_ADDR"] != "222.110.5.230")
{
	$GL_dev_ing = 0;
}

$GL_dev_ing = 0;
/*
	Global Vars
*/
$GL_page_nm = $_SERVER["SCRIPT_NAME"];
$GL_page_querystring = $_SERVER["QUERY_STRING"];
$GL_page_url = $GL_page_nm;
$GL_page_url .= ($GL_page_querystring) ? "?".$GL_page_querystring : "";
/*
 * 로그인 관련 변수
 */
$GL_Member = array();
$GL_Member = $_SESSION["dy_member"];

/*
 * 모바일 로그인 관련 변수
 */

//로그인 가능한 member_idx 설정
//사용안함
//DY_MEMBER 테이블의 can_mobile_login 필드로 확인
//$GL_Available_MobileLogin_member_idx = array(0, 10004);

$GL_Member_M = array();
$GL_Member_M = $_SESSION["dy_member_mobile"];
?>