<?php

include_once "../_init_.php";

$C_ListTable = new ListTable();

//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$order_by = "A.reg_date DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];

$available_col = array(
	"search_column",
);
$available_search_column = array(
	"product_name",
	"product_option_name",
	"product_option_idx",
);

$qryWhereAry = array();
if($_search_param) {
	$_search_param = urldecode($_search_param);
	$_search_paramAry = explode("&", $_search_param);
	parse_str($_search_param, $_search_paramAryList);
	foreach ($_search_paramAry as $sitem) {
		list($col, $val) = explode("=", $sitem);

		$col = trim($col);
		$val = trim($val);

		if (trim($val) && in_array($col, $available_col)) {
			if (trim($col) == "search_column") {
				if (trim($_search_paramAryList["search_keyword"]) != "" && in_array($val, $available_search_column)) {
					if ($val == "product_name") {
						$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
					} else {
						$qryWhereAry[] = "A.product_idx in (Select product_idx From DY_PRODUCT_OPTION Where product_option_is_del = N'N' And " . $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%')";
					}
				}
			} else {
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		$qryWhereAry[] = "A.seller_idx in (" . implode(",", $_search_paramAryList["seller_idx"]) . ")";
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
$args['qry_table_idx'] 	= "A.idx";
$args['qry_get_colum'] = "
	A.*, 
	SELLER.seller_name, P.product_name, PO.product_option_name, 
	IFNULL(MU.name, '없음') as reg_member_name, IFNULL(MU2.name, '없음') as mod_member_name
";

$args['qry_table_name'] = "
	DY_PRODUCT_SPECIAL_VALUE AS A
		JOIN DY_SELLER AS SELLER ON A.seller_idx = SELLER.seller_idx
		JOIN DY_PRODUCT AS P ON A.product_idx = P.product_idx
		JOIN DY_PRODUCT_OPTION AS PO ON A.product_option_idx = PO.product_option_idx
		LEFT JOIN DY_MEMBER_USER MU ON A.reg_member = MU.member_idx
		LEFT JOIN DY_MEMBER_USER MU2 ON A.mod_member = MU2.member_idx
";

$args['qry_where'] = "
	A.is_del = 'N' AND SELLER.seller_is_del = 'N'
	AND P.product_is_use = 'Y' AND P.product_is_del = 'N' AND P.product_is_trash = 'N'
	AND PO.product_option_is_use = 'Y' AND PO.product_option_is_del = 'N'
";

if(count($qryWhereAry) > 0) {
	$args['qry_where'] .= " And " . join(" And ", $qryWhereAry);
}

$args['qry_groupby']		= "";
$args['qry_orderby']		= $order_by;

$WholeGetListResult = $C_ListTable -> WholeGetListResult($args);

$listRst 			= "";
$listRst 			= $WholeGetListResult['listRst'];
$listRst_cnt 		= count($listRst);

$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"] = $WholeGetListResult['listRst'];

echo json_encode($grid_response, true);
