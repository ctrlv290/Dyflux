<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공통으로 사용되는 정보 변경이력  리스트 JSON
 */
//Page Info
$pageMenuIdx = 165;
//Init
include_once "../_init_.php";

$C_ListTable = new ListTable();

$manage_group_type = $_GET["manage_group_type"];


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
$avaliable_col = array(
	"view",
	"date_start",
	"date_end",
	"search_column",
	"keyword"
);
if($_search_param)
{
	$_search_param = urldecode($_search_param);
	$_search_paramAry = explode("&", $_search_param);
	$_search_paramAry_KeyVal = array();
	foreach($_search_paramAry as $sitem) {
		list($col, $val) = explode("=", $sitem);

		if(trim($val) && in_array($col, $avaliable_col)) {

			$_search_paramAry_KeyVal[$col] = $val;

			if(trim($col) == "view")
			{
				$view = trim($val);
			}elseif(trim($col) == "date_start"){
				$qryWhereAry[] = " A.regdate >= '" . $val . " 00:00:00'";
			}elseif(trim($col) == "date_end"){
				$qryWhereAry[] = "A.regdate <= '" . $val . " 23:59:59.998'";
			}
		}
	}

	if(trim($_search_paramAry_KeyVal["keyword"]) != "") {
		if ($_search_paramAry_KeyVal["search_column"] == "member_idx") {
			$qryWhereAry[] = "
			(
				A.table_idx1 = '" . $_search_paramAry_KeyVal["keyword"] . "'
				Or
				A.table_idx2 = '" . $_search_paramAry_KeyVal["keyword"] . "'
			)
			";
		} elseif ($_search_paramAry_KeyVal["search_column"] == "memo") {
			$qryWhereAry[] = "A.memo like '%" . $_search_paramAry_KeyVal["keyword"] . "%'";
		} elseif ($_search_paramAry_KeyVal["search_column"] == "member_id") {
			$qryWhereAry[] = "M.member_id like '%" . $_search_paramAry_KeyVal["keyword"] . "%'";

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
$args['qry_table_idx'] 	= "A.h_idx";
$args['qry_get_colum'] 	= " A.* ";

$table_name = "";
switch ($view)
{
	case "seller" :
		$table_name = "
			DY_SELLER_HISTORY A
			Left Outer Join DY_MEMBER M On A.member_idx = M.idx
		";
		$args['qry_get_colum']  = "
			A.*
			, (Select member_id From DY_MEMBER M Where M.idx = A.member_idx) as member_id
			, (Select seller_name From DY_SELLER S Where S.seller_idx = A.table_idx1) as target_name
			, (
				Case 
					When A.dml_flag = 'I' Then '판매처 추가'
					When A.dml_flag = 'U' Then '판매처 수정'
					When A.dml_flag = 'D' Then '판매처 삭제'
				End
				) as action_type
		";

		$qryWhereAry[] = " A.table_idx1 in (Select seller_idx From DY_SELLER S Where S.seller_type = 'MARKET_SELLER')";

		break;
	case "vendor" :
		$table_name = "
			(
				Select * From DY_MEMBER_HISTORY
				Where table_idx1 in (Select idx From DY_MEMBER Where member_type = 'VENDOR' And is_del = 'N')
				Union all
				Select * From DY_MEMBER_VENDOR_HISTORY
				Where table_idx1 in (Select idx From DY_MEMBER Where member_type = 'VENDOR' And is_del = 'N')
			) as A
			Left Outer Join DY_MEMBER M On A.member_idx = M.idx
		";
		$args['qry_get_colum']  = "
			A.h_idx, A.table_idx1, A.table_idx2, A.table_idx3, A.column_mn
			, (
				Case When A.column_mn = 'vendor_license_file' Or A.column_mn = 'vendor_bank_book_copy_file' Then 
					(Select user_filename From DY_FILES F Where F.file_idx = A.before_data)
				Else
					A.before_data
				End 
			) as before_data
			, (
				Case When A.column_mn = 'vendor_license_file' Or A.column_mn = 'vendor_bank_book_copy_file' Then 
					(Select user_filename From DY_FILES F Where F.file_idx = A.after_data)
				Else
					A.after_data
				End 			
			) as after_data
			, A.member_idx, A.dml_flag, A.memo, A.regdate
			, (Select member_id From DY_MEMBER M Where M.idx = A.member_idx) as member_id
			, (Select vendor_name From DY_MEMBER_VENDOR V Where V.member_idx = A.table_idx1) as target_name
			, (
				Case 
					When A.dml_flag = 'I' Then '벤더사 추가'
					When A.dml_flag = 'U' Then '벤더사 수정'
					When A.dml_flag = 'D' Then '벤더사 삭제'
				End
				) as action_type
		";
		break;
	case "supplier" :
		$table_name = "
			(
				Select * From DY_MEMBER_HISTORY
				Where table_idx1 in (Select idx From DY_MEMBER Where member_type = 'SUPPLIER' And is_del = 'N')
				Union all
				Select * From DY_MEMBER_SUPPLIER_HISTORY
				Where table_idx1 in (Select idx From DY_MEMBER Where member_type = 'SUPPLIER' And is_del = 'N')
			) as A
			Left Outer Join DY_MEMBER M On A.member_idx = M.idx
		";
		$args['qry_get_colum']  = "
			A.h_idx, A.table_idx1, A.table_idx2, A.table_idx3, A.column_mn
			, (
				Case When A.column_mn = 'supplier_license_file' Or A.column_mn = 'supplier_bank_book_copy_file' Then 
					(Select user_filename From DY_FILES F Where F.file_idx = A.before_data)
				Else
					A.before_data
				End 
			) as before_data
			, (
				Case When A.column_mn = 'supplier_license_file' Or A.column_mn = 'supplier_bank_book_copy_file' Then 
					(Select user_filename From DY_FILES F Where F.file_idx = A.after_data)
				Else
					A.after_data
				End 			
			) as after_data
			, A.member_idx, A.dml_flag, A.memo, A.regdate
			, (Select member_id From DY_MEMBER M Where M.idx = A.member_idx) as member_id
			, (Select supplier_name From DY_MEMBER_SUPPLIER V Where V.member_idx = A.table_idx1) as target_name
			, (
				Case 
					When A.dml_flag = 'I' Then '공급처 추가'
					When A.dml_flag = 'U' Then '공급처 수정'
					When A.dml_flag = 'D' Then '공급처 삭제'
				End
				) as action_type
		";
		break;
	case "user" :
		$table_name = "
			(
				Select * From DY_MEMBER_HISTORY
				Where table_idx1 in (Select idx From DY_MEMBER Where (member_type = 'USER' Or member_type = 'ADMIN') And is_del = 'N')
				Union all
				Select * From DY_MEMBER_USER_HISTORY
				Where table_idx1 in (Select idx From DY_MEMBER Where (member_type = 'USER' Or member_type = 'ADMIN') And is_del = 'N')
			) as A
			Left Outer Join DY_MEMBER M On A.member_idx = M.idx
		";
		$args['qry_get_colum']  = "
			A.h_idx, A.table_idx1, A.table_idx2, A.table_idx3, A.column_mn
			, A.before_data
			, (Case
				When A.table_nm = 'DY_MEMBER' Or A.table_nm = 'DY_MEMBER_USER' Then A.after_data
				When A.table_nm = 'DY_MEMBER_USER_PERMISSION' Then 
					(SELECT STUFF( (SELECT ',' + name FROM DY_MENU M WHERE M.idx IN  (SELECT * FROM dbo.CSVToTable(A.after_data)) FOR XML PATH('')), 1, 1, '') )
				End
			) as after_data
			, A.member_idx, A.dml_flag, A.memo, A.regdate
			, (Select member_id From DY_MEMBER M Where M.idx = A.member_idx) as member_id
			, (Select name From DY_MEMBER_USER V Where V.member_idx = A.table_idx1) as target_name
			, (
				Case 
					When A.dml_flag = 'I' Then '사용자 추가'
					When A.dml_flag = 'U' Then '사용자 수정'
					When A.dml_flag = 'D' Then '사용자 삭제'
				End
				) as action_type
		";
		break;
	case "product" :
		$table_name = "
			
			DY_PRODUCT_HISTORY A
			Left Outer Join DY_PRODUCT P On A.table_idx1= P.product_idx
			
		";
		$args['qry_get_colum']  = "
			A.h_idx, A.table_idx1, A.table_idx2, A.table_idx3, A.column_mn, A.table_nm
			, (
				Case When A.column_mn = 'product_img_1' 
						Or A.column_mn = 'product_img_2' 
						Or A.column_mn = 'product_img_3' 
						Or A.column_mn = 'product_img_4' 
						Or A.column_mn = 'product_img_5' 
						Or A.column_mn = 'product_img_6' 
					Then 
					(Select user_filename From DY_FILES F Where F.file_idx = A.before_data)
				Else
					A.before_data
				End 
			) as before_data
			, (
				Case When A.column_mn = 'product_img_1'
				        Or A.column_mn = 'product_img_2' 
				        Or A.column_mn = 'product_img_3' 
				        Or A.column_mn = 'product_img_4' 
				        Or A.column_mn = 'product_img_5' 
				        Or A.column_mn = 'product_img_6' 
			        Then 
					(Select user_filename From DY_FILES F Where F.file_idx = A.after_data)
				Else
					A.after_data
				End 			
			) as after_data
			
			, A.member_idx, A.dml_flag, A.memo, A.regdate
			, (Select member_id From DY_MEMBER M Where M.idx = A.member_idx)  as member_id
			, (Select product_name From DY_PRODUCT P Where P.product_idx = A.table_idx1) as target_name
			, (
				Case 
					When A.table_nm = 'DY_PRODUCT' And A.dml_flag = 'I' Then '상품 추가'
					When A.table_nm = 'DY_PRODUCT' And A.dml_flag = 'U' Then '상품 수정'
					When A.table_nm = 'DY_PRODUCT' And A.dml_flag = 'D' Then '상품 삭제'
					When A.table_nm = 'DY_PRODUCT_DETAIL' And A.dml_flag = 'I' Then '상세페이지 추가'
					When A.table_nm = 'DY_PRODUCT_DETAIL' And A.dml_flag = 'U' Then '상세페이지 수정'
					When A.table_nm = 'DY_PRODUCT_DETAIL' And A.dml_flag = 'D' Then '상세페이지 삭제'
					When A.table_nm = 'DY_PRODUCT_VENDOR_SHOW' And A.dml_flag = 'I' Then '벤더사노출 추가'
					When A.table_nm = 'DY_PRODUCT_VENDOR_SHOW' And A.dml_flag = 'U' Then '벤더사노출 수정'
					When A.table_nm = 'DY_PRODUCT_VENDOR_SHOW' And A.dml_flag = 'D' Then '벤더사노출 삭제'
					When A.table_nm = 'DY_PRODUCT_OPTION' And A.dml_flag = 'I' Then '상품 옵션 추가'
					When A.table_nm = 'DY_PRODUCT_OPTION' And A.dml_flag = 'U' Then '상품 옵션 수정'
					When A.table_nm = 'DY_PRODUCT_OPTION' And A.dml_flag = 'D' Then '상품 옵션 삭제'
				End
				) as action_type
		";
		break;
	case "site_info" :
		$table_name = "
			DY_SITE_INFO_HISTORY A
		";
		$args['qry_get_colum']  = "
			A.h_idx, A.table_idx1, A.table_idx2, A.table_idx3, A.column_mn
			, A.before_data
			, A.after_data
			, A.member_idx, A.dml_flag, A.memo, A.regdate
			, (Select member_id From DY_MEMBER M Where M.idx = A.member_idx) as member_id
			, '사이트정보' as target_name
			, (
				Case 
					When A.dml_flag = 'I' Then '사이트정보 추가'
					When A.dml_flag = 'U' Then '사이트정보 수정'
					When A.dml_flag = 'D' Then '사이트정보 삭제'
				End
				) as action_type
		";
		break;
	case "gift" :
		$table_name = "
			DY_ORDER_GIFT_HISTORY A
		";
		$args['qry_get_colum']  = "
			A.h_idx, A.table_idx1, A.table_idx2, A.table_idx3, A.column_mn
			, A.before_data
			, A.after_data
			, A.member_idx, A.dml_flag, A.memo, A.regdate
			, (Select member_id From DY_MEMBER M Where M.idx = A.member_idx) as member_id
			, '사은품정보' as target_name
			, (
				Case 
					When A.dml_flag = 'I' Then '사은품정보 추가'
					When A.dml_flag = 'U' Then '사은품정보 수정'
					When A.dml_flag = 'D' Then '사은품정보 삭제'
				End
				) as action_type
		";
		break;
}


$args['qry_table_name'] 	= $table_name;
$args['qry_where']			= " 1=1 ";
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