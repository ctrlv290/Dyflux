<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: Mobile 충전금관리 리스트
 */
//Init
include_once "../../_init_.php";

$last_member_idx = $GL_Member["member_idx"];

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
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$order_by = "C.last_charge_date DESC";

//판매처
if($_POST["seller_idx"]){
	$qryWhereAry[] = " C.member_idx = N'" . $_POST["seller_idx"] . "'";
}

$date_search_col = "";
if($date_start && $date_end){
	$qryWhereAry[] = "	 
		C.last_charge_date >= '".$date_start."' 
		And C.last_charge_date <= '".$date_end."' 
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
$args['qry_table_idx'] 	= "C.member_idx";
$args['qry_get_colum'] 	= " 
							C.*
							, V.vendor_name
							, V.vendor_grade
							, (Select charge_memo From DY_MEMBER_VENDOR_CHARGE Z Where Z.settle_idx = 0 And Z.member_idx = C.member_idx And Z.charge_date = C.last_charge_date Order by Z.charge_regdate desc LIMIT 1) as last_memo
";
$args['qry_table_name'] 	= " 
								(
								Select
									member_idx
									, Sum(charge_amount * charge_inout) as remain_amount
									, Max(charge_date) as last_charge_date
									, Max(charge_idx) as last_charge_idx
									, Sum(charge_amount * charge_inout)
									 - (Select IFNULL(Sum(settle_sale_sum), 0) From DY_SETTLE S Where S.seller_idx = MV.member_idx And S.settle_is_del = N'N')
									 + (Select IFNULL(Sum(ledger_tran_amount), 0) From DY_LEDGER L Where L.target_idx = MV.member_idx And L.ledger_is_del = N'N' And L.charge_idx = 0) 
									 as remain_amount2 
								From DY_MEMBER_VENDOR_CHARGE MV
								Where charge_is_del = N'N' And charge_inout = 1
								Group by member_idx
								) as C
								Left Outer Join DY_MEMBER_VENDOR V On C.member_idx = V.member_idx
";
$args['qry_where'] = " 1 = 1 ";
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
?>

<div class="wrap_scroll mt20">
	<table class="table_style03">
		<colgroup>
			<col style="width: 150px;" />
			<col style="width: 70px;" />
			<col style="width: 100px;" />
			<col style="width: 100px;" />
			<col style="width: 200px;" />
		</colgroup>
		<thead>
		<tr>
			<th>벤더사</th>
			<th>등급</th>
			<th>마지막 입금일</th>
			<th>충전금 잔액</th>
			<th>마지막 입금 비고</th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach($listRst as $row) {
			?>
			<tr>
				<td><?=$row["vendor_name"]?></td>
				<td class="text_center"><?=$row["vendor_grade"]?></td>
				<td class="text_center"><?=$row["last_charge_date"]?></td>
				<td class="text_right"><?=number_format($row["remain_amount2"])?></td>
				<td><?=$row["last_memo"]?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
</div>
