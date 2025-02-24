<?php
/**
 * 대출계좌관리 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class Loan extends Dbconn
{
	/**
	 * 계좌목록 반환
	 * @return array
	 */
	public function getLoanList()
	{
		$qry = "
			Select B.*, M.member_id
			, Convert(varchar(30), loan_regdate, 120) as bank_regdate2
			, isNull((Select Sum(tran_sum) as sum From DY_BANK_LOAN_TRANSACTION T Where T.loan_idx = B.loan_idx), 0) as loan_repayment
			
			From 
			     DY_BANK_LOAN_ACCOUNT B
				 Left Outer Join DY_MEMBER M On B.loan_regidx = M.idx
			Where loan_is_del = N'N'
			Order by loan_sort ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 계좌목록 반환2
	 * @return array
	 */
	public function getLoanList2()
	{
		$qry = "
			Select B.*
			From 
			     DY_BANK_LOAN_ACCOUNT B
				 Left Outer Join DY_MEMBER M On B.loan_regidx = M.idx
			Where loan_is_del = N'N'
			Order by loan_sort ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 계좌정보 반환
	 * @param $loan_idx
	 * @return array|false|null
	 */
	public function getLoanInfo($loan_idx)
	{
		$qry = "
			Select * From DY_BANK_LOAN_ACCOUNT B Where B.loan_is_del = N'N' And B.loan_idx = N'$loan_idx'
		";
		parent::db_connect();
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();

		return $_view;
	}

	/**
	 * 계좌 등록
	 * @param $loan_name
	 * @param $loan_amount
	 * @param $loan_detail
	 * @param $loan_sort
	 * @param $loan_is_use
	 * @return int
	 */
	public function insertLoanAccount($loan_name, $loan_amount, $loan_detail, $loan_sort, $loan_is_use)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "Select count(*) From DY_BANK_LOAN_ACCOUNT Where loan_is_del = N'N' And loan_sort = N'$loan_sort'";
		parent::db_connect();
		$cnt = parent::execSqlOneCol($qry);
		parent::db_close();

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		if($cnt > 0){
			$qry = "
				Update DY_BANK_LOAN_ACCOUNT
				Set loan_sort = loan_sort + 1
				Where loan_sort >= N'$loan_sort'
			";

			parent::execSqlUpdate($qry);
		}

		$qry = "
			Insert Into DY_BANK_LOAN_ACCOUNT
			(loan_name, loan_amount, loan_detail, loan_sort, loan_is_use, loan_regip, loan_regidx) 
			VALUES
			(
			 N'$loan_name'
			 , N'$loan_amount'
			 , N'$loan_detail'
			 , N'$loan_sort'
			 , N'$loan_is_use'
			 , N'$modip'
			 , N'$last_member_idx'
			)
		";

		$inserted_idx = parent::execSqlInsert($qry);

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		parent::db_close();

		return $inserted_idx;
	}

	/**
	 * 계좌 수정
	 * @param $loan_idx
	 * @param $loan_name
	 * @param $loan_amount
	 * @param $loan_detail
	 * @param $loan_is_use
	 * @return bool|resource
	 */
	public function updateLoanAccount($loan_idx, $loan_name, $loan_amount, $loan_detail, $loan_is_use)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_BANK_LOAN_ACCOUNT
			Set
			    loan_name = N'$loan_name'
				, loan_amount = N'$loan_amount'
				, loan_detail = N'$loan_detail'
				, loan_is_use = N'$loan_is_use'
			Where loan_idx = N'$loan_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 계좌 입출금내역 반환
	 * @param $date
	 * @return array
	 */
	public function getTodayLoanTransaction($date)
	{
		$qry = "
			Select
			B.loan_idx, B.loan_name
			, isNull(T.tran_in, 0) as tran_in
			, isNull(T.tran_out, 0) as tran_out
			, isNull(T.tran_sum, 0) as tran_sum
		    , isNull(T.tran_memo, '') as tran_memo
			From DY_BANK_LOAN_ACCOUNT B
				Left Outer Join DY_BANK_LOAN_TRANSACTION T On T.tran_is_del = N'N' And T.tran_date = convert(date, N'$date') And T.loan_idx = B.loan_idx
			Where B.loan_is_del = N'N'
			Order by B.loan_sort ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 계좌 입출금내역 등록/수정
	 * @param $date
	 * @param $loan_idx
	 * @param $tran_in
	 * @param $tran_out
	 * @param $tran_memo
	 * @return bool|resource
	 */
	public function saveTodayLoanTransaction($date, $loan_idx, $tran_in, $tran_out, $tran_memo)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		if(!is_numeric($tran_in)) $tran_in = 0;
		if(!is_numeric($tran_out)) $tran_out = 0;

		$tran_sum = $tran_in - $tran_out;

		$qry = "
			IF exists (select * From DY_BANK_LOAN_TRANSACTION Where tran_is_del = N'N' And loan_idx = N'$loan_idx' And tran_date = N'$date')
			Begin
				Update DY_BANK_LOAN_TRANSACTION
					Set 
						tran_in = N'$tran_in'
						, tran_out = N'$tran_out'
						, tran_sum = N'$tran_sum'
						, tran_memo = N'$tran_memo'
						, tran_modate = getdate()
						, tran_modip = N'$modip'
						, tran_modidx = N'$last_member_idx'
					Where
						tran_is_del = N'N' And loan_idx = N'$loan_idx' And tran_date = N'$date'
			End
			ELSE
			Begin
				Insert Into DY_BANK_LOAN_TRANSACTION
				(tran_date, loan_idx, tran_in, tran_out, tran_sum, tran_memo, tran_regip, tran_regidx)
				VALUES
				(
				N'$date'
				, N'$loan_idx'
				, N'$tran_in'
				, N'$tran_out'
				, N'$tran_sum'
				, N'$tran_memo'
				, N'$modip'
				, N'$last_member_idx'
				)
			End
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;

	}

	public function getTodayLoanTransactionDetail($period_type, $date)
	{

		if($period_type == "day") {
			$qry = "
				WITH CTE_TODAY AS (
					Select
						loan_idx
						, tran_date
						, tran_in
						, tran_out
						, tran_sum
						, tran_memo
					From DY_BANK_LOAN_TRANSACTION
					Where tran_is_del = N'N' And tran_date = N'$date'
				), 
				CTE_PREV_TOTAL AS (
					Select
						loan_idx
						, isNull(SUM(tran_sum), 0) as prev_sum
					From DY_BANK_LOAN_TRANSACTION
					Where tran_is_del = N'N' And tran_date < N'$date'
					Group by loan_idx
				)
				
				Select
				BANK.loan_name
				, BANK.loan_idx
				, BANK.loan_amount
				, BANK.loan_detail
				, isNull(T.tran_in, 0) as tran_in
				, isNull(T.tran_out, 0) as tran_out
				, isNull(T.tran_sum, 0) as tran_sum
				, isNull(T.tran_memo, '') as tran_memo
				, isNull(P.prev_sum, 0) as prev_sum
				, BANK.loan_amount - isNull(P.prev_sum, 0) as yesterday_remain
				, BANK.loan_amount - (isNull(P.prev_sum, 0) + isNull(T.tran_sum, 0)) as today_remain
				From 
				DY_BANK_LOAN_ACCOUNT BANK
				Left Outer Join CTE_TODAY T On BANK.loan_idx = T.loan_idx
				Left Outer Join CTE_PREV_TOTAL P On BANK.loan_idx = P.loan_idx
				Where 1 = 1
				
				Order by BANK.loan_sort ASC
			";

			parent::db_connect();
			$_list = parent::execSqlList($qry);
			parent::db_close();
		}elseif($period_type == "week" || $period_type == "month"){

			if($period_type == "week") {
				$prev_date = date('Y-m-d', strtotime("-6 days", strtotime($date)));
			}elseif($period_type == "month"){
				$prev_date = date('Y', strtotime($date)) . "-" . date('m', strtotime($date)) . "-01";
			}


			$qry = "
				WITH CTE_TODAY AS (
					Select
						loan_idx
						, isNull(Sum(tran_in), 0) as tran_in 
						, isNull(Sum(tran_out), 0) as tran_out
						, isNull(Sum(tran_sum), 0) as tran_sum
						, '' as tran_memo
					From DY_BANK_LOAN_TRANSACTION
					Where tran_is_del = N'N' And tran_date >= N'$prev_date' And tran_date <= N'$date'
					Group by loan_idx
				), 
				CTE_PREV_TOTAL AS (
					Select
						loan_idx
						, isNull(SUM(tran_sum), 0) as prev_sum
					From DY_BANK_LOAN_TRANSACTION
					Where tran_is_del = N'N' And tran_date < N'$prev_date'
					Group by loan_idx
				)
				
				Select
				BANK.loan_name
				, BANK.loan_idx
				, BANK.loan_amount
				, BANK.loan_detail
				, isNull(T.tran_in, 0) as tran_in
				, isNull(T.tran_out, 0) as tran_out
				, isNull(T.tran_sum, 0) as tran_sum
				, isNull(T.tran_memo, '') as tran_memo
				, isNull(P.prev_sum, 0) as prev_sum
				From 
				DY_BANK_LOAN_ACCOUNT BANK
				Left Outer Join CTE_TODAY T On BANK.loan_idx = T.loan_idx
				Left Outer Join CTE_PREV_TOTAL P On BANK.loan_idx = P.loan_idx
				Where 1 = 1
				Order By BANK.loan_sort ASC
			";

			parent::db_connect();
			$_list = parent::execSqlList($qry);
			parent::db_close();

		}


		return $_list;
	}

	/**
	 * 계좌들 중 가장 큰 순서 다음 값 얻어오기
	 * @return int|mixed
	 */
	public function getNextSortNum()
	{
		$qry = "
			Select Max(loan_sort) as bank_sort From DY_BANK_LOAN_ACCOUNT Where loan_is_del = N'N'
		";

		parent::db_connect();
		$sort = parent::execSqlOneCol($qry);
		parent::db_close();

		return ($sort) ? $sort+1 : 1;
	}

	/*
	 * 계좌 순서 변경 가능 여부 반환
	 * $args
	 * out : Array (result[boolean] : 가능 여부, msg[string] : 불가능 시 메시지)
	 */
	public function checkCanSortChange($args)
	{
		$idx = "";
		$dir = "";
		extract($args);

		$result = false;
		$msg = "";

		$bankInfo = $this->getLoanInfo($idx);

		$sort = $bankInfo["loan_sort"];

		if($bankInfo) {

			if ($dir == "up") {
				$qry = "
					Select loan_sort From DY_BANK_LOAN_ACCOUNT Where loan_is_del = 'N' And loan_idx = '" . $idx . "'
				";
				parent::db_connect();
				$rst = parent::execSqlOneCol($qry);
				parent::db_close();

				if ($rst) {
					if ($rst > 1) {
						$result = true;
					} else {
						$result = false;
						$msg = "이미 최상위입니다.";
					}
				} else {
					$result = false;
					$msg = "존재하지 않는 계좌입니다.";
				}

			} elseif ($dir == "dn") {
				$qry = "
				  Select count(*) From DY_BANK_LOAN_ACCOUNT 
					Where loan_is_del = 'N' 
							And loan_sort > '" . $sort . "'
				";
				parent::db_connect();
				$rst = parent::execSqlOneCol($qry);
				parent::db_close();

				if ($rst == 0) {
					$result = false;
					$msg = "이미 최하위입니다.";
				} else {
					$result = true;
				}
			}
		}else{
			$result = false;
			$msg = "존재하지 않는 계좌입니다.";
		}

		$rst = array("result"=>$result, "msg"=>$msg);
		return $rst;
	}

	/*
	 * 계좌 순서 변경
	 * $args
	 * out : boolean
	 */
	public function moveBankSort($args)
	{
		$idx = "";
		$dir = "";
		extract($args);

		$bankInfo = $this->getLoanInfo($idx);

		$sort = $bankInfo["loan_sort"];

		if($dir == "up")
		{

			$qry = "
				Update DY_BANK_LOAN_ACCOUNT
					Set loan_sort = loan_sort + 1 
					Where loan_sort = '".($sort-1)."' 
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

			$qry = "
				Update DY_BANK_LOAN_ACCOUNT
				Set loan_sort = loan_sort - 1
				Where loan_idx = '".$idx."'
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

		}elseif($dir == "dn"){

			$qry = "
				Update DY_BANK_LOAN_ACCOUNT
					Set loan_sort = loan_sort - 1 
					Where loan_sort = '".($sort+1)."' 
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

			$qry = "
				Update DY_BANK_LOAN_ACCOUNT
				Set loan_sort = loan_sort + 1
				Where loan_idx = '".$idx."'
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

		}
	}
}
?>

