<?php
/**
 * 접속 가능 도메인 설정
 */
$_GL_Able_Host = array(
	"dyflux.co.kr",
	"www.dyflux.co.kr",
	"dev.dyflux.co.kr",
);

/*
	도메인 설정
	마지막 '/' 제외
	로컬호스트 일경우 체크
*/
if($_SERVER["REMOTE_ADDR"] == "127.0.0.1" || $_SERVER["REMOTE_ADDR"] == "::1")
{
	if($_SERVER["SERVER_PORT"] == "80"){
		define('DY_LOCALHOST', true);
		define('DY_DOMAIN', 'http://localhost');
		define('DY_HTTPS_DOMAIN', 'http://localhost');
		//define('DY_DOMAIN', 'http://dev.dy.com');
		//define('DY_HTTPS_DOMAIN', 'http://dev.dy.com');
	}else{
		define('DY_LOCALHOST', true);
		define('DY_DOMAIN', 'http://localhost:'.$_SERVER["SERVER_PORT"]);
		define('DY_HTTPS_DOMAIN', 'http://localhost:'.$_SERVER["SERVER_PORT"]);
		//define('DY_DOMAIN', 'http://dev.dy.com');
		//define('DY_HTTPS_DOMAIN', 'http://dev.dy.com');
	}
}else{

	//접속 가능한 도메인이 아니면 처음 도메인으로 이동
	if(!in_array($_SERVER["HTTP_HOST"], $_GL_Able_Host)){
		header("Location: http://" . $_GL_Able_Host[0]);
		exit;
	}

	if($_SERVER["HTTP_HOST"] == "dyflux.co.kr") {
		define('DY_LOCALHOST', false);
		define('DY_DOMAIN', 'http://dyflux.co.kr');
		define('DY_HTTPS_DOMAIN', 'https://dyflux.co.kr');
	}elseif($_SERVER["HTTP_HOST"] == "www.dyflux.co.kr") {
		define('DY_LOCALHOST', false);
		define('DY_DOMAIN', 'http://www.dyflux.co.kr');
		define('DY_HTTPS_DOMAIN', 'https://www.dyflux.co.kr');
	}elseif($_SERVER["HTTP_HOST"] == "dev.dyflux.co.kr") {
		define('DY_LOCALHOST', false);
		define('DY_DOMAIN', 'http://dev.dyflux.co.kr');
		define('DY_HTTPS_DOMAIN', 'https://dev.dyflux.co.kr');
	}else{
		define('DY_LOCALHOST', false);
		define('DY_DOMAIN', 'http://dyflux.co.kr');
		define('DY_HTTPS_DOMAIN', 'https://dyflux.co.kr');
	}
}

/*
	사이트명
*/
define('DY_SITE_NAME', 'DYFLUX');

/*
쿠키를 공유하려면 .temp.co.kr 과 같이 입력하세요.
이곳에 입력이 없다면 www 붙은 도메인과 그렇지 않은 도메인은 쿠키를 공유하지 않으므로 로그인이 풀릴 수 있습니다.
*/
if($_SERVER["HTTP_HOST"] == "localhost" || $_SERVER["HTTP_HOST"] == "localhost:8080"){
	define('DY_COOKIE_DOMAIN',  "localhost");
	//define('DY_COOKIE_DOMAIN',  $_SERVER["HTTP_HOST"]);
}elseif($_SERVER["HTTP_HOST"] == "dyflux.co.kr" || $_SERVER["HTTP_HOST"] == "www.dyflux.co.kr" || $_SERVER["HTTP_HOST"] == "dev.dyflux.co.kr") {
	define('DY_COOKIE_DOMAIN',  '.dyflux.co.kr');
}else{
	//define('DY_COOKIE_DOMAIN',  ".".$_SERVER["HTTP_HOST"]);
	define('DY_COOKIE_DOMAIN',  '.dyflux.co.kr');
}

/**
 * DEV 사이트 일 경우
 */
if($_SERVER["HTTP_HOST"] == "dev.dyflux.co.kr" || $_SERVER["REMOTE_ADDR"] == "127.0.0.1" || $_SERVER["REMOTE_ADDR"] == "::1"){
	define('IS_DEV_SITE', true);
}else{
	define('IS_DEV_SITE', false);
}


/*
	메일 발송 정보
*/
define('DY_ADMIN_MAIL_SENDER_NAME', 'DYFLUX');
define('DY_ADMIN_MAIL_SENDER_EMAIL', 'master@duckyun.com');
define('DY_ADMIN_MAIL_SENDER_SMTP_HOST', 'smtp.cafe24.com');
define('DY_ADMIN_MAIL_SENDER_SMTP_ID', 'master@duckyun.com');
define('DY_ADMIN_MAIL_SENDER_SMTP_PW', 'gkswjdal816');
define('DY_ADMIN_MAIL_SENDER_SMTP_PORT', '587');
define('DY_DAUM_MAP_API', '');


/*
	뿌리오 발신 프로필
*/
define('DY_PPURIO_AL_KEY', '9776bce822d0e418290fd1a36dbc3544722a3671');

/*
	이메일 SelectBox 
*/
$GL_emailCollection = array (
	array ("email_en" => '', "email_ko" => '직접입력')
	, array ("email_en" => 'hanmail.net', "email_ko" => '다음(한메일)')
	, array ("email_en" => 'naver.com', "email_ko" => '네이버')
	, array ("email_en" => 'nate.com', "email_ko" => '네이트')
	, array ("email_en" => 'gamil.com', "email_ko" => 'Gmail')
	, array ("email_en" => 'hotmail.com', "email_ko" => '핫메일')
);
/*
	전화번호 SelectBox
*/
$GL_telCollection = array(
	"02"
, "031"
, "032"
, "033"
, "041"
, "042"
, "043"
, "051"
, "052"
, "053"
, "054"
, "055"
, "061"
, "062"
, "063"
, "064"
, "070"
, "0505"
, "010"
, "011"
, "016"
, "017"
, "018"
, "019"
);
/*
	휴대전화 SelectBox
*/
$GL_mobileCollection = array("010", "011", "016", "017", "018", "019");


/*
	Database 설정
*/
////////////////////////////////////////////////////////////////
//# connection config
////////////////////////////////////////////////////////////////


//실서비스는 => DYFLUX_dy
//개발 서비스는 => DYFLUX
if($_SERVER["HTTP_HOST"] == "dyflux.co.kr" || $_SERVER["HTTP_HOST"] == "www.dyflux.co.kr" || defined("DY_FOR_CRON_JOB")) {
	$DB['host']    = '115.90.244.154,14353';
	$DB['dbuser']  = 'sa';
	$DB['dbpass']  = 'd!@#y!@#';
	$DB['dbname']  = 'DYFLUX_dy';
	$DB['logpath'] = DY_LOG_PATH;
	$DB['dbdesc']  = '';

	$DB['cj_log_dbname']  = 'DYFLUX_dy';    // CJ 연동 DB
	// 마켓에 주문 취소 및 송장 입력을 위한 체크 (실제 오픈전엔 이지어드민과 충돌남)
	define('DY_MARKET_IS_LIVE', true);


	//실서비스 CJ 정보
	/**
	 *  접수 : V_RCPT_TOMSKEVIN010
	 * 	추적 : V_TRACE_TOMSKEVIN020
	 *	고객사코드 : 30290160
	 * 	운송장 번호대역 : 3466-8502-0016  ~ 3466-8552-0004  (총 5만 건)
	 */

	$GL_P_CLNTNUM = "30315073"; //CJ대한통운 고객ID (30315073 : 나인플러스)
	$GL_P_CLNTMGMCUSTCD = "30315073";  //CJ대한통운 고객관리거래처코드 (30315073 : 나인플러스)

	$GL_CJ_DB_TABLE_NAME = "V_RCPT_TOMSKEVIN010";
	$GL_CJ_DB_VIEW_NAME = "V_TRACE_TOMSKEVIN020";

	// 주소 정제 서버
	$GL_CJ_DB_READDR_ID = "tomskevin";
	$GL_CJ_DB_READDR_PWD = "tomskevin$#!1";
	$GL_CJ_DB_READDR_CONSTR = "
(DESCRIPTION =
	(FAILOVER = ON)
	(LOAD_BALANCE = OFF)
	(ADDRESS = (PROTOCOL=TCP)(HOST = 61.33.235.97)(PORT = 1521))
	(ADDRESS = (PROTOCOL=TCP)(HOST = 61.33.235.98)(PORT = 1521))
	(CONNECT_DATA = (SERVICE_NAME = CGIS))
)
		";

	// CJ OPEN DB
	$GL_CJ_DB_OPENDB_ID = "tomskevin";
	$GL_CJ_DB_OPENDB_PWD = "tomskevin!#$1";
	$GL_CJ_DB_OPENDB_CONSTR= "
(DESCRIPTION =
	(ADDRESS = (PROTOCOL = TCP)(HOST = 210.98.159.153)(PORT = 1521))
	(CONNECT_DATA = (SERVER = DEDICATED)(SID = OPENDB))
) 
		";

}else{
	$DB['host']    = '115.90.244.154,14353';
	$DB['dbuser']  = 'sa';
	$DB['dbpass']  = 'd!@#y!@#';
	$DB['dbname']  = 'DYFLUX';
	$DB['logpath'] = DY_LOG_PATH;
	$DB['dbdesc']  = '';

	$DB['cj_log_dbname']  = 'DYFLUX';    // CJ 연동 DB
	// 마켓에 주문 취소 및 송장 입력을 위한 체크 (실제 오픈전엔 이지어드민과 충돌남)
	define('DY_MARKET_IS_LIVE', false);

	//개발서비스 CJ 정보
	/**
	 *  접수 : V_RCPT_TOMSKEVIN010
	 * 	추적 : V_TRACE_TOMSKEVIN020
	 *	고객사코드 : 30290160
	 * 	운송장 번호대역 : 3466-8502-0016  ~ 3466-8552-0004  (총 5만 건)
	 */

	$GL_P_CLNTNUM = "30315073"; //CJ대한통운 고객ID (30315073 : 나인플러스)
	$GL_P_CLNTMGMCUSTCD = "30315073";  //CJ대한통운 고객관리거래처코드 (30315073 : 나인플러스)

	$GL_CJ_DB_TABLE_NAME = "V_RCPT_TOMSKEVIN010";
	$GL_CJ_DB_VIEW_NAME = "V_TRACE_TOMSKEVIN020";

	// 주소 정제 서버
	$GL_CJ_DB_READDR_ID = "tomskevin";
	$GL_CJ_DB_READDR_PWD = "tomskevindev$#!1";
	$GL_CJ_DB_READDR_CONSTR = "
			(DESCRIPTION =
				(ADDRESS = (PROTOCOL = TCP)(HOST = 203.248.116.111)(PORT = 1521))
				(CONNECT_DATA = (SERVER = DEDICATED)(SERVICE_NAME = CGISDEV)))
		";

	// CJ OPEN DB
	$GL_CJ_DB_OPENDB_ID = "tomskevin";
	$GL_CJ_DB_OPENDB_PWD = "tomskevindev!#$1";
	$GL_CJ_DB_OPENDB_CONSTR= "
			(DESCRIPTION =
				(ADDRESS = (PROTOCOL = TCP)(HOST = 210.98.159.153)(PORT = 1523))
				(CONNECT_DATA = (SERVER = DEDICATED)(SID = OPENDBT)))   
		";
}


/**
 * 재고작업 시 대상으로 선택 가능한 상태 리스트
 * (공통코드 - STOCK_STATUS 참조)
 */
$GL_controlStockFromAbleStatusList = array(
	"NORMAL" => "정상",
	"ABNORMAL" => "양품",
	"HOLD" => "보류",
	"BAD" => "불량재고",
	"LOSS" => '분실재고',
	"DISPOSAL" => '일반폐기'
);

/**
 * 재고작업 시 선택된 대상에 대하여 변경이 가능한 상태 리스트
 * (공통코드 - STOCK_STATUS 참조)
 */
$GL_controlStockToAbleStatusList = array(
	"NORMAL" => "정상",
	"ABNORMAL" => "양품",
	"HOLD" => "보류",
	"FAC_RETURN_EXCHNAGE" => "출고지회송 - 교환회송",
	"FAC_RETURN_BACK" => "출고지회송 - 반품회송",
	"BAD" => "불량재고",
	"BAD_OUT_EXCHANGE" => "불량 - 교환출고",
	"BAD_OUT_RETURN" => "불량 - 반품출고",
	"LOSS" => '분실재고',
	"DISPOSAL" => '일반폐기',
	"DISPOSAL_PERMANENT" => '영구폐기'
);

/**
 * 재고 상태 값 (검색 selectbox 용)
 * (공통코드 - STOCK_STATUS 참조)
 */
$GL_StockStatusList = array(
	"NORMAL" => "정상재고",
	"SHIPPED" => "정상배송",
	"ABNORMAL" => "양품",
	"HOLD" => "보류",
	"FAC_RETURN_EXCHNAGE" => "출고지회송 - 교환회송",
	"FAC_RETURN_BACK" => "출고지회송 - 반품회송",
	"BAD" => "불량재고",
	"BAD_OUT_EXCHANGE" => "불량 - 교환출고",
	"BAD_OUT_RETURN" => "불량 - 반품출고",
	"LOSS" => '분실재고',
	"DISPOSAL" => '일반폐기',
	"DISPOSAL_PERMANENT" => '영구폐기'
);

$GL_SmsSendPhone = array(
    "0318115500" => "대표번호",
    "07047548135" => "발주주문",
    "07047548051" => "전산1",
    "07047552291" => "전산2",
    "07047620456" => "회계1",
    "07047552293" => "회계2",
    "07047620455" => "CS담당",
);


$GL_SELLER_INVOICE_FORMAT = array(

	"header_print" => "Y",
	"margin_top" => 0,
	"A" => "번호|num",
	"B" => "주문일시|order_pay_date",
	"C" => "결제일시|order_pay_date",
	"D" => "주문번호|order_pay_date",
	"E" => "상품명|market_product_name",
	"F" => "옵션|market_product_option",
	"G" => "수량|order_cnt",
	"H" => "구매자|order_name",
	"I" => "구매자ID|market_order_id",
	"J" => "수취인|receive_name",
	"K" => "택배사코드|delivery_code",
	"L" => "송장번호|invoice_no",
	"M" => "배송비구분|deivery_is_free",
	"N" => "배송비|delivery_fee",
	"O" => "휴대폰번호|receive_hp_num",
	"P" => "전화번호|receive_tp_num",
	"Q" => "우편번호|receive_zipcode",
	"R" => "주소|receive_addr1",
	"S" => "배송메모|receive_memo",

);

$GL_Stock_InWhereHouse = "N'NORMAL', N'ABNORMAL', N'BAD', N'HOLD', N'DISPOSAL'";
$GL_Stock_OutWhereHouse = "N'BAD_OUT_EXCHANGE', N'BAD_OUT_RETURN', N'DISPOSAL_PERMANENT'";


$GL_Mobile_Menu_Ary = array(

	array(
		"no" => 1,
		"name" => "홈",
		"url" => "/m/main.php",
	),
	array(
		"no" => 2,
		"name" => "판매처별통계",
		"url" => "/m/settle/seller_sale.php",
	),
	array(
		"no" => 3,
		"name" => "월별상품별통계",
		"url" => "/m/settle/product_monthly.php",
	),
	array(
		"no" => 4,
		"name" => "거래현황",
		"url" => "/m/settle/transaction_state.php",
	),
	array(
		"no" => 5,
		"name" => "자금일보",
		"url" => "/m/settle/report.php",
	),
	array(
		"no" => 6,
		"name" => "충전금관리",
		"url" => "/m/settle/vendor_charge.php",
	),
	array(
		"no" => 7,
		"name" => "광고비관리",
		"url" => "/m/settle/ad_cost.php",
	),
	array(
		"no" => 8,
		"name" => "일별매출차트",
		"url" => "/m/settle/chart_daily.php",
	),
	array(
		"no" => 9,
		"name" => "매출캘린더",
		"url" => "/m/settle/chart_calendar.php",
	),
	array(
		"no" => 10,
		"name" => "기간별매출이익",
		"url" => "/m/settle/sale_profit_period.php",
	),
	array(
		"no" => 11,
		"name" => "상품별매출이익",
		"url" => "/m/settle/sale_profit_product.php",
	),
//	array(
//		"no" => 3,
//		"name" => "정산통계",
//		"url" => "",
//		"submenu" => array(
//			array(
//				"no" => 1,
//				"name" => "매출관리",
//				"url" => "/settle/sale_manage.php",
//			),
//			array(
//				"no" => 2,
//				"name" => "매출관리2",
//				"url" => "/settle/sale_manage.php",
//			),
//		),
//	)

//TEST;;
);
?>