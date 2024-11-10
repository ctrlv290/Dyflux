<?php
/**
 * User: kyu
 * Date: 2019-07-01
 * Desc: 일괄 접수 처리를 위한 jqgrid data json respon
 */
//Page Info
$pageMenuIdx = 183;
//Init
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

$order_by = "A.order_idx ASC";

//검색 가능한 컬럼 지정
$available_col = array("product_sale_type");
//검색 가능한 셀렉트박스 값 지정
$available_val = array();

$qryWhereAry = array();

$_search_param = $_GET["param"];
if($_search_param)
{
	$_search_param = urldecode($_search_param);
	$_search_paramAry = explode("&", $_search_param);
	parse_str($_search_param, $_search_paramAryList);
	foreach($_search_paramAry as $sitem) {
		list($col, $val) = explode("=", $sitem);

		$col = trim($col);
		$val = trim($val);

		if(trim($val) && in_array($col, $available_col)) {
			if(trim($col) == "product_sale_type") {
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			} else {
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = "P.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
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
$args['qry_table_idx'] 	= "A.order_idx";
$args['qry_get_colum'] 	= "
                            A.*
                            , SEL.seller_name
                            , N'N' as order_accept_pick
                            , OPM.product_idx
                            , OPM.product_option_idx
                            , P.product_sale_type
                            , SUP.supplier_name
                            , C.code_name
                            ";

$args['qry_table_name'] 	= " DY_ORDER AS A
                                    LEFT OUTER JOIN DY_SELLER AS SEL ON A.seller_idx = SEL.seller_idx
                                    LEFT OUTER JOIN DY_ORDER_PRODUCT_MATCHING AS OPM ON A.order_idx = OPM.order_idx
                                    LEFT OUTER JOIN DY_PRODUCT AS P ON OPM.product_idx = P.product_idx
                                    LEFT OUTER JOIN DY_MEMBER_SUPPLIER AS SUP ON P.supplier_idx = SUP.member_idx
                                    LEFT OUTER JOIN DY_CODE AS C ON P.product_sale_type = C.code
								";
$args['qry_where']			= " 
								A.order_is_del = N'N'
                                AND A.order_progress_step = N'ORDER_ACCEPT_TEMP'
                                AND A.order_idx = A.order_pack_idx
";

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

$WholeGetListResult = $C_ListTable -> WholeGetListResult($args);

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
?>