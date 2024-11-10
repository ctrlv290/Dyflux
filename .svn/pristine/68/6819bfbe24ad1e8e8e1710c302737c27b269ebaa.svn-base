<?php

include_once "../_init_.php";

$response = array();
$response["msg"] = "";
$response["rst"] = false;

$mode = $_POST["mode"];

if ($mode == "insert_kind") {
    $adKind = array();

    $adKind["idx"] = $_POST["idx"];
    $adKind["seller_idx"] = $_POST["seller_idx"];
    $adKind["name"] = $_POST["kind_name"];
    $adKind["memo"] = $_POST["memo"];
    $adKind["billing_type"] = $_POST["ad_billing_type"];
    $adKind["is_del"] = $_POST["use_yn"] == "Y" ? "N" : "Y";

    $adManager = new AdvertisingManager();
	$response["rst"] = $adManager->insertKind($adKind);

} elseif ($mode == "kind_format") {
    $adKindFormat = array();

    $adKindFormat["idx"] = $_POST["idx"];
    $adKindFormat["kind_idx"] = $_POST["kind_idx"];
    $adKindFormat["ad_name"] = $_POST["ad_name"];
    $adKindFormat["ad_product_name"] = $_POST["ad_product_name"];
    $adKindFormat["ad_keyword"] = $_POST["ad_keyword"];
    $adKindFormat["ad_memo"] = $_POST["ad_memo"];
    $adKindFormat["ad_cost"] = $_POST["ad_cost"];
    $adKindFormat["ad_winning_bid_date"] = $_POST["ad_winning_bid_date"];
    $adKindFormat["ad_operation_date"] = $_POST["ad_operation_date"];
    $adKindFormat["ad_display_count"] = $_POST["ad_display_count"];
    $adKindFormat["ad_click_count"] = $_POST["ad_click_count"];

    $adManager = new AdvertisingManager();
	$response["rst"] = $adManager->updateKindFormat($adKindFormat);

} elseif ($mode == "add_ad_manual" || $mode == "mod_ad_manual") {
    $adData = array();

    $adData["group_idx"] = $_POST["group_idx"];
    $adData["kind_idx"] = $_POST["kind_idx"];
    $adData["ad_name"] = $_POST["ad_name"];
    $adData["is_del"] = "N";

    if ((!$_POST["product_group"] || $_POST["product_group"] == "") && (!$_POST["product_option_group"] || $_POST["product_option_group"] == "")) {
        $response["msg"] = "상품이 입력되지 않았습니다.";
        echo json_encode($response);
        exit;
    }

    $adData["product_type"] = $_POST["ad_group_product_type"];

    if ($adData["product_type"] == "product") {
        $products = explode(",", $_POST["product_group"]);

        $productManager = new Product();

        foreach ($products as $product) {
            $productData = $productManager->getProductData($product);
            if (!$productData || !$productData["product_idx"]) {
                $response["msg"] = "상품 정보가 정확하지 않습니다.";
                echo json_encode($response);
                exit;
            }
        }

        $adData["rep_product"] = $products[0];
        $adData["product_group"] = $_POST["product_group"];

    } elseif ($adData["product_type"] == "product_option") {
        $options = explode(",", $_POST["product_option_group"]);

        $productManager = new Product();

        $i = 0;
        foreach ($options as $option) {
            $optionData = $productManager->getProductOptionData($option);
            if (!$optionData || !$optionData["product_option_idx"]) {
                $response["msg"] = "상품 옵션 정보가 정확하지 않습니다.";
                echo json_encode($response);
                exit;
            }

            if ($i == 0) {
                $adData["rep_product"] = $optionData["product_idx"];
                $adData["rep_product_option"] = $optionData["product_option_idx"];
            }

            $i++;
        }

        $adData["product_option_group"] = $_POST["product_option_group"];
    } else {
        $response["msg"] = "광고 상품 방식 정보가 없습니다.";
        echo json_encode($response);
        exit;
    }

    $indexList = $_POST["ad_spec_idx"];
    $keywordList = $_POST["ad_spec_keyword"];
    $costList = $_POST["ad_spec_cost"];
    $dpCountList = $_POST["ad_spec_dp_cnt"];
    $opCountList = $_POST["ad_spec_op_cnt"];
    $opDateList = $_POST["ad_spec_op_date"];
    $memoList = $_POST["ad_spec_memo"];

    $adManager = new AdvertisingManager();
    $oldGroup = null;

    if ($adData["group_idx"])

    $adManager->sqlTransactionBegin();

    $groupIdx = 0;
    $successCnt = 0;

    foreach ($keywordList as $key => $keyword) {
        if ($mode == "mod_ad_manual") $adData["idx"] = $indexList[$key];
        $adData["keyword"] = $keyword;
        $adData["cost"] = str_replace(",", "", $costList[$key]);
        $adData["display_count"] = str_replace(",", "", $dpCountList[$key]);
        $adData["operation_count"] = str_replace(",", "", $opCountList[$key]);
        $adData["operation_date"] = $opDateList[$key];
        $adData["memo"] = $memoList[$key];

        if($adData["keyword"] && validateDate($adData["operation_date"], 'Y-m-d') && $adData["cost"]) {
        	$isExist = $adManager->isExistData(
        		$adData["kind_idx"],
				$adData["ad_name"],
				$adData["product_type"],
				$adData["product_type"] == "product" ? $adData["product_group"] : $adData["product_option_group"],
				$adData["operation_date"],
				$adData["keyword"]
			);

        	if ($isExist) {
				$response["msg"] = "이름 : " . $adData["ad_name"] . "\n실행일 : ". $adData["operation_date"] . "\n키워드 : " . $adData["keyword"] . "\n중복데이터가 있습니다.";
				break;
			}

            $rst = $adManager->insertAdDatum($adData);
            if ($rst) {
                if ($key == 0) $groupIdx = $rst;
                if ($adManager->updateAdDatumGroupIdx($rst, $groupIdx)) $successCnt++;
            }
        } else {
			$response["msg"] = "키워드, 실행일, 비용 중 정확하지 않은 데이터가 있습니다.";
            break;
        }
    }

    if ($successCnt == count($keywordList)) {
        $response["rst"] = true;
        $response["msg"] = $successCnt."건이 정상 적용되었습니다.";
        $adManager->sqlTransactionCommit();
    } else {
        $adManager->sqlTransactionRollback();
    }

} elseif($mode == "delete_ad_group") {
    $adData = array();

    $adManager = new AdvertisingManager();
    $response["rst"] = $adManager->deleteAdGroup($_POST["group_idx"]);

} elseif($mode == "get_chart_data_by_keyword" || $mode == "get_chart_data_by_product") {
	$groupData = $_POST["group_data"];
	$periodType = $_POST["period_type"];
	$sDate = $_POST["start_date"];
	$eDate = $_POST["end_date"];

	if ($groupData["product_type"] == "product") {
		$productGroup = $groupData["product_group"];
	} elseif ($groupData["product_type"] == "product_option") {
		$productGroup = $groupData["product_option_group"];
	}

	if ($periodType == "m") {
		$sDate = $sDate."-01";
		$eDate = date("Y-m-t", strtotime($eDate."-01"));
	} elseif ($periodType == "y") {
		$sDate = $sDate."-01-01";
		$eDate = date("Y-m-t", strtotime($eDate."-12-01"));
	}

	$adManager = new AdvertisingManager();
	if ($mode == "get_chart_data_by_keyword") {
		$response["rst"] = $adManager->getAdReportByGroup($groupData["kind_idx"], $groupData["seller_idx"], $groupData["ad_name"], $groupData["product_type"], $productGroup, $periodType, $sDate, $eDate);
	} elseif ($mode == "get_chart_data_by_product") {
		$response["rst"] = $adManager->getAdReportByProduct($groupData["kind_idx"], $groupData["seller_idx"], $groupData["ad_name"], $groupData["product_type"], $productGroup, $periodType, $sDate, $eDate);
	}

} elseif ($mode == "get_chart_data_by_market") {
	$sellerIdx = $_POST["seller_idx"];

	$periodType = $_POST["period_type"];
	$sDate = $_POST["date_start"];
	$eDate = $_POST["date_end"];

	$searchCol = $_POST["search_type"];
	$searchVal = $_POST["search_keyword"];

	if ($periodType == "m") {
		$sDate = $sDate."-01";
		$eDate = date("Y-m-t", strtotime($eDate."-01"));
	} elseif ($periodType == "y") {
		$sDate = $sDate."-01-01";
		$eDate = date("Y-m-t", strtotime($eDate."-12-01"));
	}

	$adManager = new AdvertisingManager();
	$response["rst"] = $adManager->getAdReportByMarket($sellerIdx, $periodType, $sDate, $eDate, $searchCol, $searchVal);

} elseif ($mode == "add_ad_charge") {
	$sellerIdxList = $_POST["seller_idx"];
	$chargeDateList   = $_POST["charge_date"];
	$chargeCostList = $_POST["charge_cost"];
	$chargeMemoList   = $_POST["charge_memo"];

	foreach ($sellerIdxList as $key => $sellerIdx) {
		$chargeDate = $chargeDateList[$key];
		$chargeCost = str_replace(",", "", $chargeCostList[$key]);
		$chargeMemo = $chargeMemoList[$key];

		if ($sellerIdx && validateDate($chargeDate, 'Y-m-d') && $chargeCost) {
			$settleMng = new Settle();
			$response["rst"] = $settleMng->insertAdCost($sellerIdx, $chargeDate, 1, $chargeCost, '', $chargeMemoList);
		}
	}
}

if (!$response["rst"]) {
	$response["msg"] = "데이터 베이스 검색 중 문제가 발생했습니다.";
}

echo json_encode($response);