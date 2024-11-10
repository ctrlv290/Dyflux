<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 엑셀 업로드 처리 프로세스
 */

include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["msg"] = "";
$response["script"] = "";
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
);

if(isset($_FILES["xls_file"]) && $_POST["xls_type"])
{
	$fileObj = $_FILES["xls_file"];

	$target_dir = DY_XLS_UPLOAD_PATH;
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
		$response["msg"] = "업로드는 10MB 이하의 파일만 가능합니다. 파일사이즈 (" . $filesize . ")";
	}else{
		if(!in_array($filemimetype, $acceptable) && (!empty($filemimetype))) {
			$response["msg"] = "업로드가 불가능한 파일 형식입니다.";
		}else{
			if(move_uploaded_file($fileObj['tmp_name'], $new_file_name_path)) {

				$response["result"] = true;

				if($_POST["xls_type"] == "seller_regist"){
					$response["script"] = "parent.Seller.SellerXlsRead('".$new_file_name."');parent.hideLoader();";
				}elseif($_POST["xls_type"] == "vendor_regist"){
					$response["script"] = "parent.Vendor.VendorXlsRead('".$new_file_name."');parent.hideLoader();";
				}elseif($_POST["xls_type"] == "supplier_regist"){
					$response["script"] = "parent.Supplier.SupplierXlsRead('".$new_file_name."');parent.hideLoader();";
				}elseif($_POST["xls_type"] == "product_option_regist"){
					$response["script"] = "parent.Product.ProductOptionXlsRead('".$new_file_name."');parent.hideLoader();";
				}elseif($_POST["xls_type"] == "product_regist"){
					$response["script"] = "parent.Product.ProductXlsRead('".$new_file_name."');parent.hideLoader();";
				}elseif($_POST["xls_type"] == "product_select_update"){
					$response["script"] = "parent.Product.ProductXlsUpdateRead('".$new_file_name."');parent.hideLoader();";
				}elseif($_POST["xls_type"] == "product_matching_regist"){
					$response["script"] = "parent.ProductMatching.ProductMatchingXlsRead('".$new_file_name."');parent.hideLoader();";
				}elseif($_POST["xls_type"] == "product_matching_delete"){
					$response["script"] = "parent.ProductMatching.ProductMatchingXlsRead('".$new_file_name."');parent.hideLoader();";
				}elseif($_POST["xls_type"] == "stock_move"){
					$response["script"] = "parent.StockProduct.StockMoveXlsRead('".$new_file_name."');parent.hideLoader();";
				}elseif($_POST["xls_type"] == "order_invoice"){
					$response["script"] = "parent.OrderShipped.OrderInvoiceUploadXlsRead('".$new_file_name."', '".$userfilename."');parent.hideLoader();";
				}elseif($_POST["xls_type"] == "order_invoice_delete_xls"){
					$response["script"] = "parent.OrderShipped.OrderInvoiceDeleteXlsRead('".$new_file_name."', '".$userfilename."');parent.hideLoader();";
				}elseif($_POST["xls_type"] == "order_shipped_upload"){
					$response["script"] = "parent.OrderShipped.OrderShippedUploadXlsRead('".$new_file_name."', '".$userfilename."');parent.hideLoader();";
				}elseif($_POST["xls_type"] == "order_shipped_cancel_xls"){
					$response["script"] = "parent.OrderShipped.OrderShippedCancelXlsRead('".$new_file_name."', '".$userfilename."');parent.hideLoader();";
				}elseif($_POST["xls_type"] == "seller_cancel_upload"){
					$response["script"] = "parent.CSCancel.CSCancelXlsRead('".$new_file_name."', '".$userfilename."');parent.hideLoader();";
				}elseif($_POST["xls_type"] == "loss_upload"){
					$response["script"] = "parent.SettleLoss.LossUploadXlsRead('".$new_file_name."', '".$userfilename."');parent.hideLoader();";
				}elseif($_POST["xls_type"] == "add_ad_excel") {
                    $response["script"] = "parent.addAdExcelReadFile('".$new_file_name."');";
                }elseif($_POST["xls_type"] == "transaction_adjust_upload") {
                    $response["script"] = "parent.SettleTransaction.TransactionAdjustUploadXlsRead('".$new_file_name."');parent.hideLoader();";
                }elseif($_POST["xls_type"] == "transaction_upload") {
                    $response["script"] = "parent.SettleTransaction.TransactionUploadXlsRead('".$new_file_name."');";
                }
			}else{
				$response["msg"] = "업로드에 실패하였습니다.";
			}
		}
	}
}else{
	$response["msg"] = "잘못된 접근입니다.";
}

if(!$response["result"]){
	echo '
		<script>
			alert("'.$response["msg"].'");
			parent.hideLoader();
		</script>
	';
}else{
	echo '
		<script>
			'.$response["script"].'
		</script>
	';
}

?>