<?php
//Page Info
$pageMenuIdx = 313;

include_once "../_init_.php";

$list_table = new ListTable();

$page = (!$page)? '1' : $page ;
$args['page'] 		    = $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$order_by = "ST.product_option_idx, ST.stock_unit_price";
if($_GET["sidx"] && $_GET["sord"]) {
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

//비교 가능한 컬럼 지정
$available_cols = array(
	"target_date",
	"stock_inout_type",
	"stock_status",
	"search_column",
);

//검색 가능한 컬럼 지정
$searchable_cols = array(
	"product_name",
	"product_option_name",
);

$qry_where_arr = array();

$qry_where_start_today = "";
$qry_where_until_today = "";

$search_param = $_GET["param"];

$target_date = "";

if ($search_param) {
	$search_param = urldecode($search_param);
	parse_str($search_param, $search_param_map);

	foreach($search_param_map as $key => $val) {
		if (trim($val) && in_array($key, $available_cols)) {
			if ($key === "search_column") {
				if (trim($search_param_map["search_keyword"]) === "") continue;

				$all_where = [];

				foreach ($searchable_cols as $searchable_col_name) {
					if ($searchable_col_name == $val || $val == "all")
						$all_where[] = $searchable_col_name . " like N'%" . trim($search_param_map["search_keyword"]) . "%'";
				}

				$all_where_qry = "(" . join(" OR ", $all_where) . ")";
				$qry_where_arr[] = $all_where_qry;
			} else {
				if ($key == "target_date") {
					$target_date = $val;
					$qry_where_start_today = "stock_is_confirm_date >= '" . $val . " 00:00:00'";
					$qry_where_until_today = "stock_is_confirm_date <= '" . $val . " 23:59:59'";
				} elseif ($key == "stock_inout_type") {
					if ($val == "in") {
						$qry_where_arr[] = "(ST.stock_kind = 'STOCK_ORDER' OR ST.stock_kind = 'BACK')";
					} elseif ($val == "out") {
						$qry_where_arr[] = "ST.stock_status = 'SHIPPED'";
					}
				}
			}
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

//엑셀다운로드 시 표시 행수 무한
if($gridPrintForExcelDownload) {
	// paging set
	$args['show_row'] 	= 9999;
	$args['show_page'] 	= 9999;
}

// make select query
$args['qry_table_idx'] 	= "ST.product_option_idx";

$args['qry_get_colum'] 	= "
	ST.product_idx, product_name,
    ST.product_option_idx, product_option_name,
    ST.stock_unit_price,
    until_today_status_normal_total,
    until_today_status_abnormal_total,
    until_today_status_bad_total,
    until_today_status_hold_total,
    until_today_status_disposal_total,
    SUM(CASE WHEN ST.stock_kind = 'STOCK_ORDER' OR (ST.stock_kind = 'SAMPLE' AND ST.stock_type = 1) THEN stock_amount ELSE 0 END) AS daily_in_total,
    SUM(CASE WHEN ST.stock_status = 'SHIPPED' OR (ST.stock_kind = 'SAMPLE' AND ST.stock_type = -1) THEN stock_amount ELSE 0 END) as daily_out_total,
    SUM(CASE WHEN ST.stock_kind = 'BACK' THEN stock_amount ELSE 0 END) AS daily_return_total,
    SUM(CASE WHEN ST.stock_kind = 'ETC' THEN stock_type * stock_amount ELSE 0 END) AS daily_adjust_total,
    (
		until_today_status_normal_total * ST.stock_unit_price +
        until_today_status_abnormal_total * ST.stock_unit_price +
        until_today_status_bad_total * ST.stock_unit_price +
        until_today_status_hold_total * ST.stock_unit_price +
        until_today_status_disposal_total * ST.stock_unit_price
    ) AS until_today_stock_value_total
";

$args['qry_table_name'] = "
	DY_STOCK ST
		JOIN (
			SELECT
				product_option_idx,
				stock_unit_price,
				SUM(CASE WHEN stock_status = 'NORMAL' AND $qry_where_until_today THEN stock_type * stock_amount ELSE 0 END) AS until_today_status_normal_total,
				SUM(CASE WHEN stock_status = 'ABNORMAL' AND $qry_where_until_today THEN stock_type * stock_amount ELSE 0 END) AS until_today_status_abnormal_total,
				SUM(CASE WHEN stock_status = 'BAD' AND $qry_where_until_today THEN stock_type * stock_amount ELSE 0 END) AS until_today_status_bad_total,
				SUM(CASE WHEN stock_status = 'HOLD' AND $qry_where_until_today THEN stock_type * stock_amount ELSE 0 END) AS until_today_status_hold_total,
				SUM(CASE WHEN stock_status = 'DISPOSAL' AND $qry_where_until_today THEN stock_type * stock_amount ELSE 0 END) AS until_today_status_disposal_total
			FROM DY_STOCK
			WHERE stock_is_del = 'N' AND stock_is_confirm = 'Y'
			GROUP BY product_option_idx, stock_unit_price
		) AS ST_C_N ON ST.product_option_idx = ST_C_N.product_option_idx AND ST.stock_unit_price = ST_C_N.stock_unit_price
		JOIN DY_PRODUCT P ON P.product_idx = ST.product_idx
		JOIN DY_PRODUCT_OPTION PO ON PO.product_option_idx = ST.product_option_idx
";

$args['qry_where'] = "
	ST.stock_is_del = 'N'
    AND ST.stock_is_confirm = 'Y'
    AND $qry_where_start_today
    AND $qry_where_until_today
";

if(count($qry_where_arr) > 0) {
	$args['qry_where'] .= " AND " . join(" AND ", $qry_where_arr);
}

$args['qry_groupby']		= "ST.product_option_idx, ST.stock_unit_price";
$args['qry_orderby']		= $order_by;

// image set
$args['search_img'] 		= "";
$args['search_img_tag']		= "";
$args['front_img'] 			= "";
$args['next_img'] 			= "";

$args['add_element']		= "";
$args['seeQry'] 			= "0";

$args['addFormStr'] 		= '';

$list_result = $list_table->WholeGetListResult($args);

$listRst 			= "";
$listRst 			= $list_result['listRst'];
$listRst_cnt 		= count($listRst);

$startRowNum = $list_result['pageInfo']['total'] - (($args['show_row'] * $args['page']) - $args['show_row']) ;

$article_number = $list_result['pageInfo']['total'];
$article_number = $list_result['pageInfo']['total'] - ($args['show_row'] * ($page-1));

$total_query = "
	SELECT 
		SUM(stock_type * stock_amount * stock_unit_price) AS current_stock_value_total,
		SUM(CASE WHEN $qry_where_until_today THEN stock_type * stock_amount * stock_unit_price ELSE 0 END) AS until_today_stock_value_total
	FROM DY_STOCK
	WHERE stock_is_del = 'N' AND stock_is_confirm = 'Y'
	AND stock_status IN ('NORMAL', 'ABNORMAL', 'BAD', 'DISPOSAL', 'HOLD')
";

$list_table->db_connect();
$total_list = $list_table->execSqlOneRow($total_query);
$list_table->db_close();

$grid_response = array();
$grid_response["page"] = $page;
$grid_response["records"] = $list_result["pageInfo"]["total"];
$grid_response["total"] = $list_result["pageInfo"]["totalpages"];
$grid_response["rows"] = $list_result['listRst'];
$grid_response["current_stock_value_total"] = $total_list["current_stock_value_total"];
$grid_response["until_today_stock_value_total"] = $total_list["until_today_stock_value_total"];

if(!$gridPrintForExcelDownload) {
	echo json_encode($grid_response, true);
}
