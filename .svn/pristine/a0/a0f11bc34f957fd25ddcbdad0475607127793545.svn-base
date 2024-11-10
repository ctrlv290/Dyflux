<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 옵션 관리 관련 Process
 */

//Page Info
$pageMenuIdx = 176;
//Init
include_once "../_init_.php";


$C_Product = new Product();

$mode                              = $_POST["mode"];
$product_idx                       = $_POST["product_idx"];
$product_option_idx                = $_POST["product_option_idx"];
$product_option_name               = $_POST["product_option_name"];
$product_option_mix_1              = $_POST["product_option_mix_1"];
$product_option_mix_2              = $_POST["product_option_mix_2"];
$product_option_mix_3              = $_POST["product_option_mix_3"];
$product_option_sale_price         = str_replace(",", "", $_POST["product_option_sale_price_default"]);
$product_option_sale_price_A       = str_replace(",", "", $_POST["product_option_sale_price_A"]);
$product_option_sale_price_B       = str_replace(",", "", $_POST["product_option_sale_price_B"]);
$product_option_sale_price_C       = str_replace(",", "", $_POST["product_option_sale_price_C"]);
$product_option_sale_price_D       = str_replace(",", "", $_POST["product_option_sale_price_D"]);
$product_option_sale_price_E       = str_replace(",", "", $_POST["product_option_sale_price_E"]);
$product_option_warning_count      = (is_numeric($_POST["product_option_warning_count"])) ? $_POST["product_option_warning_count"] : 0;
$product_option_danger_count       = (is_numeric($_POST["product_option_danger_count"])) ? $_POST["product_option_danger_count"] : 0;
$product_option_purchase_price     = str_replace(",", "", $_POST["product_option_purchase_price"]);

if(!is_numeric($product_option_purchase_price))
{
	$product_option_purchase_price = 0;
}

$response = array();

if($mode == "add") {

	$args                                  = array();
	$args["product_idx"]                   = $product_idx;
	$args["product_option_sale_price"]     = $product_option_sale_price;
	$args["product_option_sale_price_A"]   = $product_option_sale_price_A;
	$args["product_option_sale_price_B"]   = $product_option_sale_price_B;
	$args["product_option_sale_price_C"]   = $product_option_sale_price_C;
	$args["product_option_sale_price_D"]   = $product_option_sale_price_D;
	$args["product_option_sale_price_E"]   = $product_option_sale_price_E;
	$args["product_option_warning_count"]  = $product_option_warning_count;
	$args["product_option_danger_count"]   = $product_option_danger_count;
	$args["product_option_purchase_price"] = $product_option_purchase_price;


	$product_option_mix_1_ary = explode(",", $product_option_mix_1);
	$product_option_mix_2_ary = explode(",", $product_option_mix_2);
	$product_option_mix_3_ary = explode(",", $product_option_mix_3);

	$product_option_mix_1_ary = array_filter($product_option_mix_1_ary, function($value) { return $value !== ''; });
	$product_option_mix_2_ary = array_filter($product_option_mix_2_ary, function($value) { return $value !== ''; });
	$product_option_mix_3_ary = array_filter($product_option_mix_3_ary, function($value) { return $value !== ''; });

	$product_option_name_list = array();
	foreach ($product_option_mix_1_ary as $mix1)
	{
		$mix1_val = trim($mix1);
		if(count($product_option_mix_2_ary) > 0){
			foreach($product_option_mix_2_ary as $mix2){
				$mix2_val = trim($mix2);
				if(count($product_option_mix_3_ary) > 0) {
					foreach($product_option_mix_3_ary as $mix3) {
						$mix3_val = trim($mix3);
						$product_option_name_list[] = sprintf("%s-%s-%s", $mix1_val, $mix2_val, $mix3_val);
					}
				}else{
					$product_option_name_list[] = sprintf("%s-%s", $mix1_val, $mix2_val);
				}
			}
		}else{
			$product_option_name_list[] = $mix1_val;
		}
	}

	foreach($product_option_name_list as $option)
	{
		$args["product_option_name"] = $option;
		$C_Product -> insertProductOption($args);

	}

	$response["result"] = true;

}elseif($mode == "mod"){


	$args                                  = array();
	$args["product_idx"]                   = $product_idx;
	$args["product_option_idx"]            = $product_option_idx;
	$args["product_option_name"]           = $product_option_name;
	$args["product_option_sale_price"]     = $product_option_sale_price;
	$args["product_option_sale_price_A"]   = $product_option_sale_price_A;
	$args["product_option_sale_price_B"]   = $product_option_sale_price_B;
	$args["product_option_sale_price_C"]   = $product_option_sale_price_C;
	$args["product_option_sale_price_D"]   = $product_option_sale_price_D;
	$args["product_option_sale_price_E"]   = $product_option_sale_price_E;
	$args["product_option_warning_count"]  = $product_option_warning_count;
	$args["product_option_danger_count"]   = $product_option_danger_count;
	$args["product_option_purchase_price"] = $product_option_purchase_price;

	$rst = $C_Product -> updateProductOption($args);

	$response["result"] = true;

}elseif($mode == "product_option_delete"){
	$response["result"] = false;

	$rst = $C_Product -> getProductOptionDelete($product_option_idx);

	$response["result"] = true;

}elseif($mode == "product_option_soldout_update"){
	$soldout_type = $_POST["soldout_type"];
	$change_value = $_POST["change_value"];

	$response["result"] = false;

	if($soldout_type == "product_option_soldout" || $soldout_type == "product_option_soldout_temp")
	{
		$rst = $C_Product -> ProductOptionSoldOutUpdate($product_option_idx, $soldout_type, $change_value);
		if($rst) {
			$response["result"] = true;
		}
	}

}elseif($mode == "product_option_soldout_all_update"){
	$change_value = $_POST["change_value"];

	$response["result"] = false;

	$rst = $C_Product -> ProductOptionSoldOutAllUpdate($product_idx, $change_value);
	if($rst) $response["result"] = true;

} elseif ($mode == "update_option_sold_out_memo") {
    $response["result"] = false;
    $response["msg"] = "품절 메모 변경에 실패하였습니다.";

    $memo = $_POST["product_option_sold_out_memo"];

    $rst = $C_Product->updateOptionSoldOutMemo($product_option_idx, $memo);

    if ($rst) {
        $response["result"] = true;
        $response["msg"] = "정상적으로 변경되었습니다.";
    }

} elseif ($mode == "update_option_barcode_GTIN") {
    $response["result"] = false;
    $response["msg"] = "바코드 번호 변경에 실패하였습니다.";

    $num = $_POST["product_option_barcode_GTIN"];

    $rst = $C_Product->updateOptionBarcodeGTIN($product_option_idx, $num);

    if ($rst) {
        $response["result"] = true;
        $response["msg"] = "정상적으로 변경되었습니다.";
    }

} else {
	$response["result"] = false;
}

echo json_encode($response);
exit;

?>