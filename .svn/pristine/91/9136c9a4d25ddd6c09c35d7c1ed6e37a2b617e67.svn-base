<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 재고 발주 관련 Process
 */

//Page Info
$pageMenuIdx = 184;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["data"] = array();
$response["msg"] = "";

$mode = $_POST["mode"];

if($mode == "stock_confirm_exec"){
	//입고 확정 처리

	$stock_idx = $_POST["stock_idx"];

	$C_Stock = new Stock();

	$_view = $C_Stock->getStockData($stock_idx);

	//존재하는 재고 인지 확인
	if($_view)
	{
		//이미 확정 처리 되어있는지 확인
		if($_view["stock_is_confirm"] == "N"){

			//재고 확정 처리 실행
			$rst = $C_Stock -> updateStockConfirm($stock_idx);


			$response["result"] = true;

		}else{
			$response["msg"] = "이미 확정처리된 재고입니다.";
		}
	}else{
		$response["msg"] = "존재하지 않는 재고 입니다.";
	}
} else if($mode == "stock_multi_confirm_exec"){
    //입고 다중 확정 처리

    $stock_idx = $_POST["stock_idx"];

    $stock_idx_arr = empty($stock_idx)?'NULL':"'".join("','", $stock_idx)."'";

    $C_Stock = new Stock();

    $_view = $C_Stock->getStockManyData($stock_idx_arr);

    //존재하는 재고 인지 확인
    if(count($_view) == count($stock_idx)) {
        //이미 확정 처리 되어있는지 확인
        $confirm_cnt = 0;
        foreach ($_view as $value) {
            if ($value["stock_is_confirm"] == "N") {
                $confirm_cnt++;
            }
        }
        if ($confirm_cnt == count($stock_idx)) {
                //재고 확정 처리 실행
                $rst = $C_Stock -> updateStockMultiConfirm($stock_idx_arr);
                $response["result"] = true;

        } else {
                $response["msg"] = "이미 확정처리된 재고가 있습니다.";
             }
    } else {
            $response["msg"] = "존재하지 않는 재고가 있습니다.";
    }
}

echo json_encode($response);
?>