<?php
/********************
상수 선언
 ********************/

// 이 상수가 정의되지 않으면 각각의 개별 페이지는 별도로 실행될 수 없음
define('_DYFLUX_', true);

/**
 * SSL 강제 적용 설정 (Y/N)
 */
define('DY_FORCED_SSL', 'Y');

/**
 * CRYPT_MD5 - $1$로 시작하는 12문자 salt를 가지는 MD5 암호화
 */
define('DY_PASSWORD_SALT', '$1$dyflux12$');

define('DY_CLASS_DIR',      '_CLASS');
define('DY_CSS_DIR',        'css');
define('DY_DATA_DIR',       '_data');
define('DY_IMG_DIR',        'images');
define('DY_JS_DIR',         'js');
define('DY_INCLUDE_DIR',    'include');
define('DY_SESSION_DIR',    'session');
define('DY_BANNER_DIR',		'banner');
define('DY_POPUP_DIR',		'popup');
define('DY_LOG_DIR',		'_logs');
define('DY_SHORTURL_DIR',	'_link');
define('DY_UPLOAD_DIR',		'common');
define('DY_XLS_UPLOAD_DIR',		'xls_tmp');                     //엑셀 업로드 시 엑셀 임시 저장 위치
define('DY_ORDER_XLS_UPLOAD_DIR',		'order_xls');           //발주 업로드
define('DY_PRODUCT_UPLOAD_DIR',		'product');                 //상품 이미지
define('DY_THUMBNAIL_DIR',		'thumbnail');                   //썸네일 이미지 생성 위치
define('DY_STOCK_ORDER_XLS_DIR',		'stock_order_xls');     //재고 발주 엑셀 파일
define('DY_ORDER_DOWNLOAD_XLS_DIR',		'order_download_xls');  //주문다운로드 공급처 포맷설정 엑셀 파일
define('DY_ORDER_INVOICE_XLS_DIR',		'order_invoice_upload_xls');  //주문다운로드 공급처 포맷설정 엑셀 파일
define('DY_SETTLE_XLS_DIR',		'settle_xls');  //원장 엑셀 파일
define('DY_TAX_XLS_DIR',		'tax_xls');  //세금계산서 엑셀 파일
define('DY_LOSS_XLS_DIR',		'loss_xls');  //세금계산서 엑셀 파일
define('DY_XLS_SAMPLE',		'_xls_sample');                     //엑셀 샘플 저장 위치
define('DY_STOCK_FILE_DIR',		'stock_file');                  //재고 입고 시 등록하는 파일
define('DY_CS_FILE_DIR',		'cs');                  //재고 입고 시 등록하는 파일
define('DY_BBS_FILE_DIR',		'bbs');                  //재고 입고 시 등록하는 파일
define('DY_BANNER_FILE_DIR',		'banner');                  //재고 입고 시 등록하는 파일
define('DY_MAX_UPLOAD_SIZE',	1024*1024*500);

if (PHP_VERSION >= '5.1.0') {
	//if (function_exists("date_default_timezone_set")) date_default_timezone_set("Asia/Seoul");
	date_default_timezone_set("Asia/Seoul");
}

// URL 은 브라우저상에서의 경로 (도메인으로 부터의)
if (DY_DOMAIN) {
	if(DY_FORCED_SSL == 'Y'){
		define('DY_URL', DY_HTTPS_DOMAIN);
	}else{
		define('DY_URL', DY_DOMAIN);
	}
} else {
	if (isset($dy_path['url']))
		define('DY_URL', $dy_path['url']);
	else
		define('DY_URL', '');
}


if (isset($dy_path['path'])) {
	define('DY_PATH', $dy_path['path']);
} else {
	define('DY_PATH', '');
}

if (isset($dy_path['document_home'])) {
	define('DY_DOCUMENT_HOME', $dy_path['document_home']);
} else {
	define('DY_DOCUMENT_HOME', '');
}


define('DY_CSS_URL',                DY_URL.'/'.DY_CSS_DIR);
define('DY_DATA_URL',               DY_URL.'/'.DY_DATA_DIR);
define('DY_IMG_URL',                DY_URL.'/'.DY_IMG_DIR);
define('DY_BANNER_URL',             DY_DATA_URL.'/'.DY_BANNER_DIR);
define('DY_POPUP_URL',              DY_DATA_URL.'/'.DY_POPUP_DIR);
define('DY_JS_URL',                 DY_URL.'/'.DY_JS_DIR);
define('DY_SHORTURL_URL',           DY_URL.'/'.DY_SHORTURL_DIR);
define('DY_UPLOAD_URL',             DY_DATA_URL.'/'.DY_UPLOAD_DIR);
define('DY_XLS_UPLOAD_URL',         DY_DATA_URL.'/'.DY_XLS_UPLOAD_DIR);
define('DY_ORDER_XLS_UPLOAD_URL',   DY_DATA_URL.'/'.DY_ORDER_XLS_UPLOAD_DIR);
define('DY_PRODUCT_UPLOAD_URL',     DY_DATA_URL.'/'.DY_PRODUCT_UPLOAD_DIR);
define('DY_THUMBNAIL_URL',          DY_DATA_URL.'/'.DY_THUMBNAIL_DIR);
define('DY_STOCK_ORDER_XLS_URL',    DY_DATA_URL.'/'.DY_STOCK_ORDER_XLS_DIR);
define('DY_ORDER_DOWNLOAD_XLS_URL', DY_DATA_URL.'/'.DY_ORDER_DOWNLOAD_XLS_DIR);
define('DY_ORDER_INVOICE_XLS_URL',  DY_DATA_URL.'/'.DY_ORDER_INVOICE_XLS_DIR);
define('DY_SETTLE_XLS_URL',         DY_DATA_URL.'/'.DY_SETTLE_XLS_DIR);
define('DY_TAX_XLS_URL',            DY_DATA_URL.'/'.DY_TAX_XLS_DIR);
define('DY_LOSS_XLS_URL',           DY_DATA_URL.'/'.DY_LOSS_XLS_DIR);
define('DY_XLS_SAMPLE_URL',         DY_URL.'/'.DY_XLS_SAMPLE);
define('DY_STOCK_FILE_URL',         DY_DATA_URL.'/'.DY_STOCK_FILE_DIR);
define('DY_CS_FILE_URL',            DY_DATA_URL.'/'.DY_CS_FILE_DIR);
define('DY_BBS_FILE_URL',           DY_DATA_URL.'/'.DY_BBS_FILE_DIR);
define('DY_BANNER_FILE_URL',        DY_DATA_URL.'/'.DY_BANNER_FILE_DIR);

// PATH 는 서버상에서의 절대경로
define('DY_CLASS_PATH',             DY_DOCUMENT_HOME.'/'.DY_CLASS_DIR);
define('DY_DATA_PATH',              DY_PATH.'/'.DY_DATA_DIR);
define('DY_INCLUDE_PATH',           DY_PATH.'/'.DY_INCLUDE_DIR);
define('DY_SESSION_PATH',           DY_DATA_PATH.'/'.DY_SESSION_DIR);
define('DY_LOG_PATH',               DY_PATH.'/'.DY_LOG_DIR);
define('DY_UPLOAD_PATH',            DY_DATA_PATH .'/'. DY_UPLOAD_DIR);
define('DY_XLS_UPLOAD_PATH',        DY_DATA_PATH .'/'. DY_XLS_UPLOAD_DIR);
define('DY_ORDER_XLS_UPLOAD_PATH',  DY_DATA_PATH .'/'. DY_ORDER_XLS_UPLOAD_DIR);
define('DY_PRODUCT_UPLOAD_PATH',    DY_DATA_PATH .'/'. DY_PRODUCT_UPLOAD_DIR);
define('DY_THUMBNAIL_PATH',         DY_DATA_PATH .'/'. DY_THUMBNAIL_DIR);
define('DY_STOCK_ORDER_PATH',       DY_DATA_PATH .'/'. DY_STOCK_ORDER_XLS_DIR);
define('DY_ORDER_DOWNLOAD_PATH',    DY_DATA_PATH .'/'. DY_ORDER_DOWNLOAD_XLS_DIR);
define('DY_ORDER_INVOICE_PATH',     DY_DATA_PATH .'/'. DY_ORDER_INVOICE_XLS_DIR);
define('DY_SETTLE_PATH',            DY_DATA_PATH .'/'. DY_SETTLE_XLS_DIR);
define('DY_TAX_XLS_PATH',           DY_DATA_PATH .'/'. DY_TAX_XLS_DIR);
define('DY_LOSS_XLS_PATH',          DY_DATA_PATH .'/'. DY_LOSS_XLS_DIR);
define('DY_XLS_SAMPLE_PATH',        DY_PATH .'/'. DY_XLS_SAMPLE);
define('DY_STOCK_FILE_PATH',        DY_DATA_PATH .'/'. DY_STOCK_FILE_DIR);
define('DY_CS_FILE_PATH',           DY_DATA_PATH .'/'. DY_CS_FILE_DIR);
define('DY_BBS_FILE_PATH',          DY_DATA_PATH .'/'. DY_BBS_FILE_DIR);
define('DY_BANNER_FILE_PATH',       DY_DATA_PATH .'/'. DY_BANNER_FILE_DIR);

/*Mobile*/
define('DY_Mobile_INCLUDE_PATH',    DY_PATH.'/m/'.DY_INCLUDE_DIR);

define('DY_DATA_RELATE_URL', '/' . DY_DATA_DIR);
define('DY_EDITOR_RELATE_URL', '/' . DY_PLUGIN_DIR . '/' . DY_EDITOR_DIR);

/********************
시간 상수
 ********************/
// 서버의 시간과 실제 사용하는 시간이 틀린 경우 수정하세요.
// 하루는 86400 초입니다. 1시간은 3600초
// 6시간이 빠른 경우 time() + (3600 * 6);
// 6시간이 느린 경우 time() - (3600 * 6);
define('DY_SERVER_TIME',    time());
define('DY_TIME_YMDHIS',    date('Y-m-d H:i:s', DY_SERVER_TIME));
define('DY_TIME_YMD',       substr(DY_TIME_YMDHIS, 0, 10));
define('DY_TIME_HIS',       substr(DY_TIME_YMDHIS, 11, 8));

// 입력값 검사 상수 (숫자를 변경하시면 안됩니다.)
define('DY_ALPHAUPPER',      1); // 영대문자
define('DY_ALPHALOWER',      2); // 영소문자
define('DY_ALPHABETIC',      4); // 영대,소문자
define('DY_NUMERIC',         8); // 숫자
define('DY_HANGUL',         16); // 한글
define('DY_SPACE',          32); // 공백
define('DY_SPECIAL',        64); // 특수문자

// escape string 처리 함수 지정
// addslashes 로 변경 가능
define('DY_ESCAPE_FUNCTION', 'sql_escape_string');

//이미지 파일 확장자 -
define('DY_IMAGE_EXT', 'gif|jpg|jpeg|png');

//파일 업로드 기본 제한 확장자
define('DY_UPLOAD_DENY_EXT', 'php|phtm|htm|cgi|pl|exe|jsp|asp|inc');

//JPG 썸네일 퀄리티
define('DY_THUMB_JPG_QUALITY', 90);


//썸네일 관련
define('SCALE_EXACT_FIT', 'exactfit');
define('SCALE_SHOW_ALL', 'showall');
define('EXPORT_JPG', 'jpg');
define('EXPORT_GIF', 'gif');
define('EXPORT_PNG', 'png');


//단축URL관련
// Choose your character set (default)
//$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

// The following are shuffled strings of the default character set.
// You can uncomment one of the lines below to use a pre-generated set,
// or you can generate your own using the PHP str_shuffle function.
// Using shuffled characters will ensure your generated URLs are unique
// to your installation and are harder to guess.

// $chars = 'XPzSI6v5DqLuBtVWQARy2mfwkC14F8HUTOG0aJiYpNrl9Zxgbd3Khsno7jMeEc';
// $chars = 'PAC3mfIazxgF1lVK4wJ2WEHY0dcb87TrsZeBpL9vNUMGktROijnSoq5DX6yQhu';
// $chars = 'zFr7ALOJnGRxtKSs0oQT5NeZjdI1iX8DM2lHaCVyg4mUPp63BkEubc9qWfhwYv';
// $chars = 'u7oIws3pVWZMQjA4XhNtyvglkEer1C2J5YdT6zLiFm0ObPc8S9KaDHqRBnfUGx';
// $chars = 'gZ6hdO59XTJmUP31YMG7FvQyqjlKkf8zwitx0AcupDVs2RWCIBaNreob4nLHES';

define('DY_SHORTY_CHARS', 'XPzSI6v5DqLuBtVWQARy2mfwkC14F8HUTOG0aJiYpNrl9Zxgbd3Khsno7jMeEc');

// If you want your generated URLs to even harder to guess, you can set
// the salt value below to any non empty value. This is especially useful for
// encoding consecutive numbers.
//$salt = '12345';
define('DY_SHORTY_SALT', '12345');

// The padding length to use when the salt value is configured above.
// The default value is 3.
//$padding = 6;
define('DY_SHORTY_PADDING', 6);

?>
