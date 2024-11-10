<?php
include_once "../_init_.php";

$C_Dbconn = new DBConn();
$C_Dbconn -> db_connect();

//$qry = "
//								Select stock_idx, stock_is_parent_confirm_date
//								From DY_STOCK S
//								Where
//								      stock_is_del = N'N'
//								      And order_matching_idx = N'61814'
//								      And product_idx = N'11890'
//								      And product_option_idx = N'34933'
//								      And order_idx = N'161991'
//								      And stock_status = N'SHIPPED'
//								Order by stock_idx DESC
//								LIMIT 1
//							";
//
//$_stock_idx = $C_Dbconn->execSqlOneRow($qry);
//
//print("<pre>".print_r($_stock_idx,true)."</pre>");

$product_option_idx = 33984;

$qry = "
            Select
            min(stock_idx) AS stock_idx, product_idx, product_option_idx, stock_unit_price
            , IFNULL(stock_is_parent_confirm_date, min(stock_regdate)) as stock_is_parent_confirm_date
            , IFNULL(Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End), 0) as stock_amount_NORMAL
            From DY_STOCK
            Where 1=1
                And stock_is_del = N'N'
                And product_option_idx = N'$product_option_idx'
                And stock_status = N'NORMAL' 
                AND stock_type = 1
                And stock_is_confirm = N'Y' 
                Group by product_idx, product_option_idx, stock_unit_price, stock_is_parent_confirm_date
                Order by stock_is_parent_confirm_date ASC
        ";

$stock_in_list = $C_Dbconn -> execSqlList($qry);
print("<pre>".print_r($stock_in_list,true)."</pre>");

$qry = "
            SELECT product_idx, product_option_idx, stock_unit_price, sum(stock_amount) AS stock_amount 
            FROM dy_stock 
            WHERE 1=1
			    And stock_is_del = N'N'
                And product_option_idx = N'$product_option_idx'
                And stock_status = N'NORMAL' 
                And stock_type = -1
            GROUP BY product_idx,product_option_idx,stock_unit_price
        ";

$stock_out_list = $C_Dbconn -> execSqlList($qry);

foreach ($stock_in_list as $in_key => $in) {
    $key = array_search($in['stock_unit_price'], array_column($stock_out_list, 'stock_unit_price'));

    if ($in['stock_amount_NORMAL'] < $stock_out_list[$key]['stock_amount']) {
        $stock_out_list[$key]["stock_amount"] -= $in['stock_amount_NORMAL'] ;
        $stock_in_list[$in_key]['stock_amount_NORMAL'] = 0;
    }else if ($in['stock_amount_NORMAL'] >= $stock_out_list[$key]['stock_amount']){
        $stock_in_list[$in_key]['stock_amount_NORMAL'] -= $stock_out_list[$key]["stock_amount"] ;
        $stock_out_list[$key]["stock_amount"] = 0;
    }
}

print("<pre>".print_r($stock_in_list,true)."</pre>");

$order_product_option_cnt = 10;

if ($stock_in_list) {
    foreach ($stock_in_list as $stock) {

        //남은 수량이 없으면 Pass
        if($order_product_option_cnt == 0){
            continue;
        }

        //현 금액의 재고 수량
        $_currentStockPriceAmount = intval($stock["stock_amount_NORMAL"]);


        if ($order_product_option_cnt > $_currentStockPriceAmount) {
            //현재 금액의 재고가 부족일 경우
            //현재 금액의 재고만큼만 차감 후 Next

            $order_product_option_cnt = $order_product_option_cnt - $_currentStockPriceAmount;

        } else {
            //재고가 충분 할 경우
            //주문 수량 만큼 차감
            $order_product_option_cnt = 0;
        }
    }

    //재고 차감을 모두 하였음에도 주문 수량에 못 미칠 경우
    if ($order_product_option_cnt > 0) {
        $_IsStockOK = false;
    }

} else {
    $_IsStockOK = false;
}
$C_Dbconn -> db_close();


?>