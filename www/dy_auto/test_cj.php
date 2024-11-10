<?php
//phpinfo();
include_once "../_init_.php";

// 주소 정제 서버
$CJ_DB_READDR_ID = "tomskevin";
$CJ_DB_READDR_PWD = "tomskevin$#!1";
$CJ_DB_READDR_CONSTR = "
(DESCRIPTION =
	(FAILOVER = ON)
	(LOAD_BALANCE = OFF)
	(ADDRESS = (PROTOCOL=TCP)(HOST = 61.33.235.97)(PORT = 1521))
	(ADDRESS = (PROTOCOL=TCP)(HOST = 61.33.235.98)(PORT = 1521))
	(CONNECT_DATA = (SERVICE_NAME = CGIS))
)
		";
$conn = @oci_connect($CJ_DB_READDR_ID, $CJ_DB_READDR_PWD, $CJ_DB_READDR_CONSTR, 'AL32UTF8');
if($conn) {
	echo "CJ reAddr DB : Connect Success!<br />";

	$API_CJ_Invoice = new API_CJ_Invoice();

	$_ret = $API_CJ_Invoice->repCJAddress(array('order_pack_idx' => '0', 'receive_addr' => "인천 남동구 구월동 819-3번지 푸름꽃화원",));
	print_r2($_ret);

	if($_ret["p_newaddress"] == "") {
		echo "구주소로";
	} else {
		echo "신주소로";
	}

	oci_close($conn);

} else {
	echo "CJ reAddr DB : Connect Fail!<br />";
	echo $e['message']."<br />";
	print_r($e);
}



/**
 * CJ API 를 통해 주소 정제
 * @param array ($order_pack_idx, $receive_addr)
 * @return array
 */
function repCJAddress($args) {
	//region *** CJ 주소정제 Return Array ***
	$arrRet = array(
		'order_pack_idx' => '',
		'p_clntnum' => '',
		'p_clntmgmcustcd' => '',
		'p_prngdivcd' => '',
		'p_farediv' => '',
		'p_boxtyp' => '',
		'p_cgosts' => '',
		'p_address' => '',
		'p_zipnum' => '',
		'p_zipid' => '',
		'p_oldaddress' => '',
		'p_oldaddressdtl' => '',
		'p_newaddress' => '',
		'p_nesaddressdtl' => '',
		'p_etcaddr' => '',
		'p_shortaddr' => '',
		'p_clsfaddr' => '',
		'p_clldlvbrancd' => '',
		'p_clldlvbrannm' => '',
		'p_clldlcbranshortnm' => '',
		'p_clldlvempnum' => '',
		'p_clldlvempnm' => '',
		'p_clldlvempnicknm' => '',
		'p_clsfcd' => '',
		'p_clsfnm' => '',
		'p_subclsfcd' => '',
		'p_rspsdiv' => '',
		'p_newaddryn' => '',
		'p_errorcd' => '',
		'p_errormsg' => '',
	);
	//endregion

	$order_pack_idx = "";
	$receive_addr   = "";
	extract($args);

	$arrRet["order_pack_idx"] = $order_pack_idx;

	// CJ 쪽에 Insert 해야 할 데이터
	$arrRet["p_prngdivcd"] = "01";
	$arrRet["p_farediv"] = "01";
	$arrRet["p_boxtyp"] = "1";

	$arrRet["receive_addr"] = $receive_addr;
	$arrRet["p_errorcd"] = "99999"; // 에러로만들어 놓고

	// 이부분에 CJ API DB 와 연결하여 주소정제 패키지를 가져와야 함!!!!!!!!!!!!
	$P_PRNGDIVCD         = "01";  //예약구분코드 : 일반 (01) / 반품 (02)
	$P_CGOSTS            = "91"; //(상품상태: 11(집화) / 91(배달))
	$P_ADDRESS           = $receive_addr;

	$conn = @oci_connect($this -> CJ_DB_READDR_ID, $this -> CJ_DB_READDR_PWD, $this -> CJ_DB_READDR_CONSTR, 'AL32UTF8');
	if($conn) {

		/// CJ 대한통운 주소정제
		$query = "
				BEGIN
					PKG_RVAP_ADDRSEARCH.PR_RVAP_SEARCHADDRESS
					( :P_CLNTNUM, :P_CLNTMGMCUSTCD, :P_PRNGDIVCD, :P_CGOSTS, :P_ADDRESS
					, :P_ZIPNUM, :P_ZIPID, :P_OLDADDRESS, :P_OLDADDRESSDTL, :P_NEWADDRESS
					, :P_NESADDRESSDTL, :P_ETCADDR, :P_SHORTADDR, :P_CLSFADDR, :P_CLLDLVBRANCD
					, :P_CLLDLVBRANNM, :P_CLLDLCBRANSHORTNM, :P_CLLDLVEMPNUM, :P_CLLDLVEMPNM
					, :P_CLLDLVEMPNICKNM, :P_CLSFCD, :P_CLSFNM, :P_SUBCLSFCD, :P_RSPSDIV
					, :P_NEWADDRYN, :P_ERRORCD, :P_ERRORMSG
					);
				END;
			";

		$stmt = ociparse($conn,$query);

		//region *** 주소정제 프로시저 변수 셋팅 ***
		// 입력값
		OciBindByName($stmt, ':P_CLNTNUM', $this->P_CLNTNUM, 400);
		OciBindByName($stmt, ':P_CLNTMGMCUSTCD', $this->P_CLNTMGMCUSTCD, 400);
		OciBindByName($stmt, ':P_PRNGDIVCD', $P_PRNGDIVCD, 400);
		OciBindByName($stmt, ':P_CGOSTS', $P_CGOSTS, 400);
		OciBindByName($stmt, ':P_ADDRESS', $P_ADDRESS, 400);
		//출력값
		OciBindByName($stmt, ':P_ZIPNUM', $arrRet["p_zipnum"], 400);
		OciBindByName($stmt, ':P_ZIPID', $arrRet["p_zipid"], 400);
		OciBindByName($stmt, ':P_OLDADDRESS', $arrRet["p_oldaddress"], 400);
		OciBindByName($stmt, ':P_OLDADDRESSDTL', $arrRet["p_oldaddressdtl"], 400);
		OciBindByName($stmt, ':P_NEWADDRESS', $arrRet["p_newaddress"], 400);
		OciBindByName($stmt, ':P_NESADDRESSDTL', $arrRet["p_nesaddressdtl"], 400);
		OciBindByName($stmt, ':P_ETCADDR', $arrRet["p_etcaddr"], 400);
		OciBindByName($stmt, ':P_SHORTADDR', $arrRet["p_shortaddr"], 400);
		OciBindByName($stmt, ':P_CLSFADDR', $arrRet["p_clsfaddr"], 400);
		OciBindByName($stmt, ':P_CLLDLVBRANCD', $arrRet["p_clldlvbrancd"], 400);
		OciBindByName($stmt, ':P_CLLDLVBRANNM', $arrRet["p_clldlvbrannm"], 400);
		OciBindByName($stmt, ':P_CLLDLCBRANSHORTNM', $arrRet["p_clldlcbranshortnm"], 400);
		OciBindByName($stmt, ':P_CLLDLVEMPNUM', $arrRet["p_clldlvempnum"], 400);
		OciBindByName($stmt, ':P_CLLDLVEMPNM', $arrRet["p_clldlvempnm"], 400);
		OciBindByName($stmt, ':P_CLLDLVEMPNICKNM', $arrRet["p_clldlvempnicknm"], 400);
		OciBindByName($stmt, ':P_CLSFCD', $arrRet["p_clsfcd"], 400);
		OciBindByName($stmt, ':P_CLSFNM', $arrRet["p_clsfnm"], 400);
		OciBindByName($stmt, ':P_SUBCLSFCD', $arrRet["p_subclsfcd"], 400);
		OciBindByName($stmt, ':P_RSPSDIV', $arrRet["p_rspsdiv"], 400);
		OciBindByName($stmt, ':P_NEWADDRYN', $arrRet["p_newaddryn"], 400);
		OciBindByName($stmt, ':P_ERRORCD', $arrRet["p_errorcd"], 400);
		OciBindByName($stmt, ':P_ERRORMSG', $arrRet["p_errormsg"], 400);
		//endregion

		OCIExecute($stmt);

		oci_close($conn);

		if($arrRet["p_newaddryn"] == "Y") {
			$arrRet["receive_addr"] = $arrRet["receive_addr"] . " [" . $arrRet["p_etcaddr"] . "]";
		}

	}

	return $arrRet;
}

exit;


$C_Order = new Order();
$C_API_CJ_Invoice = new API_CJ_Invoice();

$P_CLNTNUM = "30290160"; //CJ대한통운 고객ID
$P_CLNTMGMCUSTCD = "30290160";  //CJ대한통운 고객관리거래처코드

$CJ_DB_TABLE_NAME = "V_RCPT_TOMSKEVIN010";
$CJ_DB_VIEW_NAME = "V_TRACE_TOMSKEVIN020";


$print_log_idx = "1236";
$invoice_no = "346685023785";

$send_name = "덕윤";
$send_add = "경기 파주시 조리읍 정문로 28 (뇌조리) CJ대한통운 덕풍라인대리점 ((주)덕윤)";
$send_add1 = "";
$send_add2 = "";
$send_zipcode = "";
$CJ_AddInfo = $C_API_CJ_Invoice->repCJAddress(array('order_pack_idx' => '0', 'receive_addr' => $send_add,));
//print_r2($CJ_AddInfo);
if($CJ_AddInfo["p_errorcd"] == "0") {
	$send_add1 = $CJ_AddInfo["p_newaddress"];
	$send_add2 = $CJ_AddInfo["p_nesaddressdtl"];
	$send_zipcode = $CJ_AddInfo["p_zipnum"];
} else {
	echo "주소정제 에러<br />";
}

$send_phone1 = "031-811-0000";
$send_arry_phone = explode("-", $send_phone1);
if(count($send_arry_phone) != 3) {
	echo "보내는 사람 전화 번호 에러<br />";
}

$receive_name = "홍길동";
$receive_hp_num = "010-9999-8888";
$receive_arry_hp_num = explode("-", $receive_hp_num);
if(count($receive_arry_hp_num) != 3) {
	echo "받는 사람 전화 번호 에러<br />";
}
$receive_zipcode = "550-784";
$receive_addr = "대구광역시 북구 성북로9길 12 (침산동,침산동 2차 쌍용예가) 102동 402호 [침산동 , 침산쌍용예가2차아파트]";
$receive_addr1 = "";
$receive_addr2 = "";
$CJ_AddInfo = $C_API_CJ_Invoice->repCJAddress(array('order_pack_idx' => '0', 'receive_addr' => $receive_addr,));
if($CJ_AddInfo["p_errorcd"] == "0") {
	$receive_addr1 = $CJ_AddInfo["p_newaddress"];
	$receive_addr2 = $CJ_AddInfo["p_nesaddressdtl"];
	$receive_zipcode = $CJ_AddInfo["p_zipnum"];
} else {
	echo "주소정제 에러<br />";
}

$product_option_names = "키친아트 소렐 스텐 16편수 냄비[본품] - 1개 키친아트 소렐 스텐 16 국수냄비[본품] - 0개";




// CJ OPEN DB
$CJ_DB_OPENDB_ID = "tomskevin";
$CJ_DB_OPENDB_PWD = "tomskevin!#$1";
$CJ_DB_OPENDB_CONSTR= "
(DESCRIPTION =
	(ADDRESS = (PROTOCOL = TCP)(HOST = 210.98.159.153)(PORT = 1521))
	(CONNECT_DATA = (SERVER = DEDICATED)(SID = OPENDB))
)    
";
//계정 : tomskevin / tomskevindev$#!1
$conn = @oci_connect($CJ_DB_OPENDB_ID, $CJ_DB_OPENDB_PWD, $CJ_DB_OPENDB_CONSTR, 'AL32UTF8');
if($conn) {
	echo "CJ Open DB : Connect Success!<br />";


	//echo real_escape_string("간`'!@3$#%$^&^다라<br />");



	$ret_invoice_info["CUST_ID"]           = $P_CLNTNUM;    // 고객ID : 주관고객코드 (ex : 3012XXXX)
	$ret_invoice_info["RCPT_YMD"]          = date("Ymd");    // 접수일자 : YYYYMMDD 형식 必 (ex : 20160528)
	$ret_invoice_info["CUST_USE_NO"]       = $print_log_idx;    // 고객사용번호 : 기업고객이 관리하는 주문번호/ 영수번호 등 내부 관리번호(운송장번호와 매핑되는 유일값 기입) (ex : 2016051087)
	$ret_invoice_info["RCPT_DV"]           = "01";    // 접수구분 : 01 : 일반,  02 : 반품 (ex : 01)
	$ret_invoice_info["WORK_DV_CD"]        = "01";    // 작업구분코드 : 01 : 일반,  02 : 교환, 03 : A/S  (ex : 01)
	$ret_invoice_info["REQ_DV_CD"]         = "01";    // 요청구분코드 : 01 : 요청,  02 : 취소 (ex : 01)
	$ret_invoice_info["MPCK_KEY"]          = date("Ymd")."_".$P_CLNTNUM."_".$print_log_idx;    // 합포장키 : 다수데이터를 한 송장에 출력할 경우 처리(RCPT_YMD || '_' || CUST_ID || '_' || CUST_USE_NO or
	$ret_invoice_info["MPCK_SEQ"]          = "1";    // 합포장순번 : 합포장 처리건수가 다수일경우 SEQ처리를 수행한다.( 합포없는경우 무조건 1 ) (ex : 1)
	$ret_invoice_info["CAL_DV_CD"]         = "01";    // 정산구분코드 : 01: 계약 운임,  02: 자료 운임 (계약운임인지 업체에서 넣어주는 운임으로할지) (ex : 01)
	$ret_invoice_info["FRT_DV_CD"]         = "01";    // 운임구분코드 : 01: 선불,  02: 착불 ,  03: 신용 (ex : 03)
	$ret_invoice_info["CNTR_ITEM_CD"]      = "01";    // 계약품목코드 : 01: 일반 품목 (ex : 01)
	$ret_invoice_info["BOX_TYPE_CD"]       = "01";    // 박스타입코드 : 01: 극소,  02: 소,  03: 중,  04: 대,  05: 특대 (ex : 02)
	$ret_invoice_info["BOX_QTY"]           = "1";    // 박스수량 : 택배 박스 수량 (1로 기입) (ex : 1)
	$ret_invoice_info["FRT"]               = "0";    // 운임 : 운임적용구분이 자료 운임일 경우 등록 처리 (ex : 0)
	$ret_invoice_info["CUST_MGMT_DLCM_CD"] = $P_CLNTMGMCUSTCD;    // 고객관리거래처코드 : 주관사 관리 협력업체 코드 혹은 택배사 관리 업체코드 (ex : 3012XXXX)
	$ret_invoice_info["SENDR_NM"]          = $send_name;    // 송화인명 : 보내는분 성명 (ex : XXX기업㈜)
	$ret_invoice_info["SENDR_TEL_NO1"]     = $send_arry_phone[0];    // 송화인전화번호1 : 송화인전화번호1 항목은 '0'으로 시작할 것. (국번은 0으로 시작) (ex : 02)
	$ret_invoice_info["SENDR_TEL_NO2"]     = $send_arry_phone[1];    // 송화인전화번호2 :  (ex : 1588)
	$ret_invoice_info["SENDR_TEL_NO3"]     = $send_arry_phone[2];    // 송화인전화번호3 : 암호화 구간(암호화는 대한통운 내부로직이므로, 고객사 접수 시에는 무시하셔도 좋습니다.) (ex : 1255)
	$ret_invoice_info["SENDR_CELL_NO1"]    = "";    // 송화인휴대폰번호1 :  (ex : )
	$ret_invoice_info["SENDR_CELL_NO2"]    = "";    // 송화인휴대폰번호2 :  (ex : )
	$ret_invoice_info["SENDR_CELL_NO3"]    = "";    // 송화인휴대폰번호3 : 암호화 구간 (ex : )
	$ret_invoice_info["SENDR_ZIP_NO"]      = $send_zipcode;    // 송화인우편번호 :  (ex : 100100)
	$ret_invoice_info["SENDR_ADDR"]        = str_replace("'", " ", $send_add1);    // 송화인주소 : 송화인 주소 ( ~동 or ~로 까지의 앞 단 주소) (ex : 서울시 중구 서소문동)
	$ret_invoice_info["SENDR_DETAIL_ADDR"] = str_replace("'", " ", $send_add2);    // 송화인상세주소 : 송화인 상세 주소(암호화 구간, 나머지 상세 주소) (ex : 66번지)
	$ret_invoice_info["RCVR_NM"]           = $receive_name;    // 수화인명 :  (ex : 홍길동)
	$ret_invoice_info["RCVR_TEL_NO1"]      = $receive_arry_hp_num[0];    // 수화인전화번호1 : 수화인전화번호1 항목은 '0'으로 시작할 것. (국번은 0으로 시작) (ex : 031)
	$ret_invoice_info["RCVR_TEL_NO2"]      = $receive_arry_hp_num[1];    // 수화인전화번호2 :  (ex : 111)
	$ret_invoice_info["RCVR_TEL_NO3"]      = $receive_arry_hp_num[2];    // 수화인전화번호3 : 암호화 구간 (ex : 2222)
	$ret_invoice_info["RCVR_CELL_NO1"]     = "";    // 수화인휴대폰번호1 :  (ex : )
	$ret_invoice_info["RCVR_CELL_NO2"]     = "";    // 수화인휴대폰번호2 :  (ex : )
	$ret_invoice_info["RCVR_CELL_NO3"]     = "";    // 수화인휴대폰번호3 : 암호화 구간 (ex : )
	$ret_invoice_info["RCVR_ZIP_NO"]       = str_replace("-","",$receive_zipcode);    // 수화인우편번호 :  (ex : 200200)
	$ret_invoice_info["RCVR_ADDR"]         = str_replace("'", " ", $receive_addr1);    // 수화인주소 : 수화인 주소 (ex : 충남 천안시 서북구 두정동)
	$ret_invoice_info["RCVR_DETAIL_ADDR"]  = str_replace("'", " ", $receive_addr2);    // 수화인상세주소 : 수화인 상세 주소(암호화 구간) (ex : 77번지)
	$ret_invoice_info["INVC_NO"]           = $invoice_no;    // 운송장번호 : 12자리, 운송장번호 채번 로직 : 3~11 범위의 수를 MOD(7) 한 결과가 12번째 수와 같아야 한다.
	$ret_invoice_info["PRT_ST"]            = "02";    // 출력상태 : 01: 미출력,  02: 선출력,  03: 선발번 (반품은 선발번이 없음)
	$ret_invoice_info["GDS_NM"]            = str_replace("'", " ", substr($product_option_names, 0, 200));    // 상품명 :  (ex : 사과쥬스1박스)
	$ret_invoice_info["DLV_DV"]            = "01";    // 택배구분 : 택배 : '01', 중량물(설치물류) : '02', 중량물(비설치물류) : '03' / 택배의 경우 '01' 만 허용됨. (ex : 01)
	$ret_invoice_info["EAI_PRGS_ST"]       = "01";    // EAI전송상태 : DEFAULT : '01' (ex : 01)
	$ret_invoice_info["REG_EMP_ID"]        = "DY";    // 등록사원ID : 고객 사용계정 대문자로 기입 (ex : CAFE24) (ex : CAFE24)
	//$ret_invoice_info["REG_DTIME"]         = "";    // 등록일시 : SYSDATE (ex : SYSDATE)
	$ret_invoice_info["MODI_EMP_ID"]       = "DY";    // 수정사원ID : 고객 사용계정 대문자로 기입 (ex : CAFE24) (ex : CAFE24)
	//$ret_invoice_info["MODI_DTIME"]        = "";    // 수정일시 : SYSDATE (ex : SYSDATE)



	//region *** CJ Interface DB Insert 파라미터 ***
	$CUST_ID           = $ret_invoice_info["CUST_ID"];    // 고객ID : 주관고객코드 (ex : 3012XXXX)
	$RCPT_YMD          = $ret_invoice_info["RCPT_YMD"];    // 접수일자 : YYYYMMDD 형식 必 (ex : 20160528)
	$CUST_USE_NO       = $ret_invoice_info["CUST_USE_NO"];    // 고객사용번호 : 기업고객이 관리하는 주문번호/ 영수번호 등 내부 관리번호(운송장번호와 매핑되는 유일값 기입) (ex : 2016051087)
	$RCPT_DV           = $ret_invoice_info["RCPT_DV"];    // 접수구분 : 01 : 일반,  02 : 반품 (ex : 01)
	$WORK_DV_CD        = $ret_invoice_info["WORK_DV_CD"];    // 작업구분코드 : 01 : 일반,  02 : 교환, 03 : A/S  (ex : 01)
	$REQ_DV_CD         = $ret_invoice_info["REQ_DV_CD"];    // 요청구분코드 : 01 : 요청,  02 : 취소 (ex : 01)
	$MPCK_KEY          = $ret_invoice_info["MPCK_KEY"];    // 합포장키 : 다수데이터를 한 송장에 출력할 경우 처리 합포 있는 경우 해당 건들의 합포장키를 동일값으로 접수 (ex : 20160528_3012XXXX_2013051087)
	$MPCK_SEQ          = $ret_invoice_info["MPCK_SEQ"];    // 합포장순번 : 합포장 처리건수가 다수일경우 SEQ처리를 수행한다.( 합포없는경우 무조건 1 ) (ex : 1)
	$CAL_DV_CD         = $ret_invoice_info["CAL_DV_CD"];    // 정산구분코드 : 01: 계약 운임,  02: 자료 운임 (계약운임인지 업체에서 넣어주는 운임으로할지) (ex : 01)
	$FRT_DV_CD         = $ret_invoice_info["FRT_DV_CD"];    // 운임구분코드 : 01: 선불,  02: 착불 ,  03: 신용 (ex : 03)
	$CNTR_ITEM_CD      = $ret_invoice_info["CNTR_ITEM_CD"];    // 계약품목코드 : 01: 일반 품목 (ex : 01)
	$BOX_TYPE_CD       = $ret_invoice_info["BOX_TYPE_CD"];    // 박스타입코드 : 01: 극소,  02: 소,  03: 중,  04: 대,  05: 특대 (ex : 02)
	$BOX_QTY           = $ret_invoice_info["BOX_QTY"];    // 박스수량 : 택배 박스 수량 (1로 기입) (ex : 1)
	$FRT               = $ret_invoice_info["FRT"];    // 운임 : 운임적용구분이 자료 운임일 경우 등록 처리 (ex : 0)
	$CUST_MGMT_DLCM_CD = $ret_invoice_info["CUST_MGMT_DLCM_CD"];    // 고객관리거래처코드 : 주관사 관리 협력업체 코드 혹은 택배사 관리 업체코드 (ex : 3012XXXX)
	$SENDR_NM          = $ret_invoice_info["SENDR_NM"];    // 송화인명 : 보내는분 성명 (ex : XXX기업㈜)
	$SENDR_TEL_NO1     = $ret_invoice_info["SENDR_TEL_NO1"];    // 송화인전화번호1 : 송화인전화번호1 항목은 '0'으로 시작할 것. (국번은 0으로 시작) (ex : 02)
	$SENDR_TEL_NO2     = $ret_invoice_info["SENDR_TEL_NO2"];    // 송화인전화번호2 :  (ex : 1588)
	$SENDR_TEL_NO3     = $ret_invoice_info["SENDR_TEL_NO3"];    // 송화인전화번호3 : 암호화 구간(암호화는 대한통운 내부로직이므로, 고객사 접수 시에는 무시하셔도 좋습니다.) (ex : 1255)
	$SENDR_CELL_NO1    = $ret_invoice_info["SENDR_CELL_NO1"];    // 송화인휴대폰번호1 :  (ex : )
	$SENDR_CELL_NO2    = $ret_invoice_info["SENDR_CELL_NO2"];    // 송화인휴대폰번호2 :  (ex : )
	$SENDR_CELL_NO3    = $ret_invoice_info["SENDR_CELL_NO3"];    // 송화인휴대폰번호3 : 암호화 구간 (ex : )
	$SENDR_ZIP_NO      = $ret_invoice_info["SENDR_ZIP_NO"];    // 송화인우편번호 :  (ex : 100100)
	$SENDR_ADDR        = $ret_invoice_info["SENDR_ADDR"];    // 송화인주소 : 송화인 주소 ( ~동 or ~로 까지의 앞 단 주소) (ex : 서울시 중구 서소문동)
	$SENDR_DETAIL_ADDR = $ret_invoice_info["SENDR_DETAIL_ADDR"];    // 송화인상세주소 : 송화인 상세 주소(암호화 구간, 나머지 상세 주소) (ex : 66번지)
	$RCVR_NM           = $ret_invoice_info["RCVR_NM"];    // 수화인명 :  (ex : 홍길동)
	$RCVR_TEL_NO1      = $ret_invoice_info["RCVR_TEL_NO1"];    // 수화인전화번호1 : 수화인전화번호1 항목은 '0'으로 시작할 것. (국번은 0으로 시작) (ex : 031)
	$RCVR_TEL_NO2      = $ret_invoice_info["RCVR_TEL_NO2"];    // 수화인전화번호2 :  (ex : 111)
	$RCVR_TEL_NO3      = $ret_invoice_info["RCVR_TEL_NO3"];    // 수화인전화번호3 : 암호화 구간 (ex : 2222)
	$RCVR_CELL_NO1     = $ret_invoice_info["RCVR_CELL_NO1"];    // 수화인휴대폰번호1 :  (ex : )
	$RCVR_CELL_NO2     = $ret_invoice_info["RCVR_CELL_NO2"];    // 수화인휴대폰번호2 :  (ex : )
	$RCVR_CELL_NO3     = $ret_invoice_info["RCVR_CELL_NO3"];    // 수화인휴대폰번호3 : 암호화 구간 (ex : )
	$RCVR_ZIP_NO       = $ret_invoice_info["RCVR_ZIP_NO"];    // 수화인우편번호 :  (ex : 200200)
	$RCVR_ADDR         = $ret_invoice_info["RCVR_ADDR"];    // 수화인주소 : 수화인 주소 (ex : 충남 천안시 서북구 두정동)
	$RCVR_DETAIL_ADDR  = $ret_invoice_info["RCVR_DETAIL_ADDR"];    // 수화인상세주소 : 수화인 상세 주소(암호화 구간) (ex : 77번지)
	$INVC_NO           = $ret_invoice_info["INVC_NO"];    // 운송장번호 : 12자리, (ex : 301100112233)
	$PRT_ST            = $ret_invoice_info["PRT_ST"];    // 출력상태 : 01: 미출력,  02: 선출력,  03: 선발번 (반품은 선발번이 없음) *자체시스템을 이용하여 운송장을 출력하는 고객사의 경우 02, CNPLUS를 이용하여 운송장을 출력하는 경우 01 (ex : 02)
	$GDS_NM            = $ret_invoice_info["GDS_NM"];    // 상품명 :  (ex : 사과쥬스1박스)
	$DLV_DV            = $ret_invoice_info["DLV_DV"];    // 택배구분 : 택배 : '01', 중량물(설치물류) : '02', 중량물(비설치물류) : '03' / 택배의 경우 '01' 만 허용됨. (ex : 01)
	$EAI_PRGS_ST       = $ret_invoice_info["EAI_PRGS_ST"];    // EAI전송상태 : DEFAULT : '01' (ex : 01)
	$REG_EMP_ID        = $ret_invoice_info["REG_EMP_ID"];    // 등록사원ID : 고객 사용계정 대문자로 기입 (ex : CAFE24) (ex : CAFE24)
	$REG_DTIME         = $ret_invoice_info["REG_DTIME"];    // 등록일시 : SYSDATE (ex : SYSDATE)
	$MODI_EMP_ID       = $ret_invoice_info["MODI_EMP_ID"];    // 수정사원ID : 고객 사용계정 대문자로 기입 (ex : CAFE24) (ex : CAFE24)
	$MODI_DTIME        = $ret_invoice_info["MODI_DTIME"];    // 수정일시 : SYSDATE (ex : SYSDATE)
	//endregion


	$query = "
		INSERT INTO $CJ_DB_TABLE_NAME (
			CUST_ID, RCPT_YMD, CUST_USE_NO, RCPT_DV, WORK_DV_CD, REQ_DV_CD, MPCK_KEY, 
			MPCK_SEQ, CAL_DV_CD, FRT_DV_CD, CNTR_ITEM_CD, BOX_TYPE_CD, BOX_QTY, FRT, 
			CUST_MGMT_DLCM_CD, SENDR_NM, SENDR_TEL_NO1, SENDR_TEL_NO2, SENDR_TEL_NO3, 
			SENDR_CELL_NO1, SENDR_CELL_NO2, SENDR_CELL_NO3, SENDR_ZIP_NO, SENDR_ADDR, 
			SENDR_DETAIL_ADDR, RCVR_NM, RCVR_TEL_NO1, RCVR_TEL_NO2, RCVR_TEL_NO3, 
			RCVR_CELL_NO1, RCVR_CELL_NO2, RCVR_CELL_NO3, RCVR_ZIP_NO, RCVR_ADDR, 
			RCVR_DETAIL_ADDR, INVC_NO, PRT_ST, GDS_NM, DLV_DV, EAI_PRGS_ST, REG_EMP_ID, 
			REG_DTIME, MODI_EMP_ID, MODI_DTIME
		) VALUES (
			:CUST_ID, :RCPT_YMD, :CUST_USE_NO, :RCPT_DV, :WORK_DV_CD, :REQ_DV_CD, :MPCK_KEY, 
			:MPCK_SEQ, :CAL_DV_CD, :FRT_DV_CD, :CNTR_ITEM_CD, :BOX_TYPE_CD, :BOX_QTY, :FRT, 
			:CUST_MGMT_DLCM_CD, :SENDR_NM, :SENDR_TEL_NO1, :SENDR_TEL_NO2, :SENDR_TEL_NO3, 
			:SENDR_CELL_NO1, :SENDR_CELL_NO2, :SENDR_CELL_NO3, :SENDR_ZIP_NO, :SENDR_ADDR, 
			:SENDR_DETAIL_ADDR, :RCVR_NM, :RCVR_TEL_NO1, :RCVR_TEL_NO2, :RCVR_TEL_NO3, 
			:RCVR_CELL_NO1, :RCVR_CELL_NO2, :RCVR_CELL_NO3, :RCVR_ZIP_NO, :RCVR_ADDR, 
			:RCVR_DETAIL_ADDR, :INVC_NO, :PRT_ST, :GDS_NM, :DLV_DV, :EAI_PRGS_ST, 
			:REG_EMP_ID, SYSDATE, :MODI_EMP_ID, SYSDATE
		)
		";

	echo $query;
/*
// 입력예제
	//$sno = $_POST["sno"];

	//$query = "insert into student (sno) value (:v_sno)";
	$result = oci_parse($conn, $query);
	//oci_bind_by_name($result, ":v_sno", $sno);

	oci_bind_by_name($result, ":CUST_ID", $ret_invoice_info["CUST_ID"]);
	oci_bind_by_name($result, ":RCPT_YMD", $ret_invoice_info["RCPT_YMD"]);
	oci_bind_by_name($result, ":CUST_USE_NO", $ret_invoice_info["CUST_USE_NO"]);
	oci_bind_by_name($result, ":RCPT_DV", $ret_invoice_info["RCPT_DV"]);
	oci_bind_by_name($result, ":WORK_DV_CD", $ret_invoice_info["WORK_DV_CD"]);
	oci_bind_by_name($result, ":REQ_DV_CD", $ret_invoice_info["REQ_DV_CD"]);
	oci_bind_by_name($result, ":MPCK_KEY", $ret_invoice_info["MPCK_KEY"]);
	oci_bind_by_name($result, ":MPCK_SEQ", $ret_invoice_info["MPCK_SEQ"]);
	oci_bind_by_name($result, ":CAL_DV_CD", $ret_invoice_info["CAL_DV_CD"]);
	oci_bind_by_name($result, ":FRT_DV_CD", $ret_invoice_info["FRT_DV_CD"]);
	oci_bind_by_name($result, ":CNTR_ITEM_CD", $ret_invoice_info["CNTR_ITEM_CD"]);
	oci_bind_by_name($result, ":BOX_TYPE_CD", $ret_invoice_info["BOX_TYPE_CD"]);
	oci_bind_by_name($result, ":BOX_QTY", $ret_invoice_info["BOX_QTY"]);
	oci_bind_by_name($result, ":FRT", $ret_invoice_info["FRT"]);
	oci_bind_by_name($result, ":CUST_MGMT_DLCM_CD", $ret_invoice_info["CUST_MGMT_DLCM_CD"]);
	oci_bind_by_name($result, ":SENDR_NM", $ret_invoice_info["SENDR_NM"]);
	oci_bind_by_name($result, ":SENDR_TEL_NO1", $ret_invoice_info["SENDR_TEL_NO1"]);
	oci_bind_by_name($result, ":SENDR_TEL_NO2", $ret_invoice_info["SENDR_TEL_NO2"]);
	oci_bind_by_name($result, ":SENDR_TEL_NO3", $ret_invoice_info["SENDR_TEL_NO3"]);
	oci_bind_by_name($result, ":SENDR_CELL_NO1", $ret_invoice_info["SENDR_CELL_NO1"]);
	oci_bind_by_name($result, ":SENDR_CELL_NO2", $ret_invoice_info["SENDR_CELL_NO2"]);
	oci_bind_by_name($result, ":SENDR_CELL_NO3", $ret_invoice_info["SENDR_CELL_NO3"]);
	oci_bind_by_name($result, ":SENDR_ZIP_NO", $ret_invoice_info["SENDR_ZIP_NO"]);
	oci_bind_by_name($result, ":SENDR_ADDR", $ret_invoice_info["SENDR_ADDR"]);
	oci_bind_by_name($result, ":SENDR_DETAIL_ADDR", $ret_invoice_info["SENDR_DETAIL_ADDR"]);
	oci_bind_by_name($result, ":RCVR_NM", $ret_invoice_info["RCVR_NM"]);
	oci_bind_by_name($result, ":RCVR_TEL_NO1", $ret_invoice_info["RCVR_TEL_NO1"]);
	oci_bind_by_name($result, ":RCVR_TEL_NO2", $ret_invoice_info["RCVR_TEL_NO2"]);
	oci_bind_by_name($result, ":RCVR_TEL_NO3", $ret_invoice_info["RCVR_TEL_NO3"]);
	oci_bind_by_name($result, ":RCVR_CELL_NO1", $ret_invoice_info["RCVR_CELL_NO1"]);
	oci_bind_by_name($result, ":RCVR_CELL_NO2", $ret_invoice_info["RCVR_CELL_NO2"]);
	oci_bind_by_name($result, ":RCVR_CELL_NO3", $ret_invoice_info["RCVR_CELL_NO3"]);
	oci_bind_by_name($result, ":RCVR_ZIP_NO", $ret_invoice_info["RCVR_ZIP_NO"]);
	oci_bind_by_name($result, ":RCVR_ADDR", $ret_invoice_info["RCVR_ADDR"]);
	oci_bind_by_name($result, ":RCVR_DETAIL_ADDR", $ret_invoice_info["RCVR_DETAIL_ADDR"]);
	oci_bind_by_name($result, ":INVC_NO", $ret_invoice_info["INVC_NO"]);
	oci_bind_by_name($result, ":PRT_ST", $ret_invoice_info["PRT_ST"]);
	oci_bind_by_name($result, ":GDS_NM", $ret_invoice_info["GDS_NM"]);
	oci_bind_by_name($result, ":DLV_DV", $ret_invoice_info["DLV_DV"]);
	oci_bind_by_name($result, ":EAI_PRGS_ST", $ret_invoice_info["EAI_PRGS_ST"]);
	oci_bind_by_name($result, ":REG_EMP_ID", $ret_invoice_info["REG_EMP_ID"]);
	//oci_bind_by_name($result, ":REG_DTIME", SYSDATE);
	oci_bind_by_name($result, ":MODI_EMP_ID", $ret_invoice_info["MODI_EMP_ID"]);
	//oci_bind_by_name($result, ":MODI_DTIME", SYSDATE);

	$success = oci_execute($result);
	echo "success->" . $success;
	oci_free_statement($result);*/


	oci_close($conn);

} else {
	$e = oci_error();
	echo "CJ Open DB : Connect Fail!<br />";
	echo $e['message']."<br />";
	print_r($e);
}

exit;

?>