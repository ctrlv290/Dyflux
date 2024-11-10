<?php
/**
 * User: ssawoona
 * Date: 2019-02
 */

$user_agent = trim($_SERVER["HTTP_USER_AGENT"]);

$response = array(
	'result' => false,
	'ver' => '',
);

if($user_agent == "DY_AUTO") {
	$response["result"] = true;
	$response["ver"] = "0.9.7.7";
} elseif($user_agent == "DY_INVOICE") {
	$response["result"] = true;
	$response["ver"] = "0.9.6.8";
}

echo json_encode($response, true);
?>