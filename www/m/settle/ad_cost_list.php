<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: Mobile 광고비관리
 */

//Init
include_once "../../_init_.php";

$C_ListTable = new ListTable();

$date_start = $_POST["date_start"];
$date_end = $_POST["date_end"];


if(!validateDate($date_start, "Y-m-d")){
	$date_start = date("Y-m-d");
}
if(!validateDate($date_end, "Y-m-d")){
	$date_end = date("Y-m-d");
}


//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1': $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$order_by = "A.ad_regdate DESC";

//판매처
if($_POST["seller_idx"]){
	$qryWhereAry[] = " A.seller_idx = N'" . $_POST["seller_idx"] . "'";
}

$date_search_col = "";
if($date_start && $date_end){
	$qryWhereAry[] = "	 
		A.ad_date >= '".$date_start."' 
		And A.ad_date <= '".$date_end."' 
	";
}

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

// make select query
$args['qry_table_idx'] 	= "A.ad_idx";
$args['qry_get_colum'] 	= " 
							A.seller_idx
							, A.ad_date
							, Case When A.ad_inout = 1 Then Convert(varchar, ad_amount) Else '' End as ad_amount_charge 
							, Case When A.ad_inout = -1 Then Convert(varchar, ad_amount) Else '' End as ad_amount_use
							, A.ad_amount
							, A.ad_product_name
							, A.ad_memo
							, A.ad_regdate 
							, S.seller_name
							, M.member_id
";
$args['qry_table_name'] 	= " 
								DY_SETTLE_AD_COST A
								Left Outer Join DY_SELLER S On S.seller_idx = A.seller_idx
								Left Outer Join DY_MEMBER M On M.idx = A.ad_regidx
";
$args['qry_where'] = " A.ad_is_del = N'N' ";
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
?>
<div class="wrap_scroll mt20">
	<table class="table_style03">
	<colgroup>
		<col style="width: 150px;" />
		<col style="width: 90px;" />
		<col style="width: 100px;" />
		<col style="width: 100px;" />
		<col style="width: 150px;" />
		<col style="width: 150px;" />
		<col style="width: 100px;" />
		<col style="width: 140px;" />
	</colgroup>
	<thead>
	<tr>
		<th>판매처</th>
		<th>날짜</th>
		<th>광고비충전</th>
		<th>광고비사용</th>
		<th>광고상품명</th>
		<th>비고</th>
		<th>작업자</th>
		<th>등록일</th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach($listRst as $row) {
	?>
	<tr>
		<td><?=$row["seller_name"]?></td>
		<td class="text_center"><?=$row["ad_date"]?></td>
		<td class="text_right"><?=number_format($row["ad_amount_charge"])?></td>
		<td class="text_right"><?=number_format($row["ad_amount_use"])?></td>
		<td><?=$row["ad_product_name"]?></td>
		<td><?=$row["ad_memo"]?></td>
		<td><?=$row["member_id"]?></td>
		<td class="text_center"><?=date("Y-m-d H:i:s", strtotime($row["ad_regdate"]))?></td>
	</tr>
	<?php
	}
	?>
	</tbody>
</table>
</div>
