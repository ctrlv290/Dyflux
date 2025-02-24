<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 리스트 JSON
 */
//Page Info
$pageMenuIdx = 35;
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

$order_by = "A.regdate DESC";
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
	"supplier_idx",
	"search_column",
);
$available_search_column = array(
	"product_name",
	"product_option_name",
	"product_option_idx",
    "product_option_barcode_GTIN"
);
$qryWhereAry = array();
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
			if(
				$col == "product_category_l_idx"
				|| $col == "product_category_m_idx"
				|| $col == "product_sale_type"
			) {
				$qryWhereAry[] = $col . " = N'" . $val . "'";
			}elseif(trim($col) == "search_column"){
				if(trim($_search_paramAryList["search_keyword"]) != "" && in_array($val, $available_search_column)) {
				    if ($val == "product_name") {
                        $qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
                    } else {
                        $qryWhereAry[] = "A.product_idx in (Select product_idx From DY_PRODUCT_OPTION Where product_option_is_del = N'N' And " . $val . " like N'%".trim($_search_paramAryList["search_keyword"])."%')";
                    }
				}
			}else{
				$qryWhereAry[] = $col . " like N'%" . $val . "%'";
			}
		}
	}

	if (! $_search_paramAryList["period_yn"]) {
		if($_search_paramAryList["period_search_type"] == "regdate"){
			$qryWhereAry[] = "	 
			A.product_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And A.product_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";
		}elseif($_search_paramAryList["period_search_type"] == "soldoutdate"){
			$qryWhereAry[] = "	 
			A.product_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00' 
			And A.product_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59' 
		";
		}
	}

	//재고 상태
	if($_search_paramAryList["stock_status"] != "") {
		$stock_status = trim($_search_paramAryList["stock_status"]);
		if($stock_status == "in_stock"){
			$qryWhereAry[] = "
				A.product_idx in (
					Select
						DPO.product_idx 
					From 
					DY_PRODUCT_OPTION DPO 
					Left Outer Join 
					(
						Select 
							product_option_idx
							, isNull(Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End), 0) as stock_amount_NORMAL 
						From DY_STOCK S
						Where S.stock_is_del = N'N' And S.stock_is_confirm = N'Y'
						Group by product_option_idx
					) as A_A On DPO.product_option_idx = A_A.product_option_idx
					Where
						DPO.product_option_is_del = N'N' 
						And A_A.stock_amount_NORMAL > 0
				)
			";
		}elseif($stock_status == "not_enough"){
			$qryWhereAry[] = "
				A.product_idx in (
					Select
						DPO.product_idx 
					From 
					DY_PRODUCT_OPTION DPO 
					Left Outer Join 
					(
						Select 
							product_option_idx
							, isNull(Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End), 0) as stock_amount_NORMAL 
						From DY_STOCK S
						Where S.stock_is_del = N'N' And S.stock_is_confirm = N'Y'
						Group by product_option_idx
					) as A_A On DPO.product_option_idx = A_A.product_option_idx
					Where
						DPO.product_option_is_del = N'N' 
						And A_A.stock_amount_NORMAL < 1
				)
			";
		}elseif($stock_status == "warning"){
			$qryWhereAry[] = "
				A.product_idx in (
					Select
						DPO.product_idx 
					From 
					DY_PRODUCT_OPTION DPO 
					Left Outer Join 
					(
						Select 
							product_option_idx
							, isNull(Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End), 0) as stock_amount_NORMAL 
						From DY_STOCK S
						Where S.stock_is_del = N'N' And S.stock_is_confirm = N'Y'
						Group by product_option_idx
					) as A_A On DPO.product_option_idx = A_A.product_option_idx
					Where
						DPO.product_option_is_del = N'N' 
						And A_A.stock_amount_NORMAL < DPO.product_option_warning_count
				)
			";
		}elseif($stock_status == "danger"){
			$qryWhereAry[] = "
				A.product_idx in (
					Select
						DPO.product_idx 
					From 
					DY_PRODUCT_OPTION DPO 
					Left Outer Join 
					(
						Select 
							product_option_idx
							, isNull(Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End), 0) as stock_amount_NORMAL 
						From DY_STOCK S
						Where S.stock_is_del = N'N' And S.stock_is_confirm = N'Y'
						Group by product_option_idx
					) as A_A On DPO.product_option_idx = A_A.product_option_idx
					Where
						DPO.product_option_is_del = N'N' 
						And A_A.stock_amount_NORMAL < DPO.product_option_danger_count
				)
			";
		}
	}
	//판매 상태
	if($_search_paramAryList["sale_status"] != "") {
		$sale_status = trim($_search_paramAryList["sale_status"]);
		if($sale_status == "soldout"){
			$qryWhereAry[] = "
				A.product_idx in (
					Select
						product_idx
					From 
						DY_PRODUCT_OPTION YPO
					Where
						product_option_is_del = N'N' 
						And product_option_soldout = N'Y'
				)
			";
		}elseif($sale_status == "soldout_part"){
			$qryWhereAry[] = "
				A.product_idx in (
					Select
						product_idx
					From 
						DY_PRODUCT_OPTION YPO
					Where
						product_option_is_del = N'N'
					Group by product_idx
					Having 
						Sum(Case When product_option_soldout = 'Y' Then 1 Else 0 End) > 0
						And Count(product_idx) > Sum(Case When product_option_soldout = 'Y' Then 1 Else 0 End)
				)
			";
		}elseif($sale_status == "soldout_whole"){
			$qryWhereAry[] = "
				A.product_idx in (
					Select
						product_idx
					From 
						DY_PRODUCT_OPTION YPO
					Where
						product_option_is_del = N'N'
					Group by product_idx
					Having 
						Sum(Case When product_option_soldout = 'Y' Then 1 Else 0 End) > 0
						And Count(product_idx) = Sum(Case When product_option_soldout = 'Y' Then 1 Else 0 End)
				)
			";
		}elseif($sale_status == "in_stock"){
			$qryWhereAry[] = "
				A.product_idx in (
					Select
						product_idx
					From 
						DY_PRODUCT_OPTION YPO
					Where
						product_option_is_del = N'N' 
						And product_option_soldout = N'N'
						And product_option_soldout_temp = N'N'
				)
			";
		}elseif($sale_status == "soldout_temp"){
			$qryWhereAry[] = "
				A.product_idx in (
					Select
						product_idx
					From 
						DY_PRODUCT_OPTION YPO
					Where
						product_option_is_del = N'N' 
						And product_option_soldout_temp = N'Y'
				)
			";
		}elseif($sale_status == "x_sold_out_temp"){
			$qryWhereAry[] = "
				A.product_idx not in (
					Select
						product_idx
					From 
						DY_PRODUCT_OPTION YPO
					Where
						product_option_is_del = N'N' 
						And product_option_soldout_temp = N'Y'
				)
			";
		}
	}

	//공급처
	if($_search_paramAryList["supplier_idx"]){
		$qryWhereAry[] = "A.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
	}

	//벤더사 노출 여부 확인
	if(!isDYLogin()){
		$qryWhereAry[] = " 
			(
				A.product_vendor_show = N'ALL'
				Or
				(
					A.product_vendor_show = N'SELECTED' And A.product_idx in (Select product_idx From DY_PRODUCT_VENDOR_SHOW Where product_vendor_show_is_del = N'N' And vendor_idx = N'".$GL_Member["member_idx"]."')
				)
			)
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
$args['qry_table_idx'] 	= "A.product_idx";
$args['qry_get_colum'] 	= " A.product_idx
							, A.product_img_main
							, A.product_img_1, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_1) as product_img_filename_1
							, A.product_img_2, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_2) as product_img_filename_2
							, A.product_img_3, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_3) as product_img_filename_3
							, A.product_img_4, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_4) as product_img_filename_4
							, A.product_img_5, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_5) as product_img_filename_5
							, A.product_img_6, (Select save_filename From DY_FILES F Where F.file_idx = A.product_img_6) as product_img_filename_6
							, A.product_name
							, isNull((Select name From DY_CATEGORY C Where C.category_idx = A.product_category_l_idx), '') as category_l_name, A.product_category_l_idx 
							, isNull((Select name From DY_CATEGORY C Where C.category_idx = A.product_category_m_idx), '') as category_m_name, A.product_category_m_idx

							, S.seller_idx, S.seller_name
							, A.product_regdate


							, (Select count(*) From DY_PRODUCT_OPTION PO Where PO.product_idx = A.product_idx And PO.product_option_is_del = N'N' And product_option_soldout = N'Y') as soldout_cnt 
							, (Select count(*) From DY_PRODUCT_OPTION PO2 Where PO2.product_idx = A.product_idx And PO2.product_option_is_del = N'N' And product_option_soldout_temp = N'Y') as soldout_temp_cnt 
";

if(isDYLogin()) {
	$args['qry_get_colum'] .= " 
							, A.product_supplier_name
							, P.member_idx, P.supplier_name
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
                            ";

}

$args['qry_table_name'] 	= " DY_PRODUCT A 
								Left Outer Join DY_MEMBER_SUPPLIER P On A.supplier_idx = P.member_idx
								Left Outer Join DY_SELLER S On A.seller_idx = S.seller_idx
";
$args['qry_where']			= " A.product_is_del = 'N' And A.product_is_trash = 'N' And A.product_is_use = 'Y'";
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
//array_walk_recursive($WholeGetListResult['listRst'], 'htmlentities_utf8');

$_list = $WholeGetListResult['listRst'];
foreach($_list as $k => $v){
	foreach($v as $_k => $_v){
		$v[$_k] = htmlentities_utf8($_v);
	}
	$_list[$k] = $v;
}

$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"] = $_list;
$grid_response["test"] = $_search_paramAryList;
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
echo json_encode($grid_response, true);
?>