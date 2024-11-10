<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 정산 이메일 발송 로그 리스트 JSON
 */
//Page Info
$pageMenuIdx = 273;
//Init
include_once "../_init_.php";

$C_ListTable = new ListTable();

$tax_type = $_GET["tax_type"];

//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$order_by = "A.email_regdate DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$avaliable_col = array(
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
	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$qryWhereAry[] = "	 
			A.email_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And A.email_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		if(count($_search_paramAryList["supplier_idx"]) > 0) {
			$qryWhereAry[] = "A.target_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
		}
	}

	//판매처
	if($_search_paramAryList["seller_idx"]){
		if(count($_search_paramAryList["seller_idx"]) > 0) {
			$qryWhereAry[] = "A.target_idx in (" . implode(",", $_search_paramAryList["seller_idx"]) . ")";
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
$args['qry_table_idx'] 	= "A.email_idx";
$args['qry_get_colum'] 	= " A.*
							, M.member_id
                            ";

if($tax_type == "PURCHASE"){
	$args['qry_get_colum'] 	.= " 
								, S.supplier_name as target_name
	";
}else{
	$args['qry_get_colum'] 	.= " 
								, S.vendor_name as target_name
	";
}


$args['qry_table_name'] 	= " DY_SETTLE_TAX_EMAIL A
								Left Outer Join DY_MEMBER M On A.last_member_idx = M.idx
								Left Outer Join DY_SETTLE_TAX_FILE F On A.file_idx = F.file_idx
";
if($tax_type == "PURCHASE"){
	$args['qry_table_name'] 	.= " 
								Left Outer Join DY_MEMBER_SUPPLIER S On A.target_idx = S.member_idx 
	";
}else{
	$args['qry_table_name'] 	.= " 
								Left Outer Join DY_MEMBER_VENDOR S On A.target_idx = S.member_idx 
	";
}

$args['qry_where']			= " A.email_is_del = 'N' And F.tax_type = N'$tax_type'
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
echo json_encode($grid_response, true);
?>