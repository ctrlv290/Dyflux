<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 팝업 주문 리스트 JSON
 */
//Page Info
$pageMenuIdx = 205;
//Init
include_once "../_init_.php";

$C_CS = new CS();
$C_Dbconn = new DBConn();

//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$order_pack_idx = $_GET["order_pack_idx"];
$order_cs_status = $_GET["order_cs_status"];

// $_list = $C_CS->getOrderDetailRelateOrderPackIdx($order_pack_idx, "O.order_idx", "asc", $order_cs_status,"Y");
$qry ="
WITH stock_return AS (
                        SELECT O.*
                            , P.product_name, PO.product_option_name
                            , M.product_option_cnt - IFNULL(sum_stock_due_amount,0) AS product_option_cnt 
                            , M.order_matching_idx, M.product_idx, M.product_option_idx, M.product_option_sale_price
                            , SELLER.seller_name
                            , ROW_NUMBER() OVER(PARTITION BY O.order_idx Order by M.order_matching_idx ASC) as inner_no
                        FROM dy_order O 
                            LEFT OUTER JOIN dy_order_product_matching M ON O.order_idx = M.order_idx
                            Left Outer Join DY_PRODUCT_OPTION PO On M.product_option_idx = PO.product_option_idx 
                            Left Outer Join DY_PRODUCT P On P.product_idx = PO.product_idx And P.product_is_del = N'N'
                            Left Outer Join (
                            SELECT order_matching_idx, SUM(stock_due_amount) AS sum_stock_due_amount
                                                                                From DY_STOCK
                                                                                WHERE stock_is_del = N'N' And stock_kind = 'BACK'
                                                                                GROUP BY order_matching_idx
                                                                ) As ST On ST.order_matching_idx = M.order_matching_idx
                            Left Outer Join DY_SELLER SELLER On O.seller_idx = SELLER.seller_idx
				            Left Outer Join DY_MEMBER_SUPPLIER SP On SP.member_idx = P.supplier_idx
                            WHERE order_pack_idx = $order_pack_idx AND P.product_sale_type = 'SELF'
                            
                        ) Select * From stock_return Where product_option_cnt > 0;
	";
$C_Dbconn -> db_connect();
$_list = $C_Dbconn -> execSqlList($qry);
$C_Dbconn -> db_close();

$grid_response             = array();
$grid_response["page"]     = $page;
$grid_response["records"]  = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"]    = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"]     = $_list;
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
echo json_encode($grid_response, true);
?>