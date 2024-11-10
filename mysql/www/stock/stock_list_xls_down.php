<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 현재고조회 리스트 엑셀 다운로드
 */
//Page Info
$pageMenuIdx = 111;
//Init
include_once "../_init_.php";

//Buffer Start
ob_start();

//Xls Down Check Session Init
$_SESSION["STOCK_LIST"] = "Y";

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

$order_by = "STOCK.product_option_idx DESC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_search_param = $_GET["param"];
//검색 가능한 컬럼 지정
$available_col = array(
    "product_category_l_idx",
    "product_category_m_idx",
    "product_sale_type",
    "supplier_idx",
    "stock_amount_start",
    "stock_amount_end",
    "stock_alert",
    "soldout_status",
    "soldout_temp_status",
    "search_column",
);
$qryWhereAry = array();

if($_search_param)
{
$_search_param = urldecode($_search_param);
$_search_paramAry = explode("&", $_search_param);
parse_str($_search_param, $_search_paramAryList);
foreach($_search_paramAry as $sitem) {
    list($col, $val) = explode("=", $sitem);

    if(trim($val) && in_array($col, $available_col)) {
        if(
            $col == "product_category_l_idx"
            || $col == "product_category_m_idx"
            || $col == "product_sale_type"
        ) {
            $qryWhereAry[] = $col . " = N'" . $val . "'";
        }elseif(trim($col) == "stock_amount_start"){
            //재고 개수 조건 - 최소 개수
            if($_search_paramAryList["stock_status_for_amount"] != "") {
                $tmp_amount_field_name = "stock_amount_".$_search_paramAryList["stock_status_for_amount"];
                $qryWhereAry[] = $tmp_amount_field_name . " >= N'" . $val . "'";
            }
        }elseif(trim($col) == "stock_amount_end"){
            //재고 개수 조건 - 최대 개수
            if($_search_paramAryList["stock_status_for_amount"] != "") {
                $tmp_amount_field_name = "stock_amount_".$_search_paramAryList["stock_status_for_amount"];
                $qryWhereAry[] = $tmp_amount_field_name . " <= N'" . $val . "'";
            }
        }elseif(trim($col) == "stock_alert"){
            //재고상태 조건
            if($val = "stock_warning"){
                //재고경고
                $qryWhereAry[] = " stock_amount_NORMAL < product_option_warning_count ";
            }elseif($val = "stock_danger"){
                //재고위험
                $qryWhereAry[] = " stock_amount_NORMAL < product_option_danger_count ";
            }elseif($val = "stock_warning_danger"){
                $qryWhereAry[] = " (stock_amount_NORMAL < product_option_warning_count Or stock_amount_NORMAL < product_option_danger_count) ";
            }
        }elseif(trim($col) == "soldout_status"){
            //품절상태 조건
            if($val == "except_soldout"){
                //품절제외
                $qryWhereAry[] = " product_option_soldout = N'N' ";
            }elseif($val == "soldout"){
                //품절
                $qryWhereAry[] = " product_option_soldout = N'Y' ";
            }
        }elseif(trim($col) == "soldout_temp_status"){
            //일시품절상태 조건
            if($val == "except_soldout_temp"){
                //일시품절제외
                $qryWhereAry[] = " product_option_soldout_temp = N'N' ";
            }elseif($val == "soldout_temp"){
                //일시품절
                $qryWhereAry[] = " product_option_soldout_temp = N'Y' ";
            }

        }elseif(trim($col) == "search_column"){
            if(trim($_search_paramAryList["search_keyword"]) != "") {
                if($val == "product_name_option_name"){
                    $qryWhereAry[] = "
							P.product_name like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'
							Or 
							PO.product_option_name like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'
						";
                }else {
                    $qryWhereAry[] = $val . " like N'%" . trim($_search_paramAryList["search_keyword"]) . "%'";
                }
            }
        }else{
            $qryWhereAry[] = $col . " like N'%" . $val . "%'";
        }
    }
}

$date_search_col = "";
//	if($_search_paramAryList["date_start"] != "" && $_search_paramAryList["date_end"] != ""){
//		$qryWhereAry[] = "
//			P.product_regdate >= '".$_search_paramAryList["date_start"]." 00:00:00'
//			And P.product_regdate <= '".$_search_paramAryList["date_end"]." 23:59:59'
//		";
//	}

if($_search_paramAryList["product_period_type"] == "product_regdate"){
    $qryWhereAry[] = "	 
			P.product_regdate >= '".$_search_paramAryList["date_start2"]." 00:00:00' 
			And P.product_regdate <= '".$_search_paramAryList["date_end2"]." 23:59:59' 
		";
}elseif($_search_paramAryList["product_period_type"] == "product_option_regdate"){
    $qryWhereAry[] = "	 
			PO.product_option_regdate >= '".$_search_paramAryList["date_start2"]." 00:00:00' 
			And PO.product_option_regdate <= '".$_search_paramAryList["date_end2"]." 23:59:59' 
		";
}

//재고 상태
if($_search_paramAryList["stock_status"] != "") {

}
//판매 상태
if($_search_paramAryList["sale_status"] != "") {

}

//공급처
if($_search_paramAryList["supplier_idx"]){
    $qryWhereAry[] = "P.supplier_idx in (" . implode(",", $_search_paramAryList["supplier_idx"]) . ")";
}

}

// search & sort
$args['searchArr'] 	    = array();// 검색 배열
$args['sortArr'] 		= array();

if (!$args['searchWord']) $args['searchWord'] = 1;

// paging set
$args['show_row'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;
$args['show_page'] 	= ($_GET["rows"]) ? $_GET["rows"] : 10;

// make select query
$args['qry_table_idx'] 	= "PO.product_option_idx";
$args['qry_get_colum'] 	= " 
							STOCK.*
							, P.product_img_main
							, P.product_img_1, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_1) as product_img_filename_1
							, P.product_img_2, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_2) as product_img_filename_2
							, P.product_img_3, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_3) as product_img_filename_3
							, P.product_img_4, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_4) as product_img_filename_4
							, P.product_img_5, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_5) as product_img_filename_5
							, P.product_img_6, (Select save_filename From DY_FILES F Where F.file_idx = P.product_img_6) as product_img_filename_6
							, P.product_name
							, IFNULL((Select name From DY_CATEGORY C Where C.category_idx = P.product_category_l_idx), '') as category_l_name, P.product_category_l_idx 
							, IFNULL((Select name From DY_CATEGORY C Where C.category_idx = P.product_category_m_idx), '') as category_m_name, P.product_category_m_idx
							, PO.product_option_name
							, PO.product_option_warning_count
							, PO.product_option_danger_count
							, PO.product_option_soldout
							, PO.product_option_soldout_temp
							, P.product_regdate
							, S.supplier_name
							, IFNULL(Matching.stock_amount_ACCEPT, 0) as stock_amount_ACCEPT
                            ";

$args['qry_table_name'] 	= " 
								(
								Select 
									ST.product_idx, ST.product_option_idx
									, Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End) as stock_amount_NORMAL
									, Sum(Case When stock_status = 'ABNORMAL' Then stock_amount * stock_type Else 0 End) as stock_amount_ABNORMAL
									, Sum(Case When stock_status = 'BAD' Then stock_amount * stock_type Else 0 End) as stock_amount_BAD
									, Sum(Case When stock_status = 'BAD_OUT_EXCHANGE' Then stock_amount * stock_type Else 0 End) as stock_amount_BAD_OUT_EXCHANGE
									, Sum(Case When stock_status = 'BAD_OUT_RETURN' Then stock_amount * stock_type Else 0 End) as stock_amount_BAD_OUT_RETURN
									, Sum(Case When stock_status = 'HOLD' Then stock_amount * stock_type Else 0 End) as stock_amount_HOLD
									, Sum(Case When stock_status = 'FAC_RETURN_EXCHNAGE' Then stock_amount * stock_type Else 0 End) as stock_amount_FAC_RETURN_EXCHNAGE
									, Sum(Case When stock_status = 'FAC_RETURN_BACK' Then stock_amount * stock_type Else 0 End) as stock_amount_FAC_RETURN_BACK
									, Sum(Case When stock_status = 'LOSS' Then stock_amount * stock_type Else 0 End) as stock_amount_LOSS
									, Sum(Case When stock_status = 'DISPOSAL' Then stock_amount * stock_type Else 0 End) as stock_amount_DISPOSAL
									, Sum(Case When stock_status = 'DISPOSAL_PERMANENT' Then stock_amount * stock_type Else 0 End) as stock_amount_DISPOSAL_PERMANENT
									, Sum(Case When stock_status = 'INVOICE' Then stock_amount * stock_type Else 0 End) as stock_amount_INVOICE
									, Sum(Case When stock_status = 'SHIPPED' Then stock_amount * stock_type Else 0 End) as stock_amount_SHIPPED
								From DY_STOCK ST
									Where stock_is_del = N'N' And stock_is_confirm = N'Y'
									Group by ST.product_idx, product_option_idx
								) as STOCK
								Inner Join DY_PRODUCT P On STOCK.product_idx = P.product_idx 
								Inner Join DY_PRODUCT_OPTION PO On STOCK.product_option_idx = PO.product_option_idx
								Left Outer Join DY_MEMBER_SUPPLIER S On P.supplier_idx = S.member_idx
								Left Outer Join (
									Select product_option_idx, Sum(product_option_cnt)  as stock_amount_ACCEPT
										From DY_ORDER_PRODUCT_MATCHING OPM
											Inner Join DY_ORDER DO On OPM.order_idx = DO.order_idx
										Where 
											OPM.order_matching_is_del = N'N'
											And DO.order_is_del = N'N'
											And DO.order_progress_step = N'ORDER_ACCEPT'
										Group by product_option_idx 
								) Matching On PO.product_option_idx = Matching.product_option_idx
";
$args['qry_where']			= " 
								P.product_sale_type = N'SELF' 
								And P.product_is_del = N'N' 
								And P.product_is_trash = N'N' 
								And P.product_is_use = N'Y'
								And PO.product_option_is_use = N'Y'
								";
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

/**
 * ListTable 사용 안함
 * 일반 쿼리로 실행
 */
$qry = "";
$qry = "Select " . $args['qry_get_colum']  . " From " . $args['qry_table_name']  . " Where " . $args['qry_where'] . " Order By " . $args['qry_orderby'];
$C_Dbconn = new DBConn();
$C_Dbconn -> db_connect();
$_list = $C_Dbconn -> execSqlList($qry);
$C_Dbconn -> db_close();


//기본 엑셀 항목 설정 가져오기
include "../common/_xls_default_column_array.php";
$xls_header_default = $xls_column_ary["STOCK_LIST"];


//사용자 엑셀 항목 설정 가져오기
$C_ColumnModel = new ColumnModel();
$userColumnList = $C_ColumnModel -> getUserColumnXls("STOCK_LIST", $GL_Member["member_idx"]);

//조합 - 기준은 기본 엑셀 항목
$xls_header_list = array();
if($userColumnList) {
	foreach ($userColumnList as $u) {
		if($u["col_user_is_use"] == "Y") {
			$user_key = $u["col_field_name"];
			$exists   = array_filter($xls_header_default, function ($val, $key) use ($user_key) {

				return ($val["col"] == $user_key) ? true : false;

			}, ARRAY_FILTER_USE_BOTH);

			if ($exists) {
				$tmp           = reset($exists);
				$tmp["name"]   = $u["col_user_visible_name"];
				$tmp["is_use"] = ($u["col_user_is_use"] == "Y") ? true : false;
			} else {
				$tmp = $u;
			}
			$xls_header_list[] = $tmp;
		}
	}
}

//조합 - 사용자 설정에 없는 항목 넣기
foreach($xls_header_default as $d){
	$col_key = $d["col"];
	$exists   = array_filter($userColumnList, function ($val, $key) use ($col_key) {

		return ($val["col_field_name"] == $col_key) ? true : false;

	}, ARRAY_FILTER_USE_BOTH);

	if (!$exists) {
		$xls_header_list[] = $d;
	}
}

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Style\Fill;

//$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
//$writer->save("????_???.xlsx");

$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
$spreadsheet->setActiveSheetIndex(0);
$activesheet = $spreadsheet->getActiveSheet();
$xls_header = $xls_header_list;
$xls_header_end = getNameFromNumber(count($xls_header)-1);
$header_ary = array();
foreach ($xls_header as $hh)
{
	$header_ary[] = $hh["name"];
}
$activesheet->fromArray($header_ary, NULL, 'A1');
$activesheet->getStyle('A1:'.$xls_header_end.'1')->applyFromArray(
	array(
		'fill' => [
			'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
			'rotation' => 90,
			'startColor' => [
				'argb' => 'FFED7D31',
			],
			'endColor' => [
				'argb' => 'FFED7D31',
			],
		],
		'alignment' => [
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
		]
	)
);

//List Apply
$i = 2;
foreach($_list as $row_num => $row) {
	$xls_row = array();
	foreach ($xls_header as $key => $val) {
		$xls_row[] = $row[$val["col"]];
	}

	//$activesheet->fromArray($xls_row, NULL, 'A'.$i);
	$currentColumn = 0;
	foreach ($xls_row as $cellValue) {
		$field_name = $xls_header[$currentColumn]["col"];
		$cod = getNameFromNumber($currentColumn) . $i;
		$data_type = $xls_header[$currentColumn]["data_type"];
		$halign = ($xls_header[$currentColumn]["halign"]) ? $xls_header[$currentColumn]["halign"] : "center";
		$valign = ($xls_header[$currentColumn]["valign"]) ? $xls_header[$currentColumn]["valign"] : "middle";


		if($data_type == "money") {
			//통화 셀 서식 지정
			$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('"\" #,##0');
		}elseif($data_type == "number"){
			//통화 셀 서식 지정
			$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
			$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
		}elseif($data_type == "image") {

		}else{
			//강제 텍스트 지정
			$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		}

		$activesheet->getStyle($cod)->getAlignment()->setHorizontal($halign);
		$activesheet->getStyle($cod)->getAlignment()->setVertical($halign);

		++$currentColumn;
	}

	$i++;
}

//foreach(range('A', $xls_header_end) as $columnID) {
//$activesheet->getColumnDimension($columnID)->setAutoSize(true);
//}

foreach($xls_header as $key => $hh)
{
	$columnID = getNameFromNumber($key);
	$activesheet->getColumnDimension($columnID)->setWidth($hh["width"]);
}

//$Excel_writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
$Excel_writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/

function mb_basename($path) { return end(explode('/',$path)); }
function utf2euc($str) { return iconv("UTF-8","cp949//TRANSLIT", $str); }
function is_ie() {
	if(!isset($_SERVER['HTTP_USER_AGENT']))return false;
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) return true; // IE8
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 6.1') !== false) return true; // IE11
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false) return true; // IE11
	return false;
}
$user_filename = "현_재고조회.xlsx";
if (is_ie()) $user_filename = utf2euc($user_filename);

if(is_ie()){
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$user_filename.'"');
$Excel_writer->save('php://output');

ob_end_flush();
$_SESSION["STOCK_LIST"] = "";

ob_end_clean();
?>