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
			stock_due_delay_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And stock_due_delay_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";
	}

	$date_search_col = "";
	if($_search_paramAryList["date_start2"] != "" && $_search_paramAryList["date_end2"] != ""){
		$qryWhereAry[] = "	 
			stock_due_date >= '".$_search_paramAryList["date_start2"] ."'
			And stock_due_date <= '".$_search_paramAryList["date_end2"] . "'
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
$args['qry_table_idx'] 	= "A.stock_due_delay_idx";
$args['qry_get_colum'] 	= " 
							A.stock_due_delay_idx, A.stock_due_delay_date, A.stock_due_delay_msg, A.stock_due_delay_regdate
							, A.stock_due_delay_file_idx
							, (Select save_filename From DY_FILES F Where A.stock_due_delay_file_idx = F.file_idx And is_use = N'Y') as stock_due_delay_file_name
							, ST.*
							, SO.stock_order_regdate
							, SO.stock_order_date
							, SO.stock_order_in_date
							, S.supplier_name
							, P.product_name, PO.product_option_name
							, CONCAT(S.supplier_addr1, ' ', S.supplier_addr2) as supplier_addr
							, (Select member_id From DY_MEMBER M Where A.last_member_idx = M.idx) as member_id
							, (Case
								When ST.stock_kind = 'STOCK_ORDER' Then '발주'
								When ST.stock_kind = 'RETURN' Then '반품'
								When ST.stock_kind = 'EXCHANGE' Then '교환'
							End) as stock_kind_han
							, C.code_name as stock_status_name
							, IFNULL((SELECT Group_concat(Concat(MU.NAME, '확인함 [', Date_format(SC.stock_due_delay_confirm_regdate, '%Y-%m-%d %H:%i:%s'),']')) 
                            FROM   dy_stock_due_delay_confirm SC 
                            LEFT OUTER JOIN dy_member_user MU ON SC.member_idx = MU.member_idx 
                            WHERE  SC.stock_due_delay_idx = A.stock_due_delay_idx 
                            ORDER  BY SC.stock_due_delay_confirm_regdate ASC),'') AS confirm_list
							 , (Select count(*) From DY_STOCK_DUE_DELAY_CONFIRM SDDC Where SDDC.stock_due_delay_idx = A.stock_due_delay_idx And SDDC.member_idx = N'".$GL_Member["member_idx"]."') as is_confirm
                            ";

$args['qry_table_name'] 	= " 
								DY_STOCK_DUE_DELAY A
								Left Outer Join DY_STOCK ST On A.stock_idx = ST.stock_idx
								Left Outer Join DY_STOCK_ORDER SO On ST.stock_order_idx = SO.stock_order_idx 
								Left Outer Join DY_ORDER O On ST.order_idx = O.order_idx 
								Left Outer Join DY_PRODUCT P On ST.product_idx = P.product_idx 
								Left Outer Join DY_PRODUCT_OPTION PO On ST.product_option_idx = PO.product_option_idx 
								Left Outer Join DY_MEMBER_SUPPLIER S On P.supplier_idx = S.member_idx
								Left Outer Join DY_CODE C On C.parent_code = N'STOCK_STATUS' And C.code = ST.stock_status 
							";
$args['qry_where']			= " A.stock_due_delay_is_del = N'N'
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