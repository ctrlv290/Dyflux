<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품재고조회 리스트 JSON
 */
//Page Info
$pageMenuIdx = 110;
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

$order_by = "STOCK.product_option_idx DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
	"product_category_l_idx",
	"product_category_m_idx",
	"product_sale_type",
	"without_soldout",
	"soldout_status",
	"soldout_temp_status",
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

		if(trim($val) && in_array($col, $available_col)) {
			if(
				$col == "product_category_l_idx"
				|| $col == "product_category_m_idx"
				|| $col == "product_sale_type"
			) {
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			}elseif(trim($col) == "without_soldout"){
				$qryWhereAry[] = " stock_amount_NORMAL > 0 ";
			}elseif(trim($col) == "soldout_status"){
				//품절상태 조건
				if($val == "except_soldout"){
					//품절제외
					$qryWhereAry[] = " product_option_soldout = N'N' ";
				}elseif($val == "soldout"){
					//품절
					$qryWhereAry[] = " product_option_soldout = N'Y' ";
				}
			}elseif(trim($col) == "soldout_temp_status"){
				//일시품절상태 조건
				if($val == "except_soldout_temp"){
					//일시품절제외
					$qryWhereAry[] = " product_option_soldout_temp = N'N' ";
				}elseif($val == "soldout_temp"){
					//일시품절
					$qryWhereAry[] = " product_option_soldout_temp = N'Y' ";
				}
			}elseif(trim($col) == "search_column"){
                if(trim($_search_paramAryList["search_keyword"]) != "") {
                    if($val == "product_name_option_name"){
                        $qryWhereAry[] = "
							P.product_name like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'
							Or 
							PO.product_option_name like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'
						";
                    }else {
                        $qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
                    }
                }
			}else{
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	$date_search_col = "";
	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
		$qryWhereAry[] = "	 
			P.product_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And P.product_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59' 
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

// make select query
$args['qry_table_idx'] 	= "PO.product_option_idx";
$args['qry_get_colum'] 	= " 
							STOCK.*
							, P.product_img_main
							, P.product_img_1, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_1) as product_img_filename_1
							, P.product_img_2, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_2) as product_img_filename_2
							, P.product_img_3, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_3) as product_img_filename_3
							, P.product_img_4, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_4) as product_img_filename_4
							, P.product_img_5, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_5) as product_img_filename_5
							, P.product_img_6, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_6) as product_img_filename_6
							, P.product_name
							, IFNULL((Select name From DY_CATEGORY C Where C.category_idx = P.product_category_l_idx), '') as category_l_name, P.product_category_l_idx 
							, IFNULL((Select name From DY_CATEGORY C Where C.category_idx = P.product_category_m_idx), '') as category_m_name, P.product_category_m_idx
							, PO.product_option_name
							, PO.product_option_warning_count
							, PO.product_option_danger_count
							, PO.product_option_soldout
							, PO.product_option_soldout_temp
							, P.product_regdate
							, S.supplier_name
							, Matching.stock_amount_ACCEPT
                            ";

$args['qry_table_name'] 	= " 
								(
								Select 
									ST.product_idx, ST.product_option_idx, ST.stock_unit_price
									, Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End) as stock_amount_NORMAL
									, Sum(Case When stock_status = 'ABNORMAL' Then stock_amount * stock_type Else 0 End) as stock_amount_ABNORMAL
									, Sum(Case When stock_status = 'BAD' Then stock_amount * stock_type Else 0 End) as stock_amount_BAD
									, Sum(Case When stock_status = 'BAD_OUT_EXCHANGE' Then stock_amount * stock_type Else 0 End) as stock_amount_BAD_OUT_EXCHANGE
									, Sum(Case When stock_status = 'BAD_OUT_RETURN' Then stock_amount * stock_type Else 0 End) as stock_amount_BAD_OUT_RETURN
									, Sum(Case When stock_status = 'HOLD' Then stock_amount * stock_type Else 0 End) as stock_amount_HOLD
									, Sum(Case When stock_status = 'FAC_RETURN_EXCHNAGE' Then stock_amount * stock_type Else 0 End) as stock_amount_FAC_RETURN_EXCHNAGE
									, Sum(Case When stock_status = 'FAC_RETURN_BACK' Then stock_amount * stock_type Else 0 End) as stock_amount_FAC_RETURN_BACK
									, Sum(Case When stock_status = 'LOSS' Then stock_amount * stock_type Else 0 End) as stock_amount_LOSS
									, Sum(Case When stock_status = 'DISPOSAL' Then stock_amount * stock_type Else 0 End) as stock_amount_DISPOSAL
									, Sum(Case When stock_status = 'DISPOSAL_PERMANENT' Then stock_amount * stock_type Else 0 End) as stock_amount_DISPOSAL_PERMANENT
									, Sum(Case When stock_status = 'INVOICE' Then stock_amount * stock_type Else 0 End) as stock_amount_INVOICE
								From DY_STOCK ST
									Where stock_is_del = N'N' And stock_is_confirm = N'Y'
									Group by ST.product_idx, product_option_idx, stock_unit_price
								) as STOCK 
								Inner Join DY_PRODUCT P On STOCK.product_idx = P.product_idx 
								Inner Join DY_PRODUCT_OPTION PO On STOCK.product_option_idx = PO.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On P.supplier_idx = S.member_idx
								Left Outer Join (
									Select product_option_idx, Sum(product_option_cnt)  as stock_amount_ACCEPT
										From DY_ORDER_PRODUCT_MATCHING OPM
											Inner Join DY_ORDER DO On OPM.order_idx = DO.order_idx
										Where 
											OPM.order_matching_is_del = N'N'
											And DO.order_is_del = N'N'
											And DO.order_progress_step = N'ORDER_ACCEPT'
										Group by product_option_idx 
								) Matching On PO.product_option_idx = Matching.product_option_idx
";
$args['qry_where']			= " 
								P.product_sale_type = N'SELF' 
								And P.product_is_del = N'N' 
								And P.product_is_trash = N'N' 
								And P.product_is_use = N'Y'
								And PO.product_option_is_use = N'Y'
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