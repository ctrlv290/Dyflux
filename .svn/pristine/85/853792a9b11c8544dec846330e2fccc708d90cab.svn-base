<?php
/**
 * 계좌관리 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class Bank extends Dbconn
{
	/**
	 * 계좌목록 반환
	 * @return array
	 */
	public function getBankList()
	{
		$qry = "
			Select B.* , C.code_name as bank_type_han, M.member_id
			, Convert(varchar(30), bank_regdate, 120) as bank_regdate2
			From 
			     DY_BANK_ACCOUNT B
				 Left Outer Join DY_CODE C On C.parent_code = N'BANK_TYPE' And B.bank_type = C.code
				 Left Outer Join DY_MEMBER M On B.bank_regidx = M.idx
			Where bank_is_del = N'N'
			Order by bank_sort ASC
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
	public function getBankList2()
	{
		$qry = "
			Select B.*
			From 
			     DY_BANK_ACCOUNT B
				 Left Outer Join DY_CODE C On C.parent_code = N'BANK_TYPE' And B.bank_type = C.code
				 Left Outer Join DY_MEMBER M On B.bank_regidx = M.idx
			Where bank_is_del = N'N'
			Order by bank_sort ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 계좌정보 반환
	 * @param $bank_idx
	 * @return array|false|null
	 */
	public function getBankInfo($bank_idx)
	{
		$qry = "
			Select * From DY_BANK_ACCOUNT B Where B.bank_is_del = N'N' And B.bank_idx = N'$bank_idx'
		";
		parent::db_connect();
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();

		return $_view;
	}

	/**
	 * 계좌 등록
	 * @param $bank_type
	 * @param $bank_name
	 * @param $bank_sort
	 * @param $bank_is_use
	 * @return int
	 */
	public function insertBankAccount($bank_type, $bank_name, $bank_sort, $bank_is_use)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "Select count(*) From DY_BANK_ACCOUNT Where bank_is_del = N'N' And bank_sort = N'$bank_sort'";
		parent::db_connect();
		$cnt = parent::execSqlOneCol($qry);
		parent::db_close();

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		if($cnt > 0){
			$qry = "
				Update DY_BANK_ACCOUNT
				Set bank_sort = bank_sort + 1
				Where bank_sort >= N'$bank_sort'
			";

			parent::execSqlUpdate($qry);
		}

		$qry = "
			Insert Into DY_BANK_ACCOUNT
			(bank_type, bank_name, bank_sort, bank_is_use, bank_regip, bank_regidx) 
			VALUES
			(
			 N'$bank_type'
			 , N'$bank_name'
			 , N'$bank_sort'
			 , N'$bank_is_use'
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
	 * @param $bank_idx
	 * @param $bank_type
	 * @param $bank_name
	 * @param $bank_is_use
	 * @return bool|resource
	 */
	public function updateBankAccount($bank_idx, $bank_type, $bank_name, $bank_is_use)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_BANK_ACCOUNT
			Set
			    bank_type = N'$bank_type'
				, bank_name = N'$bank_name'
				, bank_is_use = N'$bank_is_use'
			Where bank_idx = N'$bank_idx'
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
	public function getTodayBankTransaction($date)
	{
		$qry = "
			Select
			B.bank_idx, B.bank_name, C.code_name as bank_type_han
			, isNull(T.tran_in, 0) as tran_in
			, isNull(T.tran_out, 0) as tran_out
			, isNull(T.tran_sum, 0) as tran_sum
		    , isNull(T.tran_memo, '') as tran_memo
			From DY_BANK_ACCOUNT B
				Left Outer Join DY_BANK_TRANSACTION T On T.tran_is_del = N'N' And T.tran_date = convert(date, N'$date') And T.bank_idx = B.bank_idx
				Left Outer Join DY_CODE C On C.parent_code = N'BANK_TYPE' And B.bank_type = C.code
			Where B.bank_is_del = N'N'
			Order by B.bank_type ASC, B.bank_sort ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 계좌 입출금내역 등록/수정
	 * @param $date
	 * @param $bank_idx
	 * @param $tran_in
	 * @param $tran_out
	 * @param $tran_memo
	 * @return bool|resource
	 */
	public function saveTodayBankTransaction($date, $bank_idx, $tran_in, $tran_out, $tran_memo)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		if(!is_numeric($tran_in)) $tran_in = 0;
		if(!is_numeric($tran_out)) $tran_out = 0;

		$tran_sum = $tran_in - $tran_out;

		$qry = "
			IF exists (select * From DY_BANK_TRANSACTION Where tran_is_del = N'N' And bank_idx = N'$bank_idx' And tran_date = N'$date')
			Begin
				Update DY_BANK_TRANSACTION
					Set 
						tran_in = N'$tran_in'
						, tran_out = N'$tran_out'
						, tran_sum = N'$tran_sum'
						, tran_memo = N'$tran_memo'
						, tran_modate = getdate()
						, tran_modip = N'$modip'
						, tran_modidx = N'$last_member_idx'
					Where
						tran_is_del = N'N' And bank_idx = N'$bank_idx' And tran_date = N'$date'
			End
			ELSE
			Begin
				Insert Into DY_BANK_TRANSACTION
				(tran_date, bank_idx, tran_in, tran_out, tran_sum, tran_memo, tran_regip, tran_regidx)
				VALUES
				(
				N'$date'
				, N'$bank_idx'
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

	public function getTodayBankTransactionDetail($period_type, $date, $bank_type)
	{

		if($period_type == "day") {
			$qry = "
				WITH CTE_TODAY AS (
					Select
						bank_idx
						, tran_date
						, tran_in
						, tran_out
						, tran_sum
						, tran_memo
					From DY_BANK_TRANSACTION
					Where tran_is_del = N'N' And tran_date = N'$date'
				), 
				CTE_PREV_TOTAL AS (
					Select
						bank_idx
						, isNull(SUM(tran_sum), 0) as prev_sum
					From DY_BANK_TRANSACTION
					Where tran_is_del = N'N' And tran_date < N'$date'
					Group by bank_idx
				)
				
				Select
				BANK.bank_name
				, BANK.bank_idx
				, isNull(T.tran_in, 0) as tran_in
				, isNull(T.tran_out, 0) as tran_out
				, isNull(T.tran_sum, 0) as tran_sum
				, isNull(T.tran_memo, '') as tran_memo
				, isNull(P.prev_sum, 0) as prev_sum
				From 
				DY_BANK_ACCOUNT BANK
				Left Outer Join CTE_TODAY T On BANK.bank_idx = T.bank_idx
				Left Outer Join CTE_PREV_TOTAL P On BANK.bank_idx = P.bank_idx
				Where BANK.bank_type = N'$bank_type'
				
				Order by BANK.bank_sort ASC
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
						bank_idx
						, isNull(Sum(tran_in), 0) as tran_in 
						, isNull(Sum(tran_out), 0) as tran_out
						, isNull(Sum(tran_sum), 0) as tran_sum
						, '' as tran_memo
					From DY_BANK_TRANSACTION
					Where tran_is_del = N'N' And tran_date >= N'$prev_date' And tran_date <= N'$date'
					Group by bank_idx
				), 
				CTE_PREV_TOTAL AS (
					Select
						bank_idx
						, isNull(SUM(tran_sum), 0) as prev_sum
					From DY_BANK_TRANSACTION
					Where tran_is_del = N'N' And tran_date < N'$prev_date'
					Group by bank_idx
				)
				
				Select
				BANK.bank_name
				, BANK.bank_idx
				, isNull(T.tran_in, 0) as tran_in
				, isNull(T.tran_out, 0) as tran_out
				, isNull(T.tran_sum, 0) as tran_sum
				, isNull(T.tran_memo, '') as tran_memo
				, isNull(P.prev_sum, 0) as prev_sum
				From 
				DY_BANK_ACCOUNT BANK
				Left Outer Join CTE_TODAY T On BANK.bank_idx = T.bank_idx
				Left Outer Join CTE_PREV_TOTAL P On BANK.bank_idx = P.bank_idx
				Where BANK.bank_type = N'$bank_type'
				Order By BANK.bank_sort ASC
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
			Select Max(bank_sort) as bank_sort From DY_BANK_ACCOUNT Where bank_is_del = N'N'
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

		$bankInfo = $this->getBankInfo($idx);

		$sort = $bankInfo["bank_sort"];

		if($bankInfo) {

			if ($dir == "up") {
				$qry = "
					Select bank_sort From DY_BANK_ACCOUNT Where bank_is_del = 'N' And bank_idx = '" . $idx . "'
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
				  Select count(*) From DY_BANK_ACCOUNT 
					Where bank_is_del = 'N' 
							And bank_sort > '" . $sort . "'
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

		$bankInfo = $this->getBankInfo($idx);

		$sort = $bankInfo["bank_sort"];

		if($dir == "up")
		{

			$qry = "
				Update DY_BANK_ACCOUNT
					Set bank_sort = bank_sort + 1 
					Where bank_sort = '".($sort-1)."' 
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

			$qry = "
				Update DY_BANK_ACCOUNT
				Set bank_sort = bank_sort - 1
				Where bank_idx = '".$idx."'
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

		}elseif($dir == "dn"){

			$qry = "
				Update DY_BANK_ACCOUNT
					Set bank_sort = bank_sort - 1 
					Where bank_sort = '".($sort+1)."' 
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

			$qry = "
				Update DY_BANK_ACCOUNT
				Set bank_sort = bank_sort + 1
				Where bank_idx = '".$idx."'
			";
			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();

		}
	}
}
?>

