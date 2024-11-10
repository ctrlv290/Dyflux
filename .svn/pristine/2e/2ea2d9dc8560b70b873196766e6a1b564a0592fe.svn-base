<?php

include_once "../_init_.php";

$listTable = new ListTable();

//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['pagename']	    = $GL_page_nm;

$tableNm = $_GET["table_nm"];

$tableIdx = $_GET["table_idx"] ? $_GET["table_idx"] : "idx";
$tableAlias = $_GET["table_alias"] ? $_GET["table_alias"] : "A";

$columns = $_GET["columns"] ? $_GET["columns"] : "*";

$joins = $_GET["joins"] ? $_GET["joins"] : array();
$wheres = $_GET["wheres"] ? $_GET["wheres"] : array();

$groupBy = $_GET["groupBy"] ? $_GET["groupBy"] : "";
$orderBy = $_GET["orderBy"] ? $_GET["orderBy"] : "";

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

// make select query
$args['qry_table_idx'] = $tableAlias . "." . $tableIdx;
$args['qry_get_colum'] = $tableAlias . "." . $columns;
$args['qry_table_name'] = $tableNm . " AS " . $tableAlias;
$args['qry_where'] = "";

if (count($joins)) $args['qry_table_name'] = $args['qry_table_name']." ".join(" ", $joins);
if (count($wheres)) $args['qry_where'] = " ".join(" ", $wheres);

$args['qry_groupby'] = $groupBy;
$args['qry_orderby'] = $orderBy;

// image set
$args['search_img'] 		= "";
$args['search_img_tag']		= "";
$args['front_img'] 			= "";
$args['next_img'] 			= "";

$args['add_element']		= "";
$args['seeQry'] 			= "0";

$args['addFormStr'] 		= '';

$wholeGetListResult = $listTable->WholeGetListResult($args);

$listRst 			= "";
$listRst 			= $wholeGetListResult['listRst'];

$gridResponse = array();
$gridResponse["page"] = $page;
$gridResponse["records"] = $wholeGetListResult["pageInfo"]["total"];
$gridResponse["total"] = $wholeGetListResult["pageInfo"]["totalpages"];
$gridResponse["rows"] = $wholeGetListResult['listRst'];

echo json_encode($gridResponse, true);