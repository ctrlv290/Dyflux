<?php

class ReportManager {
    public $default_table = "DY_SETTLE";
    public $default_table_alias = "SETTLE";
    public $item_list = [];

    public $sql;
    public $sql_cols = [];
    public $sql_where = [];
    public $sql_join = [];

    public function createItem() {
        $item = new Item();
        $item->name = "total_day";
        $item->sql = "SUM(CASE WHEN DATE_FORMAT('#0#', '#2#') = '#1#' THEN '#3#' ELSE 0 END) AS total_day";
        $item->args[0] = "'settle_date'";
        $item->args[1] = "condition_date";
        $item->args[2] = "'%Y-%m-%d'";
        $item->args[3] = "display_item";
        $item->table_name = $this->default_table;
        $this->item_list[] = $item;
    }

    public function transformItemSql(Item &$item) {
        $this->sql_cols[] = $item->toColSql();

        if($this->default_table != $item->table_name) {
            if(!array_search($this->default_table, $item->join_possible_list)) {
                //error 처리
            }
            $sql_join[] = "JOIN $item->table_name AS $item->table_alias ON $item->table_alias.$item->join_key = $this->default_table_alias.$item->join_key";
        }
    }
}

class Item {
    public $name;
    public $sql = "";
    public $args = [];
    public $table_name = "DY_SETTLE";
    public $table_alias = "SETTLE";
    public $join_key = "seller_idx";
    public $join_possible_list = [];

    public function __construct()
    {
    }

    public setName

    public function toColSql() {
        $to_sql = $this->sql;
        foreach($this->args as $key => $arg) {
            $to_sql = str_replace("#".$key."#", $arg, $to_sql);
        }

        return $to_sql;
    }
}

class Dimension {

}

$condition_list = [];
$condition_list[0] = "2020-04-28";




$segment_list = [
    1 => "seller_idx",
    2 => [
        "settle_sale_sum" => "판매가",
        "settle_purchase_sum" => "매입가",
        "settle_sale_profit" => "영업이익"
    ]
];
$display_list = [];

$qry = "";

foreach($segment_list as $key => $segment) {
    if($key == 1) {

    } else {
        foreach ($segment as $key_1 => $seg_1) {

            $qry .= "
                CTE_SGMT_$key_1 (
                    SELECT $segment, 
                )
            ";
        }
    }
}
