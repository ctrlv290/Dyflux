<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공통 파일업로드 (DY_FILES 테이블에 Insert)
 */
//Init
include_once "../_init_.php";
setlocale(LC_ALL,'ko_KR.UTF-8');
set_time_limit(0);


$C_Files = new Files();

$response = array();
$response["result"] = false;
$response["msg"] = "";
$response["uploadInfo"] = array();

$acceptable = array(
	'application/pdf',
	'image/jpeg',
	'image/jpg',
	'image/gif',
	'image/png'
);
$denied_extension = array("php", "php5", "lib", "inc", "html", "js", "css", "sql");

//print_r($_POST);
//print_r($_FILES);
$_upload_type = $_POST["_dy_upload_type"];
$_upload_input_name = $_POST['_dy_upload_input_name'];
$_upload_no = $_POST["_dy_upload_no"];
$_file_idx = $_POST["_dy_upload_file_idx"];
$_target_table = $_POST["_dy_upload_target_table"];
$_target_table_column = $_POST["_dy_upload_target_table_column"];
$_target_filename = $_POST["_dy_upload_target_filename"];
$_target_input_hidden = $_POST["_dy_upload_target_input_hidden"];


if($_upload_type == "document"){
	$acceptable = array(
		'application/pdf',
		'image/jpeg',
		'image/jpg',
		'image/gif',
		'image/png'
	);
	$save_path = DY_DATA_DIR .'/'. DY_UPLOAD_DIR;
	$target_dir = DY_UPLOAD_PATH;
	$target_dir_name = DY_UPLOAD_DIR;
}elseif($_upload_type == "product"){
	$acceptable = array(
		'image/jpeg',
		'image/jpg',
		'image/gif',
		'image/png'
	);
	$save_path = DY_DATA_DIR .'/'. DY_PRODUCT_UPLOAD_DIR;
	$target_dir = DY_PRODUCT_UPLOAD_PATH;
	$target_dir_name = DY_PRODUCT_UPLOAD_DIR;
}elseif($_upload_type == "stock_document") {
	$acceptable      = array(
		'application/pdf',
		'image/jpeg',
		'image/jpg',
		'image/gif',
		'image/png',
		'text/plain',
		'application/x-hwp',
		'application/vnd.ms-excel',
		'application/msexcel',
		'application/x-msexcel',
		'application/x-ms-excel',
		'application/x-excel',
		'application/x-dos_ms_excel',
		'application/xls',
		'application/x-xls',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	);
	$save_path       = DY_DATA_DIR . '/' . DY_STOCK_FILE_DIR;
	$target_dir      = DY_STOCK_FILE_PATH;
	$target_dir_name = DY_STOCK_FILE_DIR;
}elseif($_upload_type == "cs_file") {
	$acceptable      = array(
		'application/pdf',
		'image/jpeg',
		'image/jpg',
		'image/gif',
		'image/png',
		'text/plain',
		'application/x-hwp',
		'application/vnd.ms-excel',
		'application/msexcel',
		'application/x-msexcel',
		'application/x-ms-excel',
		'application/x-excel',
		'application/x-dos_ms_excel',
		'application/xls',
		'application/x-xls',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	);
	$save_path       = DY_DATA_DIR . '/' . DY_CS_FILE_DIR;
	$target_dir      = DY_CS_FILE_PATH;
	$target_dir_name = DY_CS_FILE_DIR;
}elseif($_upload_type == "bbs_file") {
	$acceptable      = null;
	$save_path       = DY_DATA_DIR . '/' . DY_BBS_FILE_DIR;
	$target_dir      = DY_BBS_FILE_PATH;
	$target_dir_name = DY_BBS_FILE_DIR;
}


if(isset($_FILES[$_upload_input_name]) && $_target_table && $_target_input_hidden)
{
	$userfilename = basename($_FILES[$_upload_input_name]["name"]);
	$target_file = $target_dir . '/' . $userfilename;
	$extension = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

	list($usec, $sec) = explode(" ",microtime());
	$uploadFilename = (round(((float)$usec + (float)$sec))).rand(1,10000);		//  업로드 파일명 날짜에 따라 변환
	$new_file_name = $uploadFilename.".".$extension;							//  새로운 파일명 생성
	$new_file_name_path = $target_dir . '/' . $new_file_name;

	$filesize = $_FILES[$_upload_input_name]["size"];                          //  파일 사이즈
	$filemimetype = $_FILES[$_upload_input_name]["type"];                      //  파일 MIME TYPE

	if(($filesize >= DY_MAX_UPLOAD_SIZE) || ($filesize == 0)) {
		$response["msg"] = "업로드는 10MB 이하의 파일만 가능합니다." . $filesize;
	}else{
		if(in_array($extension, $denied_extension)){
			$response["msg"] = "업로드가 불가능한 파일 형식입니다.";
		}elseif( $acceptable != null && !in_array($filemimetype, $acceptable) && !empty($filemimetype) ) {
			$response["msg"] = "업로드가 불가능한 파일 형식입니다.";
		}else{
			if(move_uploaded_file($_FILES[$_upload_input_name]['tmp_name'], $new_file_name_path)) {

				$response["result"] = true;

				$args = array();
				$args["ref_table"] = $_target_table;
				$args["ref_table_idx"] = 0;
				$args["ref_table_column"] = $_target_table_column;
				$args["user_filename"] = $userfilename;
				$args["save_path"] = $save_path;
				$args["save_webpath"] = $save_path;
				$args["save_filename"] = $new_file_name;
				$args["extension"] = $extension;
				$args["mimetype"] = $filemimetype;
				$args["file_size"] = $filesize;
				$args["num"] = $_upload_no;

				$insert_idx = $C_Files ->insertFile($args);

				$response["uploadInfo"] = array(
					"file_idx" => $insert_idx,
					"userfilename" => $userfilename,
					"extension" => $extension,
					"path" => $target_dir_name,
					"new_file_name" => $new_file_name,
				);

			}else{
				$response["msg"] = "업로드에 실패하였습니다.".$new_file_name_path;
			}
		}
	}
}else{
	$response["msg"] = "잘못된 접근입니다.";
}


echo json_encode($response, true);
?>

