<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공통 파일업로드
 */
//Init
$GL_JsonHeader = true; //Json Header
include_once "../_init_.php";

$xls_name = $_GET["xls_name"];
$returnValue = array();
$returnValue["result"] = false;
if(isset($_SESSION[$xls_name]) && $_SESSION[$xls_name] == "wait"){
	$returnValue["result"] = false;
}else{
	$returnValue["result"] = true;

}
echo json_encode($returnValue);

?>