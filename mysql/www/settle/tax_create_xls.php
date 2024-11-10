<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 세금계산서 엑셀 생성
 */
//Page Info
$pageMenuIdx = 78;
//Init
include_once "../_init_.php";

$C_Settle = new Settle();

$tax_type   = $_POST["tax_type"];
$date_ym    = $_POST["date_ym"];
$target_idx = $_POST["target_idx"];

$time = strtotime($date_ym . "-01");

$date_start = date('Y-m-d', $time);
$date_end = date('Y-m-t', $time);

$date_title = date('Y년 m월', $time);

$_list = $C_Settle -> getSettleDailySum($date_ym, $tax_type, $target_idx);

$name = $_list[0]["target_name"];

if($tax_type == "SALE"){
	$header_target_text = "매출처";
}else{
	$header_target_text = "매입처";
}

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Style\Fill;

//$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
//$writer->save("????_???.xlsx");

$row_index = $start_row = 2;
$title_end = "C";

try {
	$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
	$spreadsheet->setActiveSheetIndex(0);
	$activesheet = $spreadsheet->getActiveSheet();

	//제목 입력
	$activesheet->mergeCells("A" . $row_index . ":" . $title_end . $row_index);
	$activesheet->setCellValueExplicit("A" . $row_index, $header_target_text . ": " . $name, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

	$row_index++;

	//날짜 입력
	$activesheet->mergeCells("A" . $row_index . ":" . $title_end . $row_index);
	$activesheet->setCellValueExplicit("A" . $row_index, "날짜 : " . $date_title, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

	$row_index++;
	$row_index++;


	//헤더 병합 설정
	$activesheet->mergeCells("B" . $row_index . ":C" . $row_index);   //내용

	//헤더명
	$activesheet->setCellValueExplicit("A" . $row_index, "날짜", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
	$activesheet->setCellValueExplicit("B" . $row_index, "판매일보금액", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

	//헤더 스타일
	$activesheet->getStyle('A' . $row_index . ':' . 'C' . $row_index)->applyFromArray(
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

	$activesheet->getColumnDimension("A")->setWidth(16);
	$activesheet->getColumnDimension("B")->setWidth(12);
	$activesheet->getColumnDimension("C")->setWidth(28);

	$row_index++;

	//List Apply
	foreach ($_list as $row_num => $row) {

		//첫행 일경우 날짜 RowSpan = 4
		$activesheet->mergeCells("A" . $row_index . ":A" . ($row_index + 3));   //날짜 Marge

		//날짜
		//강제 텍스트 지정
		//$activesheet->setCellValueExplicit("A:" . $row_index, $row["settle_date"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->setCellValueExplicit("A" . $row_index, $row["settle_date"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->getStyle("A" . $row_index)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
		$activesheet->getStyle("A" . $row_index)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


		//과세 텍스트 입력
		$activesheet->setCellValueExplicit("B" . $row_index, "과세", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->getStyle("B" . $row_index)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		//금액 입력
		$activesheet->setCellValueExplicit("C" . $row_index, $row["taxation_amt"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle("C" . $row_index)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		$activesheet->getStyle("C" . $row_index)->getNumberFormat()->setFormatCode('#,##0');

		$row_index++;

		//면세 텍스트 입력
		$activesheet->setCellValueExplicit("B" . $row_index, "면세", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->getStyle("B" . $row_index)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		//금액 입력
		$activesheet->setCellValueExplicit("C" . $row_index, $row["free_amt"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle("C" . $row_index)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		$activesheet->getStyle("C" . $row_index)->getNumberFormat()->setFormatCode('#,##0');

		$row_index++;

		//영세 텍스트 입력
		$activesheet->setCellValueExplicit("B" . $row_index, "영세", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->getStyle("B" . $row_index)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
		$activesheet->getStyle("C" . $row_index)->getNumberFormat()->setFormatCode('#,##0');

		//금액 입력
		$activesheet->setCellValueExplicit("C" . $row_index, $row["samll_amt"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle("C" . $row_index)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		$activesheet->getStyle("C" . $row_index)->getNumberFormat()->setFormatCode('#,##0');

		$row_index++;


		//합계 텍스트 입력
		$activesheet->setCellValueExplicit("B" . $row_index, "합계", PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
		$activesheet->getStyle("B" . $row_index)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

		//금액 입력
		$activesheet->setCellValueExplicit("C" . $row_index, $row["sum_amt"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
		$activesheet->getStyle("C" . $row_index)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
		$activesheet->getStyle("C" . $row_index)->getNumberFormat()->setFormatCode('#,##0');


		$row_index++;
	}


	$activesheet->getStyle("A" . ($start_row + 3) . ":" . $title_end . ($row_index - 1))->applyFromArray(
		array(
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					'color' => ['argb' => 'FF000000']
				],
			]
		)
	);

	//엑셀 파일명
	list($usec, $sec) = explode(" ", microtime());
	$create_filename = (round(((float)$usec + (float)$sec))) . rand(1, 10000);        // 날짜에 따라 변환
	$create_filename .= ".xlsx";

	//엑셀 생성
	//저장 위치 DY_STOCK_ORDER_PATH
	$Excel_writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/
	$Excel_writer->save(DY_TAX_XLS_PATH . "/" . $create_filename);

	//$Excel_writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
	//$Excel_writer->save('php://output');
}catch (\PhpOffice\PhpSpreadsheet\Exception $exception){

}

$response = array();
$response["result"] = false;

//파일 생성 확인
if(file_exists(DY_TAX_XLS_PATH."/".$create_filename)){

	//파일생성로그 입력
	$inserted_idx = $C_Settle->insertTaxFileLog($create_filename, $target_idx, $tax_type, "", $date_title);
	$response["result"] = true;
	$response["target_idx"] = $target_idx;
	$response["file_idx"] = $inserted_idx;
	$response["filename"] = $create_filename;

}
echo json_encode($response);

?>