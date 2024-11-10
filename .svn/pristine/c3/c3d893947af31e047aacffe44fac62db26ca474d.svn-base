<?php

//Page Info
$pageMenuIdx = 294;

//Init
include "../_init_.php";

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$mode = $_POST["mode"];
$act = $_POST["act"];

$operationTime = $_POST["operation_time"];

if (! preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $operationTime)) {
    $operationTime = "23:59:59";
}

$operationDatetime = $_POST["operation_date"] . " " . $operationTime . ".000";

$xlsFileName = $_POST["xls_filename"];
$xlsValidRow = $_POST["xls_valid_row"];

$response = array();
$response["result"] = false;
$response["msg"] = "";
$response["err"] = array();
$response["userdata"] = array("field_name" => "");

$xlsFileFullPath = DY_XLS_UPLOAD_PATH . "/" . $xlsFileName;

if(file_exists($xlsFileFullPath) && !is_dir($xlsFileFullPath)) {
    $spreadsheet = IOFactory::load($xlsFileFullPath);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    if(count($sheetData) > 1) {

        array_shift($sheetData);
        $listData = array();
        $insertedCount = 0;
        $rowIndex = 0;

        $stockConn = new Stock();
        $productConn = new Product();

        global $GL_controlStockFromAbleStatusList;

        foreach($sheetData as $row) {
            if(trim($row["A"]) == "" && trim($row["B"]) == "" && trim($row["C"]) == "" && trim($row["D"]) == "" && trim($row["E"]) == "") {
                continue;
            }

            $rowValid = true;

            //Row Index
            $rowIndex++;
            $row["xls_idx"] = $rowIndex;

            //정보 초기화
            $productOptionIdx = "";
            $stockUnitPrice = "";
            $stockStatus = "";
            $stockStatusCode = "";

            $stockCurrentAmount = 0;
            $stockNewAmount = 0;

            $stockMsg = $row["E"];

            //A : 상품옵션코드
            $val = trim($row["A"]);

            if ($val == "") {
                $rowValid = false;
                $row["A"] = "상품옵션코드가 입력되지 않았습니다.";
            }else{
                $rst = $productConn->getProductOptionData($val);
                if (!$rst) {
                    $rowValid = false;
                    $row["A"] = "상품옵션코드가 정확하지 않습니다.";
                }else{
                    $productOptionIdx = $val;
                    $row["product_name"] = $rst["product_name"];
                    $row["product_option_name"] = $rst["product_option_name"];
                }
            }

            //B : 원가
            $val = str_replace(",", "", trim($row["B"]));

            if ($val == "") {
                $rowValid = false;
                $row["B"] = "원가가 입력되지 않았습니다.";
            }else{
                if(!is_numeric($val)){
                    $rowValid = false;
                    $row["B"] = "원가는 숫자만 허용됩니다.";
                }else{
                    if(intval($val) == 0){
                        $rowValid = false;
                        $row["B"] = "원가는 0이 될 수 없습니다.";
                    }else{
                        $stockUnitPrice = $val;
                    }
                }
            }

            //C : 상태
            $val = trim($row["C"]);

            if ($val == "") {
                $rowValid = false;
                $row["C"] = "상태값이 입력되지 않았습니다.";
            } else {
                $stockStatusCode = array_search($val, $GL_controlStockFromAbleStatusList);
                if ($stockStatusCode) {
                    $stockStatus = $val;
                } else {
                    $rowValid = false;
                    $row["C"] = "상태값이 정확하지 않습니다.";
                }
            }

            //현재 수량 확인
            $stockCurrentAmount = $stockConn->getStockAmountByPrice($productOptionIdx, "NORMAL", $stockUnitPrice, $operationDatetime);
            $row["current_stock_amount"] = $stockCurrentAmount;

            //D : 변경 수량
            $val = str_replace(",", "", trim($row["D"]));

            if ($val == "") {
                $rowValid = false;
                $row["D"] = "작업수량이 입력되지 않았습니다.";

            }else{
                if(!is_numeric($val)){
                    $rowValid = false;
                    $row["D"] = "작업수량은 숫자만 허용됩니다.";
                }else{
                    if ($stockCurrentAmount == $val) {
                        $rowValid = false;
                        $row["D"] = "현재 수량과 변경 수량이 동일합니다.";
                    }

                    $stockNewAmount = $val;
                }
            }

            $row["valid"] = $rowValid;

            if($act == "grid") { //리스트로 반환
                $listData[] = $row;
            } elseif($act == "save") { //적용!!
                if($rowValid) {
                    $rst = $stockConn->changeAmount($productOptionIdx, $stockUnitPrice, $stockStatusCode, $stockCurrentAmount, $stockNewAmount, $stockMsg, $operationDatetime);
                    if($rst["result"]){
                        $insertedCount++;
                    }
                }
            }
        }

    } else {
        $response["msg"] = "파일에 데이터가 존재하지 않습니다.";
    }

    if($act == "grid") {
        //그리드 리스트 리턴일 때..
        $response["page"] = 1;
        $response["records"] = count($listData);
        $response["total"] = 1;
        $response["rows"] = $listData;
    }elseif($act == "save"){
        //적용일 때..
        $response["result"] = true;
        $response["msg"] = $insertedCount;
    }

} else {
    $response["msg"] = "파일 경로나 이름이 잘못되었습니다. 다시 확인하여 주세요.";
}

echo json_encode($response, true);

