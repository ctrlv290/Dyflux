<?php

/**
 * Class AdvertisingManager
 * User : kyu
 * Date : 2019-12-03
 */

class AdvertisingManager extends DBConn
{
    public function getKind($idx) {
        $qry = "SELECT * FROM DY_AD_KINDS WHERE idx = N'$idx'";

        $this->db_connect();
        $rst = $this->execSqlOneRow($qry);
        $this->db_close();

        return $rst;
    }

    public function getKinds() {
        $qry = "SELECT K.*, CONCAT(S.seller_name, ' ', kind_name) AS kind_full_name  
                FROM DY_AD_KINDS AS K LEFT OUTER JOIN DY_SELLER AS S ON K.seller_idx = S.seller_idx";

        $this->db_connect();
        $rst = $this->execSqlList($qry);
        $this->db_close();

        return $rst;
    }

    public function getAdDatum($adIdx) {
        $qry = "SELECT * FROM DY_AD_DATA WHERE is_del = N'N' AND idx = N'$adIdx' LIMIT 1";

        $this->db_connect();
        $rst = $this->execSqlOneRow($qry);
        $this->db_close();

        return $rst;
    }

    public function getAdDatumByName($kindIdx, $adName) {
    	$qry = "SELECT * FROM DY_AD_DATA WHERE is_del = N'N' AND kind_idx = N'$kindIdx' AND ad_name = N'$adName' LIMIT 1";

    	$this->db_connect();
    	$rst = $this->execSqlOneRow($qry);
    	$this->db_close();

    	return $rst;
	}

	public function getProductNameList($idx) {
    	$qry = "SELECT product_type, product_group, product_option_group FROM DY_AD_DATA WHERE is_del = N'N' AND idx = N'$idx'";
    	$this->db_connect();
    	$rst = $this->execSqlOneRow($qry);

    	if (!$rst) return null;

    	$pdtIdxNmMap = array();

    	$pdtList = explode(",", $rst[$rst["product_type"]."_group"]);
    	foreach ($pdtList as $pdt) {
    		$qry = "
    			SELECT ".$rst["product_type"]."_name AS p_name FROM DY_".strtoupper($rst["product_type"])." 
    			WHERE 
    				".$rst["product_type"]."_is_del = N'N' 
					AND ".$rst["product_type"]."_is_use = N'Y'
					AND ".$rst["product_type"]."_idx = N'$pdt'
    		";

			$name = $this->execSqlOneCol($qry);
			$pdtIdxNmMap[$pdt] = $name;
		}

    	$this->db_close();

    	return $pdtIdxNmMap;
	}

    public function isExistData($kindIdx, $adName, $productType, $productGroup, $operationDate, $keyword = null) {
        $wherePtd = " AND product_group = N'$productGroup' ";
        if ($productType == "product_option")
        	$wherePtd = " AND product_option_group = N'$productGroup' ";

    	$qry = "
			SELECT idx FROM DY_AD_DATA 
			WHERE kind_idx = N'$kindIdx'
				AND ad_name = N'$adName'
				".$wherePtd."
				AND operation_date = N'$operationDate'
        ";

    	if ($keyword != null) {
    		$qry .= " AND keyword = N'$keyword'";
		}

        $this->db_connect();
        $rst = $this->execSqlOneCol($qry);
        $this->db_close();

        return $rst > 0;
    }

    public function getBillingTypes() {
        $qry = "SELECT code, code_name FROM DY_CODE WHERE parent_code = N'AD_BILLING_TYPE' AND is_del = N'N'";

        $this->db_connect();
        $rst = $this->execSqlList($qry);
        $this->db_close();

        return $rst;
    }

    public function getKindFormatData($kindIdx) {
        $qry = "SELECT * FROM DY_AD_KIND_FORMAT WHERE kind_idx = N'$kindIdx'";

        $this->db_connect();
        $rst = $this->execSqlOneRow($qry);
        $this->db_close();

        return $rst;
    }

    public function insertKind($adKind) {
        global $GL_Member;
        $ip = $_SERVER["REMOTE_ADDR"];
        $member = $GL_Member["member_idx"];

        $qry = "
            INSERT INTO DY_AD_KINDS
            (
                idx,
                seller_idx,
                kind_name,
                billing_type,
                memo,
                is_del,
                reg_ip,
                reg_member,
                last_member
            )
            VALUES
            (
                N'".$adKind["idx"]."',
                N'".$adKind["seller_idx"]."',
                N'".$adKind["name"]."',
                N'".$adKind["billing_type"]."',
                N'".$adKind["memo"]."',
                N'".$adKind["is_del"]."',
                N'$ip',
                N'$member',
                N'$member'
            )
            ON DUPLICATE KEY UPDATE
                seller_idx = N'".$adKind["seller_idx"]."',
                kind_name = N'".$adKind["name"]."',
                billing_type = N'".$adKind["billing_type"]."',
                memo = N'".$adKind["memo"]."',
                is_del = N'".$adKind["is_del"]."',
                mod_date = NOW(),
                mod_ip = N'$ip',
                mod_member = N'$member',
                last_member = N'$member'
        ";

        $this->db_connect();
        $rst = $this->execSqlInsert($qry);
        $this->db_close();

        return $rst;
    }

    public function updateKindFormat($adKindFormat) {
        global $GL_Member;
        $ip = $_SERVER["REMOTE_ADDR"];
        $member = $GL_Member["member_idx"];

        $qry = "
            INSERT INTO DY_AD_KIND_FORMAT
            (
                idx,
                kind_idx,
                ad_name,
                ad_product_name,
                ad_keyword,
                ad_cost,
                ad_winning_bid_date,
                ad_operation_date,
                ad_display_count,
                ad_click_count,
                ad_memo,
                reg_ip,
                reg_member,
                last_member
            )
            VALUES
            (
                N'".$adKindFormat["idx"]."',
                N'".$adKindFormat["kind_idx"]."',
                N'".$adKindFormat["ad_name"]."',
                N'".$adKindFormat["ad_product_name"]."',
                N'".$adKindFormat["ad_keyword"]."',
                N'".$adKindFormat["ad_cost"]."',
                N'".$adKindFormat["ad_winning_bid_date"]."',
                N'".$adKindFormat["ad_operation_date"]."',
                N'".$adKindFormat["ad_display_count"]."',
                N'".$adKindFormat["ad_click_count"]."',
                N'".$adKindFormat["ad_memo"]."',
                N'$ip',
                N'$member',
                N'$member'
            )
            ON DUPLICATE KEY UPDATE
                ad_name = N'".$adKindFormat["ad_name"]."',
                ad_product_name = N'".$adKindFormat["ad_product_name"]."',
                ad_keyword = N'".$adKindFormat["ad_keyword"]."',
                ad_cost = N'".$adKindFormat["ad_cost"]."',
                ad_winning_bid_date = N'".$adKindFormat["ad_winning_bid_date"]."',
                ad_operation_date = N'".$adKindFormat["ad_operation_date"]."',
                ad_display_count = N'".$adKindFormat["ad_display_count"]."',
                ad_click_count = N'".$adKindFormat["ad_click_count"]."',
                ad_memo = N'".$adKindFormat["ad_memo"]."',
                mod_date = NOW(),
                mod_ip = N'$ip',
                mod_member = N'$member',
                last_member = N'$member'
        ";

        $this->db_connect();
        $rst = $this->execSqlInsert($qry);
        $this->db_close();

        return $rst;
    }

    public function insertAdDatum($adData) {
        global $GL_Member;
        $ip = $_SERVER["REMOTE_ADDR"];
        $member = $GL_Member["member_idx"];

        $adProductTypeColQry = "";
        $adProductTypeValQry = "";
        $adProductTypeUdpQry = "";

        if ($adData["product_type"] == "product") {
            $adProductTypeColQry = "rep_product, product_group";
            $adProductTypeValQry = "N'".$adData["rep_product"]."', N'".$adData["product_group"]."'";

            $adProductTypeUdpQry = "rep_product = N'".$adData["rep_product"]."',
                rep_product_option = NULL,
                product_group = N'".$adData["product_group"]."',
                product_option_group = NULL";
        } elseif ($adData["product_type"] == "product_option") {
            $adProductTypeColQry = "rep_product, rep_product_option, product_option_group";
            $adProductTypeValQry = "N'".$adData["rep_product"]."', N'".$adData["rep_product_option"]."', N'".$adData["product_option_group"]."'";

            $adProductTypeUdpQry = "rep_product = N'".$adData["rep_product"]."',
                rep_product_option = N'".$adData["rep_product_option"]."',
                product_group = NULL,
                product_option_group = N'".$adData["product_option_group"]."'";
        }

        $qry = "
            INSERT INTO DY_AD_DATA
            (
                idx,
                kind_idx,
                ad_name,
                product_type,
                ".$adProductTypeColQry.",
                keyword,
                cost,
                operation_date,
                display_count,
                operation_count,
                memo,
                is_del,
                reg_ip,
                reg_member,
                last_member
            )
            VALUES
            (
                N'".$adData["idx"]."',
                N'".$adData["kind_idx"]."',
                N'".$adData["ad_name"]."',
                N'".$adData["product_type"]."',
                ".$adProductTypeValQry.",
                N'".$adData["keyword"]."',
                N'".$adData["cost"]."',
                N'".$adData["operation_date"]."',
                N'".$adData["display_count"]."',
                N'".$adData["operation_count"]."',
                N'".$adData["memo"]."',
                N'".$adData["is_del"]."',
                N'$ip',
                N'$member',
                N'$member'
            )
            ON DUPLICATE KEY UPDATE
                kind_idx = N'".$adData["kind_idx"]."',
                ad_name = N'".$adData["ad_name"]."',
                product_type = N'".$adData["product_type"]."',
                ".$adProductTypeUdpQry.",
                keyword = N'".$adData["keyword"]."',
                cost = N'".$adData["cost"]."',
                operation_date = N'".$adData["operation_date"]."',
                display_count = N'".$adData["display_count"]."',
                operation_count = N'".$adData["operation_count"]."',
                memo = N'".$adData["memo"]."',
                is_del = N'".$adData["is_del"]."',
                mod_date = NOW(),
                mod_ip = N'$ip',
                mod_member = N'$member',
                last_member = N'$member'
        ";

        $this->db_connect();
        $rst = $this->execSqlInsert($qry);
        $this->db_close();

        return $rst;
    }

    public function deleteAdDatum($idx) {
        $qry = "UPDATE DY_AD_DATA SET is_del = N'Y' WHERE idx = N'$idx'";

        $this->db_connect();
        $rst = $this->execSqlUpdate($qry);
        $this->db_close();

        return $rst;
    }

    public function deleteAdGroup($groupIdx) {
        $qry = "UPDATE DY_AD_DATA SET is_del = N'Y' WHERE group_idx = N'$groupIdx'";

        $this->db_connect();
        $rst = $this->execSqlUpdate($qry);
        $this->db_close();

        return $rst;
    }

    public function updateAdDatumGroupIdx($idx, $groupIdx) {
        $qry = "UPDATE DY_AD_DATA SET group_idx = N'$groupIdx' WHERE idx = N'$idx'";

        $this->db_connect();
        $rst = $this->execSqlUpdate($qry);
        $this->db_close();

        return $rst;
    }

    public function getAdReport($sellerIdx = null, $kindIdx = null, $startDate = null, $endDate = null, $includeProduct = null, $includeOption = null) {
        $whereSeller = $sellerIdx ? " AND S.seller_idx = N'$sellerIdx' " : "";
        $whereKind = $kindIdx ? " AND A.kind_idx = N'$kindIdx' " : "";
        $whereStartDate = $startDate ? " AND DATE(A.operation_date) >= DATE('$startDate')" : "";
        $whereEndDate = $endDate ? " AND DATE(A.operation_date) <= DATE('$endDate')" : "";
        $whereIncludeProduct = $includeProduct ? " AND A.product_group LIKE '%".$includeProduct."%'" : "";
        $whereIncludeOption = $includeOption ? " AND A.product_option_group LIKE '%".$includeOption."%'" : "";

        $qry = "
            SELECT S.seller_idx, S.seller_name, K.kind_name, A.*
            FROM DY_AD_DATA AS A
                JOIN DY_AD_KINDS AS K ON A.kind_idx = K.idx
                JOIN DY_SELLER AS S ON K.seller_idx = S.seller_idx
            WHERE A.is_del = N'N'
            ".$whereSeller."
            ".$whereKind."
            ".$whereStartDate."
            ".$whereEndDate."
            ".$whereIncludeProduct."
            ".$whereIncludeOption."
        ";

        $this->db_connect();
        $targetAdList = $this->execSqlList($qry);

        if (!$targetAdList) {
            $this->db_close();
            return false;
        }

        $rstList = array();

        foreach ($targetAdList as $targetAd) {
            $settleWhereQry = "";
            if ($targetAd["product_type"] == "product") {
                $settleWhereQry = " AND S.product_idx IN (". $targetAd["product_group"] .") ";
            } elseif ($targetAd["product_type"] == "product_option") {
                $settleWhereQry = " AND S.product_option_idx IN (". $targetAd["product_option_group"] .") ";
            }

            $qry = "
                SELECT IFNULL(COUNT(*), 0) AS total_order_count, IFNULL(SUM(IFNULL(order_cnt, 0)), 0) AS total_product_count, IFNULL(SUM(IFNULL(settle_sale_supply, 0)), 0) AS total_sale_amount
                FROM DY_SETTLE AS S
                WHERE
                    S.seller_idx = N'".$targetAd["seller_idx"]."'
                    AND DATE(S.settle_regdate) = DATE('".$targetAd["operation_date"]."') 
                    AND S.settle_is_del = N'N'
                    ".$settleWhereQry."
            ";

            $rst = $this->execSqlOneRow($qry);

            $rstList[] = array_merge($targetAd, $rst);
        }

        $this->db_close();

        return $rstList;
    }

    public function getAdReportByGroup($kindIdx, $sellerIdx, $adName, $pdtType, $pdtGroup, $periodType, $sDate, $eDate) {
		$report = array();
		$report["success"] = false;
		$report["period"] = array();
		$report["keywords"] = array();

		$qry = "";

    	$whereADPdt = "AND AD.product_group = N'$pdtGroup'";
    	$whereSTPdt = "AND ST.product_idx IN ($pdtGroup)";

		if ($pdtType == "product_option") {
			$whereADPdt = "AND AD.product_option_group = N'$pdtGroup'";
			$whereSTPdt = "AND ST.product_option_idx IN ($pdtGroup)";
		}

    	$this->db_connect();

    	if ($periodType == "d") {
			$qry = "
				WITH CTE_AD AS
				(
					SELECT
						AD.idx, AD.ad_name, K.seller_idx, S.seller_name, K.kind_name,
						AD.product_group, AD.keyword, AD.cost, AD.display_count, AD.operation_count,
						AD.operation_date
					FROM DY_AD_DATA AS AD
						JOIN DY_AD_KINDS AS K ON AD.kind_idx = K.idx
						JOIN DY_SELLER AS S ON K.seller_idx = S.seller_idx
					WHERE 
						AD.kind_idx = N'$kindIdx'
						AND K.seller_idx = N'$sellerIdx'
						AND AD.ad_name = N'$adName'
						AND AD.product_type = N'$pdtType'
						$whereADPdt
						AND AD.is_del = N'N'
						AND DATE(AD.operation_date) >= DATE(N'$sDate')
						AND DATE(AD.operation_date) <= DATE(N'$eDate') 
				),
				CTE_ST AS
				(
					SELECT
						ST.seller_idx,
						ST.settle_date,
						ST.product_idx,
						ST.product_option_idx,
						ST.product_name,
						ST.product_option_name,
						SUM(IFNULL(ST.order_amt, 0)) AS sum_sale_amount,
						COUNT(*) AS total_order_count
					FROM DY_SETTLE AS ST
					WHERE
						ST.seller_idx = N'$sellerIdx'
						$whereSTPdt
						AND ST.settle_is_del = N'N'
						AND DATE(ST.settle_date) >= DATE(N'$sDate')
						AND DATE(ST.settle_date) <= DATE(N'$eDate')
					GROUP BY ST.settle_date
				)
				
				SELECT A.*, B.total_operation_count
				FROM
				(
					SELECT CA.idx, CA.ad_name, CA.seller_idx, CA.seller_name, CA.kind_name,
						CA.keyword, CA.cost, CA.display_count, CA.operation_count,
						CA.operation_date, CS.settle_date, CS.product_idx, CS.product_option_idx,
						CS.product_name, CS.product_option_name,
						CS.sum_sale_amount, CS.total_order_count
					FROM CTE_AD AS CA
						LEFT JOIN CTE_ST AS CS 
							ON CA.seller_idx = CS.seller_idx
							AND DATE(CA.operation_date) = DATE(CS.settle_date)
							AND CA.product_group LIKE CONCAT('%', CS.product_idx, '%')
					UNION
					SELECT CA.idx, CA.ad_name, CA.seller_idx, CA.seller_name, CA.kind_name,
						CA.keyword, CA.cost, CA.display_count, CA.operation_count,
						CA.operation_date, CS.settle_date, CS.product_idx, CS.product_option_idx,
						CS.product_name, CS.product_option_name,
						CS.sum_sale_amount, CS.total_order_count
					FROM CTE_ST AS CS
						LEFT JOIN CTE_AD AS CA
							ON CA.seller_idx = CS.seller_idx
							AND DATE(CA.operation_date) = DATE(CS.settle_date)
							AND CA.product_group LIKE CONCAT('%', CS.product_idx, '%')
				) AS A
				LEFT OUTER JOIN
				(
					SELECT SUM(IFNULL(operation_count, 0)) AS total_operation_count, operation_date
					FROM CTE_AD
					GROUP BY operation_date
				) AS B ON A.operation_date = B.operation_date
			";
		} elseif ($periodType == "m" || $periodType == "y") {
			$qryDateFormat = "%Y-%m";
    		if ($periodType == "y") $qryDateFormat = "%Y";

			$qry = "
				WITH CTE_AD AS
				(
					SELECT
						AD.idx, AD.ad_name, K.seller_idx, S.seller_name, K.kind_name,
						AD.product_group, AD.keyword, 
						SUM(AD.cost) AS cost,
						SUM(AD.display_count) AS display_count, 
						SUM(AD.operation_count) AS operation_count,
						DATE_FORMAT(AD.operation_date, '$qryDateFormat') AS operation_date
					FROM DY_AD_DATA AS AD
						JOIN DY_AD_KINDS AS K ON AD.kind_idx = K.idx
						JOIN DY_SELLER AS S ON K.seller_idx = S.seller_idx
					WHERE 
						AD.kind_idx = N'$kindIdx'
						AND K.seller_idx = N'$sellerIdx'
						AND AD.ad_name = N'$adName'
						AND AD.product_type = N'$pdtType'
						$whereADPdt
						AND AD.is_del = N'N'
						AND DATE_FORMAT(AD.operation_date, '$qryDateFormat') >= DATE_FORMAT('$sDate', '$qryDateFormat')
						AND DATE_FORMAT(AD.operation_date, '$qryDateFormat') <= DATE_FORMAT('$eDate', '$qryDateFormat')
					GROUP BY DATE_FORMAT(AD.operation_date, '$qryDateFormat'), keyword
				),
				CTE_ST AS
				(
					SELECT
						ST.seller_idx,
						DATE_FORMAT(ST.settle_date, '$qryDateFormat') AS settle_date,
						ST.product_idx,
						ST.product_option_idx,
						ST.product_name,
						ST.product_option_name,
						SUM(IFNULL(ST.order_amt, 0)) AS sum_sale_amount,
						COUNT(*) AS total_order_count
					FROM DY_SETTLE AS ST
					WHERE
						ST.seller_idx = N'$sellerIdx'
						$whereSTPdt
						AND ST.settle_is_del = N'N'
						AND DATE_FORMAT(ST.settle_date, '$qryDateFormat') >= DATE_FORMAT('$sDate', '$qryDateFormat')
						AND DATE_FORMAT(ST.settle_date, '$qryDateFormat') <= DATE_FORMAT('$eDate', '$qryDateFormat')
					GROUP BY DATE_FORMAT(ST.settle_date, '$qryDateFormat')
				)
				
				SELECT A.*, B.total_operation_count
				FROM
				(
					SELECT CA.idx, CA.ad_name, CA.seller_idx, CA.seller_name, CA.kind_name,
						CA.keyword, CA.cost, CA.display_count, CA.operation_count,
						CA.operation_date, CS.settle_date, CS.product_idx, CS.product_option_idx,
						CS.product_name, CS.product_option_name,
						CS.sum_sale_amount, CS.total_order_count
					FROM CTE_AD AS CA
						LEFT JOIN CTE_ST AS CS 
							ON CA.seller_idx = CS.seller_idx
							AND CA.operation_date = CS.settle_date
							AND CA.product_group LIKE CONCAT('%', CS.product_idx, '%')
					UNION
					SELECT CA.idx, CA.ad_name, CA.seller_idx, CA.seller_name, CA.kind_name,
						CA.keyword, CA.cost, CA.display_count, CA.operation_count,
						CA.operation_date, CS.settle_date, CS.product_idx, CS.product_option_idx,
						CS.product_name, CS.product_option_name,
						CS.sum_sale_amount, CS.total_order_count
					FROM CTE_ST AS CS
						LEFT JOIN CTE_AD AS CA
							ON CA.seller_idx = CS.seller_idx
							AND CA.operation_date = CS.settle_date
							AND CA.product_group LIKE CONCAT('%', CS.product_idx, '%')
				) AS A
				LEFT OUTER JOIN
				(
					SELECT SUM(IFNULL(operation_count, 0)) AS total_operation_count, operation_date
					FROM CTE_AD
					GROUP BY operation_date
				) AS B ON A.operation_date = B.operation_date
			";
		}

		$report["period"] = getPeriodList($periodType, $sDate, $eDate);

		$rst = $this->execSqlList($qry);
		$this->db_close();

		if ($rst) {
			//keyword index
			$i = 0;
			foreach ($rst as &$data) {
				if ($data["keyword"]) {
					if (!array_key_exists($data["keyword"], $report["keywords"])){
						$report["keywords"][$data["keyword"]] = $i;
						$data["keywordIdx"] = $i++;
					} else {
						$data["keywordIdx"] = $report["keywords"][$data["keyword"]];
					}
				}
			}

			$report["list"] = $rst;
			$report["success"] = true;
		}

    	return $report;
	}

	public function getAdReportByProduct($kindIdx, $sellerIdx, $adName, $pdtType, $pdtGroup, $periodType, $sDate, $eDate) {
		$report = array();
		$report["success"] = false;
		$report["period"] = getPeriodList($periodType, $sDate, $eDate);

		$qry = "";

		$whereADPdt = "AND AD.product_group = N'$pdtGroup'";
		$whereSTPdt = "AND ST.product_idx IN ($pdtGroup)";

		if ($pdtType == "product_option") {
			$whereADPdt = "AND AD.product_option_group = N'$pdtGroup'";
			$whereSTPdt = "AND ST.product_option_idx IN ($pdtGroup)";
		}

		$this->db_connect();

		if ($periodType == "d") {
			$qry = "
				WITH CTE_AD AS
				(
					SELECT
						AD.idx, AD.ad_name, K.seller_idx, S.seller_name, K.kind_name,
						AD.product_type, AD.product_group, AD.product_option_group,
						AD.operation_date,
						GROUP_CONCAT(AD.keyword) AS keywords, 
						SUM(AD.cost) AS cost, 
						SUM(AD.display_count) AS display_count, 
						SUM(AD.operation_count) AS operation_count
					FROM DY_AD_DATA AS AD
						JOIN DY_AD_KINDS AS K ON AD.kind_idx = K.idx
						JOIN DY_SELLER AS S ON K.seller_idx = S.seller_idx
					WHERE 
						AD.kind_idx = N'$kindIdx'
						AND K.seller_idx = N'$sellerIdx'
						AND AD.ad_name = N'$adName'
						AND AD.product_type = N'$pdtType'
						$whereADPdt
						AND AD.is_del = N'N'
						AND DATE(AD.operation_date) >= DATE('$sDate')
						AND DATE(AD.operation_date) <= DATE('$eDate')
					GROUP BY AD.operation_date
				),
				CTE_ST AS
				(
					SELECT
						ST.seller_idx,
						ST.settle_date,
						ST.product_idx,
						ST.product_option_idx,
						ST.product_name,
						ST.product_option_name,
						SUM(IFNULL(ST.order_amt, 0)) AS sum_sale_amount,
						COUNT(*) AS total_order_count
					FROM DY_SETTLE AS ST
					WHERE
						ST.seller_idx = N'$sellerIdx'
						$whereSTPdt
						AND ST.settle_is_del = N'N'
						AND DATE(ST.settle_date) >= DATE('$sDate')
						AND DATE(ST.settle_date) <= DATE('$eDate')
					GROUP BY ST.settle_date
				)
				
				SELECT A.*
				FROM
				(
					SELECT CA.idx, CA.ad_name, CA.seller_idx, CA.seller_name, CA.kind_name,
						CA.keywords, CA.cost, CA.display_count, CA.operation_count,
						CA.operation_date, CS.settle_date, CS.sum_sale_amount, CS.total_order_count
					FROM CTE_AD AS CA
						LEFT JOIN CTE_ST AS CS 
							ON CA.seller_idx = CS.seller_idx
							AND CA.operation_date = CS.settle_date
							AND CA.product_group LIKE CONCAT('%', CS.product_idx, '%')
					UNION
					SELECT CA.idx, CA.ad_name, CA.seller_idx, CA.seller_name, CA.kind_name,
						CA.keywords, CA.cost, CA.display_count, CA.operation_count,
						CA.operation_date, CS.settle_date, CS.sum_sale_amount, CS.total_order_count
					FROM CTE_ST AS CS
						LEFT JOIN CTE_AD AS CA
							ON CA.seller_idx = CS.seller_idx
							AND CA.operation_date = CS.settle_date
							AND CA.product_group LIKE CONCAT('%', CS.product_idx, '%')
				) AS A
			";
		} elseif ($periodType == "m" || $periodType == "Y") {
			$qryDateFormat = "%Y-%m";
			if ($periodType == "y") $qryDateFormat = "%Y";

			$qry = "
				WITH CTE_AD AS
				(
					SELECT
						AD.idx, AD.ad_name, K.seller_idx, S.seller_name, K.kind_name,
						AD.product_group,
						GROUP_CONCAT(AD.keyword) AS keywords,
						SUM(AD.cost) AS cost,
						SUM(AD.display_count) AS display_count, 
						SUM(AD.operation_count) AS operation_count,
						DATE_FORMAT(AD.operation_date, '$qryDateFormat') AS operation_date
					FROM DY_AD_DATA AS AD
						JOIN DY_AD_KINDS AS K ON AD.kind_idx = K.idx
						JOIN DY_SELLER AS S ON K.seller_idx = S.seller_idx
					WHERE 
						AD.kind_idx = N'$kindIdx'
						AND K.seller_idx = N'$sellerIdx'
						AND AD.ad_name = N'$adName'
						AND AD.product_type = N'$pdtType'
						$whereADPdt
						AND AD.is_del = N'N'
						AND DATE_FORMAT(AD.operation_date, '$qryDateFormat') >= DATE_FORMAT('$sDate', '$qryDateFormat')
						AND DATE_FORMAT(AD.operation_date, '$qryDateFormat') <= DATE_FORMAT('$eDate', '$qryDateFormat')
					GROUP BY DATE_FORMAT(AD.operation_date, '$qryDateFormat'), keyword
				),
				CTE_ST AS
				(
					SELECT
						ST.seller_idx,
						DATE_FORMAT(ST.settle_date, '$qryDateFormat') AS settle_date,
						ST.product_idx,
						ST.product_option_idx,
						ST.product_name,
						ST.product_option_name,
						SUM(IFNULL(ST.order_amt, 0)) AS sum_sale_amount,
						COUNT(*) AS total_order_count
					FROM DY_SETTLE AS ST
					WHERE
						ST.seller_idx = N'$sellerIdx'
						$whereSTPdt
						AND ST.settle_is_del = N'N'
						AND DATE_FORMAT(ST.settle_date, '$qryDateFormat') >= DATE_FORMAT('$sDate', '$qryDateFormat')
						AND DATE_FORMAT(ST.settle_date, '$qryDateFormat') <= DATE_FORMAT('$eDate', '$qryDateFormat')
					GROUP BY DATE_FORMAT(ST.settle_date, '$qryDateFormat')
				)
				
				SELECT A.*
				FROM
				(
					SELECT CA.idx, CA.ad_name, CA.seller_idx, CA.seller_name, CA.kind_name,
						CA.keywords, CA.cost, CA.display_count, CA.operation_count,
						CA.operation_date, CS.settle_date, CS.sum_sale_amount, CS.total_order_count
					FROM CTE_AD AS CA
						LEFT JOIN CTE_ST AS CS 
							ON CA.seller_idx = CS.seller_idx
							AND CA.operation_date = CS.settle_date
							AND CA.product_group LIKE CONCAT('%', CS.product_idx, '%')
					UNION
					SELECT CA.idx, CA.ad_name, CA.seller_idx, CA.seller_name, CA.kind_name,
						CA.keywords, CA.cost, CA.display_count, CA.operation_count,
						CA.operation_date, CS.settle_date, CS.sum_sale_amount, CS.total_order_count
					FROM CTE_ST AS CS
						LEFT JOIN CTE_AD AS CA
							ON CA.seller_idx = CS.seller_idx
							AND CA.operation_date = CS.settle_date
							AND CA.product_group LIKE CONCAT('%', CS.product_idx, '%')
				) AS A
			";
		}

		$rst = $this->execSqlList($qry);
		$this->db_close();

		if ($rst) {
			$report["list"] = $rst;
			$report["ad_name"] = $adName;
			$report["success"] = true;
		}

		return $report;
	}

	public function getAdReportByMarket($sellerIdx, $periodType, $sDate, $eDate, $sCol, $sVal) {
		$report = array();
		$report["success"] = false;

    	$whereQry = "";
    	if ($sellerIdx) {
    		$whereQry = " AND S.seller_idx = N'$sellerIdx' ";
		}

		$qryDateFormat = "%Y";
		if ($periodType == "m") $qryDateFormat .= "-%m";
		if ($periodType == "d") $qryDateFormat .= "-%m-%d";

		$qry = "
			SELECT
				S.seller_idx, S.seller_name,
				GROUP_CONCAT(DISTINCT product_group) AS product_group,
				GROUP_CONCAT(DISTINCT product_option_group) AS product_option_group,
				SUM(A.cost) AS sum_cost, 
				SUM(A.display_count) AS sum_display_count,
				SUM(A.operation_count) AS sum_operation_count,
				SUM(IFNULL(ST.settle_sale_sum, 0)) AS sum_sale_amt,
				SUM(IFNULL(ST.settle_sale_profit, 0)) AS sum_profit_amt,
				DATE_FORMAT(A.operation_date, '$qryDateFormat') AS operation_date
			FROM DY_AD_DATA AS A
				JOIN DY_AD_KINDS AS K ON A.kind_idx = K.idx
				JOIN DY_SELLER AS S ON K.seller_idx = S.seller_idx
				LEFT OUTER JOIN DY_SETTLE AS ST ON S.seller_idx = ST.seller_idx
					AND (A.product_group LIKE CONCAT('%', ST.product_idx, '%') OR A.product_option_group LIKE CONCAT('%', ST.product_option_idx, '%'))
					AND DATE(A.operation_date) = DATE(ST.settle_regdate)
				WHERE
					A.is_del = N'N'
					$whereQry
					AND DATE(A.operation_date) >= DATE('$sDate')
					AND DATE(A.operation_date) <= DATE('$eDate')
			GROUP BY DATE_FORMAT(A.operation_date, '$qryDateFormat'), S.seller_idx
			ORDER BY seller_idx, operation_date
		";

		$this->db_connect();
		$rst = $this->execSqlList($qry);
		$this->db_close();

		if ($rst) {
			$report["list"] = $rst;
			$report["success"] = true;
		} else {
			$report["list"] = array();
		}

		return $report;
	}
}
