<?php
/**
 * User: ssawo
 * Date: 2019-03
 */
//Init
include "../_init_.php";
//https://dymallkr.cafe24api.com/api/v2/oauth/authorize?response_type=code&client_id=hfmI2ISvaKtbjYjI9q90gG&state=90052&redirect_uri=https://dymallkr.cafe24.com&scope=mall.read_order,mall.write_order
//https://dymallkr.cafe24api.com/api/v2/oauth/authorize?response_type=code&client_id=hfmI2ISvaKtbjYjI9q90gG&state=90052&redirect_uri=https://www.dyflux.co.kr/dy_auto/_cafe24_request_token.php&scope=mall.read_order,mall.write_order
//https://dymallkr.cafe24api.com/api/v2/oauth/authorize?response_type=code&client_id=hfmI2ISvaKtbjYjI9q90gG&state=90052&redirect_uri=https%3A%2F%2Fwww.dyflux.co.kr%2Fdy_auto%2F_cafe24_request_token.php&scope=mall.read_order,mall.write_order
//https://dymallkr.cafe24api.com/api/v2/oauth/authorize?response_type=code&client_id={보안코드1}&state={판매처코드}&redirect_uri=https://www.dyflux.co.kr/dy_auto/_cafe24_request_token.php&scope=mall.read_order,mall.write_order
$C_Login = new Login();
$C_Login->setLoginSessionByToken();     // 토큰으로 로그인 시키기

$C_Seller     = new Seller();
$C_API_Cafe24 = new API_Cafe24();

$mall_id       = "";
$access_token  = "";
$refresh_token = "";
$client_id     = "";
$client_secret = "";
$this_url      = "";
$_code         = $_GET["code"];
$_state        = $_GET["state"];
$seller_idx    = $_state;
$_seller       = $C_Seller->getAllSellerData($seller_idx);

$_token = $C_API_Cafe24->getAPIToken(array(
	'seller_idx' => $seller_idx,
	'_code' => $_code,
));


//echo json_encode($_token, true);
exec_script_and_close("alert('토큰이 갱신되었습니다.');");

?>

