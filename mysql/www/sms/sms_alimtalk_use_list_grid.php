<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 확장주문검색 리스트 JSON
 */
//Page Info
$pageMenuIdx = 99;
//Init
include_once "../_init_.php";

$C_ListTable = new ListTable();

$_search_param = $_GET["param"];

//검색 가능한 컬럼 지정
$available_col = array(
    "search_column",
);
//검색 가능한 셀렉트박스 값 지정
$available_val = array(
    "DEST_PHONE",
    "TEMPLATE_CODE",
);
//검색 가능한 셀렉트박스 값 지정
$available_val_for_search_column = array(

);

$year = date('Y');
$month = date('m');

if($_search_param)
{
    $_search_param = urldecode($_search_param);
    $_search_paramAry = explode("&", $_search_param);
    parse_str($_search_param, $_search_paramAryList);
    foreach($_search_paramAry as $sitem) {
        list($col, $val) = explode("=", $sitem);

        if($col == "year") {
            $year = isset($val) ? $val : date('Y');
        }

        if($col == "month") {
            $month = isset($val) ? $val : date('m');
        }

        if(trim($val) && in_array($col, $available_col)) {
            if(trim($col) == "search_column" && in_array($val, $available_val)){
                if(trim($_search_paramAryList["search_keyword"]) != "") {
                    $qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
                }
            }else{
                $qryWhereAry[] = $col . " like N'%" . $val . "%'";
            }
        }
    }
}

$table = "BIZ_LOG_" . $year . $month;

$C_Dbconn = new DBConn();
$qry = " SELECT COUNT(*) as cnt FROM DYFLUX_SMS.$table";
$C_Dbconn->db_connect();
$cnt = $C_Dbconn->execSqlOneCol($qry);
$C_Dbconn->db_close();

if($cnt == 1) {
//******************************* 리스트 기본 설정 ******************************//
// get info set
    $page = (!$page) ? '1' : $page;
    $args['page'] = (!$page) ? '1' : $page;
    $args['searchVar'] = $searchVar;
    $args['searchWord'] = $searchWord;
    $args['sortBy'] = $sortBy;
    $args['sortType'] = $sortType;
    $args['pagename'] = $GL_page_nm;

// search & sort
    $args['searchArr'] = array();// 검색 배열
    $args['sortArr'] = array();

    if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
    $args['show_row'] = ($_GET["rows"]) ? $_GET["rows"] : 10;
    $args['show_page'] = ($_GET["rows"]) ? $_GET["rows"] : 10;

// make select query
    $args['qry_table_idx'] = "CMID";
    $args['qry_get_colum'] = " * ";
    $args['qry_table_name'] = "DYFLUX_SMS.".$table;
    $args['qry_where'] = " 1=1 ";

    if (count($qryWhereAry) > 0) {
        $args['qry_where'] .= " And " . join(" And ", $qryWhereAry);
    }

    $args['qry_groupby'] = "";
    $args['qry_orderby'] = $order_by;

// image set
    $args['search_img'] = "";
    $args['search_img_tag'] = "";
    $args['front_img'] = "";
    $args['next_img'] = "";

    $args['add_element'] = "";
    $args['seeQry'] = "0";

    $args['addFormStr'] = '';

    $WholeGetListResult = $C_ListTable->WholeGetListResult($args);

    $listRst = "";
    $listRst = $WholeGetListResult['listRst'];
    $listRst_cnt = count($listRst);

    $startRowNum = $WholeGetListResult['pageInfo']['total'] - (($args['show_row'] * $args['page']) - $args['show_row']);


    $article_number = $WholeGetListResult['pageInfo']['total'];
    $article_number = $WholeGetListResult['pageInfo']['total'] - ($args['show_row'] * ($page - 1));

    $grid_response = array();
    $grid_response["page"] = $page;
    $grid_response["records"] = $WholeGetListResult["pageInfo"]["total"];
    $grid_response["total"] = $WholeGetListResult["pageInfo"]["totalpages"];
    $grid_response["rows"] = $WholeGetListResult['listRst'];
}else{
    $grid_response = array();
    $grid_response["page"] = 1;
    $grid_response["records"] = 0;
    $grid_response["total"] = 0;
    $grid_response["rows"] =  array();
}

echo json_encode($grid_response, true);
?>

