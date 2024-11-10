<?php
/**
 * 자금일보 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class Report extends Dbconn
{
	/**
	 * 계정과목을 반환한다 (지출/수입)
	 * @param $inout
	 * @return array
	 */
	public function getAccountCodeList($inout)
	{
		$qry = "
			Select 
				account_idx, account_name
			From DY_ACCOUNT_CODE
			Where account_inout = N'$inout'
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 자금일보 항목, 날짜 별 목록 가져오기
	 * @param $tran_date
	 * @param $tran_type
	 * @param $tran_inout
	 * @param $period_type
	 * @param $tran_fixed
	 * @return array
	 */
	public function getReportDataByDate($tran_date, $tran_type, $tran_inout, $period_type = "day", $tran_fixed = "")
	{
		$addColumn = "";
		$addTable = "";
		if($tran_type == "BANK_CUSTOMER_IN"){
			$addColumn = ", S.seller_name as target_name ";
			$addTable = " Left Outer Join DY_SELLER S On S.seller_idx = R.target_idx ";
		}elseif($tran_type == "BANK_CUSTOMER_OUT"){
			$addColumn = ", S.supplier_name as target_name ";
			$addTable = " Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = R.target_idx ";
		}

		//수정 불가 항목에 대한 추가 쿼리
		if($tran_fixed) {
			$fixedQry = " And R.tran_fixed = N'$tran_fixed' ";
		}

		if($period_type == "day") {
			$qry = "
				Select R.*, A.account_name
				$addColumn
				From DY_SETTLE_REPORT R 
				Left Outer Join DY_ACCOUNT_CODE A On R.account_idx = A.account_idx
				$addTable
				Where R.tran_is_del = N'N' And R.tran_date = N'$tran_date' And R.tran_type = N'$tran_type' And R.tran_inout = N'$tran_inout' $fixedQry
				Order by A.account_name ASC, R.tran_regdate ASC
			";

			parent::db_connect();
			$_list = parent::execSqlList($qry);
			parent::db_close();
		}else{

			if($period_type == "week") {
				$prev_date = date('Y-m-d', strtotime("-6 days", strtotime($tran_date)));
			}elseif($period_type == "month"){
				$prev_date = date('Y', strtotime($tran_date)) . "-" . date('m', strtotime($tran_date)) . "-01";
			}

			$qry = "
				Select R.*, A.account_name
				$addColumn
				From DY_SETTLE_REPORT R 
				Left Outer Join DY_ACCOUNT_CODE A On R.account_idx = A.account_idx
				$addTable
				Where R.tran_is_del = N'N' 
				      And R.tran_date >= N'$prev_date' 
				      And R.tran_date <= N'$tran_date' 
				      And R.tran_type = N'$tran_type' And R.tran_inout = N'$tran_inout'
				      And R.tran_inout = N'$tran_inout'
					  $fixedQry
				Order by A.account_name ASC
			";

			parent::db_connect();
			$_list = parent::execSqlList($qry);
			parent::db_close();
		}

		return $_list;
	}

	public function getReportDataByMonth($tran_date, $tran_type, $tran_inout)
	{
		$date_time = strtotime($tran_date);

		//월간누계
		$date_start = date('Y', $date_time) . "-" . date('m', $date_time) . "-01";
		$qry = "
			Select 
			Sum(tran_amount) as tran_amount
			From DY_SETTLE_REPORT R 
			Where R.tran_is_del = N'N' 
			  And R.tran_date >= N'$date_start' 
			  And R.tran_date <= N'$tran_date' 
			  And R.tran_type = N'$tran_type' And R.tran_inout = N'$tran_inout'
		";

		parent::db_connect();
		$month_sum = parent::execSqlOneCol($qry);
		parent::db_close();

		//년간누계
		$date_start = date('Y', $date_time) . "-01-01";
		$date_end = date('Y-m-t', $date_time);
		$qry = "
			Select 
			Sum(tran_amount) as tran_amount
			From DY_SETTLE_REPORT R 
			Where R.tran_is_del = N'N' 
			  And R.tran_date >= N'$date_start' 
			  And R.tran_date <= N'$date_end' 
			  And R.tran_type = N'$tran_type' And R.tran_inout = N'$tran_inout'
		";

		parent::db_connect();
		$year_sum = parent::execSqlOneCol($qry);
		parent::db_close();

		$returnValue = array();

		$returnValue["month"] = array("text"=> date('m', $date_time)."월 누계", "sum" => $month_sum);
		$returnValue["year"] = array("text"=> date('Y', $date_time)."년 누계", "sum" => $year_sum);

		return $returnValue;
	}

	/**
	 * 계정과목별 합계 가져오기
	 * @param $tran_date
	 * @param $tran_inout
	 * @param string $period_type
	 * @return array
	 */
	public function getReportSumDataByAccount($tran_date, $tran_inout, $period_type = "day")
	{

		$returnValue = array();
		$returnValue["list"] = array();
		$returnValue["cash_sum"] = 0;

		$addQry = "";
		//카드 포함으로 변경 - 19.06.13
		//if($tran_inout == "OUT"){
			//$addQry = " And tran_type <> N'CARD_OUT' ";
		//}

		if($period_type == "day") {
			$qry = "
				Select *, A.account_name
				From
			     (
			       Select
			              account_idx
			              , SUM(tran_amount) as tran_amount
				   From DY_SETTLE_REPORT
			       Where tran_is_del = N'N' And tran_date = N'$tran_date' And tran_inout = N'$tran_inout'
			       $addQry
			       Group by account_idx
			     ) as R 
				Left Outer Join DY_ACCOUNT_CODE A On R.account_idx = A.account_idx
				Order by A.account_name ASC
			";

			parent::db_connect();
			$_list = parent::execSqlList($qry);
			parent::db_close();

			//지출 일 경우 현금의 합만 따로 계산
			if($tran_inout == "OUT") {
				$qry = "
					 Select
			            SUM(tran_amount) as tran_amount_sum
					   From DY_SETTLE_REPORT
				       Where tran_is_del = N'N' 
				         And tran_date = N'$tran_date' 
				         And tran_inout = N'$tran_inout' 
						 And tran_type <> N'CARD_OUT'
				";

				parent::db_connect();
				$returnValue["cash_sum"] = parent::execSqlOneCol($qry);
				parent::db_close();
			}

		}else{

			if($period_type == "week") {
				$prev_date = date('Y-m-d', strtotime("-6 days", strtotime($tran_date)));
			}elseif($period_type == "month"){
				$prev_date = date('Y', strtotime($tran_date)) . "-" . date('m', strtotime($tran_date)) . "-01";
			}

			$qry = "
				Select *, A.account_name
				From
			     (
			       Select
			              account_idx
			              , SUM(tran_amount) as tran_amount
				   From DY_SETTLE_REPORT
			       Where tran_is_del = N'N' 
			         And tran_date >= N'$prev_date' And tran_date <= N'$tran_date' 
			         And tran_inout = N'$tran_inout'
			         $addQry
			       Group by account_idx
			     ) as R 
				Left Outer Join DY_ACCOUNT_CODE A On R.account_idx = A.account_idx
				Order by A.account_name ASC
			";

			parent::db_connect();
			$_list = parent::execSqlList($qry);
			parent::db_close();

			//지출 일 경우 현금의 합만 따로 계산
			if($tran_inout == "OUT") {
				$qry = "
					 Select
			            SUM(tran_amount) as tran_amount_sum
					   From DY_SETTLE_REPORT
				       Where tran_is_del = N'N' 
			                And tran_date >= N'$prev_date' And tran_date <= N'$tran_date' 
			                And tran_inout = N'$tran_inout'
						    And tran_type <> N'CARD_OUT'
				";

				parent::db_connect();
				$returnValue["cash_sum"] = parent::execSqlOneCol($qry);
				parent::db_close();
			}
		}

		$returnValue["list"] = $_list;

		return $returnValue;
	}

	/**
	 * 자금일보 항목 입력/수정
	 * @param $tran_idx
	 * @param $tran_date
	 * @param $tran_type
	 * @param $tran_inout
	 * @param $account_idx
	 * @param $tran_memo
	 * @param $tran_amount
	 * @param $target_idx
	 * @param $tran_user
	 * @param $tran_card_no
	 * @param $tran_purpose
	 * @return bool|int|resource
	 */
	public function saveReportData($tran_idx, $tran_date, $tran_type, $tran_inout, $account_idx, $tran_memo, $tran_amount, $target_idx, $tran_user, $tran_card_no, $tran_purpose, $tran_is_sync, $tran_sync_idx = null)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		if($tran_idx){

			//자금일보 Update
			$qry = "
				Update DY_SETTLE_REPORT
				Set
					account_idx = N'$account_idx'
					, tran_memo = N'$tran_memo'
					, tran_amount = N'$tran_amount'
					, target_idx = N'$target_idx'
					, tran_user = N'$tran_user'
					, tran_card_no = N'$tran_card_no'
					, tran_purpose = N'$tran_purpose'
					, tran_moddate = getdate()
					, tran_modip = N'$modip'
					, tran_modidx = N'$last_member_idx'
				Where
					tran_idx = N'$tran_idx'
			";

			$inserted_idx = parent::execSqlUpdate($qry);

			//매출매입원장 Update
			//아래 두가지 내용일 때만 Update
			//통장입출금내역>수입(거래처별)
			//통장입출금내역>지출(거래처별)
			$is_ledger = false;
			if($tran_type == "BANK_CUSTOMER_IN") {
				//통장입출금내역>수입(거래처별)
				$is_ledger = true;
				$ledger_type          = "LEDGER_SALE";
			}elseif($tran_type == "BANK_CUSTOMER_OUT") {
				//통장입출금내역>지출(거래처별)
				$is_ledger = true;
				$ledger_type          = "LEDGER_PURCHASE";
			}

			//계정과목 이름 얻기
			$qry = "Select account_name From DY_ACCOUNT_CODE Where account_idx = N'$account_idx'";
			$account_name = parent::execSqlOneCol($qry);

			if($is_ledger) {
				$ledger_add_type      = "TRAN";
				$ledger_date          = $tran_date;
				$ledger_title         = $account_name;
				$ledger_adjust_amount = 0;
				$ledger_tran_amount   = $tran_amount;
				$ledger_refund_amount = 0;
				$ledger_memo          = $tran_memo;

				$qry = "
					Update DY_LEDGER
					Set
					    target_idx = N'$target_idx'
					    , account_idx = N'$account_idx'
					    , ledger_title = N'$ledger_title'
					    , ledger_tran_amount = N'$ledger_tran_amount'
					    , ledger_memo = N'$ledger_memo'
					WHERE tran_idx = N'$tran_idx'
				";
				$tmp = parent::execSqlUpdate($qry);
			}


		}else{
			$val_tran_is_sync = $tran_is_sync ? "N'Y'" : "N'N'";

			$col_tran_sync_idx = "";
			$val_tran_sync_idx = "";

			if ($tran_sync_idx) {
				$col_tran_sync_idx = ", tran_sync_idx";
				$val_tran_sync_idx = ", N'$tran_sync_idx'";
			}

			//자금일보 등록
			$qry = "
				Insert Into DY_SETTLE_REPORT
				(
				 tran_date, tran_type, account_idx, tran_inout, target_idx, tran_memo, tran_amount
				, tran_user, tran_card_no, tran_purpose
				, tran_regip, tran_regidx, tran_is_sync
				$col_tran_sync_idx
				)
				VALUES
				(
				 N'$tran_date'
				 , N'$tran_type'
				 , N'$account_idx'
				 , N'$tran_inout'
				 , N'$target_idx'
				 , N'$tran_memo'
				 , N'$tran_amount'
				 , N'$tran_user'
				 , N'$tran_card_no'
				 , N'$tran_purpose'
				 , N'$modip'
				 , N'$last_member_idx'
				 , $val_tran_is_sync
				 $val_tran_sync_idx
				)
			";
			$inserted_idx = parent::execSqlInsert($qry);

			//계좌간 이체 - 연동되었다면 서로 인덱스를 알 수 있도록
			if ($tran_sync_idx) {
				$this->updateReportDataSync($tran_sync_idx, $inserted_idx);
			}

			//매출매입원장 Update
			//아래 두가지 내용일 때만 등록
			//통장입출금내역>수입(거래처별)
			//통장입출금내역>지출(거래처별)
			$is_ledger = false;
			if($tran_type == "BANK_CUSTOMER_IN") {
				//통장입출금내역>수입(거래처별)
				$is_ledger = true;
				$ledger_type          = "LEDGER_SALE";
			}elseif($tran_type == "BANK_CUSTOMER_OUT") {
				//통장입출금내역>지출(거래처별)
				$is_ledger = true;
				$ledger_type          = "LEDGER_PURCHASE";
			}

			//계정과목 이름 얻기
			$qry = "Select account_name From DY_ACCOUNT_CODE Where account_idx = N'$account_idx'";
			$account_name = parent::execSqlOneCol($qry);

			if($is_ledger) {
				$ledger_add_type      = "TRAN";
				$ledger_date          = $tran_date;
				$ledger_title         = $account_name;
				$ledger_adjust_amount = 0;
				$ledger_tran_amount   = $tran_amount;
				$ledger_refund_amount = 0;
				$ledger_memo          = $tran_memo;

				$qry = "
					Insert Into DY_LEDGER
					(
					 target_idx, ledger_type, ledger_add_type, ledger_date, ledger_title
					 , ledger_adjust_amount, ledger_tran_amount, ledger_refund_amount, ledger_memo
					 , tran_idx, account_idx
					 , ledger_regip, ledger_regidx
				    )
				    VALUES 
					(
					 N'$target_idx'
					 , N'$ledger_type'
					 , N'$ledger_add_type'
					 , N'$ledger_date'
					 , N'$ledger_title'
					 , N'$ledger_adjust_amount'
					 , N'$ledger_tran_amount'
					 , N'$ledger_refund_amount'
					 , N'$ledger_memo'
					 , N'$inserted_idx'
					 , N'$account_idx'
					 , N'$modip'
					 , N'$last_member_idx'
					)
				";
				$inserted_idx3 = parent::execSqlInsert($qry);
			}
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		parent::db_close();
		return $inserted_idx;
	}


	/**
	 * 계좌간 이체 - 등록 시, 연동된 계좌 서로 지정하도록 (private)
	 * @param $tran_idx
	 * @param $tran_sync_idx
	 * @return bool|resource
	 */
	private function updateReportDataSync($tran_idx, $tran_sync_idx) {
		$qry = "SELECT COUNT(*) AS cnt FROM DY_SETTLE_REPORT WHERE tran_idx = $tran_idx";
		$cnt = parent::execSqlOneCol($qry);

		if ($cnt) {
			$qry = "UPDATE DY_SETTLE_REPORT SET tran_sync_idx = N'$tran_sync_idx' WHERE tran_idx = N'$tran_idx'";
			return parent::execSqlUpdate($qry);
		} else {
			return false;
		}
	}

	/**
	 * 계좌간이체 - 연동된 계좌간 이체 정보
	 * @param $tran_idx
	 * @return int|mixed
	 */
	public function getReportSynchronizedIdx($tran_idx) {
		parent::db_connect();

		$qry = "SELECT tran_sync_idx FROM DY_SETTLE_REPORT WHERE tran_is_del = N'N' AND tran_idx = $tran_idx";
		$rst = parent::execSqlOneCol($qry);

		parent::db_close();

		return $rst;
	}

	/**
	 * 자금일보 내역 삭제
	 * @param $tran_idx
	 * @return bool|resource
	 */
	public function deleteReportData($tran_idx){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//연결된 데이터 확인
		$qry = "
			Select * From DY_SETTLE_REPORT
			Where tran_idx = N'$tran_idx' And tran_is_del = N'N'
		";

		$_view = parent::execSqlOneRow($qry);

		if($_view) {

			//충전금연동 IDX
			$charge_idx = $_view["charge_idx"];

			//매출/매입 원장 연동 IDX
			$tran_idx = $_view["tran_idx"];

			//자금일보 삭제
			$qry = "
				Update DY_SETTLE_REPORT
					Set tran_is_del = N'Y', tran_moddate = getdate(), tran_modip = N'$modip', tran_modidx = N'$last_member_idx'
			    Where tran_idx = N'$tran_idx'
			";
			$rst = parent::execSqlUpdate($qry);


			if($charge_idx) {
				//충전금 삭제 (있을 경우)
				$qry  = "
					Update DY_MEMBER_VENDOR_CHARGE
					Set charge_is_del = N'Y', charge_moddate = getdate(), charge_modip = N'$modip', charge_modidx = N'$last_member_idx'
					Where charge_idx = N'$charge_idx'
				";
				$tmp2 = parent::execSqlUpdate($qry);


				//매출원장 삭제 (있을 경우)
				$qry = "
					Update DY_LEDGER
						Set ledger_is_del = N'Y', ledger_moddate = getdate(), ledger_modip = N'$modip', ledger_modidx = N'$last_member_idx'
						Where charge_idx = N'$charge_idx'
				";
				$tmp3 = parent::execSqlUpdate($qry);
			}


			//매출원장과 연동된 내역일 경우 삭제 (충전금 별개)
			//매출원장 삭제 (있을 경우)
			$qry = "
					Update DY_LEDGER
						Set ledger_is_del = N'Y', ledger_moddate = getdate(), ledger_modip = N'$modip', ledger_modidx = N'$last_member_idx'
						Where tran_idx = N'$tran_idx'
				";
			$tmp4 = parent::execSqlUpdate($qry);

		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		parent::db_close();

		return $rst;
	}
}
?>

