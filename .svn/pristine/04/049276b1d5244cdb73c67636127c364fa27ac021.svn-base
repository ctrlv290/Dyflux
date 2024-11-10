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


$member_idx = $_SESSION['dy_member']['member_idx'];


$_search_param = $_GET["param"];

//검색 가능한 컬럼 지정
$available_col = array(
    "search_column",
);
//검색 가능한 셀렉트박스 값 지정
$available_val = array(
    "A.sms_receive_num",
    "A.sms_msg",
);
//검색 가능한 셀렉트박스 값 지정
$available_val_for_search_column = array(

);

if($_search_param)
{
    $_search_param = urldecode($_search_param);
    $_search_paramAry = explode("&", $_search_param);
    parse_str($_search_param, $_search_paramAryList);
    foreach($_search_paramAry as $sitem) {
        list($col, $val) = explode("=", $sitem);

        $col = trim($col);
        $val = trim($val);

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

    if(!empty($_search_paramAryList["date_start"]) && !empty($_search_paramAryList["date_end"])) {
        $qryWhereAry[] = "	 
			A.sms_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00'
			And A.sms_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59'
		";
    }
}

$C_ListTable = new ListTable();

//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

// make select query
$args['qry_table_idx'] 	= "A.idx";
$args['qry_get_colum'] 	= " A.*, B.member_id ";
$args['qry_table_name'] 	= " DY_SMS_PERSONAL A 
                                LEFT OUTER JOIN DY_MEMBER_USER B ON A.member_idx=B.member_idx 
                                ";
$args['qry_where']			= " A.member_idx='$member_idx' ";

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
$args['seeQry'] 			= "";

$args['addFormStr'] 		= '';

$WholeGetListResult = $C_ListTable -> WholeGetListResult($args);

$listRst 			= "";
$listRst 			= $WholeGetListResult['listRst'];
$listRst_cnt 		= count($listRst);

$startRowNum = $WholeGetListResult['pageInfo']['total'] - (($args['show_row'] * $args['page']) - $args['show_row']) ;


$article_number = $WholeGetListResult['pageInfo']['total'];
$article_number = $WholeGetListResult['pageInfo']['total'] - ($args['show_row'] * ($page-1));

$grid_response             = array();
$grid_response["page"]     = $page;
$grid_response["records"]  = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"]    = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"]     = $WholeGetListResult['listRst'];

//$tmp_arr = array();
//$tmp_arr[] = array('idx'=>'1','date'=>'2018-01-01','time'=>'00:00:00','name'=>'작업자','rphone'=>'02-0000-0000','sphone'=>'010-0000-0000','scon'=>'전송내용1');
//$tmp_arr[] = array('idx'=>'2','date'=>'2018-01-01','time'=>'00:00:00','name'=>'작업자','rphone'=>'02-0000-0000','sphone'=>'010-0000-0000','scon'=>'전송내용2');
//$tmp_arr[] = array('idx'=>'3','date'=>'2018-01-01','time'=>'00:00:00','name'=>'작업자','rphone'=>'02-0000-0000','sphone'=>'010-0000-0000','scon'=>'전송내용3');
//$tmp_arr[] = array('idx'=>'4','date'=>'2018-01-01','time'=>'00:00:00','name'=>'작업자','rphone'=>'02-0000-0000','sphone'=>'010-0000-0000','scon'=>'전송내용4');
//$tmp_arr[] = array('idx'=>'5','date'=>'2018-01-01','time'=>'00:00:00','name'=>'작업자','rphone'=>'02-0000-0000','sphone'=>'010-0000-0000','scon'=>'전송내용5');
//$tmp_arr[] = array('idx'=>'6','date'=>'2018-01-01','time'=>'00:00:00','name'=>'작업자','rphone'=>'02-0000-0000','sphone'=>'010-0000-0000','scon'=>'전송내용6');
//$tmp_arr[] = array('idx'=>'7','date'=>'2018-01-01','time'=>'00:00:00','name'=>'작업자','rphone'=>'02-0000-0000','sphone'=>'010-0000-0000','scon'=>'전송내용7');
//$tmp_arr[] = array('idx'=>'8','date'=>'2018-01-01','time'=>'00:00:00','name'=>'작업자','rphone'=>'02-0000-0000','sphone'=>'010-0000-0000','scon'=>'전송내용8');
//$tmp_arr[] = array('idx'=>'9','date'=>'2018-01-01','time'=>'00:00:00','name'=>'작업자','rphone'=>'02-0000-0000','sphone'=>'010-0000-0000','scon'=>'전송내용9');
//$tmp_arr[] = array('idx'=>'10','date'=>'2018-01-01','time'=>'00:00:00','name'=>'작업자','rphone'=>'02-0000-0000','sphone'=>'010-0000-0000','scon'=>'전송내용10');
//
//$grid_response             = array();
//$grid_response["page"]     = "1";
//$grid_response["total"]    = "2";
//$grid_response["records"]  = "10";
//$grid_response["rows"]     = $tmp_arr;

echo json_encode($grid_response, true);
?>

