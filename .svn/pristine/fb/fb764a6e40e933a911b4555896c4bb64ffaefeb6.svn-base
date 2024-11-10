<?php

$pageMenuIdx = 297;
$pagePermissionIdx = 297;

include_once "../_init_.php";

$listTable = new ListTable();

//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$searchParam = $_GET["param"];

if($searchParam) {
    $searchParam = urldecode($searchParam);
    $_search_paramAry = explode("&", $searchParam);
	parse_str($searchParam, $_search_paramAryList);

    foreach($_search_paramAry as $sitem) {
        list($col, $val) = explode("=", $sitem);

        if(trim($val) && $col == "kind_name") {
            $qryWhereAry[] = $col . " like N'%" . $val . "%'";
        }
    }

	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$qryWhereAry[] = "	 
			A.operation_date >= '".$_search_paramAryList["date_start"]."' 
			AND A.operation_date <= '".$_search_paramAryList["date_end"]."' 
		";
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
        K.kind_name, S.seller_name, A2.group_cnt,
        CASE
            WHEN A.rep_product_option IS NULL 
            THEN P.product_name
            ELSE CONCAT(P.product_name, ' ', PO.product_option_name)
        END AS rep_product_full_name
    ";
$args['qry_table_name'] = " 
        DY_AD_DATA AS A
            LEFT OUTER JOIN (
                SELECT MIN(idx) AS min_idx, COUNT(*) AS group_cnt FROM DY_AD_DATA GROUP BY group_idx
            ) AS A2 ON A.idx = A2.min_idx
            LEFT OUTER JOIN DY_AD_KINDS AS K ON A.kind_idx = K.idx
            LEFT OUTER JOIN DY_PRODUCT AS P ON A.rep_product = P.product_idx
            LEFT OUTER JOIN DY_PRODUCT_OPTION AS PO ON A.rep_product_option = PO.product_option_idx
            LEFT OUTER JOIN DY_SELLER AS S ON K.seller_idx = S.seller_idx
    ";

$args['qry_where'] = " A.is_del = 'N' ";
if(count($qryWhereAry) > 0)
{
	$args['qry_where'] .= " And " . join(" And ", $qryWhereAry);
}

$args['qry_groupby'] = "";
$args['qry_orderby'] = "A.idx ASC";

$WholeGetListResult = $listTable -> WholeGetListResult($args);

$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"] = $WholeGetListResult['listRst'];

echo json_encode($grid_response, true);
