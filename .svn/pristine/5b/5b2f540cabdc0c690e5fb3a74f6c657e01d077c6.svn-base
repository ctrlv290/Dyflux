<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 휴지통 상품 리스트 JSON
 */
//Page Info
$pageMenuIdx = 63;
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

$order_by = "A.product_is_trash_date DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$avaliable_col = array(
	"product_category_l_idx",
	"product_category_m_idx",
	"product_sale_type",
	"supplier_idx",
	"search_column",
);
$qryWhereAry = array();
if($_search_param)
{
	$_search_param = urldecode($_search_param);
	$_search_paramAry = explode("&", $_search_param);
	parse_str($_search_param, $_search_paramAryList);
	foreach($_search_paramAry as $sitem) {
		list($col, $val) = explode("=", $sitem);

		if(trim($val) && in_array($col, $avaliable_col)) {
			if(
				$col == "product_category_l_idx"
				|| $col == "product_category_m_idx"
				|| $col == "product_sale_type"
			) {
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			}elseif(trim($col) == "search_column"){
				if(trim($_search_paramAryList["search_keyword"]) != "") {
					$qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
				}
			}else{
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	$date_search_col = "";
	if($_search_paramAryList["period_search_type"] == "regdate"){
		$qryWhereAry[] = "	 
			A.product_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And A.product_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";
	}elseif($_search_paramAryList["period_search_type"] == "is_trash_date"){
		$qryWhereAry[] = "	 
			A.product_is_trash_date >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And A.product_is_trash_date <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";
	}
	//재고 상태
	if($_search_paramAryList["stock_status"] != "") {

	}
	//판매 상태
	if($_search_paramAryList["sale_status"] != "") {

	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = "A.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
	}

}

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

//엑셀다운로드 시 표시 행수 무한
if($gridPrintForExcelDownload) {
	// paging set
	$args['show_row'] 	= 9999;
	$args['show_page'] 	= 9999;
}

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

// make select query
$args['qry_table_idx'] 	= "A.product_idx";
$args['qry_get_colum'] 	= " A.product_idx
							, A.product_img_1, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_1) as product_img_filename_1
							, A.product_img_2, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_2) as product_img_filename_2
							, A.product_img_3, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_3) as product_img_filename_3
							, A.product_img_4, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_4) as product_img_filename_4
							, A.product_img_5, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_5) as product_img_filename_5
							, A.product_img_6, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_6) as product_img_filename_6
							, A.product_name
							, (Select name From DY_CATEGORY C Where C.category_idx = A.product_category_l_idx) as category_l_name, A.product_category_l_idx 
							, (Select name From DY_CATEGORY C Where C.category_idx = A.product_category_m_idx) as category_m_name, A.product_category_m_idx
							, P.member_idx, P.supplier_name
							, S.seller_idx, S.seller_name
							, A.product_supplier_name, A.product_regdate, A.product_is_trash_date
							, Case 
								When A.product_vendor_show = 'SHOW' Then 'Y'
								When A.product_vendor_show = 'HIDE' Then 'N'
								When A.product_vendor_show = 'ALL' Then '전체노출'
								When A.product_vendor_show = 'SELECTED' Then '특정업체노출'
							End as product_vendor_show
							, Case
								When product_sale_type = 'SELF' Then '사입/자체'
								When product_sale_type = 'CONSIGNMENT' Then '위탁'
							End as product_sale_type
							, Convert(varchar(30), A.product_regdate, 120) as product_regdate2
							, Convert(varchar(30), A.product_is_trash_date, 120) as product_is_trash_date2
                            ";

$args['qry_table_name'] 	= " DY_PRODUCT A 
								Left Outer Join DY_MEMBER_SUPPLIER P On A.supplier_idx = P.member_idx
								Left Outer Join DY_SELLER S On A.seller_idx = S.seller_idx
";
$args['qry_where']			= " A.product_is_del = 'N' And A.product_is_trash = 'Y' And A.product_is_use = 'Y'";
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
/*
$WholeGetListResult['listRst'];
$WholeGetListResult['pageInfo'][''];
array("startpage"=>$startpage,"endpage"=>$endpage,"prevpage"=>$prevpage,"nextpage"=>$nextpage,"total"=>$total,"searchVar"=>$searchVar,"totalpages"=>$totalpages);
$WholeGetListResult['listPageLink'];
$WholeGetListResult['searchForm'];
$WholeGetListResult['sortLink'][];
*/
//******************************* 리스트 기본 설정 끝 ******************************//
//print_r($WholeGetListResult);

$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"] = $WholeGetListResult['listRst'];
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
if(!$gridPrintForExcelDownload) {
	echo json_encode($grid_response, true);
}
?>