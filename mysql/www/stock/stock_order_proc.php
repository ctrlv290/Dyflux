<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 재고 발주 관련 Process
 */

//Page Info
$pageMenuIdx = 184;
//Init
include_once "../_init_.php";

//print_r2($_POST);

$mode                      = $_POST["mode"];
$stock_order_idx           = $_POST["stock_order_idx"];
$stock_order_date          = $_POST["stock_order_date"];
$stock_order_in_date       = $_POST["stock_order_in_date"];
$stock_order_officer_name  = $_POST["stock_order_officer_name"];
$stock_order_officer_tel   = $_POST["stock_order_officer_tel"];
$supplier_idx              = $_POST["supplier_idx"];
$stock_order_supplier_name = $_POST["stock_order_supplier_name"];
$stock_order_supplier_tel  = $_POST["stock_order_supplier_tel"];
$stock_order_receiver_name = $_POST["stock_order_receiver_name"];
$stock_order_receiver_tel  = $_POST["stock_order_receiver_tel"];
$stock_order_receiver_addr = $_POST["stock_order_receiver_addr"];
$product_idx_ary           = $_POST["product_idx"];
$product_option_idx_ary    = $_POST["product_option_idx"];
$stock_unit_price_ary      = $_POST["stock_unit_price"];
$stock_due_amount_ary      = $_POST["stock_due_amount"];
$stock_msg_ary             = $_POST["stock_order_msg"];
$stock_idx_ary             = $_POST["stock_idx"];

if($mode == "add"){

	//넘어온 발주 상품 체크
	$product_idx_count = count($product_idx_ary);

	if(
		$product_idx_count == count($product_option_idx_ary)
		&& $product_idx_count == count($stock_unit_price_ary)
		&& $product_idx_count == count($stock_due_amount_ary)
		&& $product_idx_count == count($stock_msg_ary)
	) {
		$args                              = array();
		$args["stock_order_idx"]           = $stock_order_idx;
		$args["stock_order_date"]          = $stock_order_date;
		$args["stock_order_in_date"]       = $stock_order_in_date;
		$args["stock_order_officer_name"]  = $stock_order_officer_name;
		$args["stock_order_officer_tel"]   = $stock_order_officer_tel;
		$args["supplier_idx"]              = $supplier_idx;
		$args["stock_order_supplier_name"] = $stock_order_supplier_name;
		$args["stock_order_supplier_tel"]  = $stock_order_supplier_tel;
		$args["stock_order_receiver_name"] = $stock_order_receiver_name;
		$args["stock_order_receiver_tel"]  = $stock_order_receiver_tel;
		$args["stock_order_receiver_addr"] = $stock_order_receiver_addr;
		$args["product_idx_ary"]           = $product_idx_ary;
		$args["product_option_idx_ary"]    = $product_option_idx_ary;
		$args["stock_unit_price_ary"]      = $stock_unit_price_ary;
		$args["stock_due_amount_ary"]      = $stock_due_amount_ary;
		$args["stock_msg_ary"]             = $stock_msg_ary;

		$C_Stock = new Stock();

		$stock_order_idx = $C_Stock -> insertStockOrder($args);
		//print_r2($args);
		
		if($stock_order_idx){
			$exec_script = "
				alert('발주되었습니다.');		
				try{
					opener.opener.StockOrder.StockOrderListReload();
					opener.opener.StockOrder.StockOrderDocumentDownload('".$stock_order_idx."', '');
				}catch(e){
					try{
						if(opener != null)
						{
							opener.StockOrder.StockOrderListReload();
							opener.StockOrder.StockOrderDocumentDownload('".$stock_order_idx."', '');
						}
					}catch(e){}
				}
			";
				exec_script_and_close($exec_script);
		}else{
			put_msg_and_back("오류가 발생하였습니다.");
		}

	}else{
		put_msg_and_back("잘못된 접근입니다.");
	}
}elseif($mode == "mod"){
	$C_Stock = new Stock();

	//발주 정보 확인
	$_view = $C_Stock->getStockOrderData($stock_order_idx);

	if($_view) {
		//넘어온 발주 상품 체크
		$product_idx_count = count($product_idx_ary);

		if (
			$product_idx_count == count($product_option_idx_ary)
			&& $product_idx_count == count($stock_unit_price_ary)
			&& $product_idx_count == count($stock_due_amount_ary)
			&& $product_idx_count == count($stock_msg_ary)
		) {
			$args                              = array();
			$args["stock_order_idx"]           = $stock_order_idx;
			$args["stock_order_date"]          = $stock_order_date;
			$args["stock_order_in_date"]       = $stock_order_in_date;
			$args["stock_order_officer_name"]  = $stock_order_officer_name;
			$args["stock_order_officer_tel"]   = $stock_order_officer_tel;
			$args["supplier_idx"]              = $supplier_idx;
			$args["stock_order_supplier_name"] = $stock_order_supplier_name;
			$args["stock_order_supplier_tel"]  = $stock_order_supplier_tel;
			$args["stock_order_receiver_name"] = $stock_order_receiver_name;
			$args["stock_order_receiver_tel"]  = $stock_order_receiver_tel;
			$args["stock_order_receiver_addr"] = $stock_order_receiver_addr;
			$args["product_idx_ary"]           = $product_idx_ary;
			$args["product_option_idx_ary"]    = $product_option_idx_ary;
			$args["stock_unit_price_ary"]      = $stock_unit_price_ary;
			$args["stock_due_amount_ary"]      = $stock_due_amount_ary;
			$args["stock_msg_ary"]             = $stock_msg_ary;
			$args["stock_idx_ary"]             = $stock_idx_ary;


			$C_Stock = new Stock();

			$rst = $C_Stock->updateStockOrder($args);
			//print_r2($args);

			if ($rst) {
				$exec_script = "
				opener.StockOrder.StockOrderListReload();
				alert('발주정보를 저장하였습니다.');
				opener.StockOrder.StockOrderDocumentDownload('".$stock_order_idx."', '');
			";
				exec_script_and_close($exec_script);
			} else {
				put_msg_and_back("오류가 발생하였습니다.");
			}

		} else {
			put_msg_and_back("잘못된 접근입니다. Err1");
		}
	}else{
		put_msg_and_back("잘못된 접근입니다. Err2");
	}
}
?>