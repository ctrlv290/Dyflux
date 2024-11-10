<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 로그인이력 리스트 JSON
 */
//Page Info
$pageMenuIdx = 58;
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

$order_by = "A.member_login_regdate DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$avaliable_col = array(
	"nameidip",
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
			if($col == "nameidip")
			{
				$qryWhereAry[] = " ( 
							U.name like N'%" . $val . "%' 
							Or V.vendor_name like N'%" . $val . "%' 
							Or S.supplier_name like N'%" . $val . "%' 
							Or M.member_id like N'%" . $val . "%' 
							Or A.member_login_regip like N'%" . $val . "%'
				)";
			}else {
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$qryWhereAry[] = "
			A.member_login_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And A.member_login_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59'
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
$args['qry_table_idx'] 	= "A.member_login_idx";
$args['qry_get_colum'] 	= " A.*
							, M.member_id
							, (
								Case 
									When M.member_type = 'USER' Or M.member_type = 'ADMIN' Then U.name
									When M.member_type = 'VENDOR' Then '[벤더사]'+V.vendor_name
									When M.member_type = 'SUPPLIER' Then '[공급처]'+S.supplier_name
								End
							) as member_name
                            ";

$args['qry_table_name'] 	= " DY_MEMBER_LOGIN_LOG A
								Left Outer Join DY_MEMBER M On M.idx = A.member_idx 
								Left Outer Join DY_MEMBER_USER U On U.member_idx = A.member_idx 
								Left Outer Join DY_MEMBER_VENDOR V On V.member_idx = A.member_idx 
								Left Outer Join DY_MEMBER_SUPPLIER S On S.member_idx = A.member_idx 
";
$args['qry_where']			= " M.is_del = 'N' ";
if($GL_Member["member_type"] == "VENDOR" || $GL_Member["member_type"] == "SUPPLIER"){
	$args['qry_where'] .= " And A.member_idx = N'". $GL_Member["member_idx"] ."'";
}
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