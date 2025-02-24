<?php
/**
 * Class API_CJ_Invoce
 * User : ssawoona
 * Date : 2019
 */
class API_CJ_Invoice extends Dbconn
{
	public $P_CLNTNUM;                  //CJ대한통운 고객ID
	public $P_CLNTMGMCUSTCD;            //CJ대한통운 고객관리거래처코드
	public $CJ_DB_TABLE_NAME;           //
	public $CJ_DB_VIEW_NAME;            //
	public $CJ_DB_READDR_ID;            //주소 정제 서버
	public $CJ_DB_READDR_PWD;           //주소 정제 서버
	public $CJ_DB_READDR_CONSTR;        //주소 정제 서버
	public $CJ_DB_OPENDB_ID;            //CJ OPEN DB
	public $CJ_DB_OPENDB_PWD;           //CJ OPEN DB
	public $CJ_DB_OPENDB_CONSTR;        //CJ OPEN DB

	/**
	 * 설정 내역을 불러온다 
	 * site_config.inc 에서 설정된 내용
	 * API_CJ_Invoice constructor.
	 */
	function __construct() {
		global $GL_P_CLNTNUM, $GL_P_CLNTMGMCUSTCD, $GL_CJ_DB_TABLE_NAME, $GL_CJ_DB_VIEW_NAME, $GL_CJ_DB_READDR_ID, $GL_CJ_DB_READDR_PWD, $GL_CJ_DB_READDR_CONSTR, $GL_CJ_DB_OPENDB_ID, $GL_CJ_DB_OPENDB_PWD, $GL_CJ_DB_OPENDB_CONSTR;
		Dbconn::__construct();

		$this->P_CLNTNUM = $GL_P_CLNTNUM;
		$this->P_CLNTMGMCUSTCD = $GL_P_CLNTMGMCUSTCD;
		$this->CJ_DB_TABLE_NAME = $GL_CJ_DB_TABLE_NAME;
		$this->CJ_DB_VIEW_NAME = $GL_CJ_DB_VIEW_NAME;
		$this->CJ_DB_READDR_ID = $GL_CJ_DB_READDR_ID;
		$this->CJ_DB_READDR_PWD = $GL_CJ_DB_READDR_PWD;
		$this->CJ_DB_READDR_CONSTR = $GL_CJ_DB_READDR_CONSTR;
		$this->CJ_DB_OPENDB_ID = $GL_CJ_DB_OPENDB_ID;
		$this->CJ_DB_OPENDB_PWD = $GL_CJ_DB_OPENDB_PWD;
		$this->CJ_DB_OPENDB_CONSTR = $GL_CJ_DB_OPENDB_CONSTR;
	}

	/**
	 * CJ 다음순서 송장 번호 체번 및 체번 정보 Insert
	 * @param $args
	 * @return array
	 */
	public function getNewCJInvoiceNum($args) {
		global $DB;
		$arrRet = array(
			'result' => false,
			'msg' => '',
			'order_pack_idx' => '',
			'new_invoice_no' => '',
			'cj_addinfo' => array(),
		);
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "SELECT ".$DB['cj_log_dbname'].".dbo.GET_NEXT_INVOICE_NO() AS CJ_NEW_INVOICE_NO";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		$new_cj_invoice_no = $rst["CJ_NEW_INVOICE_NO"];

		$CJ_AddInfo = $this->repCJAddress($args);
		extract($CJ_AddInfo);
		$arrRet["msg"] = $p_errormsg;
		if($p_errorcd != "0") {
			return $arrRet;
		}

		// 기존에 같은 order_pack_idx 가 있으면 삭제 플래그 추가 (새로운 송장번호 발급시)
//		$qry = "
//			UPDATE DY_INVOICE_CJ
//			SET invoice_is_del = N'Y'
//			, invoice_moddate = GETDATE()
//			, invoice_modip =  N'$modip'
//			, last_member_idx = N'$last_member_idx'
//			WHERE order_pack_idx = N'$order_pack_idx'
//			";
//		parent::db_connect();
//		parent::execSqlUpdate($qry);
//		parent::db_close();
		$qry = "
			Insert Into ".$DB['cj_log_dbname'].".dbo.DY_INVOICE_CJ
			(
				order_pack_idx,invoice_no,
				p_clntnum,p_clntmgmcustcd,p_prngdivcd,p_farediv,p_boxtyp,
				p_cgosts,p_address,p_zipnum,p_zipid,p_oldaddress,p_oldaddressdtl,p_newaddress,
				p_nesaddressdtl,p_etcaddr,p_shortaddr,p_clsfaddr,p_clldlvbrancd,p_clldlvbrannm,p_clldlcbranshortnm,p_clldlvempnum,
				p_clldlvempnm,p_clldlvempnicknm,p_clsfcd,p_clsfnm,p_subclsfcd,p_rspsdiv,p_newaddryn,p_errorcd,p_errormsg,
				invoice_regip,last_member_idx
			 )
			 VALUES 
			(
				N'$order_pack_idx',
				N'$new_cj_invoice_no',
				N'$p_clntnum',
				N'$p_clntmgmcustcd',
				N'$p_prngdivcd',				
				N'$p_farediv',
				N'$p_boxtyp',				
				N'$p_cgosts',
				N'$p_address',
				N'$p_zipnum',
				N'$p_zipid',
				N'$p_oldaddress',
				N'$p_oldaddressdtl',
				N'$p_newaddress',
				N'$p_nesaddressdtl',
				N'$p_etcaddr',
				N'$p_shortaddr',
				N'$p_clsfaddr',
				N'$p_clldlvbrancd',
				N'$p_clldlvbrannm',
				N'$p_clldlcbranshortnm',
				N'$p_clldlvempnum',
				N'$p_clldlvempnm',
				N'$p_clldlvempnicknm',
				N'$p_clsfcd',
				N'$p_clsfnm',
				N'$p_subclsfcd',
				N'$p_rspsdiv',
				N'$p_newaddryn',
				N'$p_errorcd',
				N'$p_errormsg',
				N'$modip',
				N'$last_member_idx'
			)
		";

		parent::db_connect();
		$inst_rst = parent::execSqlInsert($qry);
		parent::db_close();
		if($inst_rst) {
			$arrRet["result"] = true;
			$arrRet["order_pack_idx"] = $order_pack_idx;
			$arrRet["new_invoice_no"] = $new_cj_invoice_no;
			$arrRet["cj_addinfo"] = $CJ_AddInfo;
		}
		return $arrRet;
	}

	/**
	 * CJ API를 통해 CJ 쪽 DB에 운송장 입력
	 * @param $invoice_no 송장번호
	 */
	public function insertCJInvoice($args) {
		global $DB;
		$arrRet = array(
			'result' => false,
			'cj_delivery_idx' => 0,
			'ret_msg' => '',
		);
		//region *** CJ Interface DB Insert 파라미터 초기화 ***
		$CUST_ID           = "";    // 고객ID : 주관고객코드 (ex : 3012XXXX)
		$RCPT_YMD          = "";    // 접수일자 : YYYYMMDD 형식 必 (ex : 20160528)
		$CUST_USE_NO       = "";    // 고객사용번호 : 기업고객이 관리하는 주문번호/ 영수번호 등 내부 관리번호(운송장번호와 매핑되는 유일값 기입) (ex : 2016051087)
		$RCPT_DV           = "";    // 접수구분 : 01 : 일반,  02 : 반품 (ex : 01)
		$WORK_DV_CD        = "";    // 작업구분코드 : 01 : 일반,  02 : 교환, 03 : A/S  (ex : 01)
		$REQ_DV_CD         = "";    // 요청구분코드 : 01 : 요청,  02 : 취소 (ex : 01)
		$MPCK_KEY          = "";    // 합포장키 : 다수데이터를 한 송장에 출력할 경우 처리(RCPT_YMD || '_' || CUST_ID || '_' || CUST_USE_NO or RCPT_YMD || '_' || CUST_ID || '_' || INVC_NO 등의 형식을 통해 유일값 입력)* 합포 있는 경우 해당 건들의 합포장키를 동일값으로 접수 (ex : 20160528_3012XXXX_2013051087)"
		$MPCK_SEQ          = "";    // 합포장순번 : 합포장 처리건수가 다수일경우 SEQ처리를 수행한다.( 합포없는경우 무조건 1 ) (ex : 1)
		$CAL_DV_CD         = "";    // 정산구분코드 : 01: 계약 운임,  02: 자료 운임 (계약운임인지 업체에서 넣어주는 운임으로할지) (ex : 01)
		$FRT_DV_CD         = "";    // 운임구분코드 : 01: 선불,  02: 착불 ,  03: 신용 (ex : 03)
		$CNTR_ITEM_CD      = "";    // 계약품목코드 : 01: 일반 품목 (ex : 01)
		$BOX_TYPE_CD       = "";    // 박스타입코드 : 01: 극소,  02: 소,  03: 중,  04: 대,  05: 특대 (ex : 02)
		$BOX_QTY           = "";    // 박스수량 : 택배 박스 수량 (1로 기입) (ex : 1)
		$FRT               = "";    // 운임 : 운임적용구분이 자료 운임일 경우 등록 처리 (ex : 0)
		$CUST_MGMT_DLCM_CD = "";    // 고객관리거래처코드 : 주관사 관리 협력업체 코드 혹은 택배사 관리 업체코드 (ex : 3012XXXX)
		$SENDR_NM          = "";    // 송화인명 : 보내는분 성명 (ex : XXX기업㈜)
		$SENDR_TEL_NO1     = "";    // 송화인전화번호1 : 송화인전화번호1 항목은 '0'으로 시작할 것. (국번은 0으로 시작) (ex : 02)
		$SENDR_TEL_NO2     = "";    // 송화인전화번호2 :  (ex : 1588)
		$SENDR_TEL_NO3     = "";    // 송화인전화번호3 : 암호화 구간(암호화는 대한통운 내부로직이므로, 고객사 접수 시에는 무시하셔도 좋습니다.) (ex : 1255)
		$SENDR_CELL_NO1    = "";    // 송화인휴대폰번호1 :  (ex : )
		$SENDR_CELL_NO2    = "";    // 송화인휴대폰번호2 :  (ex : )
		$SENDR_CELL_NO3    = "";    // 송화인휴대폰번호3 : 암호화 구간 (ex : )
		$SENDR_ZIP_NO      = "";    // 송화인우편번호 :  (ex : 100100)
		$SENDR_ADDR        = "";    // 송화인주소 : 송화인 주소 ( ~동 or ~로 까지의 앞 단 주소) (ex : 서울시 중구 서소문동)
		$SENDR_DETAIL_ADDR = "";    // 송화인상세주소 : 송화인 상세 주소(암호화 구간, 나머지 상세 주소) (ex : 66번지)
		$RCVR_NM           = "";    // 수화인명 :  (ex : 홍길동)
		$RCVR_TEL_NO1      = "";    // 수화인전화번호1 : 수화인전화번호1 항목은 '0'으로 시작할 것. (국번은 0으로 시작) (ex : 031)
		$RCVR_TEL_NO2      = "";    // 수화인전화번호2 :  (ex : 111)
		$RCVR_TEL_NO3      = "";    // 수화인전화번호3 : 암호화 구간 (ex : 2222)
		$RCVR_CELL_NO1     = "";    // 수화인휴대폰번호1 :  (ex : )
		$RCVR_CELL_NO2     = "";    // 수화인휴대폰번호2 :  (ex : )
		$RCVR_CELL_NO3     = "";    // 수화인휴대폰번호3 : 암호화 구간 (ex : )
		$RCVR_ZIP_NO       = "";    // 수화인우편번호 :  (ex : 200200)
		$RCVR_ADDR         = "";    // 수화인주소 : 수화인 주소 (ex : 충남 천안시 서북구 두정동)
		$RCVR_DETAIL_ADDR  = "";    // 수화인상세주소 : 수화인 상세 주소(암호화 구간) (ex : 77번지)
		$INVC_NO           = "";    // 운송장번호 : 12자리, 운송장번호 채번 로직 : 3~11 범위의 수를 MOD(7) 한 결과가 12번째 수와 같아야 한다.Ex: 운송장번호 301100112233 의 경우, 3~11의 수(110011223) 을 MOD(7)한 결과가 3 이기에 적합한운송장번호이다. (ex : 301100112233)"
		$PRT_ST            = "";    // 출력상태 : 01: 미출력,  02: 선출력,  03: 선발번 (반품은 선발번이 없음) *자체시스템을 이용하여 운송장을 출력하는 고객사의 경우 02, CNPLUS를 이용하여 운송장을 출력하는 경우 01 (ex : 02)"
		$GDS_NM            = "";    // 상품명 :  (ex : 사과쥬스1박스)REG_EMP_ID
		$DLV_DV            = "";    // 택배구분 : 택배 : '01', 중량물(설치물류) : '02', 중량물(비설치물류) : '03' / 택배의 경우 '01' 만 허용됨. (ex : 01)
		$EAI_PRGS_ST       = "";    // EAI전송상태 : DEFAULT : '01' (ex : 01)
		$REG_EMP_ID        = "";    // 등록사원ID : 고객 사용계정 대문자로 기입 (ex : CAFE24) (ex : CAFE24)
		$REG_DTIME         = "";    // 등록일시 : SYSDATE (ex : SYSDATE)
		$MODI_EMP_ID       = "";    // 수정사원ID : 고객 사용계정 대문자로 기입 (ex : CAFE24) (ex : CAFE24)
		$MODI_DTIME        = "";    // 수정일시 : SYSDATE (ex : SYSDATE)
		//endregion
		$SENDR_TEL_NO = ""; // 전화번호가 세자리로 안짤렸을때 용
		$RCVR_TEL_NO  = "";  // 전화번호가 세자리로 안짤렸을때 용
		$RCPT_DV      = "01";    // 접수구분 : 01 : 일반,  02 : 반품 (ex : 01)
		extract($args);
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		//region *** 파라미터 에러 체크 ***
		$data_state = "Y";
		$msg = "";
		if(!$GDS_NM	|| !$SENDR_NM || !$RCVR_NM || !$SENDR_ADDR || !$RCVR_ADDR) {
			$data_state = "N";
			$msg = "필수값 없음";
			//echo $msg."<br />";
		}

		if($SENDR_TEL_NO != "") {
			$arry_SENDR_TEL_NO = explode("-", $SENDR_TEL_NO);
			if(count($arry_SENDR_TEL_NO) == 3) {
				$SENDR_TEL_NO1 = $arry_SENDR_TEL_NO[0];
				$SENDR_TEL_NO2 = $arry_SENDR_TEL_NO[1];
				$SENDR_TEL_NO3 = $arry_SENDR_TEL_NO[2];
			} else {
				$data_state = "N";
				$msg = "송화인 전화번호 올바르지 않음";
				//echo $msg."<br />";
			}
		}
		if(!$SENDR_TEL_NO1 || !$SENDR_TEL_NO2 || !$SENDR_TEL_NO3) {
			$data_state = "N";
			$msg = "송화인 전화번호 올바르지 않음";
			//echo $msg."<br />";
		}
		if(substr($SENDR_TEL_NO1,0,1) != "0") {
			$data_state = "N";
			$msg = "송화인 전화번호 올바르지 않음";
			//echo $msg."<br />";
		}

		if($RCVR_TEL_NO != "") {
			$arry_RCVR_TEL_NO = explode("-", $RCVR_TEL_NO);
			if(count($arry_RCVR_TEL_NO) == 3) {
				$RCVR_TEL_NO1 = $arry_RCVR_TEL_NO[0];
				$RCVR_TEL_NO2 = $arry_RCVR_TEL_NO[1];
				$RCVR_TEL_NO3 = $arry_RCVR_TEL_NO[2];
			} else {
				$data_state = "N";
				$msg = "수화인 전화번호 올바르지 않음";
				//echo $msg."<br />";
			}
		}
		if(!$RCVR_TEL_NO1 || !$RCVR_TEL_NO2 || !$RCVR_TEL_NO3) {
			$data_state = "N";
			$msg = "수화인 전화번호 올바르지 않음";
			echo $msg."<br />";
		}
		if(substr($RCVR_TEL_NO1,0,1) != "0") {
			$data_state = "N";
			$msg = "수화인 전화번호 올바르지 않음";
			//echo $msg."<br />";
		}

		$CJ_AddrInfo = $this->repCJAddress(array('order_pack_idx' => '0', 'receive_addr' => $SENDR_ADDR." ".$SENDR_DETAIL_ADDR,));
		if($CJ_AddrInfo["p_errorcd"] == "0") {
			$SENDR_ADDR = $CJ_AddrInfo["p_newaddress"];
			$SENDR_DETAIL_ADDR = $CJ_AddrInfo["p_nesaddressdtl"];
			$SENDR_ZIP_NO = $CJ_AddrInfo["p_zipnum"];

			if($CJ_AddrInfo["p_newaddress"] == "") {
				$SENDR_ADDR = $CJ_AddrInfo["p_oldaddress"];
				$SENDR_DETAIL_ADDR = $CJ_AddrInfo["p_oldaddressdtl"];
			}

		} else {
			$data_state = "N";
			$msg = "송화인 주소정제 에러";
			//echo $msg."<br />";
		}

		$CJ_AddrInfo = $this->repCJAddress(array('order_pack_idx' => '0', 'receive_addr' => $RCVR_ADDR." ".$RCVR_DETAIL_ADDR,));
		if($CJ_AddrInfo["p_errorcd"] == "0") {
			$RCVR_ADDR = $CJ_AddrInfo["p_newaddress"];
			$RCVR_DETAIL_ADDR = $CJ_AddrInfo["p_nesaddressdtl"];
			$RCVR_ZIP_NO = $CJ_AddrInfo["p_zipnum"];

			if($CJ_AddrInfo["p_newaddress"] == "") {
				$RCVR_ADDR = $CJ_AddrInfo["p_oldaddress"];
				$RCVR_DETAIL_ADDR = $CJ_AddrInfo["p_oldaddressdtl"];
			}
		} else {
			$data_state = "N";
			$msg = "수화인 주소정제 에러";
			//echo $msg."<br />";
		}

		if($RCPT_DV == "02") {
			if($INVC_NO) {
				$data_state = "N";
				$msg = "운송장번호 있음(반품)";
				//echo $msg."<br />";
			}
		} else {
			if(!$INVC_NO) {
				$data_state = "N";
				$msg = "운송장번호 없음";
				//echo $msg."<br />";
			}
		}
		//endregion

		$_deliver = array(
			'CUST_USE_NO' => "",    // 고객사용번호 : 기업고객이 관리하는 주문번호/ 영수번호 등 내부 관리번호(운송장번호와 매핑되는 유일값 기입) (ex : 2016051087)
			'RCPT_DV' => "",    // 접수구분 : 01 : 일반,  02 : 반품 (ex : 01)
			'SENDR_NM' => "",    // 송화인명 : 보내는분 성명 (ex : XXX기업㈜)
			'SENDR_TEL_NO1' => "",    // 송화인전화번호1 : 송화인전화번호1 항목은 '0'으로 시작할 것. (국번은 0으로 시작) (ex : 02)
			'SENDR_TEL_NO2' => "",    // 송화인전화번호2 :  (ex : 1588)
			'SENDR_TEL_NO3' => "",    // 송화인전화번호3 : 암호화 구간(암호화는 대한통운 내부로직이므로, 고객사 접수 시에는 무시하셔도 좋습니다.) (ex : 1255)
			'SENDR_ZIP_NO' => "",    // 송화인우편번호 :  (ex : 100100)
			'SENDR_ADDR' => "",    // 송화인주소 : 송화인 주소 ( ~동 or ~로 까지의 앞 단 주소) (ex : 서울시 중구 서소문동)
			'SENDR_DETAIL_ADDR' => "",    // 송화인상세주소 : 송화인 상세 주소(암호화 구간, 나머지 상세 주소) (ex : 66번지)
			'RCVR_NM' => "",    // 수화인명 :  (ex : 홍길동)
			'RCVR_TEL_NO1' => "",    // 수화인전화번호1 : 수화인전화번호1 항목은 '0'으로 시작할 것. (국번은 0으로 시작) (ex : 031)
			'RCVR_TEL_NO2' => "",    // 수화인전화번호2 :  (ex : 111)
			'RCVR_TEL_NO3' => "",    // 수화인전화번호3 : 암호화 구간 (ex : 2222)
			'RCVR_ZIP_NO' => "",    // 수화인우편번호 :  (ex : 200200)
			'RCVR_ADDR' => "",    // 수화인주소 : 수화인 주소 (ex : 충남 천안시 서북구 두정동)
			'RCVR_DETAIL_ADDR' => "",    // 수화인상세주소 : 수화인 상세 주소(암호화 구간) (ex : 77번지)
			'INVC_NO' => "",    // 운송장번호 : 12자리, 운송장번호 채번 로직 : 3~11 범위의 수를 MOD(7) 한 결과가 12번째 수와 같아야 한다. Ex: 운송장번호 301100112233 의 경우, 3~11의 수(110011223) 을 MOD(7)한 결과가 3 이기에 적합한운송장번호이다. (ex : 301100112233)"
			'GDS_NM' => "",    // 상품명 :  (ex : 사과쥬스1박스)
		);

		//region *** 파라미터 기본 값 및 변수 셋팅 ***
		$CUST_ID           = $this->P_CLNTNUM;    // 고객ID : 주관고객코드 (ex : 3012XXXX)
		$RCPT_YMD          = date("Ymd");    // 접수일자 : YYYYMMDD 형식 必 (ex : 20160528)
//		$RCPT_DV           = "01";    // 접수구분 : 01 : 일반,  02 : 반품 (ex : 01)
		$WORK_DV_CD        = "01";    // 작업구분코드 : 01 : 일반,  02 : 교환, 03 : A/S  (ex : 01)
		$REQ_DV_CD         = "01";    // 요청구분코드 : 01 : 요청,  02 : 취소 (ex : 01)
		$MPCK_SEQ          = "1";    // 합포장순번 : 합포장 처리건수가 다수일경우 SEQ처리를 수행한다.( 합포없는경우 무조건 1 ) (ex : 1)
		$CAL_DV_CD         = "01";    // 정산구분코드 : 01: 계약 운임,  02: 자료 운임 (계약운임인지 업체에서 넣어주는 운임으로할지) (ex : 01)
		if($FRT_DV_CD == "") {
			$FRT_DV_CD = "03";    // 운임구분코드 : 01: 선불,  02: 착불 ,  03: 신용 (ex : 03)
		}
		$CNTR_ITEM_CD      = "01";    // 계약품목코드 : 01: 일반 품목 (ex : 01)
		$BOX_TYPE_CD       = "01";    // 박스타입코드 : 01: 극소,  02: 소,  03: 중,  04: 대,  05: 특대 (ex : 02)
		$BOX_QTY           = "1";    // 박스수량 : 택배 박스 수량 (1로 기입) (ex : 1)
		$FRT               = "0";    // 운임 : 운임적용구분이 자료 운임일 경우 등록 처리 (ex : 0)
		$CUST_MGMT_DLCM_CD = $this->P_CLNTMGMCUSTCD;    // 고객관리거래처코드 : 주관사 관리 협력업체 코드 혹은 택배사 관리 업체코드 (ex : 3012XXXX)
//		$SENDR_NM          = $send_name;    // 송화인명 : 보내는분 성명 (ex : XXX기업㈜)
//		$SENDR_TEL_NO1     = $send_arry_phone[0];    // 송화인전화번호1 : 송화인전화번호1 항목은 '0'으로 시작할 것. (국번은 0으로 시작) (ex : 02)
//		$SENDR_TEL_NO2     = $send_arry_phone[1];    // 송화인전화번호2 :  (ex : 1588)
//		$SENDR_TEL_NO3     = $send_arry_phone[2];    // 송화인전화번호3 : 암호화 구간(암호화는 대한통운 내부로직이므로, 고객사 접수 시에는 무시하셔도 좋습니다.) (ex : 1255)
//		$SENDR_CELL_NO1    = "";    // 송화인휴대폰번호1 :  (ex : )
//		$SENDR_CELL_NO2    = "";    // 송화인휴대폰번호2 :  (ex : )
//		$SENDR_CELL_NO3    = "";    // 송화인휴대폰번호3 : 암호화 구간 (ex : )
		$SENDR_ZIP_NO      = str_replace("-","",$SENDR_ZIP_NO);    // 송화인우편번호 :  (ex : 100100)
		$SENDR_ADDR        = str_replace("'", " ", $SENDR_ADDR);    // 송화인주소 : 송화인 주소 ( ~동 or ~로 까지의 앞 단 주소) (ex : 서울시 중구 서소문동)
		$SENDR_DETAIL_ADDR = str_replace("'", " ", $SENDR_DETAIL_ADDR);    // 송화인상세주소 : 송화인 상세 주소(암호화 구간, 나머지 상세 주소) (ex : 66번지)
//		$RCVR_NM           = $receive_name;    // 수화인명 :  (ex : 홍길동)
//		$RCVR_TEL_NO1      = $receive_arry_hp_num[0];    // 수화인전화번호1 : 수화인전화번호1 항목은 '0'으로 시작할 것. (국번은 0으로 시작) (ex : 031)
//		$RCVR_TEL_NO2      = $receive_arry_hp_num[1];    // 수화인전화번호2 :  (ex : 111)
//		$RCVR_TEL_NO3      = $receive_arry_hp_num[2];    // 수화인전화번호3 : 암호화 구간 (ex : 2222)
//		$RCVR_CELL_NO1     = "";    // 수화인휴대폰번호1 :  (ex : )
//		$RCVR_CELL_NO2     = "";    // 수화인휴대폰번호2 :  (ex : )
//		$RCVR_CELL_NO3     = "";    // 수화인휴대폰번호3 : 암호화 구간 (ex : )
		$RCVR_ZIP_NO       = str_replace("-","",$RCVR_ZIP_NO);    // 수화인우편번호 :  (ex : 200200)
		$RCVR_ADDR         = str_replace("'", " ", $RCVR_ADDR);    // 수화인주소 : 수화인 주소 (ex : 충남 천안시 서북구 두정동)
		$RCVR_DETAIL_ADDR  = str_replace("'", " ", $RCVR_DETAIL_ADDR);    // 수화인상세주소 : 수화인 상세 주소(암호화 구간) (ex : 77번지)
//		$INVC_NO           = $INVC_NO;    // 운송장번호 : 12자리, 운송장번호 채번 로직 : 3~11 범위의 수를 MOD(7) 한 결과가 12번째 수와 같아야 한다.
		if($PRT_ST == "") {
			$PRT_ST = "02";    // 출력상태 : 01: 미출력,  02: 선출력,  03: 선발번 (반품은 선발번이 없음)
		}
		$GDS_NM            = str_replace("'", " ", substr($GDS_NM, 0, 200));    // 상품명 :  (ex : 사과쥬스1박스)
		$DLV_DV            = "01";    // 택배구분 : 택배 : '01', 중량물(설치물류) : '02', 중량물(비설치물류) : '03' / 택배의 경우 '01' 만 허용됨. (ex : 01)
		$EAI_PRGS_ST       = "01";    // EAI전송상태 : DEFAULT : '01' (ex : 01)
		$REG_EMP_ID        = "TOMSKEVIN";    // 등록사원ID : 고객 사용계정 대문자로 기입 (ex : CAFE24) (ex : CAFE24)
		$MODI_EMP_ID       = "TOMSKEVIN";    // 수정사원ID : 고객 사용계정 대문자로 기입 (ex : CAFE24) (ex : CAFE24)
		//endregion


		//region *** DY DB 에 배송 요청 로그 Insert ***
		$cj_table_fields = "
			CUST_ID, RCPT_YMD, CUST_USE_NO, RCPT_DV, WORK_DV_CD, REQ_DV_CD, MPCK_KEY, 
			MPCK_SEQ, CAL_DV_CD, FRT_DV_CD, CNTR_ITEM_CD, BOX_TYPE_CD, BOX_QTY, FRT, 
			CUST_MGMT_DLCM_CD, SENDR_NM, SENDR_TEL_NO1, SENDR_TEL_NO2, SENDR_TEL_NO3, 
			SENDR_CELL_NO1, SENDR_CELL_NO2, SENDR_CELL_NO3, SENDR_ZIP_NO, SENDR_ADDR, 
			SENDR_DETAIL_ADDR, RCVR_NM, RCVR_TEL_NO1, RCVR_TEL_NO2, RCVR_TEL_NO3, 
			RCVR_CELL_NO1, RCVR_CELL_NO2, RCVR_CELL_NO3, RCVR_ZIP_NO, RCVR_ADDR, 
			RCVR_DETAIL_ADDR, INVC_NO, PRT_ST, GDS_NM, DLV_DV, EAI_PRGS_ST, REG_EMP_ID, 
			REG_DTIME, MODI_EMP_ID, MODI_DTIME
		";
		// DY DB Insert
		$query = "
		INSERT INTO ".$DB['cj_log_dbname'].".dbo.DY_CJ_DELIVERY (
			$cj_table_fields
			,data_state, msg
			,delivery_regip, delivery_modip, last_member_idx
		) VALUES (
			N'$CUST_ID', N'$RCPT_YMD', N'$CUST_USE_NO', N'$RCPT_DV', N'$WORK_DV_CD', N'$REQ_DV_CD', N'$MPCK_KEY', 
			N'$MPCK_SEQ', N'$CAL_DV_CD', N'$FRT_DV_CD', N'$CNTR_ITEM_CD', N'$BOX_TYPE_CD', N'$BOX_QTY', N'$FRT', 
			N'$CUST_MGMT_DLCM_CD', N'$SENDR_NM', N'$SENDR_TEL_NO1', N'$SENDR_TEL_NO2', N'$SENDR_TEL_NO3', 
			N'$SENDR_CELL_NO1', N'$SENDR_CELL_NO2', N'$SENDR_CELL_NO3', N'$SENDR_ZIP_NO', N'$SENDR_ADDR', 
			N'$SENDR_DETAIL_ADDR', N'$RCVR_NM', N'$RCVR_TEL_NO1', N'$RCVR_TEL_NO2', N'$RCVR_TEL_NO3', 
			N'$RCVR_CELL_NO1', N'$RCVR_CELL_NO2', N'$RCVR_CELL_NO3', N'$RCVR_ZIP_NO', N'$RCVR_ADDR', 
			N'$RCVR_DETAIL_ADDR', N'$INVC_NO', N'$PRT_ST', N'$GDS_NM', N'$DLV_DV', N'$EAI_PRGS_ST', 
			N'$REG_EMP_ID', format(GETDATE(),'yyyy-MM-dd HH:mm:ss.fff'), N'$MODI_EMP_ID', format(GETDATE(),'yyyy-MM-dd HH:mm:ss.fff'),
			N'$data_state', N'$msg',
			N'$modip', N'$modip', N'$last_member_idx'
		)
		";
		parent::db_connect();
		$cj_delivery_idx = parent::execSqlInsert($query);
		parent::db_close();
		$CUST_USE_NO       = $cj_delivery_idx;    // 고객사용번호 : 기업고객이 관리하는 주문번호/ 영수번호 등 내부 관리번호(운송장번호와 매핑되는 유일값 기입) (ex : 2016051087)
		$MPCK_KEY          = date("Ymd")."_".$CUST_ID."_".$CUST_USE_NO;    // 합포장키 : 다수데이터를 한 송장에 출력할 경우 처리(RCPT_YMD || '_' || CUST_ID || '_' || CUST_USE_NO or
		$query = "
			UPDATE ".$DB['cj_log_dbname'].".dbo.DY_CJ_DELIVERY SET
			CUST_USE_NO = N'$CUST_USE_NO', MPCK_KEY = N'$MPCK_KEY'
			WHERE cj_delivery_idx = '$cj_delivery_idx'
		";
		parent::db_connect();
		parent::execSqlUpdate($query);
		parent::db_close();
		//endregion


		//region *** CJ OPEN DB 에 배송 요청 Insert ***
		if($data_state == "Y") {
			$conn = @oci_connect($this->CJ_DB_OPENDB_ID, $this->CJ_DB_OPENDB_PWD, $this->CJ_DB_OPENDB_CONSTR, 'AL32UTF8');
			if ($conn) {

				$query = "
					INSERT INTO $this->CJ_DB_TABLE_NAME (
						$cj_table_fields
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
				$result = oci_parse($conn, $query);

				oci_bind_by_name($result, ":CUST_ID", $CUST_ID);
				oci_bind_by_name($result, ":RCPT_YMD", $RCPT_YMD);
				oci_bind_by_name($result, ":CUST_USE_NO", $CUST_USE_NO);
				oci_bind_by_name($result, ":RCPT_DV", $RCPT_DV);
				oci_bind_by_name($result, ":WORK_DV_CD", $WORK_DV_CD);
				oci_bind_by_name($result, ":REQ_DV_CD", $REQ_DV_CD);
				oci_bind_by_name($result, ":MPCK_KEY", $MPCK_KEY);
				oci_bind_by_name($result, ":MPCK_SEQ", $MPCK_SEQ);
				oci_bind_by_name($result, ":CAL_DV_CD", $CAL_DV_CD);
				oci_bind_by_name($result, ":FRT_DV_CD", $FRT_DV_CD);
				oci_bind_by_name($result, ":CNTR_ITEM_CD", $CNTR_ITEM_CD);
				oci_bind_by_name($result, ":BOX_TYPE_CD", $BOX_TYPE_CD);
				oci_bind_by_name($result, ":BOX_QTY", $BOX_QTY);
				oci_bind_by_name($result, ":FRT", $FRT);
				oci_bind_by_name($result, ":CUST_MGMT_DLCM_CD", $CUST_MGMT_DLCM_CD);
				oci_bind_by_name($result, ":SENDR_NM", $SENDR_NM);
				oci_bind_by_name($result, ":SENDR_TEL_NO1", $SENDR_TEL_NO1);
				oci_bind_by_name($result, ":SENDR_TEL_NO2", $SENDR_TEL_NO2);
				oci_bind_by_name($result, ":SENDR_TEL_NO3", $SENDR_TEL_NO3);
				oci_bind_by_name($result, ":SENDR_CELL_NO1", $SENDR_CELL_NO1);
				oci_bind_by_name($result, ":SENDR_CELL_NO2", $SENDR_CELL_NO2);
				oci_bind_by_name($result, ":SENDR_CELL_NO3", $SENDR_CELL_NO3);
				oci_bind_by_name($result, ":SENDR_ZIP_NO", $SENDR_ZIP_NO);
				oci_bind_by_name($result, ":SENDR_ADDR", $SENDR_ADDR);
				oci_bind_by_name($result, ":SENDR_DETAIL_ADDR", $SENDR_DETAIL_ADDR);
				oci_bind_by_name($result, ":RCVR_NM", $RCVR_NM);
				oci_bind_by_name($result, ":RCVR_TEL_NO1", $RCVR_TEL_NO1);
				oci_bind_by_name($result, ":RCVR_TEL_NO2", $RCVR_TEL_NO2);
				oci_bind_by_name($result, ":RCVR_TEL_NO3", $RCVR_TEL_NO3);
				oci_bind_by_name($result, ":RCVR_CELL_NO1", $RCVR_CELL_NO1);
				oci_bind_by_name($result, ":RCVR_CELL_NO2", $RCVR_CELL_NO2);
				oci_bind_by_name($result, ":RCVR_CELL_NO3", $RCVR_CELL_NO3);
				oci_bind_by_name($result, ":RCVR_ZIP_NO", $RCVR_ZIP_NO);
				oci_bind_by_name($result, ":RCVR_ADDR", $RCVR_ADDR);
				oci_bind_by_name($result, ":RCVR_DETAIL_ADDR", $RCVR_DETAIL_ADDR);
				oci_bind_by_name($result, ":INVC_NO", $INVC_NO);
				oci_bind_by_name($result, ":PRT_ST", $PRT_ST);
				oci_bind_by_name($result, ":GDS_NM", $GDS_NM);
				oci_bind_by_name($result, ":DLV_DV", $DLV_DV);
				oci_bind_by_name($result, ":EAI_PRGS_ST", $EAI_PRGS_ST);
				oci_bind_by_name($result, ":REG_EMP_ID", $REG_EMP_ID);
				oci_bind_by_name($result, ":MODI_EMP_ID", $MODI_EMP_ID);

				$success = oci_execute($result);
				if(!$success) {
					$data_state = "N";
					$msg = "CJ OPEN DB 요청 실패";
					//echo $msg."<br />";
				}
				if($RCVR_NM == "마마무4") {
					$data_state = "N";
					$msg        = "CJ OPEN DB 요청 실패22";
				}


				oci_free_statement($result);
				oci_close($conn);

			} else {
				$data_state = "N";
				$msg = "CJ OPEN DB 연결 실패";
				//echo $msg."<br />";

			}
			$query = "
				UPDATE ".$DB['cj_log_dbname'].".dbo.DY_CJ_DELIVERY SET
				data_state = N'$data_state', msg = N'$msg'
				WHERE cj_delivery_idx = '$cj_delivery_idx'
			";
			parent::db_connect();
			parent::execSqlUpdate($query);
			parent::db_close();
		}
		//endregion

		$arrRet["result"] = ($data_state == "Y" ? true : false);
		$arrRet["cj_delivery_idx"] = $cj_delivery_idx;
		$arrRet["ret_msg"] = $msg;

		return $arrRet;
	}

	/**
	 * CJ API 를 통해 주소 정제
	 * @param array ($order_pack_idx, $receive_addr)
	 * @return array
	 */
	public function repCJAddress($args) {
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

	/**
	 * 인쇄 했을때 로그 저장 후 재출력 갯수 Return
	 * @param $args
	 * @return $print_count (해당 송장으로 재출력된 갯수)
	 */
	public function insertInvoicePrintLog($args) {

		$order_pack_idx = "";
		extract($args);

		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$print_count = 0;

		$qry = "
			SELECT COUNT(print_log_idx) print_count FROM DY_INVOICE_PRINT_LOG
			WHERE invoice_no = N'".$invoice_no."'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		$print_count = (int)$rst["print_count"];

		$qry = "
			Insert Into DY_INVOICE_PRINT_LOG (
				print_date,print_date_count,order_pack_idx,invoice_no,
				print_count, print_type,
				product_option_names,receive_name,receive_zipcode,receive_addr,receive_memo,
				receive_hp_num,receive_tp_num,delivery_code,delivery_name,
				p_clsfcd,p_subclsfcd,p_clsfaddr,p_clldlcbranshortnm,p_clldlvempnm,
				p_clldlvempnicknm,p_prngdivcd,p_farediv,p_boxtyp,delivery_fee,
				send_name,send_phone1,send_phone2,send_add,
				print_regip,last_member_idx				
			) VALUES (
				N'" . $print_date ."',
				N'" . $print_date_count ."',
				N'" . $order_pack_idx ."',
				N'" . $invoice_no ."',
				N'" . $print_count ."',
				N'" . $print_type ."',
				N'" . $product_option_names ."',
				N'" . $receive_name ."',
				N'" . $receive_zipcode ."',
				N'" . $receive_addr ."',
				N'" . $receive_memo ."',
				N'" . $receive_hp_num ."',
				N'" . $receive_tp_num ."',
				N'" . $delivery_code ."',
				N'" . $delivery_name ."',
				N'" . $p_clsfcd ."',
				N'" . $p_subclsfcd ."',
				N'" . $p_clsfaddr ."',
				N'" . $p_clldlcbranshortnm ."',
				N'" . $p_clldlvempnm ."',
				N'" . $p_clldlvempnicknm ."',
				N'" . $p_prngdivcd ."',
				N'" . $p_farediv ."',
				N'" . $p_boxtyp ."',
				N'" . $delivery_fee ."',
				N'" . $send_name ."',
				N'" . $send_phone1 ."',
				N'" . $send_phone2 ."',
				N'" . $send_add ."',				
				N'" . $modip ."',
				N'" . $last_member_idx ."'
			)
		";
		parent::db_connect();
		$print_log_idx = parent::execSqlInsert($qry);
		parent::db_close();

		if($print_count ==0 && $print_log_idx > 0) {
			// CJ OPEN DB 에 추가 하여 대한통운에 배송 요청 보냄
			$_deliver = array(
				'RCPT_DV' => "01",    // 접수구분 : 01 : 일반,  02 : 반품 (ex : 01)

				'SENDR_NM' => $send_name,    // 송화인명 : 보내는분 성명 (ex : XXX기업㈜)
				'SENDR_TEL_NO' => $send_phone1,    // 송화인전화번호 : '0'으로 시작할 것. (국번은 0으로 시작) (ex : 02)
				'SENDR_ADDR' => $send_add,    // 송화인주소 : 송화인 주소
				'RCVR_NM' => $receive_name,    // 수화인명 :  (ex : 홍길동)
				'RCVR_TEL_NO' => ($receive_hp_num != "" ? $receive_hp_num : $receive_tp_num),    // 수화인전화번호1 : 수화인전화번호 항목은 '0'으로 시작할 것. (국번은 0으로 시작) (ex : 031)
				'RCVR_ADDR' => $receive_addr,    // 수화인주소 : 수화인 주소 (

				'INVC_NO' => $invoice_no,    // 운송장번호 : 12자리, 운송장번호 채번 로직 : 3~11 범위의 수를 MOD(7) 한 결과가 12번째 수와 같아야 한다. Ex: 운송장번호 301100112233 의 경우, 3~11의 수(110011223) 을 MOD(7)한 결과가 3 이기에 적합한운송장번호이다. (ex : 301100112233)"
				'GDS_NM' => $product_option_names,    // 상품명 :  (ex : 사과쥬스1박스)
			);
			$_ret = $this->insertCJInvoice($_deliver);
			if(!$_ret["result"]) {
				$print_count = -1;
				/// **** 에러가 나서 롤백 될경우 상태를 [송장 -> 접수] 로 변경 ***
				//송장삭제
				$cs_msg         = "송장 출력 오류로 상태 변경2 [송장 -> 접수]";
				//송장번호로 송장 삭제
				$C_Order = new Order();
				$c_rst = $C_Order -> deleteOrderInvoiceByInvoiceNo($invoice_no, false, $cs_msg);
			}
		}

		return $print_count;
	}

	/**
	 * 현재 출력될 오늘 날짜 출력 차수 조회
	 * @return int
	 */
	public function getPrintDateCount() {
		$qry = "
			SELECT (ISNULL(MAX(print_date_count), 0) + 1) is_print_date_count 
			FROM DY_INVOICE_PRINT_LOG
			WHERE print_date = CONVERT(INT, CONVERT(VARCHAR, GETDATE(), 112))
			AND print_is_del = N'N' AND print_type = N'F'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();
		$print_date_count = (int)$rst["is_print_date_count"];

		return $print_date_count;
	}

	/**
	 * 조회 날짜의 출력 차수 리스트 조회
	 * @param $date
	 * @return array
	 */
	public function getPrintDateCountList($date) {
		$qry = "
			SELECT 
				print_date_count
				, COUNT(print_date_count) print_count
				, RIGHT(CONVERT(VARCHAR, MIN(print_regdate), 120), 8) print_time
			FROM DY_INVOICE_PRINT_LOG
			WHERE print_date = '".$date."' 
			AND print_is_del = N'N' AND print_type = N'F'
			GROUP BY print_date_count
			ORDER BY print_date_count DESC
		";
		parent::db_connect();
		$list = parent::execSqlList($qry);
		parent::db_close();

		return $list;
	}

	private  function  testRandomStr($len) {
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $len; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	private  function  testRandomNum($len) {
		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $len; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}
?>