<?php

include_once "../_init_.php";

$listTable = new ListTable();

//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['pagename']	    = $GL_page_nm;

$whereQry = "";

if($_GET["param"]) {
    parse_str($_GET["param"], $searchParamArray);

    foreach ($searchParamArray as $key => $val) {
        if ($key == "seller_idx" && trim($val)) {
			$whereQry .= " AND SL.seller_idx = N'$val'";
        } elseif ($key == "kind_idx" && trim($val)) {
			$whereQry .= " AND A.kind_idx = N'$val'";
        } elseif ($key == "ad_name" && trim($val)) {
			$whereQry .= " AND A.name LIKE '%".$val."%'";
        } elseif ($key == "keyword" && trim($val)) {
            $whereQry .= " AND A.keywords LIKE '%".$val."%'";
        } elseif ($key == "date_start" && trim($val)) {
			if ($searchParamArray["period_type"] == "y") $val = $val."-01-01";
        	if ($searchParamArray["period_type"] == "m") $val = $val."-01";
			$whereQry .= " AND DATE(A.operation_date) >= DATE('$val') ";
        } elseif ($key == "date_end" && trim($val)) {
			if ($searchParamArray["period_type"] == "y") {
				$val = $val."-12-01";
				$val = date("Y-m-t", strtotime($val));
			}
			if ($searchParamArray["period_type"] == "m") {
				$val = $val."-01";
				$val = date("Y-m-t", strtotime($val));
			}
			$whereQry .= " AND DATE(A.operation_date) <= DATE('$val') ";
        } elseif ($key == "search_column" && trim($val)) {
            if(trim($searchParamArray["search_keyword"]) != "") {
            	if ($val == "A.product_group" || $val =="A.product_option_group") {
            		$pdtMng = new Product();
					$pdtList = null;

            		if ($val == "A.product_group") {
						$rst = $pdtMng->execSqlList("SELECT product_idx AS pdt_idx FROM DY_PRODUCT WHERE product_name LIKE N'%".trim($searchParamArray["search_keyword"])."%'");
					} else {
						$rst = $pdtMng->execSqlList("SELECT product_option_idx AS pdt_idx FROM DY_PRODUCT_OPTION WHERE product_option_name LIKE N'%".trim($searchParamArray["search_keyword"])."%'");
					}

					foreach ($rst as $pdt) {
						$pdtList[] = $val." like N'%" . $pdt["pdt_idx"] . "%'";
					}

					$whereQry .= " AND (" . implode(" OR ", $pdtList) . ") ";
				} else {
					$whereQry .= " AND ".$val." like N'%" . trim($searchParamArray["search_keyword"]) . "%'";
				}
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

$args["qry_get_colum"] = "
        A.idx, A.group_idx, A.kind_idx,
        SL.seller_idx, SL.seller_name, K.kind_name, 
        A.ad_name, P.product_name AS rep_product_name, A.operation_date,
        A.product_type, A.product_group, A.product_option_group,
        GROUP_CONCAT(DISTINCT A.keyword) AS keywords
    ";

$args['qry_table_name'] = "
        DY_AD_DATA AS A
            JOIN DY_AD_KINDS AS K ON A.kind_idx = K.idx
            JOIN DY_SELLER AS SL ON K.seller_idx = SL.seller_idx
            JOIN DY_PRODUCT AS P ON A.rep_product = P.product_idx  
    ";

$args['qry_where'] = " A.is_del = N'N' ".$whereQry;
$args['qry_groupby'] = "A.kind_idx, A.ad_name, A.product_group, A.product_option_group";
$args['qry_orderby'] = "A.kind_idx, A.ad_name";

$WholeGetListResult = $listTable -> WholeGetListResult($args);

$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"] = $WholeGetListResult['listRst'];

echo json_encode($grid_response, true);
