<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 관리 관련 Process
 */

//Page Info
$pageMenuIdx = 36;
//Init
include "../_init_.php";

$C_Product = new Product();
$C_Files = new Files();
//print_r($_POST);

$mode                      = $_POST["mode"];
$product_idx               = $_POST["product_idx"];
$product_sale_type         = $_POST["product_sale_type"];
$supplier_idx              = $_POST["supplier_idx"];
$product_name              = $_POST["product_name"];
$product_supplier_name     = $_POST["product_supplier_name"];
$product_supplier_option   = $_POST["product_supplier_option"];
$seller_idx                = $_POST["seller_idx"];
$product_origin            = $_POST["product_origin"];
$product_manufacturer      = $_POST["product_manufacturer"];
$product_md                = $_POST["product_md"];
$product_delivery_fee_sale = $_POST["product_delivery_fee_sale"];
$product_delivery_fee_buy  = $_POST["product_delivery_fee_buy"];
$product_delivery_type     = $_POST["product_delivery_type"];
$product_category_l_idx    = $_POST["product_category_l_idx"];
$product_category_m_idx    = $_POST["product_category_m_idx"];
$product_sales_date        = $_POST["product_sales_date"];
$product_tax_type          = $_POST["product_tax_type"];
$product_notice_idx        = $_POST["product_notice_idx"];
$product_notice_1_content  = $_POST["product_notice_1_content"];
$product_notice_2_content  = $_POST["product_notice_2_content"];
$product_notice_3_content  = $_POST["product_notice_3_content"];
$product_notice_4_content  = $_POST["product_notice_4_content"];
$product_notice_5_content  = $_POST["product_notice_5_content"];
$product_notice_6_content  = $_POST["product_notice_6_content"];
$product_notice_7_content  = $_POST["product_notice_7_content"];
$product_notice_8_content  = $_POST["product_notice_8_content"];
$product_notice_9_content  = $_POST["product_notice_9_content"];
$product_notice_10_content = $_POST["product_notice_10_content"];
$product_notice_11_content = $_POST["product_notice_11_content"];
$product_notice_12_content = $_POST["product_notice_12_content"];
$product_notice_13_content = $_POST["product_notice_13_content"];
$product_notice_14_content = $_POST["product_notice_14_content"];
$product_notice_15_content = $_POST["product_notice_15_content"];
$product_notice_16_content = $_POST["product_notice_16_content"];
$product_notice_17_content = $_POST["product_notice_17_content"];
$product_notice_18_content = $_POST["product_notice_18_content"];
$product_notice_19_content = $_POST["product_notice_19_content"];
$product_notice_20_content = $_POST["product_notice_20_content"];
$product_img_main          = 0;
$product_img_1             = $_POST["product_img_1"];
$product_img_2             = $_POST["product_img_2"];
$product_img_3             = $_POST["product_img_3"];
$product_img_4             = $_POST["product_img_4"];
$product_img_5             = $_POST["product_img_5"];
$product_img_6             = $_POST["product_img_6"];
$product_desc              = $_POST["product_desc"];
$product_vendor_show       = $_POST["product_vendor_show"];
$product_vendor_show_type  = $_POST["product_vendor_show_type"];

if($product_vendor_show == "SHOW"){
	$product_vendor_show = $product_vendor_show_type;
}

//쇼핑몰 상세페이지
$product_detail_idx        = $_POST["product_detail_idx"];
$product_detail_mall_name  = $_POST["product_detail_mall_name"];
$product_detail_url        = $_POST["product_detail_url"];

//벤더사 노출 선택 리스트
$product_vendor_show_list  = $_POST["product_vendor_show_list"];
$product_vendor_show_list  = explode(",", $product_vendor_show_list);

//이미지 대표 선택
for($i = 1 ;$i < 7; $i++)
{
	if($_POST["product_img_" . $i . "_default"] == "Y")
	{
		$product_img_main = $i;
	}
}

if($mode == "add") {
	$args                              = array();
	$args["product_idx"]               = $product_idx;
	$args["product_sale_type"]         = $product_sale_type;
	$args["supplier_idx"]              = $supplier_idx;
	$args["product_name"]              = $product_name;
	$args["product_supplier_name"]     = $product_supplier_name;
	$args["product_supplier_option"]   = $product_supplier_option;
	$args["seller_idx"]                = $seller_idx;
	$args["product_origin"]            = $product_origin;
	$args["product_manufacturer"]      = $product_manufacturer;
	$args["product_md"]                = $product_md;
	$args["product_delivery_fee_sale"] = $product_delivery_fee_sale;
	$args["product_delivery_fee_buy"]  = $product_delivery_fee_buy;
	$args["product_delivery_type"]     = $product_delivery_type;
	$args["product_category_l_idx"]    = $product_category_l_idx;
	$args["product_category_m_idx"]    = $product_category_m_idx;
	$args["product_sales_date"]        = $product_sales_date;
	$args["product_tax_type"]          = $product_tax_type;
	$args["product_notice_idx"]        = $product_notice_idx;
	$args["product_notice_1_content"]  = $product_notice_1_content;
	$args["product_notice_2_content"]  = $product_notice_2_content;
	$args["product_notice_3_content"]  = $product_notice_3_content;
	$args["product_notice_4_content"]  = $product_notice_4_content;
	$args["product_notice_5_content"]  = $product_notice_5_content;
	$args["product_notice_6_content"]  = $product_notice_6_content;
	$args["product_notice_7_content"]  = $product_notice_7_content;
	$args["product_notice_8_content"]  = $product_notice_8_content;
	$args["product_notice_9_content"]  = $product_notice_9_content;
	$args["product_notice_10_content"] = $product_notice_10_content;
	$args["product_notice_11_content"] = $product_notice_11_content;
	$args["product_notice_12_content"] = $product_notice_12_content;
	$args["product_notice_13_content"] = $product_notice_13_content;
	$args["product_notice_14_content"] = $product_notice_14_content;
	$args["product_notice_15_content"] = $product_notice_15_content;
	$args["product_notice_16_content"] = $product_notice_16_content;
	$args["product_notice_17_content"] = $product_notice_17_content;
	$args["product_notice_18_content"] = $product_notice_18_content;
	$args["product_notice_19_content"] = $product_notice_19_content;
	$args["product_notice_20_content"] = $product_notice_20_content;
	$args["product_img_main"]          = $product_img_main;
	$args["product_img_1"]             = $product_img_1;
	$args["product_img_2"]             = $product_img_2;
	$args["product_img_3"]             = $product_img_3;
	$args["product_img_4"]             = $product_img_4;
	$args["product_img_5"]             = $product_img_5;
	$args["product_img_6"]             = $product_img_6;
	$args["product_desc"]              = $product_desc;
	$args["product_vendor_show"]       = $product_vendor_show;
	$args["product_vendor_show_list"]  = $product_vendor_show_list;
	$args["product_detail_mall_name"]  = $product_detail_mall_name;
	$args["product_detail_url"]        = $product_detail_url;

	$product_idx = $C_Product -> insertProduct($args);

	if($product_idx) {
		//업로드 파일 Update
		for ($i = 1; $i < 7; $i++) {
			$colName = "product_img_" . $i;
			if ($$colName) {
				$argsFile                  = array();
				$argsFile["file_idx"]      = $$colName;
				$argsFile["ref_table_idx"] = $product_idx;
				$tmp                       = $C_Files->updateFileActive($argsFile);
			}
		}
	}

	if($_POST["goto_option"] == "Y"){
		go_replace("product_write.php?product_idx=".$product_idx);
	}else {
		go_replace("product_list.php");
	}
	//print_r2($args);
}elseif($mode == "mod") {
	$args                              = array();
	$args["product_idx"]               = $product_idx;
	$args["product_sale_type"]         = $product_sale_type;
	$args["supplier_idx"]              = $supplier_idx;
	$args["product_name"]              = $product_name;
	$args["product_supplier_name"]     = $product_supplier_name;
	$args["product_supplier_option"]   = $product_supplier_option;
	$args["seller_idx"]                = $seller_idx;
	$args["product_origin"]            = $product_origin;
	$args["product_manufacturer"]      = $product_manufacturer;
	$args["product_md"]                = $product_md;
	$args["product_delivery_fee_sale"] = $product_delivery_fee_sale;
	$args["product_delivery_fee_buy"]  = $product_delivery_fee_buy;
	$args["product_delivery_type"]     = $product_delivery_type;
	$args["product_category_l_idx"]    = $product_category_l_idx;
	$args["product_category_m_idx"]    = $product_category_m_idx;
	$args["product_sales_date"]        = $product_sales_date;
	$args["product_tax_type"]          = $product_tax_type;
	$args["product_notice_idx"]        = $product_notice_idx;
	$args["product_notice_1_content"]  = $product_notice_1_content;
	$args["product_notice_2_content"]  = $product_notice_2_content;
	$args["product_notice_3_content"]  = $product_notice_3_content;
	$args["product_notice_4_content"]  = $product_notice_4_content;
	$args["product_notice_5_content"]  = $product_notice_5_content;
	$args["product_notice_6_content"]  = $product_notice_6_content;
	$args["product_notice_7_content"]  = $product_notice_7_content;
	$args["product_notice_8_content"]  = $product_notice_8_content;
	$args["product_notice_9_content"]  = $product_notice_9_content;
	$args["product_notice_10_content"] = $product_notice_10_content;
	$args["product_notice_11_content"] = $product_notice_11_content;
	$args["product_notice_12_content"] = $product_notice_12_content;
	$args["product_notice_13_content"] = $product_notice_13_content;
	$args["product_notice_14_content"] = $product_notice_14_content;
	$args["product_notice_15_content"] = $product_notice_15_content;
	$args["product_notice_16_content"] = $product_notice_16_content;
	$args["product_notice_17_content"] = $product_notice_17_content;
	$args["product_notice_18_content"] = $product_notice_18_content;
	$args["product_notice_19_content"] = $product_notice_19_content;
	$args["product_notice_20_content"] = $product_notice_20_content;
	$args["product_img_main"]          = $product_img_main;
	$args["product_img_1"]             = $product_img_1;
	$args["product_img_2"]             = $product_img_2;
	$args["product_img_3"]             = $product_img_3;
	$args["product_img_4"]             = $product_img_4;
	$args["product_img_5"]             = $product_img_5;
	$args["product_img_6"]             = $product_img_6;
	$args["product_desc"]              = $product_desc;
	$args["product_vendor_show"]       = $product_vendor_show;
	$args["product_vendor_show_list"]  = $product_vendor_show_list;
	$args["product_detail_idx"]        = $product_detail_idx;
	$args["product_detail_mall_name"]  = $product_detail_mall_name;
	$args["product_detail_url"]        = $product_detail_url;

	$product_idx = $C_Product->updateProduct($args);

	if ($product_idx) {
		//업로드 파일 Update
		for ($i = 1; $i < 7; $i++) {
			$colName = "product_img_" . $i;
			if ($$colName) {
				$argsFile                  = array();
				$argsFile["file_idx"]      = $$colName;
				$argsFile["ref_table_idx"] = $product_idx;
				$tmp                       = $C_Files->updateFileActive($argsFile);
			}
		}
	}


	go_replace("product_list.php");
}elseif($mode == "product_goto_trash") {

	$C_Product->gotoTrashProduct($product_idx);

	$response           = array();
	$response["result"] = true;
	echo json_encode($response);
	exit;
}elseif($mode == "product_restore_trash") {
	$product_idx_list = $_POST["product_idx_list"];
	$C_Product->restoreTrashProduct($product_idx_list);

	$response           = array();
	$response["result"] = true;
	echo json_encode($response);
	exit;
}
?>