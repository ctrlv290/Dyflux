<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 매칭 팝업에서 사용되는 상품 검색 Grid List
 */
//Page Info
$pageMenuIdx = 305;
//Init
include_once "../_init_.php";

$listTable = new ListTable();

//******************************* 리스트 기본 설정 ******************************//
// get info set
$page               = (!$page) ? '1' : $page;
$args['page']       = (!$page) ? '1' : $page;
$args['searchVar']  = $searchVar;
$args['searchWord'] = $searchWord;
$args['sortBy']     = $sortBy;
$args['sortType']   = $sortType;
$args['pagename']   = $GL_page_nm;

$order_by = "A.product_idx ASC ";
if($_GET["sidx"] && $_GET["sord"]) {
    $order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$avaliable_col = array(
    "product_name",
	"product_sale_type",
);
$qryWhereAry = array();
if($_search_param) {
    $_search_param = urldecode($_search_param);
    $_search_paramAry = explode("&", $_search_param);
    parse_str($_search_param, $_search_paramAryList);
    foreach($_search_paramAry as $sitem) {
        list($col, $val) = explode("=", $sitem);

        if(trim($val) && in_array($col, $avaliable_col)) {
            $qryWhereAry[] = $col . " like N'%" . $val . "%'";
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
$args['qry_table_idx'] 	= "A.product_idx";
$args['qry_get_colum'] 	= " A.product_idx, A.product_name, A.product_sale_type, S.supplier_name, C.code_name ";

$args['qry_table_name'] 	= " DY_PRODUCT A
									LEFT OUTER JOIN DY_MEMBER_SUPPLIER S On A.supplier_idx = S.member_idx
									LEFT OUTER JOIN DY_CODE AS C ON A.product_sale_type = C.code
";
$args['qry_where']			= " A.product_is_del = 'N' AND A.product_is_use = N'Y' AND A.product_is_trash = N'N'";

if(count($qryWhereAry) > 0)
{
    $args['qry_where'] .= " And " . join(" And ", $qryWhereAry);
}
$args['qry_groupby']		= "";
$args['qry_orderby']		= $order_by;

// image set
$args['search_img'] 		= "";
$args['search_img_tag']		= "";
$args['front_img'] 			= "";
$args['next_img'] 			= "";

$args['add_element']		= "";
$args['seeQry'] 			= "0";

$args['addFormStr'] 		= '';

$WholeGetListResult = $listTable -> WholeGetListResult($args);

$listRst 			= "";
$listRst 			= $WholeGetListResult['listRst'];
$listRst_cnt 		= count($listRst);

$startRowNum = $WholeGetListResult['pageInfo']['total'] - (($args['show_row'] * $args['page']) - $args['show_row']) ;

$article_number = $WholeGetListResult['pageInfo']['total'];
$article_number = $WholeGetListResult['pageInfo']['total'] - ($args['show_row'] * ($page-1));

$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"] = $WholeGetListResult['listRst'];

echo json_encode($grid_response, true);
