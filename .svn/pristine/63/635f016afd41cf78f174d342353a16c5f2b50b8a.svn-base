<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 월별상품별 리스트 JSON
 */
//Page Info
$pageMenuIdx = 133;
//Init
include_once "../_init_.php";


//검색 가능한 컬럼 지정
$available_col = array(
	"product_category_l_idx",
	"product_category_m_idx",
	"soldout_status",
);

$available_search_col = array(

);

$qryWhereAry = array();

foreach($_GET as $col => $val){
	if(trim($val) && in_array($col, $available_col)) {
		if(
			$col == "product_category_l_idx"
			|| $col == "product_category_m_idx"
		) {
			$qryWhereAry[] = $col . " = N'" . $val . "'";
		}elseif(trim($col) == "soldout_status"){
			//품절상태 조건
			if($val == "except_soldout"){
				//품절제외
				$qryWhereAry[] = " product_option_soldout = N'N' ";
			}elseif($val == "soldout"){
				//품절
				$qryWhereAry[] = " product_option_soldout = N'Y' ";
			}
		}
	}
}


$date_search_col = "";
$date_start = $_GET["date_start_year"] . "-" . $_GET["date_start_month"] . "-1";
$date_end = $_GET["date_end_year"] . "-" . $_GET["date_end_month"] . "-1";

$date_start = date("Y-m-d", strtotime($date_start));
$date_end = date("Y-m", strtotime($date_end)) . "-" . date("t", strtotime($date_end));

//공급처
if($_GET["supplier_idx"]){
	$settle_add_where2 = " And product_option_idx in (Select product_option_idx From DY_PRODUCT_OPTION Where product_option_is_del = N'N' And supplier_idx = N'".$_GET["supplier_idx"]."')";
}

//판매처
if($_GET["seller_idx"]){
	$settle_add_where = " And seller_idx = N'".$_GET["seller_idx"]."'";
}

$qry = "
WITH CTE_CATEGORY(c1_idx, c1_name, c2_idx, c2_name, c1_sort, c2_sort)
AS
(
	Select 
	C1.category_idx as c1_idx, C1.name as c1_name, C2.category_idx as c2_idx, C2.name as c2_name, C1.sort as c1_sort, C2.sort as c2_sort
	From 
	DY_CATEGORY C1
	Inner Join DY_CATEGORY C2
	On C2.parent_category_idx = C1.category_idx
)
";

$qry .= "
	Select
		A.*, C1.name, C2.c2_name, C1.sort, C2.c2_sort
		, ROW_NUMBER() Over(PARTITION BY C1.sort Order by C1.sort, Case When C2.c2_sort IS NULL Then 10000 Else C2.c2_sort End) as rowNum
	From (
		Select 
			product_category_l_idx, product_category_m_idx
			, Sum(settle_sale_supply) as sum_settle_sale_supply
			, Sum(product_option_purchase_price) as sum_product_option_purchase_price
			, Sum(product_option_cnt) as sum_product_option_cnt 
			, count(*) OVER(PARTITION BY product_category_l_idx) as rowCnt
		From 
		(
			Select *
			From DY_SETTLE 
			Where 
			settle_is_del = N'N'
		    And settle_date >= N'$date_start' And settle_date <= N'$date_end'
			$settle_add_where 
			$settle_add_where2
		) ST
		Group by ROLLUP(product_category_l_idx, product_category_m_idx)
		Having product_category_l_idx is not null
	) as A
	Left Outer Join DY_CATEGORY C1 On C1.parent_category_idx = 0 And C1.category_idx = A.product_category_l_idx
	Left Outer Join CTE_CATEGORY C2 On C2.c1_idx = A.product_category_l_idx And C2.c2_idx = A.product_category_m_idx
	Order by C1.sort, Case When C2.c2_sort IS NULL Then 10000 Else C2.c2_sort End
";

$C_Dbconn = new Dbconn();

$C_Dbconn->db_connect();
$_list = $C_Dbconn->execSqlList($qry);
$C_Dbconn->db_close();

$response = array();
$response["result"] = true;
$response["data"] = $_list;

if(!$gridPrintForExcelDownload) {
	echo json_encode($response, true);
}
?>