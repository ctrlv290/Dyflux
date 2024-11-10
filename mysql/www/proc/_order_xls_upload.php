<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주 엑셀 업로드 처리 프로세스
 */

//Page Info
$pagePermissionIdx = 73; //주문관리->발주 페이지

include_once "../_init_.php";

$response_type         = $_POST["response_type"];

$response = array();
$response["result"] = false;
$response["msg"] = "";
$response["script"] = "";
$response["fileName"] = "";
$response["uploadInfo"] = array();

$acceptable = array(
	'application/vnd.ms-excel',
	'application/msexcel',
	'application/x-msexcel',
	'application/x-ms-excel',
	'application/x-excel',
	'application/x-dos_ms_excel',
	'application/xls',
	'application/x-xls',
	'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	'application/haansoftxls',
);

if(isset($_FILES["xls_file"]) && $_POST["xls_type"])
{
	$fileObj = $_FILES["xls_file"];

	$target_dir = DY_ORDER_XLS_UPLOAD_PATH;
	$userfilename = basename($fileObj["name"]);
	$target_file = $target_dir . '/' . $userfilename;
	$extension = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

	list($usec, $sec) = explode(" ",microtime());
	$uploadFilename = (round(((float)$usec + (float)$sec))).rand(1,10000);		//  업로드 파일명 날짜에 따라 변환
	$new_file_name = $uploadFilename.".".$extension;							//  새로운 파일명 생성
	$new_file_name_path = $target_dir . '/' . $new_file_name;

	$filesize = $fileObj["size"];                          //  파일 사이즈
	$filemimetype = $fileObj["type"];                      //  파일 MIME TYPE

	if(($filesize >= DY_MAX_UPLOAD_SIZE) || ($filesize == 0)) {
		$response["msg"] = "업로드는 10MB 이하의 파일만 가능합니다." . $filesize . "//" . DY_MAX_UPLOAD_SIZE;
	}else{
		if(!in_array($filemimetype, $acceptable) && (!empty($filemimetype))) {
			$response["msg"] = "업로드가 불가능한 파일 형식입니다. MIME : " . $filemimetype;
		}else{
			if(move_uploaded_file($fileObj['tmp_name'], $new_file_name_path)) {

				$response["result"] = true;

				if($_POST["xls_type"] == "order_seller_upload"){
					$response["script"] = "parent.Order.OrderSellerXlsRead('".$new_file_name."');";
					$response["fileName"] = $new_file_name;
				}

			}else{
				$response["msg"] = "업로드에 실패하였습니다.";
			}
		}
	}
}else{
	$response["msg"] = "잘못된 접근입니다.";
}
if($response_type == "json") {
	echo json_encode($response, true);
} else {
	if (!$response["result"]) {
		echo '
		<script>
			alert("' . $response["msg"] . '");
			parent.hideLoader();
		</script>
	';
	} else {
		echo '
		<script>
			' . $response["script"] . '
		</script>
	';
	}
}

?>