<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 원장 엑셀 생성
 */
//Page Info
$pageMenuIdx = 78;
//Init
include_once "../_init_.php";

$C_Settle = new Settle();

$target_idx  = $_POST["target_idx"];
$ledger_type = $_POST["ledger_type"];
$date_start  = $_POST["date_start"];
$date_end    = $_POST["date_end"];
$is_shrink   = $_POST["is_shrink"];

if ($ledger_type == 'LEDGER_SALE') {
    $C_Seller = new Seller();
    $_seller_view = $C_Seller->getUseSellerAllData($target_idx);

    $seller_type = $_seller_view["seller_type"];
    $vendor_use_charge = $_seller_view["vendor_use_charge"];
}
if ($ledger_type == 'LEDGER_PURCHASE') {
    $C_Supplier = new Supplier();
    $_supplier_view = $C_Supplier->getSupplierData($target_idx);

    $vendor_use_prepay = $_supplier_view["supplier_use_prepay"];
}



$date_start_time = strtotime($date_start);
$date_end_time = strtotime($date_end);

$ledger_period = date('Y년 m월', $date_start_time) . " ~ " . date('Y년 m월', $date_end_time);

$i = 0;
do{
	$new_date_time = strtotime('+'.$i++.' month', $date_start_time);
	$_search_date_ary[] =  "" . date('Y-m-d', $new_date_time) . "";
//	$month_ary[] = array(
//		"date" => date('Y-m', $new_date_time),
//	);
	$month_ary[] = date('Y-m', $new_date_time);
}while ($new_date_time < $date_end_time);

$xls_list = array();

foreach($month_ary as $month) {
	if($ledger_type == "LEDGER_PURCHASE") {
		$_list = $C_Settle->getPurchaseLedgerList($target_idx, $month);
	}else{
		$_list = $C_Settle->getSaleLedgerList($target_idx, $month);
	}
	$xls_list[] = array(
		"month" => $month,
		"list" => $_list
	);
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

$row_index = $start_row = 2;

//헤더명 입력
if($ledger_type == "LEDGER_PURCHASE") {
	//매입거래처별원장
	$type_text        = "매입액";
	$tran_amount_text = "송금액";
}elseif($ledger_type == "LEDGER_SALE"){
	//매출거래처별원장
	$type_text        = "매출액";
	$tran_amount_text = "실입금액";
}

foreach($xls_list as $xls_set) {

	if($ledger_type == "LEDGER_PURCHASE") {
		//전월이월
		$_prev_total = $C_Settle->getPurchaseLedgerPrevSum($target_idx, $xls_set["month"]);

		//마감금액
		$_prev_sum_settle_amount       = $_prev_total["sum_settle_amount"];
		$_prev_sum_ledge_adjust_amount = $_prev_total["sum_ledger_adjust_amount"];
		$_prev_sum_ledge_tran_amount   = $_prev_total["sum_ledger_tran_amount"];
		$_prev_sum_ledge_refund_amount = $_prev_total["sum_ledger_refund_amount"];
		$_prev_sum_stock_amount        = $_prev_total["sum_stock_amount"];

		//잔액
		$_prev_remain_total = $_prev_sum_settle_amount + $_prev_sum_ledge_adjust_amount - $_prev_sum_ledge_tran_amount + $_prev_sum_ledge_refund_amount + $_prev_sum_stock_amount;

	}else{
		//전월이월
		$_prev_total = $C_Settle->getSaleLedgerPrevSum($target_idx, $xls_set["month"]);

		//마감금액
		$_prev_sum_settle_amount          = $_prev_total["sum_settle_amount"];
		$_prev_sum_ledge_adjust_amount    = $_prev_total["sum_ledger_adjust_amount"];
		$_prev_sum_ledge_tran_amount      = $_prev_total["sum_ledger_tran_amount"];
		$_prev_sum_ledge_refund_amount    = $_prev_total["sum_ledger_refund_amount"];

		//잔액
		$_prev_remain_total = $_prev_sum_settle_amount + $_prev_sum_ledge_adjust_amount - $_prev_sum_ledge_tran_amount + $_prev_sum_ledge_refund_amount;
	}

	//제목 입력
	$title_end = ($is_shrink != "Y") ? "I" : "G";
	$activesheet->mergeCells("A" . $row_index . ":".$title_end . $row_index);   //년월
	$activesheet->setCellValueExplicit("A" . $row_index, date('Y년 m월', strtotime($xls_set["month"]."-01")), PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

	$row_index++;

	if ($is_shrink != "Y") {

		//상내내용 포함

		//헤더 병합 설정
		$activesheet->mergeCells("A" . $row_index . ":A" . ($row_index + 1));   //일자
		$activesheet->mergeCells("B" . $row_index . ":B" . ($row_index + 1));   //내용
		$activesheet->mergeCells("C" . $row_index . ":E" . $row_index);       //매출액
		$activesheet->mergeCells("F" . $row_index . ":F" . ($row_index + 1));   //실입금액
		$activesheet->mergeCells("G" . $row_index . ":G" . ($row_index + 1));   //공제/환급액등
		$activesheet->mergeCells("H" . $row_index . ":H" . ($row_index + 1));   //잔액
		$activesheet->mergeCells("I" . $row_index . ":I" . ($row_index + 1));   //비고

		//헤더명
		$activesheet->setCellValueExplicit("A" . $row_index, "일자", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->setCellValueExplicit("B" . $row_index, "내용", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->setCellValueExplicit("C" . $row_index, $type_text, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->setCellValueExplicit("F" . $row_index, $tran_amount_text, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->setCellValueExplicit("G" . $row_index, "공제/환급액 등", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->setCellValueExplicit("H" . $row_index, "잔액", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->setCellValueExplicit("I" . $row_index, "비고", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

		$activesheet->setCellValueExplicit("C" . ($row_index + 1), "마감", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->setCellValueExplicit("D" . ($row_index + 1), "보정", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->setCellValueExplicit("E" . ($row_index + 1), "합계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

		//헤더 스타일
		$activesheet->getStyle('A' . $row_index . ':' . 'I' . ($row_index + 1))->applyFromArray(
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


		$xls_header = array(
			array(
				"header_name" => "일자",
				"field_name" => "dt",
				"width" => 16,
			),
			array(
				"header_name" => "내용",
				"field_name" => "ledger_title",
				"width" => 22,
			),
			array(
				"header_name" => "마감",
				"field_name" => "closing_settle_amount",
				"width" => 16,
			),
			array(
				"header_name" => "보정",
				"field_name" => "sum_adjust_amount",
				"width" => 14,
			),
			array(
				"header_name" => "합계",
				"field_name" => "sum_settle_amount",
				"width" => 14,
			),
			array(
				"header_name" => "송금액/실입금액",
				"field_name" => "sum_ledger_tran_amount",
				"width" => 14,
			),
			array(
				"header_name" => "공제/환급금액",
				"field_name" => "sum_ledger_refund_amount",
				"width" => 16,
			),
			array(
				"header_name" => "잔액",
				"field_name" => "sum_remain_amount",
				"width" => 16,
			),
			array(
				"header_name" => "비고",
				"field_name" => "ledger_memo",
				"width" => 40,
			)
		);

		$row_index++;
		$row_index++;

	} else {

		//상내내용 제외

		//헤더명
		$activesheet->setCellValueExplicit("A" . $row_index, "일자", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->setCellValueExplicit("B" . $row_index, "내용", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->setCellValueExplicit("C" . $row_index, $type_text, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->setCellValueExplicit("D" . $row_index, $tran_amount_text, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->setCellValueExplicit("E" . $row_index, "공제/환급액 등", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->setCellValueExplicit("F" . $row_index, "잔액", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->setCellValueExplicit("G" . $row_index, "비고", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		//헤더 스타일
		$activesheet->getStyle('A' . $row_index . ':' . 'G' . $row_index)->applyFromArray(
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

		$xls_header = array(
			array(
				"header_name" => "일자",
				"field_name" => "dt",
				"width" => 16,
			),
			array(
				"header_name" => "내용",
				"field_name" => "ledger_title",
				"width" => 22,
			),
			array(
				"header_name" => "매출액/매입액",
				"field_name" => "sum_settle_amount",
				"width" => 16,
			),
			array(
				"header_name" => "송금액/실입금액",
				"field_name" => "sum_ledger_tran_amount",
				"width" => 14,
			),
			array(
				"header_name" => "공제/환급액",
				"field_name" => "sum_ledger_refund_amount",
				"width" => 16,
			),
			array(
				"header_name" => "잔액",
				"field_name" => "sum_remain_amount",
				"width" => 16,
			),
			array(
				"header_name" => "비고",
				"field_name" => "ledger_memo",
				"width" => 32,
			)
		);

		$row_index++;
	}


	//전월이월
    if(($seller_type == "VENDOR_SELLER" && $vendor_use_charge == "Y") || $vendor_use_prepay == "Y") {
        $cellValue = $_prev_remain_total * -1;
    }else{
        $cellValue = $_prev_remain_total;
    }
	if ($is_shrink != "Y"){
		//강제 텍스트 지정
		$cod        = getNameFromNumber(1) . $row_index;
		$activesheet->setCellValueExplicit($cod, "전월이월", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->getStyle($cod)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

		$cod        = getNameFromNumber(7) . $row_index;
		$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
		$activesheet->getStyle($cod)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
	}else{
		//강제 텍스트 지정
		$cod        = getNameFromNumber(1) . $row_index;
		$activesheet->setCellValueExplicit($cod, "전월이월", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->getStyle($cod)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

		$cod        = getNameFromNumber(5) . $row_index;
		$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
		$activesheet->getStyle($cod)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
	}
	$row_index++;

	$_list = $xls_set["list"];

	//합계 계산
	$_total_closing = 0;
	$_total_adjust = 0;
	$_total_tran = 0;
	$_total_refund = 0;
	$_total_remain = $_prev_remain_total;

	//List Apply
	foreach ($_list as $row_num => $row) {

		$xls_row = array();
		foreach ($xls_header as $key => $val) {
			$xls_row[] = $row[$val["field_name"]];
		}

		$closing_settle_amount = $row["closing_settle_amount"];
		$adjust_settle_amount = $row["adjust_settle_amount"];
		$sum_ledge_adjust_amount = $row["sum_ledger_adjust_amount"];
		$sum_ledge_tran_amount = $row["sum_ledger_tran_amount"];
		$sum_ledge_refund_amount = $row["sum_ledger_refund_amount"];

		//보정금액 합계
		$adjust_total = $adjust_settle_amount + $sum_ledge_adjust_amount;
		//매입합계
		$purchase_total = $closing_settle_amount + $adjust_total;

		//잔액
		$remain_total = $purchase_total - $sum_ledge_tran_amount + $sum_ledge_refund_amount;

        $_total_remain += $remain_total;

		$_total_closing += $closing_settle_amount;
		$_total_adjust += $adjust_settle_amount + $sum_ledge_adjust_amount;
		$_total_tran += $sum_ledge_tran_amount;
		$_total_refund += $sum_ledge_refund_amount;

		$currentColumn = 0;
		foreach ($xls_row as $cellValue) {
			$field_name = $xls_header[$currentColumn]["field_name"];
			$cod        = getNameFromNumber($currentColumn) . $row_index;


			if ($field_name == "closing_settle_amount"
				|| $field_name == "sum_adjust_amount"
				|| $field_name == "sum_settle_amount"
				|| $field_name == "sum_ledger_tran_amount"
				|| $field_name == "sum_ledger_refund_amount"
				|| $field_name == "sum_remain_amount"
			) {

				if($field_name == "sum_remain_amount"){
                    if(($seller_type == "VENDOR_SELLER" && $vendor_use_charge == "Y") || $vendor_use_prepay == "Y") {
                        $cellValue = $_total_remain * -1;
                    }else {
                        $cellValue = $_total_remain;
                    }
				}

				//통화 셀 서식 지정
				$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
				$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
				//$activesheet->getStyle($cod)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

			} elseif($field_name == "ledger_memo") {

				//강제 텍스트 지정
				$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
				$activesheet->getStyle($cod)->getAlignment()->setWrapText(true);


			} else {

				//강제 텍스트 지정
				$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
				$activesheet->getStyle($cod)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
			}

			++$currentColumn;
		}

		$row_index++;

		//세부항목 가져오기
		if($is_shrink != "Y") {
			$_detail_list = "";
			$_detail_list = $C_Settle->getLedgerDetail($target_idx, $row["dt"], $ledger_type);
			if ($_detail_list) {
				foreach ($_detail_list as $row_sub) {

					//헤더 스타일
					$activesheet->getStyle('A' . $row_index . ':' . $title_end . $row_index)->applyFromArray(
						array(
							'fill' => [
								'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
								'rotation' => 90,
								'startColor' => [
									'argb' => 'FFf6fcff',
								],
								'endColor' => [
									'argb' => 'FFf6fcff',
								],
							]
						)
					);

					$xls_row = array();
					foreach ($xls_header as $key => $val) {
						$xls_row[] = $row_sub[$val["field_name"]];
					}

					$currentColumn = 0;
					foreach ($xls_row as $cellValue) {
						$field_name = $xls_header[$currentColumn]["field_name"];
						$cod        = getNameFromNumber($currentColumn) . $row_index;

						if ($field_name == "closing_settle_amount"
							|| $field_name == "sum_adjust_amount"
							|| $field_name == "sum_settle_amount"
							|| $field_name == "sum_ledger_tran_amount"
							|| $field_name == "sum_ledger_refund_amount"
							|| $field_name == "sum_remain_amount"
						) {

							//통화 셀 서식 지정
							$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
							//$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('"\" #,##0');
							$activesheet->getStyle($cod)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

						} elseif ($field_name == "ledger_memo") {

							//강제 텍스트 지정
							$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
							$activesheet->getStyle($cod)->getAlignment()->setWrapText(true);


						} else {

							//강제 텍스트 지정
							$activesheet->setCellValueExplicit($cod, $cellValue, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
							$activesheet->getStyle($cod)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
						}


						++$currentColumn;
					}
					$row_index++;
				}
			}
		}
	}


	$row_index++;


	//합계설정
	$activesheet->mergeCells("A" . $row_index . ":B" . $row_index);   //합계
	$cod = getNameFromNumber(0) . $row_index;
	$activesheet->setCellValueExplicit($cod, "합계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	$activesheet->getStyle($cod)->getAlignment()->setHorizontal("center");

	if($is_shrink != "Y") {

		//마감
		$cod = getNameFromNumber(2) . $row_index;
		$activesheet->setCellValueExplicit($cod, $_total_closing, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
		//보정
		$cod = getNameFromNumber(3) . $row_index;
		$activesheet->setCellValueExplicit($cod, $_total_adjust, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
		//합계
		$cod = getNameFromNumber(4) . $row_index;
		$activesheet->setCellValueExplicit($cod, $_total_closing + $_total_adjust, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
		//송금액/입금액
		$cod = getNameFromNumber(5) . $row_index;
		$activesheet->setCellValueExplicit($cod, $_total_tran, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
		//공제/환금액
		$cod = getNameFromNumber(6) . $row_index;
		$activesheet->setCellValueExplicit($cod, $_total_refund, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');


		$totalRowRangeCod = getNameFromNumber(0) . $row_index . ":" . getNameFromNumber(8) . $row_index;
		$activesheet->getStyle($totalRowRangeCod)->applyFromArray(
			array(
				'fill' => [
					'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
					'rotation' => 90,
					'startColor' => [
						'argb' => 'FFC4C4C4',
					],
					'endColor' => [
						'argb' => 'FFC4C4C4',
					],
				]
			)
		);
	}else{

		//합계
		$cod = getNameFromNumber(2) . $row_index;
		$activesheet->setCellValueExplicit($cod, $_total_closing + $_total_adjust, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
		//송금액/입금액
		$cod = getNameFromNumber(3) . $row_index;
		$activesheet->setCellValueExplicit($cod, $_total_tran, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');
		//공제/환금액
		$cod = getNameFromNumber(4) . $row_index;
		$activesheet->setCellValueExplicit($cod, $_total_refund, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('#,##0');


		$totalRowRangeCod = getNameFromNumber(0) . $row_index . ":" . getNameFromNumber(6) . $row_index;
		$activesheet->getStyle($totalRowRangeCod)->applyFromArray(
			array(
				'fill' => [
					'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
					'rotation' => 90,
					'startColor' => [
						'argb' => 'FFC4C4C4',
					],
					'endColor' => [
						'argb' => 'FFC4C4C4',
					],
				]
			)
		);
	}

	$row_index++;
}

$activesheet->getStyle("A".$start_row.":".$title_end.($row_index-1))->applyFromArray(
	array(
		'borders' => [
			'allBorders' => [
				'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				'color' => ['argb' => 'FF000000']
			],
		]
	)
);

//foreach(range('A', $xls_header_end) as $columnID) {
//$activesheet->getColumnDimension($columnID)->setAutoSize(true);
//}

foreach($xls_header as $key => $hh)
{
	$columnID = getNameFromNumber($key);
	$activesheet->getColumnDimension($columnID)->setWidth($hh["width"]);
}


//엑셀 파일명
list($usec, $sec) = explode(" ",microtime());
$create_filename = (round(((float)$usec + (float)$sec))).rand(1,10000);		// 날짜에 따라 변환
$create_filename .= ".xlsx";

//엑셀 생성
//저장 위치 DY_STOCK_ORDER_PATH
$Excel_writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/
$Excel_writer->save(DY_SETTLE_PATH."/".$create_filename);

//$Excel_writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
//$Excel_writer->save('php://output');

$response = array();
$response["result"] = false;

//파일 생성 확인
if(file_exists(DY_SETTLE_PATH."/".$create_filename)){

	//파일생성로그 입력
	$inserted_idx = $C_Settle->insertLedgerFileLog($create_filename, $target_idx, $ledger_type, "", $ledger_period, $is_shrink);
	$response["result"] = true;
	$response["target_idx"] = $target_idx;
	$response["file_idx"] = $inserted_idx;
	$response["filename"] = $create_filename;

}
echo json_encode($response);

?>