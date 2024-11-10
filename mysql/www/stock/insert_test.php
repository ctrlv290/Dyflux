<?php
include_once "../_init_.php";

$C_Dbconn = new DBConn();
$C_SETTLE = new Settle();
$C_Dbconn -> db_connect();

$stock_msg = '';
$last_member_idx = 10030;
$modip = '112.161.232.160';

for($order_matching_idx = 84665 ; $order_matching_idx <= 84728 ; $order_matching_idx++){

//    if($order_matching_idx < 78393 + 13){
//        $stock_unit_price = 10000;
//    }else{
        $stock_unit_price = 2000;
//    }
    $select_qry = "SELECT * FROM dy_order_product_matching WHERE order_matching_idx = $order_matching_idx";

    $select_rst = $C_Dbconn->execSqlOneRow($select_qry);

    $order_idx = $select_rst["order_idx"];
    $stock_amount = $select_rst["product_option_cnt"];
    $product_idx = $select_rst['product_idx'];
    $product_option_idx = $select_rst['product_option_idx'];;
//
//
//    $qry = "
//			Insert Into DY_STOCK
//			(
//			 stock_ref_idx, product_idx, product_option_idx, stock_kind, order_idx, order_matching_idx, stock_order_idx,
//			 stock_order_is_ready, stock_order_msg, stock_in_date, stock_due_date,
//			 stock_type, stock_status, stock_unit_price, stock_due_amount, stock_amount, stock_msg,
//			 stock_file_idx, stock_request_date, stock_request_member_idx, stock_is_proc, stock_is_proc_date, stock_is_proc_member_idx,
//			 stock_is_confirm, stock_is_confirm_date, stock_is_confirm_member_idx,
//			 stock_regip, last_member_idx
//		    )
//		    VALUES
//			(
//			 0, N'$product_idx', N'$product_option_idx', N'ORDER', N'$order_idx', N'$order_matching_idx', 0,
//			 N'N', N'$stock_msg', null, null,
//			 -1, N'NORMAL', N'$stock_unit_price', 0, N'$stock_amount', N'$stock_msg',
//			 0, NOW(), N'$last_member_idx', N'Y', NOW(), N'$last_member_idx',
//			 N'Y', NOW(), N'$last_member_idx',
//			 N'$modip', N'$last_member_idx'
//			)
//		";
//
//    $inserted_idx1 = $C_Dbconn -> execSqlInsert($qry);
//
//    $qry = "
//			Update DY_STOCK
//				Set stock_ref_idx = N'$inserted_idx1'
//				Where stock_idx = N'$inserted_idx1'
//		";
//    $tmp = $C_Dbconn -> execSqlUpdate($qry);
//
//    $qry = "
//			Insert Into DY_STOCK
//			(
//			 stock_ref_idx, product_idx, product_option_idx, stock_kind, order_idx, order_matching_idx, stock_order_idx,
//			 stock_order_is_ready, stock_order_msg, stock_in_date, stock_due_date,
//			 stock_type, stock_status, stock_unit_price, stock_due_amount, stock_amount, stock_msg,
//			 stock_file_idx, stock_request_date, stock_request_member_idx, stock_is_proc, stock_is_proc_date, stock_is_proc_member_idx,
//			 stock_is_confirm, stock_is_confirm_date, stock_is_confirm_member_idx,
//			 stock_invoice_date,
//			 stock_regip, last_member_idx
//		    )
//		    VALUES
//			(
//			 0, N'$product_idx', N'$product_option_idx', N'ORDER', N'$order_idx', N'$order_matching_idx', 0,
//			 N'N', N'$stock_msg', null, null,
//			 1, N'SHIPPED', N'$stock_unit_price', 0, N'$stock_amount', N'$stock_msg',
//			 0, NOW(), N'$last_member_idx', N'Y', NOW(), N'$last_member_idx',
//			 N'Y', NOW(), N'$last_member_idx',
//			 NOW(),
//			 N'$modip', N'$last_member_idx'
//			)
//		";
//
//    $inserted_idx2 = $C_Dbconn -> execSqlInsert($qry);
//
//    $qry = "
//			Update DY_STOCK
//				Set stock_ref_idx = N'$inserted_idx2'
//				Where stock_idx = N'$inserted_idx2'
//		";
//    $tmp = $C_Dbconn -> execSqlUpdate($qry);
//
//    $qry = "
//			Update DY_ORDER_PRODUCT_MATCHING
//				Set product_option_purchase_price = N'$stock_unit_price'
//				Where order_matching_idx = N'$order_matching_idx'
//		";
//    $tmp = $C_Dbconn -> execSqlUpdate($qry);

    $rst = $C_SETTLE -> insertSettleFromOrder($order_idx, "order_idx", "SELF", $order_matching_idx, "SHIPPED", true);
    $test = $rst;
}

$C_Dbconn -> db_close();
echo("end")

?>