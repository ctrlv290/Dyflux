<?php

$pageMenuIdx = 298;
$pagePermissionIdx = 298;

include_once "../_init_.php";

$listTable = new ListTable();

//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['pagename']	    = $GL_page_nm;

$whereQry = "";
$inWhereQry = "";

if($_GET["param"]) {
    parse_str($_GET["param"], $searchParamArray);

    foreach ($searchParamArray as $key => $val) {
        if ($key == "seller_idx" && trim($val)) {
            $inWhereQry .= " AND SL.seller_idx = N'$val'";
        } elseif ($key == "kind_idx" && trim($val)) {
            $inWhereQry .= " AND AD.kind_idx = N'$val'";
        } elseif ($key == "ad_name" && trim($val)) {
            $inWhereQry .= " AND AD.name LIKE '%".$val."%'";
        } elseif ($key == "keyword" && trim($val)) {
            $whereQry .= " AND A.keyword LIKE '%".$val."%'";
        } elseif ($key == "date_start" && trim($val)) {
            $inWhereQry .= " AND DATE(AD.operation_date) >= DATE('$val')";
        } elseif ($key == "date_end" && trim($val)) {
            $inWhereQry .= " AND DATE(AD.operation_date) <= DATE('$val')";
        } elseif ($key == "search_column" && trim($val)) {
            if(trim($searchParamArray["search_keyword"]) != "") {
                $whereQry .= " AND ".$val." like N'%" . trim($searchParamArray["search_keyword"]) . "%'";
            }
        }
    }
}

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

// make select query
$args['qry_table_idx'] = "A.idx";
$args['qry_get_colum'] = "
    A.*, 
    SUM(AD2.cost) AS total_cost, 
    SUM(AD2.operation_count) AS total_operation_count,
    COUNT(*) AS keyword_count
    ";

$args['qry_table_name'] = "
        (
        SELECT
            AD.*, SL.seller_idx, K.kind_name, SL.seller_name,
            IFNULL(COUNT(S.settle_idx), 0) AS total_order_count,
            IFNULL(SUM(IFNULL(S.order_cnt, 0)), 0) AS total_product_count,
            IFNULL(SUM(IFNULL(S.settle_sale_supply, 0)), 0) AS total_sale_amount,
            P2.product_name AS rep_product_name,
            P.product_name,
            PO.product_option_name
        FROM DY_AD_DATA AS AD
            JOIN DY_AD_KINDS AS K ON AD.kind_idx = K.idx
            JOIN DY_SELLER AS SL ON K.seller_idx = SL.seller_idx
            JOIN DY_PRODUCT AS P2 ON AD.rep_product = P2.product_idx
            LEFT OUTER JOIN DY_SETTLE AS S 
                ON K.seller_idx = S.seller_idx 
                AND DATE(AD.operation_date) = DATE(S.settle_regdate) 
                AND AD.product_group LIKE CONCAT('%', S.product_idx, '%')
                AND S.settle_is_del = N'N'
            LEFT OUTER JOIN DY_PRODUCT AS P ON S.product_idx = P.product_idx
            LEFT OUTER JOIN DY_PRODUCT_OPTION AS PO ON S.product_option_idx = PO.product_option_idx
        WHERE AD.is_del = N'N'".$inWhereQry."
        GROUP BY AD.idx
        ) AS A
            LEFT OUTER JOIN DY_AD_DATA AS AD2 
                ON AD2.ad_name = A.ad_name 
                AND AD2.kind_idx = A.kind_idx
                AND
					CASE
						WHEN A.product_type = N'product'
						THEN AD2.product_group = A.product_group
						ELSE AD2.product_option_group = A.product_option_group
					END
				AND DATE(AD2.operation_date) = DATE(A.operation_date)
                AND AD2.is_del = N'N'
    ";

$args['qry_groupby'] = "A.idx";
$args['qry_where'] = " 1 = 1 ".$whereQry;
$args['qry_orderby'] = "A.kind_idx, A.operation_date, ad_name";

$WholeGetListResult = $listTable -> WholeGetListResult($args);

$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"] = $WholeGetListResult['listRst'];

echo json_encode($grid_response, true);
