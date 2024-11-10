<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 합포 Process
 */

//Page Info
$pageMenuIdx = 73;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["msg"] = "";

$C_Order = new Order();

$mode                  = $_POST["mode"];

if($mode == "sum_package") {

	$current_order_idx = $_POST["current_order_idx"];
	$parent_order_idx  = $_POST["parent_order_idx"];

	$tmp = $C_Order -> execOrderPackage($current_order_idx, $parent_order_idx, "", true);

	$response["result"] = true;
}elseif($mode == "check_package_able"){

	$_list = $C_Order -> checkOrderPackageAble();

	$response["result"] = true;
	$response["data"] = $_list;
}elseif($mode == "auto_package_exec"){

	$pack_cnt = $C_Order -> autoOrderPackageExec();

	$response["result"] = true;
	$response["data"] = $pack_cnt;

}elseif($mode == "package_except_exec_one"){

	//일괄 합포 제외
//	[except] => Array
//	(
//		[1] => Array
//		(
//			[0] => Array
//			(
//				[except_no] => 1
//                            [order_pack_idx] => 100332
//                            [order_idx] => 100332
//                            [product_option_idx] => 30031
//                            [product_option_cnt] => 1
//                        )
//
//                    [1] => Array
//	(
//		[except_no] => 1
//                            [order_pack_idx] => 100332
//                            [order_idx] => 100735
//                            [product_option_idx] => 30002
//                            [product_option_cnt] => 1
//                        )
//
//                )
//
//        )

	$except_list = $_POST["except"];
	$cs_msg = $_POST["cs_msg"];

	foreach($except_list as $except)
	{
		$rst = $C_Order -> separateOrderExceptOne($except, $cs_msg);
	}

	$response["result"] = true;

}elseif($mode == "package_except_exec_batch"){


	$except_batch_list = $_POST["except"];

	foreach ($except_batch_list as $except_list) {
		foreach($except_list as $except){
			$rst = $C_Order -> separateOrderExceptOne($except);
		}
	}
}

echo json_encode($response, true);
?>
