<?php

include_once "../_init_.php";

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$mode = $_POST["mode"];

$response = array();

if ($mode == "grid") {
    $response["page"] = 1;
    $response["total"] = 1;

    $excelFileName = $_POST["xls_filename"];
    $excelFileFullPath = DY_XLS_UPLOAD_PATH . "/" . $excelFileName;

    if(file_exists($excelFileFullPath) && !is_dir($excelFileFullPath)) {
        $spreadsheet = IOFactory::load($excelFileFullPath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        if(count($sheetData) > 1) {
            $kindIdx = $_POST["kind_idx"];
            $adManager = new AdvertisingManager();
            $kindFormat = $adManager->getKindFormatData($kindIdx);

            if (!$kindFormat) {
                $response["msg"] = "해당 광고 업체에 엑셀 포맷이 없습니다. 광고 종류 관리에서 포맷을 확인해주세요.";
                $response["rst"] = false;
                echo json_decode($response, true);
                return;
            }

            //extract header
            foreach ($sheetData as $rowData) {
                if(trim($rowData["A"]) != "" && trim($rowData["B"]) != "" && trim($rowData["C"]) != "" && trim($rowData["D"]) != ""){
                    break;
                }else{
                    array_shift($sheetData);
                }
            }

            $headerList = array();
            $excelHeaderList = current($sheetData);

            foreach ($excelHeaderList as $key => $val) {
                $findKey = array_search($val, $kindFormat);
                if ($findKey !== false) {
                    $headerList[$key] = $findKey;
                }
            }

            //extract data
            array_shift($sheetData);

            if(count($sheetData) < 1) {
                $response["msg"] = "엑셀 파일 활성 시트에 데이터가 없습니다.";
                $response["rst"] = false;
                echo json_decode($response, true);
                return;
            }

            $gridData = array();

            foreach ($sheetData as $rowData) {
                $adDatum = array();
                $adDatum["valid"] = true;
				$adDatum["matched"] = false;

                foreach ($headerList as $key => $header) {
                	if ($header == "ad_operation_date") {
                		$adDatum[$header] = date("Y-m-d", strtotime($rowData[$key]));
					} elseif ($header == "ad_winning_bid_date") {
                		if (!array_key_exists("ad_operation_date", $headerList)) {
							$adDatum[$header] = date("Y-m-d", strtotime($rowData[$key]." +1 days"));
						}
					} else {
						$adDatum[$header] = $rowData[$key];
					}
                }

				if ($existDatum = $adManager->getAdDatumByName($kindIdx, $adDatum["ad_name"])) {
					$adDatum["product_type"] = $existDatum["product_type"];
					$adDatum["product_group"] = $existDatum["product_group"];
					$adDatum["product_option_group"] = $existDatum["product_option_group"];

					$adDatum["product_name_list"] = $adManager->getProductNameList($existDatum["idx"]);
				}

				if ($adManager->isExistData(
						$kindIdx,
						$adDatum["ad_name"],
						$adDatum["product_type"],
						$adDatum[$adDatum["product_type"]."_group"],
						$adDatum["ad_operation_date"],
						$adDatum["ad_keyword"])) {
					$adDatum["valid"] = false;
					$adDatum["valid_text"] = "이미 동일한 데이터가 존재합니다.";
				}

                $gridData[] = $adDatum;
            }

            //grouping
            $groupIdx = 0;
            $groupData = array();

            foreach ($gridData as &$gridDatum) {
                $findable = false;

                foreach ($groupData as $key => &$groupDatum) {
                    if ($gridDatum["ad_name"] == $groupDatum["ad_name"] && $gridDatum["ad_product_name"] == $groupDatum["ad_product_name"]) {
                        $findable = true;
                        $gridDatum["group_idx"] = $key;
                        $groupDatum["group_count"] += 1;
                    }
                }

                if (!$findable) {
                    $groupData[$groupIdx] = array("ad_name" => $gridDatum["ad_name"], "ad_product_name" => $gridDatum["ad_product_name"], "group_count"=> 1);
                    $gridDatum["group_idx"] = $groupIdx;
                    $groupIdx++;
                }
            }

            foreach ($groupData as &$groupDatum) {
                foreach ($gridData as &$gridDatum) {
                    if ($groupDatum["ad_name"] == $gridDatum["ad_name"] && $groupDatum["ad_product_name"] == $gridDatum["ad_product_name"])
                        $gridDatum["group_count"] = $groupDatum["group_count"];
                }
            }

            usort($gridData, function($a, $b) {
                $diff = $a["group_idx"] <=> $b["group_idx"];
                if ($diff == 0) {
                    $diff2 = $a["ad_operation_date"] <=> $b["ad_operation_date"];
                    if ($diff2 == 0) {
                        return $a["ad_keyword"] <=> $b["ad_keyword"];
                    }
                    return $diff2;
                } else {
                    return $diff;
                }
            });

            $response["rst"] = true;
            $response["page"] = 1;
            $response["records"] = count($gridData);
            $response["total"] = 1;
            $response["rows"] = $gridData;

        } else {
            $response["msg"] = "엑셀 파일 활성 시트에 데이터가 없습니다.";
            $response["rst"] = false;
            echo json_decode($response, true);
            return;
        }
    } else {
        $response["msg"] = "엑셀 파일을 찾을 수 없습니다.";
        $response["rst"] = false;
        echo json_decode($response, true);
        return;
    }
} elseif ($mode == "execute") {
    $response["rst"] = false;
    $response["msg"] = "";

    $postData = $_POST["list"];
    $adKindIdx = $_POST["kind_idx"];

    $repAdIdx = 0;
    $insertedCnt = 0;

    $repAdDatum = null;

    $pdtManage = new Product();
    $adManager = new AdvertisingManager();

    $adManager->db_connect();;
    $adManager->sqlTransactionBegin();

    foreach ($postData as $postDatum) {
        if ($postDatum["rep_row"] == "true") {
            $repAdDatum = $postDatum;

            //get rep product
            if ($repAdDatum["product_type"] == "product") {
                $productGroup = explode(",", $repAdDatum["product_group"]);
                $repAdDatum["rep_product"] = $productGroup[0];

            } elseif ($repAdDatum["product_type"] == "product_option") {
                $productGroup = explode(",", $repAdDatum["product_option_group"]);
                $product = $pdtManage->getProductOptionData($productGroup[0]);
                $repAdDatum["rep_product"] = $product["product_idx"];
                $repAdDatum["rep_product_option"] = $product["product_option_idx"];
            }
        }

        $postDatum["kind_idx"] = $adKindIdx;
        $postDatum["product_type"] = $repAdDatum["product_type"];
        $postDatum["rep_product"] = $repAdDatum["rep_product"];
        if ($repAdDatum["rep_product_option"])
            $postDatum["rep_product_option"] = $repAdDatum["rep_product_option"];

        $postDatum["product_group"] = $repAdDatum["product_group"];
        $postDatum["product_option_group"] = $repAdDatum["product_option_group"];

        //modify column name for db
        $postDatum["keyword"] = $postDatum["ad_keyword"];
        $postDatum["cost"] = str_replace(",", "", $postDatum["ad_cost"]);
        $postDatum["display_count"] = str_replace(",", "", $postDatum["ad_display_count"]);
        $postDatum["operation_count"] = str_replace(",", "", $postDatum["ad_click_count"]);
        $postDatum["operation_date"] = $postDatum["ad_operation_date"];
        $postDatum["memo"] = $postDatum["ad_memo"];
        $postDatum["is_del"] = "N";

        $insertedIdx = $adManager->insertAdDatum($postDatum);

        if ($insertedIdx) {
            if ($postDatum["rep_row"] == "true") $repAdIdx = $insertedIdx;

            $rst = $adManager->updateAdDatumGroupIdx($insertedIdx, $repAdIdx);
            if ($rst) $insertedCnt++;
        }
    }

    if (count($postData) == $insertedCnt) {
        $adManager->sqlTransactionCommit();
        $response["rst"] = true;
    } else {
        $adManager->sqlTransactionRollback();
        $response["msg"] = "데이터 베이스 등록 중 문제가 발생했습니다.";
    }

    $adManager->db_close();
}

echo json_encode($response, true);
