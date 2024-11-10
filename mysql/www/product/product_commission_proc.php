<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 수수료관리 관련 Process
 */

//Page Info
$pageMenuIdx = 206;
//Init
include_once "../_init_.php";

$mode = $_POST["mode"];

$C_Product = new Product();

if($mode == "add") {

	$seller_idx          = $_POST["seller_idx"];
    $comm_type           = $_POST["comm_type"];
	$market_product_no   = $_POST["market_product_no"];
	$market_commission   = $_POST["market_commission"];
	$delivery_commission = $_POST["delivery_commission"];
	$product_idx_list    = $_POST["product_idx"];
	$product_option_idx  = $_POST["product_option_idx"];
    $event_unit_price    = $_POST["event_unit_price"];

	//수수료정보 중복확인
//	$dup = $C_Product->dupCheckProductCommissionMarketProductNo($seller_idx, $market_product_no);
    $dup = $C_Product->dupCheckProductCommissionMarketProductNo($seller_idx, $comm_type, $market_commission, $delivery_commission, $event_unit_price);

	if(!$dup){
		put_msg_and_back("해당 판매처에 이미 등록 된 수수료율 입니다.");
		exit;
	}

	//상품옵션 중복확인
//    if($comm_type == "NORMAL") {
        $C_Product->db_connect();
        $dup_product_option = Array();
        foreach ($product_option_idx as $key) {
            $qry = "
            SELECT count(*) FROM dy_market_commission C
                LEFT OUTER JOIN dy_market_commission_product CP ON C.comm_idx = CP.comm_idx
                WHERE C.comm_is_del = N'N'
                AND C.comm_type = N'$comm_type'
                AND C. seller_idx = N'$seller_idx'
                AND CP.product_option_idx = '$key'
			";
            $dup = $C_Product->execSqlOneCol($qry);
            if ($dup > 0) {
                array_push($dup_product_option, $key);
            }
        }
        $C_Product->db_close();
        if (count($dup_product_option) > 0) {
            if($comm_type == "NORMAL") {
                put_msg_and_back("해당 판매처에 이미 등록 된 상품옵션이 있습니다. " . "(" . implode($dup_product_option, ', ') . ")");
                exit;
            }else{
                put_msg_and_back("해당 판매처에 이미 행사 등록 된 상품옵션이 있습니다. " . "(" . implode($dup_product_option, ', ') . ")");
                exit;
            }
        }
//    }

    $rst = $C_Product->insertProductCommission($seller_idx, $comm_type, $market_commission, $delivery_commission, $product_idx_list, $product_option_idx, $event_unit_price);

	if($rst){
		$exec_script = "
				alert('등록되었습니다.');		
				try{
					opener.ProductCommission.ProductCommissionListReload();
				}catch(e){
				}
			";
		exec_script_and_close($exec_script);
	}else{
		put_msg_and_back("오류가 발생하였습니다.");
		exit;
	}

}elseif($mode == "update"){

    $seller_idx          = $_POST["seller_idx"];
	$comm_idx            = $_POST["comm_idx"];
    $comm_type           = $_POST["comm_type"];
    $market_commission   = $_POST["market_commission"];
	$delivery_commission = $_POST["delivery_commission"];
	$product_idx_list    = $_POST["product_idx"];
	$product_option_idx  = $_POST["product_option_idx"];
    $event_unit_price    = $_POST["event_unit_price"];

//    $dup = $C_Product->dupCheckProductCommissionMarketProductNo($seller_idx, $comm_type, $market_commission, $delivery_commission, $event_unit_price);
//
//    if(!$dup){
//        put_msg_and_back("중복된 수수료 정보입니다.");
//        exit;
//    }

    //상품옵션 중복확인
    if($comm_type == "NORMAL") {
        $C_Product->db_connect();
        $dup_product_option = Array();
        foreach ($product_option_idx as $key) {
            $qry = "
            SELECT count(*) FROM dy_market_commission C
                LEFT OUTER JOIN dy_market_commission_product CP ON C.comm_idx = CP.comm_idx
                WHERE C.comm_is_del = N'N'
                AND C.comm_idx != N'$comm_idx'
                AND C.comm_type = N'$comm_type'
                AND C. seller_idx = N'$seller_idx'
                AND CP.product_option_idx = '$key'
			";

            $dup = $C_Product->execSqlOneCol($qry);
            if ($dup > 0) {
                array_push($dup_product_option, $key);
            }
        }
        $C_Product->db_close();
        if (count($dup_product_option) > 0) {
            put_msg_and_back("해당 판매처 수수료관리에 이미 등록 된 상품옵션이 있습니다. " . "(" . implode($dup_product_option, ', ') . ")");
            exit;
        }
    }

	$rst = $C_Product->updateProductCommission($comm_idx, $comm_type, $market_commission, $delivery_commission, $product_idx_list, $product_option_idx, $event_unit_price);

	if($rst){
		$exec_script = "
				alert('수정되었습니다.');		
				try{
					opener.ProductCommission.ProductCommissionListReload();
				}catch(e){
				}
			";
		exec_script_and_close($exec_script);
	}else{
		put_msg_and_back("오류가 발생하였습니다.");
		exit;
	}

}

?>