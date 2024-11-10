<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 재고 발주 관련 Process
 */

//Page Info
$pageMenuIdx = 117;
//Init
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["data"] = array();
$response["msg"] = "";

$mode = $_POST["mode"];

if($mode == "stock_receiving_all"){
	//전체 입고 처리

	$stock_idx = $_POST["stock_idx"];

	$C_Stock = new Stock();

	//현재 재고 상태 확인
	$_view = $C_Stock -> getStockData($stock_idx);
	if($_view){

		//처리 여부 확인
		if($_view["stock_is_proc"] == "N"){

			//전체 입고 처리!!
			$rst = $C_Stock -> setStockProcAll($stock_idx);

			$response["result"] = true;
		}else{
			$response["msg"] = "이미 처리된 재고입니다.";
		}

	}else{
		$response["msg"] = "잘못된 재고 데이터 입니다.";
	}

	echo json_encode($response);
	exit;
}elseif($mode == "stock_receiving_partial"){
	//부분입고처리

	$stock_idx = $_POST["stock_idx"];
	$stock_ref_idx = $_POST["stock_ref_idx"];
	$stock_file_idx = $_POST["stock_file_idx"];

	$C_Stock = new Stock();

	//print_r2($_POST);
	/**
	 * 부분 입고를 위한 각 항목 별 체크
	 */
	$stock_data_list = array();

	//정상
	$stock_amount_normal = str_replace(",", "", $_POST["stock_amount_normal"]);
	$stock_in_date_normal = $_POST["stock_in_date_normal"];
	$stock_msg_normal = $_POST["stock_msg_normal"];

	if($stock_amount_normal == ""){
		$stock_amount_normal = 0;
	}

	if(!is_numeric($stock_amount_normal)){
		put_msg_and_back("정상수량이 정확하지 않습니다.");
	}

	$stock_data_list[] = array(
		"code" => "NORMAL",
		"amount" => $stock_amount_normal,
		"date" => $stock_in_date_normal,
		"msg" => $stock_msg_normal,
	);

	//불량
	$stock_amount_bad = str_replace(",", "", $_POST["stock_amount_bad"]);
	$stock_in_date_bad = $_POST["stock_in_date_bad"];
	$stock_msg_bad = $_POST["stock_msg_bad"];

	if(is_numeric($stock_amount_bad) && $stock_amount_bad != "0" && $stock_amount_bad != ""){
		$stock_data_list[] = array(
			"code" => "BAD",
			"amount" => $stock_amount_bad,
			"date" => $stock_in_date_bad,
			"msg" => $stock_msg_bad,
		);
	}

	//양품
	$stock_amount_abnormal = str_replace(",", "", $_POST["stock_amount_abnormal"]);
	$stock_in_date_abnormal = $_POST["stock_in_date_abnormal"];
	$stock_msg_abnormal = $_POST["stock_msg_abnormal"];

	if(is_numeric($stock_amount_abnormal) && $stock_amount_abnormal != "0" && $stock_amount_abnormal != ""){
		$stock_data_list[] = array(
			"code" => "ABNORMAL",
			"amount" => $stock_amount_abnormal,
			"date" => $stock_in_date_abnormal,
			"msg" => $stock_msg_abnormal,
		);
	}

	//부족
	$stock_amount_shortage = str_replace(",", "", $_POST["stock_amount_shortage"]);
	$stock_in_date_shortage = $_POST["stock_in_date_shortage"];
	$stock_msg_shortage = $_POST["stock_msg_shortage"];

	if(is_numeric($stock_amount_shortage) && $stock_amount_shortage != "0" && $stock_amount_shortage != ""){
		$stock_data_list[] = array(
			"code" => "SHORTAGE",
			"amount" => $stock_amount_shortage,
			"date" => $stock_in_date_shortage,
			"msg" => $stock_msg_shortage,
		);
	}

	//교환
	$stock_amount_exchange = str_replace(",", "", $_POST["stock_amount_exchange"]);
	$stock_in_date_exchange = $_POST["stock_in_date_exchange"];
	$stock_msg_exchange = $_POST["stock_msg_exchange"];

	if(is_numeric($stock_amount_exchange) && $stock_amount_exchange != "0" && $stock_amount_exchange != ""){
		$stock_data_list[] = array(
			"code" => "EXCHANGE",
			"amount" => $stock_amount_exchange,
			"date" => $stock_in_date_exchange,
			"msg" => $stock_msg_exchange,
		);
	}

	//분실
	$stock_amount_loss = str_replace(",", "", $_POST["stock_amount_loss"]);
	$stock_in_date_loss = $_POST["stock_in_date_loss"];
	$stock_msg_loss = $_POST["stock_msg_loss"];

	if(is_numeric($stock_amount_loss) && $stock_amount_loss != "0" && $stock_amount_loss != ""){
		$stock_data_list[] = array(
			"code" => "LOSS",
			"amount" => $stock_amount_loss,
			"date" => $stock_in_date_loss,
			"msg" => $stock_msg_loss,
		);
	}

	//출고지회송 - 교환회송
	$stock_amount_fac_return_exchange = str_replace(",", "", $_POST["stock_amount_fac_return_exchange"]);
	$stock_in_date_fac_return_exchange = $_POST["stock_in_date_fac_return_exchange"];
	$stock_msg_fac_return_exchange = $_POST["stock_msg_fac_return_exchange"];

	if(is_numeric($stock_amount_fac_return_exchange) && $stock_amount_fac_return_exchange != "0" && $stock_amount_fac_return_exchange != ""){
		$stock_data_list[] = array(
			"code" => "FAC_RETURN_EXCHNAGE",
			"amount" => $stock_amount_fac_return_exchange,
			"date" => $stock_in_date_fac_return_exchange,
			"msg" => $stock_msg_fac_return_exchange,
		);
	}

	//출고지회송 - 반품회송
	$stock_amount_fac_return_back = str_replace(",", "", $_POST["stock_amount_fac_return_back"]);
	$stock_in_date_fac_return_back = $_POST["stock_in_date_fac_return_back"];
	$stock_msg_fac_return_back = $_POST["stock_msg_fac_return_back"];

	if(is_numeric($stock_amount_fac_return_back) && $stock_amount_fac_return_back != "0" && $stock_amount_fac_return_back != ""){
		$stock_data_list[] = array(
			"code" => "FAC_RETURN_BACK",
			"amount" => $stock_amount_fac_return_back,
			"date" => $stock_in_date_fac_return_back,
			"msg" => $stock_msg_fac_return_back,
		);
	}

	//구매자회송 - 교환불가회송
	$stock_amount_buyer_out_no_exchange = str_replace(",", "", $_POST["stock_amount_buyer_out_no_exchange"]);
	$stock_in_date_buyer_out_no_exchange = $_POST["stock_in_date_buyer_out_no_exchange"];
	$stock_msg_buyer_out_no_exchange = $_POST["stock_msg_buyer_out_no_exchange"];

	if(is_numeric($stock_amount_buyer_out_no_exchange) && $stock_amount_buyer_out_no_exchange != "0" && $stock_amount_buyer_out_no_exchange != ""){
		$stock_data_list[] = array(
			"code" => "BAD_OUT_EXCHANGE",
			"amount" => $stock_amount_buyer_out_no_exchange,
			"date" => $stock_in_date_buyer_out_no_exchange,
			"msg" => $stock_msg_buyer_out_no_exchange,
		);
	}

	//구매자회송 - 반품불가회송
	$stock_amount_buyer_out_no_back = str_replace(",", "", $_POST["stock_amount_buyer_out_no_back"]);
	$stock_in_date_buyer_out_no_back = $_POST["stock_in_date_buyer_out_no_back"];
	$stock_msg_buyer_out_no_back = $_POST["stock_msg_buyer_out_no_back"];

	if(is_numeric($stock_amount_buyer_out_no_back) && $stock_amount_buyer_out_no_back != "0" && $stock_amount_buyer_out_no_back != ""){
		$stock_data_list[] = array(
			"code" => "FAC_RETURN_BACK",
			"amount" => $stock_amount_buyer_out_no_back,
			"date" => $stock_in_date_buyer_out_no_back,
			"msg" => $stock_msg_buyer_out_no_back,
		);
	}

	//보류
	$stock_amount_hold = str_replace(",", "", $_POST["stock_amount_hold"]);
	$stock_in_date_hold = $_POST["stock_in_date_hold"];
	$stock_msg_hold = $_POST["stock_msg_hold"];

	if(is_numeric($stock_amount_hold) && $stock_amount_hold != "0" && $stock_amount_hold != ""){
		$stock_data_list[] = array(
			"code" => "HOLD",
			"amount" => $stock_amount_hold,
			"date" => $stock_in_date_hold,
			"msg" => $stock_msg_hold,
		);
	}

	//기타처리
	$stock_amount_etc = str_replace(",", "", $_POST["stock_amount_etc"]);
	$stock_in_date_etc = $_POST["stock_in_date_etc"];
	$stock_msg_etc = $_POST["stock_msg_etc"];

	if(is_numeric($stock_amount_etc) && $stock_amount_etc != "0" && $stock_amount_etc != ""){
		$stock_data_list[] = array(
			"code" => "ETC",
			"amount" => $stock_amount_etc,
			"date" => $stock_in_date_etc,
			"msg" => $stock_msg_etc,
		);
	}

	//현재 재고 상태 확인
	$_view = $C_Stock -> getStockData($stock_idx);
	if($_view){

		//처리 여부 확인
		if($_view["stock_is_proc"] == "N"){

			//부분 입고 처리!!
			$rst = $C_Stock -> setStockProcPartial($stock_idx, $stock_data_list, 0);

			if($rst) {
				//첨부파일 Update
				if($stock_file_idx) {
					$C_Files                   = new Files();
					$argsFile                  = array();
					$argsFile["file_idx"]      = $stock_file_idx;
					$argsFile["ref_table_idx"] = $stock_ref_idx;
					$tmp                       = $C_Files->updateFileActive($argsFile);
				}

				$response["result"] = true;
			}else{
				$response["msg"] = "오류가 발생하였습니다.";
			}
		}else{
			$response["msg"] = "이미 처리된 재고입니다.";
		}

	}else{
		$response["msg"] = "잘못된 재고 데이터 입니다.";
	}

	if($response["result"]){
		$script = "
			try{
				opener.StockDue.StockDueListReload();
			}catch(e){}
			try{
				opener.StockDue.StockDelayListReload();
			}catch(e){}
		";
		put_msg_and_exec_script_and_close("입고 처리가 완료되었습니다.", $script);
	}else{
		put_msg_and_back($response["msg"]);
	}
}elseif($mode == "regist_stock_due_delay"){
	//입고 지연 등록

	$stock_idx                = $_POST["stock_idx"];
	$stock_due_delay_date     = $_POST["stock_due_delay_date"];
	$stock_due_delay_msg      = $_POST["stock_due_delay_msg"];
	$stock_due_delay_file_idx = $_POST["stock_due_delay_file_idx"];

	$C_Stock = new Stock();

	//print_r2($_POST);
	$args                              = array();
	$args["stock_idx"]                 = $stock_idx;
	$args["stock_due_delay_date"]      = $stock_due_delay_date;
	$args["stock_due_delay_msg"]       = $stock_due_delay_msg;
	$args["stock_due_delay_file_idx"]  = $stock_due_delay_file_idx;

	$inserted_idx = $C_Stock->insertStockDueDelay($args);

	if($inserted_idx) {
		//첨부파일 Update
		$C_Files                   = new Files();
		$argsFile                  = array();
		$argsFile["file_idx"]      = $stock_due_delay_file_idx;
		$argsFile["ref_table_idx"] = $inserted_idx;
		$tmp                       = $C_Files->updateFileActive($argsFile);

		$script = "
			try{
				opener.StockDue.StockDueListReload();
			}catch(e){}
			try{
				opener.StockDue.StockDelayListReload();
			}catch(e){}
		";
		put_msg_and_exec_script_and_close("지연입고 등록처리가 완료되었습니다.", $script);
	}else{
		put_msg_and_back("등록에 실패하였습니다.");
	}

}elseif($mode == "stock_due_delay_confirm"){
	//입고지연 확인

	$stock_due_delay_idx = $_POST["stock_due_delay_idx"];

	$C_Stock = new Stock();
	$inserted_idx = $C_Stock -> insertStockDueDelayConfirm($stock_due_delay_idx);

	if($inserted_idx){
		$response["result"] = true;
	}else{
		$response["msg"] = "이미 확인하였습니다.";
	}

	echo json_encode($response);
	exit;

}elseif($mode == "stock_due_add"){
	//추가입고

	$stock_idx        = $_POST["stock_idx"];
	$stock_due_date   = $_POST["stock_due_date"];
	$stock_due_amount = $_POST["stock_due_amount"];
	$stock_msg        = $_POST["stock_msg"];

	$stock_due_amount = str_replace(",", "", $stock_due_amount);

	$C_Stock = new Stock();
	$inserted_idx = $C_Stock -> insertStockOrderAdd($stock_idx, $stock_due_date, $stock_due_amount, $stock_msg);

	if($inserted_idx){
		$script = "
			try{
				opener.StockDue.StockDueListReload();
			}catch(e){}
			try{
				opener.StockDue.StockDelayListReload();
			}catch(e){}
		";
		put_msg_and_exec_script_and_close("추가입고 처리가 완료되었습니다.", $script);
	}else{
		put_msg_and_back($response["msg"]);
	}

}