<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 입고예정 리스트 JSON
 */
//Page Info
$pageMenuIdx = 117;
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

$order_by = "stock_request_date DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$avaliable_col = array(
	"supplier_idx",
	"stock_status",
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
				$col == "stock_status"
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
	    if ($_search_paramAryList["period_type"] == "order_accept_date") {
            $qryWhereAry[] = "
			stock_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And stock_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";
        } else {
            $qryWhereAry[] = "
			stock_is_confirm_date >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And stock_is_confirm_date <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";
        }
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = "P.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
	}

}

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

//엑셀다운로드 시 표시 행수 무한
if($gridPrintForExcelDownload) {
	// paging set
	$args['show_row'] 	= 9999;
	$args['show_page'] 	= 9999;
}

// make select query
$args['qry_table_idx'] 	= "A.stock_idx";
$args['qry_get_colum'] 	= " A.*
							, (Select save_filename From DY_FILES F Where F.file_idx = A.stock_file_idx And F.is_use = N'Y') as stock_file_name
							, SO.stock_order_regdate
							, SO.stock_order_date
							, SO.stock_order_in_date
							, S.supplier_name
							, P.product_name, PO.product_option_name
							, (S.supplier_addr1 + ' ' + S.supplier_addr2) as supplier_addr
							, (Select member_id From DY_MEMBER M Where A.stock_request_member_idx = M.idx) as member_id
							, (Case
								When A.stock_kind = 'STOCK_ORDER' Then '발주'
								When A.stock_kind = 'RETURN' Then '반품'
								When A.stock_kind = 'EXCHANGE' Then '교환'
								When A.stock_kind = 'BACK' Then '회수'
							End) as stock_kind_han
							, C.code_name as stock_status_name
							
							, (Case
								When A.order_idx = 0 And A.stock_order_idx <> 0 Then A.stock_order_idx
								When A.order_idx <> 0 And A.stock_order_idx = 0 Then A.order_idx
							End)
							as stock_code
							, convert(varchar(20), stock_request_date, 120) as stock_request_date_convert
							, (Case
								When A.stock_is_confirm = 'N' Then '미확정'
								When A.stock_is_confirm = 'Y' Then '확정'
							End) as stock_is_confirm_han
							, Convert(varchar(30), stock_request_date, 120) as stock_request_date2
							, Case When O.order_idx is not null Then
								O.receive_name + '/' + O.receive_hp_num + '/' + O.receive_addr1 + ' ' + O.receive_addr2
							 Else '' End as order_info
                            ";

$args['qry_table_name'] 	= " DY_STOCK A 
								Left Outer Join DY_STOCK_ORDER SO On A.stock_order_idx = SO.stock_order_idx 
								Left Outer Join DY_ORDER O On A.order_idx = O.order_idx 
								Left Outer Join DY_PRODUCT P On A.product_idx = P.product_idx 
								Left Outer Join DY_PRODUCT_OPTION PO On A.product_option_idx = PO.product_option_idx 
								Left Outer Join DY_MEMBER_SUPPLIER S On P.supplier_idx = S.member_idx
								Left Outer Join DY_CODE C On C.parent_code = N'STOCK_STATUS' And C.code = A.stock_status 
							";
$args['qry_where']			= " A.stock_kind in ('STOCK_ORDER', 'RETURN', 'EXCHANGE', 'BACK')
								And (
									A.stock_order_idx = 0
									Or
									(SO.stock_order_is_del = 'N' And SO.stock_order_is_order in (N'Y', N'T'))
								)
								And A.stock_is_proc in (N'Y', N'P')
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
if(!$gridPrintForExcelDownload) {
	echo json_encode($grid_response, true);
}
?>