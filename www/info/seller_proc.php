<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처관리 관련 Process
 */
//Page Info
$pageMenuIdx = 43;
//Permission IDX
$pagePermissionIdx = 43;
//Init
include "../_init_.php";

$C_Seller = new Seller();
$C_ManageGroup = new ManageGroup();
//print_r($_POST);

$mode                   = $_POST["mode"];
$seller_idx             = $_POST["seller_idx"];
$market_type            = $_POST["market_type"];
$market_code            = $_POST["market_code"];
$seller_name            = $_POST["seller_name"];
$manage_group_idx       = $_POST["manage_group_idx"];
$market_login_id        = $_POST["market_login_id"];
$market_login_pw        = $_POST["market_login_pw"];
$market_auth_code       = $_POST["market_auth_code"];
$market_auth_code2      = $_POST["market_auth_code2"];
$market_admin_url       = $_POST["market_admin_url"];
$market_mall_url        = $_POST["market_mall_url"];
$market_product_url     = $_POST["market_product_url"];
$seller_auto_order      = ($_POST["seller_auto_order"] == "N") ? "N" : "Y";
$seller_invoice_product = ($_POST["seller_invoice_product"] == "Y") ? "Y" : "N";
$seller_invoice_option  = ($_POST["seller_invoice_option"] == "Y") ? "Y" : "N";
$seller_use_api          = $_POST["seller_use_api"];
$seller_is_use          = $_POST["seller_is_use"];


if($mode == "add")
{
	$args = array();
	$args["market_type"]            = $market_type;
	$args["market_code"]            = $market_code;
	$args["seller_name"]            = $seller_name;
	$args["manage_group_idx"]       = $manage_group_idx;
	$args["market_login_id"]        = $market_login_id;
	$args["market_login_pw"]        = $market_login_pw;
	$args["market_auth_code"]       = $market_auth_code;
	$args["market_auth_code2"]      = $market_auth_code2;
	$args["market_admin_url"]       = $market_admin_url;
	$args["market_mall_url"]        = $market_mall_url;
	$args["market_product_url"]     = $market_product_url;
	$args["seller_auto_order"]      = $seller_auto_order;
	$args["seller_invoice_product"] = $seller_invoice_product;
	$args["seller_invoice_option"]  = $seller_invoice_option;
	$args["seller_use_api"]         = $seller_use_api;
	$args["seller_is_use"]          = $seller_is_use;
	$C_Seller->insertSeller($args);

	$exec_script = "
		opener.Seller.SellerListReload();
	";

	exec_script_and_close($exec_script);

}elseif($mode == "mod"){

	$args = array();
	$args["seller_idx"]             = $seller_idx;
	$args["market_type"]            = $market_type;
	$args["market_code"]            = $market_code;
	$args["seller_name"]            = $seller_name;
	$args["manage_group_idx"]       = $manage_group_idx;
	$args["market_login_id"]        = $market_login_id;
	$args["market_login_pw"]        = $market_login_pw;
	$args["market_auth_code"]       = $market_auth_code;
	$args["market_auth_code2"]      = $market_auth_code2;
	$args["market_admin_url"]       = $market_admin_url;
	$args["market_mall_url"]        = $market_mall_url;
	$args["market_product_url"]     = $market_product_url;
	$args["seller_auto_order"]      = $seller_auto_order;
	$args["seller_invoice_product"] = $seller_invoice_product;
	$args["seller_invoice_option"]  = $seller_invoice_option;
	$args["seller_use_api"]         = $seller_use_api;
	$args["seller_is_use"]          = $seller_is_use;
	$C_Seller->updateSeller($args);

	$exec_script = "
		opener.Seller.SellerListReload();
	";

	exec_script_and_close($exec_script);

}elseif($mode == "get_market_type_list"){
	$response = array();
	$response["result"] = false;
	$response["list"] = array();
	$rst = $C_Seller->getMarketList($market_type);
	if($rst)
	{
		$response["result"] = true;
		$response["list"] = $rst;
	}
	echo json_encode($response);
	exit;
}elseif($mode == "get_seller_group_list"){
	$response = array();
	$response["result"] = false;
	$response["list"] = array();
	$rst = $C_Seller->getSellerGroupList();
	$response["result"] = true;
	$response["list"] = $rst;
	echo json_encode($response);
	exit;
}elseif($mode == "add_seller_group"){

	$seller_group_name = $_POST["seller_group_name"];

	$rst = $C_ManageGroup->insertManageGroup($seller_group_name);

	$response["result"] = true;
	echo json_encode($response);
	exit;

}elseif($mode == "mod_seller_group"){

	$seller_group_idx = $_POST["seller_group_idx"];
	$seller_group_name = $_POST["seller_group_name"];

	$args = array();
	$args["seller_group_idx"] = $seller_group_idx;
	$args["seller_group_name"] = $seller_group_name;

	$rst = $C_ManageGroup->updateManageGroup($args);

	$response["result"] = true;
	echo json_encode($response);
	exit;
}elseif($mode == "del_seller_group"){

	$manage_group_idx = $_POST["manage_group_idx"];

	$rst = $C_ManageGroup->deleteManageGroup($manage_group_idx);

	$response["result"] = true;
	echo json_encode($response);
	exit;
}

?>