<?php
/**
 * 재고관리 관련 Class
 * User: woox
 * Date: 2018-11-10
 */
class Stock extends Dbconn
{

	/**
	 * 신규 발주 입력
	 * @param $args
	 * @return bool|string
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function insertStockOrder($args)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		$stock_order_idx            = "";
		$stock_order_is_ready       = "Y";
		$stock_order_date           = "";
		$stock_order_in_date        = "";
		$stock_order_officer_name   = "";
		$stock_order_officer_tel    = "";
		$supplier_idx               = "";
		$stock_order_supplier_name  = "";
		$stock_order_supplier_tel   = "";
		$stock_order_receiver_name  = "";
		$stock_order_receiver_tel   = "";
		$stock_order_receiver_addr  = "";
		$member_idx                 = $last_member_idx;

		$product_idx                = "";
		$product_option_idx         = "";
		$order_idx                  = 0;
		$stock_kind                 = "STOCK_ORDER"; //재고 구분값 (발주, 교환, 반품)
		$stock_type                 = "0";  //대기 : 0
		$stock_status               = "STOCK_ORDER_READY"; //재고 상태값 (발주 신청)
		$stock_unit_price           = "";
		$stock_due_amount           = "";
		$stock_amount               = "";
		$stock_msg                  = "";
		$stock_order_msg            = "";
		$stock_request_member_idx   = $last_member_idx;
		$stock_regip                = "";

		$product_idx_ary            = array();
		$product_option_idx_ary     = array();
		$stock_unit_price_ary       = array();
		$stock_due_amount_ary       = array();
		$stock_msg_ary              = array();

		extract($args);

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//발주서 Insert
		$qry = "
			INSERT INTO 
			DY_STOCK_ORDER
			(
			stock_order_date, stock_order_in_date,  
			stock_order_officer_name, stock_order_officer_tel,  
			supplier_idx,  
			stock_order_supplier_name, stock_order_supplier_tel,  
			stock_order_receiver_name, stock_order_receiver_tel, stock_order_receiver_addr,  
			member_idx, stock_order_regip, last_member_idx 
			) 
			VALUES
			(
			N'$stock_order_date' 
			, N'$stock_order_in_date' 
			, N'$stock_order_officer_name' 
			, N'$stock_order_officer_tel' 
			, N'$supplier_idx' 
			, N'$stock_order_supplier_name' 
			, N'$stock_order_supplier_tel' 
			, N'$stock_order_receiver_name' 
			, N'$stock_order_receiver_tel' 
			, N'$stock_order_receiver_addr' 
			, N'$member_idx' 
			, N'$modip' 
			, N'$last_member_idx' 
			)";
		$stock_order_idx = parent::execSqlInsert($qry);

		if(count($product_idx_ary) > 0 && count($product_option_idx_ary) > 0) {

			foreach($product_idx_ary as $key => $val) {

				$product_idx        = $product_idx_ary[$key];
				$product_option_idx = $product_option_idx_ary[$key];
				$stock_unit_price   = str_replace(",", "", $stock_unit_price_ary[$key]);
				$stock_due_amount   = str_replace(",", "", $stock_due_amount_ary[$key]);
				$stock_amount       = 0;
				$stock_order_msg    = $stock_msg_ary[$key];

				//발주 상품 입력
				$qry = "
					INSERT INTO 
					DY_STOCK
					(
					product_idx,  
					product_option_idx,  
					stock_order_idx,
					stock_order_is_ready,
					stock_kind,
					stock_type,  
					stock_status,  
					stock_unit_price,  
					stock_due_amount,  
					stock_amount,  
					stock_due_date,
					stock_order_msg,  
					stock_request_member_idx,  
					stock_regip,  
					last_member_idx 
					) 
					VALUES
					(
					N'$product_idx' 
					, N'$product_option_idx' 
					, N'$stock_order_idx' 
					, N'$stock_order_is_ready' 
					, N'$stock_kind' 
					, N'$stock_type' 
					, N'$stock_status' 
					, N'$stock_unit_price' 
					, N'$stock_due_amount' 
					, N'$stock_amount' 
					, N'$stock_order_in_date' 
					, N'$stock_order_msg' 
					, N'$stock_request_member_idx' 
					, N'$modip' 
					, N'$last_member_idx' 
					)
				";
				$stock_idx_tmp = parent::execSqlInsert($qry);

				//관계(부모) IDX 를 자신의 IDX 로 Update
				$qry = "Update DY_STOCK Set stock_ref_idx = stock_idx Where stock_idx = N'$stock_idx_tmp'";
				parent::execSqlUpdate($qry);
			}

			parent::sqlTransactionCommit();     //트랜잭션 커밋
			$returnValue = true;
		}else{
			parent::sqlTransactionRollback();     //트랜잭션 롤백
		}

		parent::db_close();

		//발주서 파일 생성
		if($returnValue) {
			$stock_order_file_idx = $this->createStockOrderDocument($stock_order_idx);
			$returnValue = $stock_order_idx;
		}

		return $returnValue;
	}

	/**
	 * 발주 수정
	 * @param $args
	 * @return string
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function updateStockOrder($args){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = "";

		$stock_order_idx            = "";
		$stock_order_is_ready       = "Y";
		$stock_order_date           = "";
		$stock_order_in_date        = "";
		$stock_order_officer_name   = "";
		$stock_order_officer_tel    = "";
		$supplier_idx               = "";
		$stock_order_supplier_name  = "";
		$stock_order_supplier_tel   = "";
		$stock_order_receiver_name  = "";
		$stock_order_receiver_tel   = "";
		$stock_order_receiver_addr  = "";
		$member_idx                 = $last_member_idx;

		$product_idx                = "";
		$product_option_idx         = "";
		$order_idx                  = 0;
		$stock_kind                 = "STOCK_ORDER"; //재고 구분값 (발주, 교환, 반품)
		$stock_type                 = "0";  //대기 : 0
		$stock_status               = "STOCK_ORDER_READY"; //재고 상태값 (발주 신청)
		$stock_unit_price           = "";
		$stock_due_amount           = "";
		$stock_amount               = "";
		$stock_msg                  = "";
		$stock_order_msg            = "";
		$stock_request_member_idx   = $last_member_idx;
		$stock_regip                = "";

		$product_idx_ary            = array();
		$product_option_idx_ary     = array();
		$stock_unit_price_ary       = array();
		$stock_due_amount_ary       = array();
		$stock_amount_ary           = array();
		$stock_msg_ary              = array();
		$stock_idx_ary              = array();

		extract($args);

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//발주서 Insert
		$qry = "
			Update
			DY_STOCK_ORDER
			Set
				stock_order_date = N'$stock_order_date',
				stock_order_in_date = N'$stock_order_in_date',
				stock_order_officer_name = N'$stock_order_officer_name',
				stock_order_officer_tel = N'$stock_order_officer_tel',
				stock_order_supplier_name = N'$stock_order_supplier_name',
				stock_order_supplier_tel = N'$stock_order_supplier_tel',
				stock_order_receiver_name = N'$stock_order_receiver_name',
				stock_order_receiver_tel = N'$stock_order_receiver_tel',
				stock_order_receiver_addr = N'$stock_order_receiver_addr',
				stock_order_moddate = getdate(),
			    stock_order_modip = N'$modip',
			    last_member_idx = N'$last_member_idx'
			Where stock_order_idx = N'$stock_order_idx'			
		";
		$tmp = parent::execSqlUpdate($qry);

		//발주내역 삭제
		//기존 발주 내역 중에서 수정되어 넘어온 product_option_idx 에 없으면 삭제 처리
		$qry2 = "";
		if(count($product_option_idx_ary) > 0){
			$qry2 = " And product_option_idx not in (".join(',', $product_option_idx_ary).")";
		}
		$qry = "
				Update DY_STOCK
				Set
				    stock_is_del = N'Y'
				    , stock_moddate = getdate()
				    , stock_modip = N'$modip'
				    , last_member_idx = N'$last_member_idx'
				Where 
					stock_order_idx = N'$stock_order_idx'
				    And stock_is_del = N'N'
		";
		$qry .= $qry2;
		$rst2 = parent::execSqlUpdate($qry);

		//넘어온 발주내역 처리
		if(count($product_idx_ary) > 0 && count($product_option_idx_ary) > 0) {

			foreach($product_idx_ary as $key => $val) {

				$product_idx        = $product_idx_ary[$key];
				$product_option_idx = $product_option_idx_ary[$key];
				$stock_unit_price   = str_replace(",", "", $stock_unit_price_ary[$key]);
				$stock_due_amount   = str_replace(",", "", $stock_due_amount_ary[$key]);
				$stock_amount       = 0;
				$stock_order_msg    = $stock_msg_ary[$key];
				$stock_idx          = $stock_idx_ary[$key];

				if($stock_idx == "") {
					//stock_idx 가 없는 경우 Insert
					//발주 상품 입력
					$qry = "
						INSERT INTO 
						DY_STOCK
						(
						product_idx,  
						product_option_idx,  
						stock_order_idx,
						stock_order_is_ready,
						stock_kind,  
						stock_type,  
						stock_status,  
						stock_unit_price,  
						stock_due_amount,  
						stock_amount,  
						stock_due_date,  
						stock_order_msg,  
						stock_request_member_idx,  
						stock_regip,  
						last_member_idx 
						) 
						VALUES
						(
						N'$product_idx' 
						, N'$product_option_idx' 
						, N'$stock_order_idx' 
						, N'$stock_order_is_ready' 
						, N'$stock_kind' 
						, N'$stock_type' 
						, N'$stock_status' 
						, N'$stock_unit_price' 
						, N'$stock_due_amount' 
						, N'$stock_amount' 
						, N'$stock_order_in_date' 
						, N'$stock_order_msg' 
						, N'$stock_request_member_idx' 
						, N'$modip' 
						, N'$last_member_idx' 
						)
					";
					$stock_idx_tmp = parent::execSqlInsert($qry);

					//관계(부모) IDX 를 자신의 IDX 로 Update
					$qry = "Update DY_STOCK Set stock_ref_idx = stock_idx Where stock_idx = N'$stock_idx_tmp'";
					parent::execSqlUpdate($qry);

				}else{
					//stock_idx 가 있는 경우 수정
					//발주 상품 수정
					$qry = "
						Update DY_STOCK
						Set
							stock_unit_price = N'$stock_unit_price',
							stock_due_amount = N'$stock_due_amount',
							stock_amount = N'$stock_amount',
							stock_due_date = N'$stock_order_in_date',
							stock_order_msg = N'$stock_order_msg',
						    stock_moddate = getdate(),
						    stock_modip = N'$modip',
						    last_member_idx = N'$last_member_idx'
						Where stock_idx = N'$stock_idx'
					";

					parent::execSqlUpdate($qry);
				}
			}

			parent::sqlTransactionCommit();     //트랜잭션 커밋

			$returnValue = true;
		}else{
			parent::sqlTransactionRollback();     //트랜잭션 롤백
		}
		parent::db_close();

		//발주서 파일 생성
		if($returnValue) {
			$stock_order_file_idx = $this->createStockOrderDocument($stock_order_idx);
			$returnValue = $stock_order_file_idx;
		}

		return $returnValue;
	}

	/**
	 * 발주서 정보 반환
	 * @param $stock_order_idx : "발주 테이블" IDX
	 * @return array|false|null
	 */
	public function getStockOrderData($stock_order_idx){

		$qry = "
			Select 
				O.*
			     , S.supplier_name as supplier_info_name 
			     , S.supplier_ceo_name as supplier_info_ceo_name 
			     , S.supplier_addr1 + ' ' + S.supplier_addr2 as supplier_info_addr 
			     , S.supplier_license_number as supplier_info_license_no 
				 , isNull((Select Top 1 stock_order_file_idx From DY_STOCK_ORDER_FILE F Where O.stock_order_idx = F.stock_order_idx Order by stock_order_file_idx DESC), 0) as stock_order_file_idx
			From DY_STOCK_ORDER O
			Left Outer Join DY_MEMBER_SUPPLIER S On O.supplier_idx = S.member_idx
			Where O.stock_order_idx = N'$stock_order_idx' And O.stock_order_is_del = N'N'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;

	}

	/**
	 * 발주서 엑셀 생성
	 * @param $stock_order_idx : 발주서 IDX
	 * @return string : 발주서 파일 명 (저장 위치 : DY_STOCK_ORDER_PATH)
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public function createStockOrderDocument($stock_order_idx){

		$returnValue = "";

		//발주서 체크
		$qry = "
			Select O.*, S.supplier_name, S.supplier_ceo_name, S.supplier_addr1, S.supplier_addr2, S.supplier_license_number
			From 
			    DY_STOCK_ORDER O
				Left Outer Join DY_MEMBER_SUPPLIER S On O.supplier_idx = S.member_idx
			Where 
		        O.stock_order_is_del = N'N' 
			    And O.stock_order_idx = N'$stock_order_idx'
		";

		parent::db_connect();
		$stock_order_view = parent::execSqlOneRow($qry);
		parent::db_close();

		if($stock_order_view) {
			$xls_sample      = "발주서_파일생성_양식.xlsx";
			$xls_sample_path = DY_XLS_SAMPLE_PATH . "/" . $xls_sample;

			require '../vendor/autoload.php';
			$spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($xls_sample_path);
			$spreadsheet->setActiveSheetIndex(0);
			$activesheet = $spreadsheet->getActiveSheet();
			//기본 발주서 양식 Load

			/**
			 * B3 : {{발주일}}
			 *
			 * B6 : {{발주사_상호명}}
			 * B7 : {{발주사_대표자명}}
			 * B8 : {{발주사_주소}}
			 * B9 : {{발주사_사업자번호}}
			 * B10 : {{발주사_담당자}}
			 * B11 : {{발주사_연락처}}
			 *
			 * G6 : {{공급처_상호명}}
			 * G7 : {{공급처_대표자명}}
			 * G8 : {{공급처_주소}}
			 * G9 : {{공급처_사업자번호}}
			 * G10 : {{공급처_담당자}}
			 * G11 : {{공급처_연락처}}
			 *
			 * B14 : {{배송지_고객명}}
			 * G14 : {{배송지_연락처}}
			 * B15 : {{배송지_주소}}
			 *
			 * 발주내역 시작 Row 19
			 * TODO : 30개 까지 Border 및 셀합병 되어 있음 그 이상일 경우 검토필요
			 * [
			 *      A19 : {{상품명}}
			 *      B19 : {{옵션명}}
			 *      D19 : {{단가}}
			 *      F19 : {{수량}}
			 *      G19 : {{금액}}
			 *      H19 : {{비고}}
			 * ]
			 */

			//사이트 정보 얻기
			$C_SiteInfo = new SiteInfo();
			$_site_info = $C_SiteInfo->getSiteInfo();

			//발주일
			$activesheet->setCellValueExplicit("B3", $stock_order_view["stock_order_date"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			//발주사_상호명
			$activesheet->setCellValueExplicit("B6", $_site_info["site_name"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			//발주사_대표자명
			$activesheet->setCellValueExplicit("B7", $_site_info["ceo_name"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			//발주사_주소
			$activesheet->setCellValueExplicit("B8", $_site_info["addr1"] . " " .$_site_info["addr2"] , PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			$activesheet->getStyle("B8")->getAlignment()->setWrapText(true);
			//발주사_사업자번호
			$activesheet->setCellValueExplicit("B9", $_site_info["license_no"] , PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			//발주사_담당자
			$activesheet->setCellValueExplicit("B10", $stock_order_view["stock_order_officer_name"] , PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			//발주사_연락처
			$activesheet->setCellValueExplicit("B11", $stock_order_view["stock_order_officer_tel"] , PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

			//공급처_상호명
			$activesheet->setCellValueExplicit("G6", $stock_order_view["supplier_name"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			//공급처_대표자명
			$activesheet->setCellValueExplicit("G7", $stock_order_view["supplier_ceo_name"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			//공급처_주소
			$activesheet->setCellValueExplicit("G8", $stock_order_view["supplier_addr1"] . " " . $stock_order_view["supplier_addr2"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			$activesheet->getStyle("G8")->getAlignment()->setWrapText(true);
			//공급처_사업자번호
			$activesheet->setCellValueExplicit("G9", $stock_order_view["supplier_license_number"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			//공급처_담당자
			$activesheet->setCellValueExplicit("G10", $stock_order_view["stock_order_supplier_name"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			//공급처_연락처
			$activesheet->setCellValueExplicit("G11", $stock_order_view["stock_order_supplier_tel"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

			//배송지_고객명
			$activesheet->setCellValueExplicit("B14", $stock_order_view["stock_order_receiver_name"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			//배송지_연락처
			$activesheet->setCellValueExplicit("G14", $stock_order_view["stock_order_receiver_tel"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			//배송지_주소
			$activesheet->setCellValueExplicit("B15", $stock_order_view["stock_order_receiver_addr"], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

			//발주내역 Loading
			$qry = "
				Select
					S.*, P.product_name, PO.product_option_name
				From DY_STOCK S 
						Left Outer Join DY_PRODUCT P On S.product_idx = P.product_idx
						Left Outer JOin DY_PRODUCT_OPTION PO On S.product_option_idx = PO.product_option_idx
				Where 
				      S.stock_is_del = N'N'
				      And S.stock_order_is_ready = N'Y'
				      And S.stock_order_idx = N'$stock_order_idx'
				Order By S.stock_idx ASC
			";
			parent::db_connect();
			$_order_list = parent::execSqlList($qry);
			parent::db_close();


			//발주 내역 입력
			//시작번호 설정
			$list_start_row_num = 19;
			$list_current_row_num = $list_start_row_num;
			$stock_list_array = array(
				//상품명
				array(
					"COL_NAME" => "A",
					"FIELD_NAME" => "product_name",
					"DATA_TYPE" => "string",
				),
				//옵션명
				array(
					"COL_NAME" => "B",
					"FIELD_NAME" => "product_option_name",
					"DATA_TYPE" => "string",
				),
				//단가
				array(
					"COL_NAME" => "D",
					"FIELD_NAME" => "stock_unit_price",
					"DATA_TYPE" => "currency",
				),
				//수량
				array(
					"COL_NAME" => "F",
					"FIELD_NAME" => "stock_due_amount",
					"DATA_TYPE" => "numeric",
				),
				//금액
				array(
					"COL_NAME" => "G",
					"FIELD_NAME" => "stock_cal_price",
					"DATA_TYPE" => "currency",
				),
				//비고
				array(
					"COL_NAME" => "H",
					"FIELD_NAME" => "stock_order_msg",
					"DATA_TYPE" => "string",
				),

			);
			if($_order_list)
			{
				foreach($_order_list as $stock)
				{
					//새로운 Row 삽입
					$activesheet->insertNewRowBefore($list_current_row_num,1);

					$activesheet->mergeCells('B'.$list_current_row_num.':C'.$list_current_row_num);
					$activesheet->mergeCells('D'.$list_current_row_num.':E'.$list_current_row_num);
					$activesheet->mergeCells('H'.$list_current_row_num.':I'.$list_current_row_num);

					//다음 빈 Row 의 스타일 복사
					$activesheet->duplicateConditionalStyle($activesheet->getConditionalStyles("A" . ($list_current_row_num + 1) . ":J" . ($list_current_row_num + 1)), "A" . ($list_current_row_num) . ":J" . ($list_current_row_num));


					foreach($stock_list_array as $row){
						$cod = $row["COL_NAME"] . $list_current_row_num;

						if($row["FIELD_NAME"] == "stock_cal_price") {
							$tmp_price = $stock["stock_unit_price"] * $stock["stock_due_amount"];
							$activesheet->setCellValue($cod, $tmp_price);

							$activesheet->setCellValueExplicit($cod, $tmp_price, PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
							$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('"\" #,###');

						}else{

							if($row["DATA_TYPE"] == "string"){
								$activesheet->setCellValueExplicit($cod, $stock[$row["FIELD_NAME"]], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
							}elseif($row["DATA_TYPE"] == "numeric"){
								$activesheet->setCellValueExplicit($cod, $stock[$row["FIELD_NAME"]], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
								$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode("#,##0");
							}elseif($row["DATA_TYPE"] == "currency"){
								$activesheet->setCellValueExplicit($cod, $stock[$row["FIELD_NAME"]], PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
								$activesheet->getStyle($cod)->getNumberFormat()->setFormatCode('"\" #,##0');
							}

						}
						$activesheet->getStyle($cod)->getAlignment()->setWrapText(true)->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_GENERAL);
					}
					$list_current_row_num++;
				}
			}

			//엑셀 파일명
			list($usec, $sec) = explode(" ",microtime());
			$create_filename = (round(((float)$usec + (float)$sec))).rand(1,10000);		// 날짜에 따라 변환
			$create_filename .= ".xlsx";

			//엑셀 생성
			//저장 위치 DY_STOCK_ORDER_PATH
			$Excel_writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/
			$Excel_writer->save(DY_STOCK_ORDER_PATH."/".$create_filename);
			//$Excel_writer->save('php://output');

			//파일 생성 확인
			if(file_exists(DY_STOCK_ORDER_PATH."/".$create_filename)){

				//엑셀 파일 생성 로그 입력
				$stock_order_file_idx = $this->insertStockOrderFileLog($stock_order_idx, $create_filename);

				//발주서에 파일명 Update
				$qry = "
					Update DY_STOCK_ORDER
					Set 
					    stock_order_file_idx = N'$stock_order_file_idx',
					    stock_order_filename = N'$create_filename'
					Where stock_order_idx = N'$stock_order_idx'
				";
				parent::db_connect();
				$tmp = parent::execSqlUpdate($qry);
				parent::db_close();

				$returnValue = $stock_order_file_idx;
			}

		}
		return $returnValue;
	}

	/**
	 * 발주서 파일 생성 로그 함수
	 * @param $stock_order_idx : 발주서 IDX
	 * @param $filename : 생성된 파일명
	 * @return int
	 */
	public function insertStockOrderFileLog($stock_order_idx, $filename)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Insert Into DY_STOCK_ORDER_FILE
			(stock_order_idx, stock_order_file_name, stock_order_file_regip, last_member_idx)
			VALUES 
			(
			N'$stock_order_idx'
			, N'$filename'
			, N'$modip'
			, N'$last_member_idx'
			)
		";

		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 발주서 파일 생성 로그 정보 반환
	 * @param $stock_order_file_idx
	 * @return array|false|null
	 */
	public function getStockOrderFileLog($stock_order_file_idx){
		$qry = "
			Select * From DY_STOCK_ORDER_FILE
			Where stock_order_file_idx = N'$stock_order_file_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 이메일 발송 로그 Insert
	 * 삭제 상태로 입력 됨
	 * 메일 발송 후 stock_order_email_is_del = 'N' 업데이트 필요
	 * @param $stock_order_idx                  : 발주서 IDX
	 * @param $stock_order_file_idx             : 발주서 파일 IDX
	 * @param $supplier_idx                     : 공급처 IDX
	 * @param $stock_order_email_receiver       : 수신 Email
	 * @param $stock_order_email_title          : 메일 제목
	 * @param $stock_order_email_msg            : 메일 내용
	 * @param $stock_order_email_receiver_cc    : 함께 받은 발송자 Email
	 * @return int
	 */
	public function insertStockOrderEmailSendLog($stock_order_idx, $stock_order_file_idx, $supplier_idx, $stock_order_email_receiver, $stock_order_email_title, $stock_order_email_msg, $stock_order_email_receiver_cc)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Insert Into DY_STOCK_ORDER_EMAIL
			(
			 stock_order_idx, stock_order_file_idx, supplier_idx
			 , stock_order_email_receiver, stock_order_email_title, stock_order_email_msg
			 , stock_order_email_receiver_cc
			 , stock_order_email_regip, last_member_idx
			 , stock_order_email_is_del
			)
			VALUES 
			(
			 N'$stock_order_idx',
			 N'$stock_order_file_idx',
			 N'$supplier_idx',
			 N'$stock_order_email_receiver',
			 N'$stock_order_email_title',
			 N'$stock_order_email_msg',
			 N'$stock_order_email_receiver_cc',
			 N'$modip',
			 N'$last_member_idx',
			 N'Y'
			)
		";

		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();


		return $rst;
	}

	/**
	 * 이메일 발송 로그 삭제 상태 변경 => 'N'
	 * @param $stock_order_email_idx
	 * @return bool|resource
	 */
	public function updateStockOrderEmailSendLogIsDel($stock_order_email_idx)
	{
		$qry = "
			Update DY_STOCK_ORDER_EMAIL
			Set stock_order_email_is_del = N'N'
			Where stock_order_email_idx = N'$stock_order_email_idx'
		";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 발주서 다운로드 로그 Insert
	 * 이메일 발송을 통한 발주서 다운로드 시 로그 Insert
	 * @param $stock_order_idx
	 * @param $stock_order_email_idx
	 * @return string
	 */
	public function insertStockOrderDocumentDownLog($stock_order_idx, $stock_order_file_idx, $stock_order_email_idx){
		$modip   = $_SERVER["REMOTE_ADDR"];
		$referer = $_SERVER["HTTP_REFERER"];
		$agent   = $_SERVER["HTTP_USER_AGENT"];

		$returnValue = "";

		$qry = "
			Insert Into DY_STOCK_ORDER_FILE_DOWN_LOG
			(
			 stock_order_idx
			 , stock_order_file_idx, stock_order_email_idx
			 , HTTP_REFERER, USER_AGENT, stock_order_file_down_regip
			) 
			VALUES 
			(
			 N'$stock_order_idx',
			 N'$stock_order_file_idx',
			 N'$stock_order_email_idx',
			 N'$referer',
			 N'$agent',
			 N'$modip'
			)
		";

		parent::db_connect();
		$rst = parent::execSqlInsert($qry);
		parent::db_close();


		return $returnValue;
	}

	/**
	 * 발주서 발주하기 실행
	 * @param $stock_order_idx : "발주서" IDX
	 * @return bool|resource
	 */
	public function placeStockOrder($stock_order_idx){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//발주서 Update
		$qry = "
			Update DY_STOCK_ORDER
				Set 
				    stock_order_is_order = N'Y'
					, stock_order_is_order_member_idx = N'$last_member_idx'
					, stock_order_is_order_date = getdate()
					, stock_order_moddate = getdate()
					, stock_order_modip = N'$modip'
				Where stock_order_idx = N'$stock_order_idx'
		";
		$rst = parent::execSqlUpdate($qry);

		//발주 재고 생성일 Update
		$qry = "
			Update DY_STOCK
				Set 
				    stock_request_date = getdate()
				Where stock_order_idx = N'$stock_order_idx'
		";
		$rst = parent::execSqlUpdate($qry);

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		parent::db_close();

		return $rst;
	}

	/**
	 * 발주서 발주취소!!
	 * @param $stock_order_idx : "발주서" IDX
	 * @return bool|resource
	 */
	public function cancelStockOrder($stock_order_idx){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_STOCK_ORDER
				Set 
				    stock_order_is_order = N'C'
				    , stock_order_is_order_member_idx = N'$last_member_idx'
				    , stock_order_is_order_date = getdate()
					, stock_order_modip = N'$modip'
				Where stock_order_idx = N'$stock_order_idx'
					And stock_order_is_order = N'Y'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 재고 정보 반환
	 * @param $stock_idx    : "재고" IDX
	 * @return array|false|null
	 */
	public function getStockData($stock_idx){

		$qry = "
			Select * From DY_STOCK Where stock_idx = N'$stock_idx'
			And stock_is_del = N'N'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

    /**
     * 재고 정보 리스트 반환
     * @param $stock_idx    : "재고" IDX 배열
     * @return array|false|null
     */
    public function getStockManyData($stock_idx){

        $qry = "
			Select * From DY_STOCK Where stock_idx IN ($stock_idx)
			And stock_is_del = N'N'
		";

        parent::db_connect();
        $rst = parent::execSqlList($qry);
        parent::db_close();

        return $rst;
    }

	/**
	 * 재고 상세 정보 반환
	 * @param $stock_idx    : "재고" IDX
	 * @return array|false|null
	 */
	public function getStockDataDetail($stock_idx){

		$qry = "
			Select A.*
					, P.product_name, PO.product_option_name
					, S.supplier_name
			        , SO.stock_order_date
					, O.order_name, O.order_tp_num, O.order_hp_num, O.order_addr1, O.order_addr2
					, O.receive_name, O.receive_tp_num, O.receive_hp_num, O.receive_addr1, O.receive_addr2
			From DY_STOCK A
			    Left Outer Join DY_STOCK_ORDER SO On A.stock_order_idx = SO.stock_order_idx
				Left Outer Join DY_PRODUCT P On A.product_idx = P.product_idx
				Left Outer Join DY_PRODUCT_OPTION PO On A.product_option_idx = PO.product_option_idx
				Left Outer Join DY_MEMBER_SUPPLIER S On P.supplier_idx = S.member_idx
				Left Outer Join DY_ORDER O On O.order_idx = A.order_idx
				
			Where stock_idx = N'$stock_idx'
			And stock_is_del = N'N'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	public function getStockNonProcCountByStockIdx($stock_ref_idx){

		$qry = "
			Select
				count(*)
		    From DY_STOCK S
		    Where
		      S.stock_is_del = N'N'
			  And S.stock_is_confirm = N'N'
			  And S.stock_is_proc = N'N'
			  And S.stock_ref_idx = N'$stock_ref_idx'

		";

		parent::db_connect();
		$cnt = parent::execSqlOneCol($qry);
		parent::db_close();

		return $cnt;

	}

	/**
	 * 사용안함 - 전체입고 처리(발주 재고)
	 * @param $stock_idx : "재고" IDX
	 * @return bool|resource
	 */
	public function setStockProcAll($stock_idx){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];
		$nowdate = date('Y-m-d');

		//이미 입고 처리 되었는지 확인
		$_view = $this->getStockData($stock_idx);

		if($_view) {
			//미 입고 처리 상태 일 경우에만 실행
			if($_view["stock_is_proc"] == "N") {

				//전체 입고 처리
				$qry = "
					Update DY_STOCK
						Set
						    stock_amount = stock_due_amount
						    , stock_in_date = N'$nowdate'
						    , stock_status = N'NORMAL'
							, stock_is_proc = N'Y'
							, stock_is_proc_date = getdate()
							, stock_is_proc_member_idx = N'$last_member_idx'
							, stock_moddate = getdate()
							, stock_modip = N'$modip'
							, last_member_idx = N'$last_member_idx'
						Where
							stock_idx = N'$stock_idx'
				";
				parent::db_connect();
				$rst = parent::execSqlUpdate($qry);
				parent::db_close();

				//발주 한 재고일 경우
				//발주건 발주취소 불가하도록 변경
				if($_view["stock_kind"] == "STOCK_ORDER") {
					$qry = "
						Update DY_STOCK_ORDER
							Set
								stock_order_is_order = N'T'
							Where stock_order_idx = N'" . $_view["stock_order_idx"] . "'
					";

					parent::db_connect();
					$rst = parent::execSqlUpdate($qry);
					parent::db_close();
				}


			}
		}

		return $rst;
	}

	/**
	 * 입고처리!!! (구 - 부분입고 처리)
	 * @param $stock_idx        : "재고" IDX
	 * @param $stock_list       : array 각 타입 별 재고 리스트 배열
	 * @param $stock_file_idx   : 첨부 파일 IDX
	 * @return bool
	 */
	public function setStockProcPartial($stock_idx, $stock_list, $stock_file_idx)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		//이미 입고 처리 되었는지 확인
		$_view = $this->getStockData($stock_idx);

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		if($_view) {
			//미 입고 처리 상태 일 경우에만 실행
			if ($_view["stock_is_proc"] == "N") {
				if(count($stock_list) > 0){
					foreach($stock_list as $st){

						//정상재고는 현 재고를 Update
						//나머지는 재고 추가
						$_in_code = $st["code"];
						$_in_stock_amount = $st["amount"];
						$_in_stock_date = $st["date"];
						$_in_stock_msg = $st["msg"];

						if($_in_code == "NORMAL"){

							//입고 처리 :: Update
							$stock_type = 1; //입고
							$qry = "
								Update DY_STOCK
									Set
									    stock_amount = N'$_in_stock_amount'
									    , stock_in_date = N'$_in_stock_date'
									    , stock_type = N'$stock_type'
									    , stock_msg = N'$_in_stock_msg'
									    , stock_status = N'NORMAL'
										, stock_is_proc = N'Y'
										, stock_is_proc_date = getdate()
										, stock_is_proc_member_idx = N'$last_member_idx'
									    , stock_file_idx = N'$stock_file_idx'
										, stock_moddate = getdate()
										, stock_modip = N'$modip'
										, last_member_idx = N'$last_member_idx'
									Where
										stock_idx = N'$stock_idx'
							";
							$rst = parent::execSqlUpdate($qry);

						}else{

							$stock_order_is_ready = "N";
							$stock_order_msg = "";
							//입고 처리 :: Insert
							/**
							 * 입력하여 넘어온 날짜 값을
							 *
							 * 불량, 양품 은 입고일로
							 * 나머지는 입고예정일로
							 *
							 * 불량, 양품 은 현재 입고 처리 되는 재고의 입고 예정일을 사용함
							 *
							 * 불량, 양품 은 stock_type 을 +1 로 [재고]
							 * 나머지는 0 으로 [대기상태]
							 *
							 * 불량, 양품 은 예정수량 및 입고수량 모두 입력
							 * 나머지는 예정수량 만 입력, 입고수량은 0
							 *
							 * 기타처리는 처리완료 상태로 입력
							 * 기타처리는 넘어온 날짜를 처리완료일로 입력
							 */

							$stock_in_date    = ""; //입고일
							$stock_due_date   = ""; //입고예정일
							$stock_type       = 0;  //입고or출고or대기 상태
							$stock_due_amount = 0;  //예정수량
							$stock_amount     = 0;  //입고수량

							/**
							 * 불량, 양품 은 확정목록으로 stock_is_proc = "Y"
							 * 나머지는 예정 목록에 남겨둠 stock_is_proc = "N"
							 */
							$stock_is_proc = "N";   //

							if($_in_code == "BAD" || $_in_code == "ABNORMAL"){
								$stock_in_date = $_in_stock_date;
								$stock_due_date = $_view["stock_due_date"];
								$stock_type = 1;
								$stock_is_proc = "Y";
								$stock_due_amount = $_in_stock_amount;
								$stock_amount = $_in_stock_amount;
							}elseif($_in_code == "ETC"){
								$stock_in_date = $_in_stock_date;
								$stock_due_date = $_view["stock_due_date"];
								$stock_type = 0;
								$stock_is_proc = "Y";
								$stock_due_amount = $_in_stock_amount;
								$stock_amount = $_in_stock_amount;
							}else{
								$stock_in_date = "";
								$stock_due_date = $_in_stock_date;
								$stock_type = 0;
								$stock_is_proc = "N";
								$stock_due_amount = $_in_stock_amount;
								$stock_amount = 0;
							}

							$qry = "

								Insert Into DY_STOCK
								(
								 stock_ref_idx, product_idx, product_option_idx
								 , stock_kind, order_idx, stock_order_idx, stock_order_is_ready, stock_order_msg
								 , stock_in_date, stock_due_date
								 , stock_type, stock_status
								 , stock_unit_price, stock_due_amount, stock_amount, stock_msg
								 , stock_file_idx, stock_request_date, stock_request_member_idx
								 , stock_is_proc, stock_is_proc_date, stock_is_proc_member_idx
								 , stock_regip, last_member_idx
								 )
								 SELECT 
								 
									stock_ref_idx, product_idx, product_option_idx
									, stock_kind, order_idx, stock_order_idx, N'$stock_order_is_ready', N'$stock_order_msg'
									, N'$stock_in_date', N'$stock_due_date'
									, N'$stock_type', N'$_in_code'
									, stock_unit_price, N'$stock_due_amount', N'$stock_amount', N'$_in_stock_msg'
									, N'$stock_file_idx', getdate(), N'$last_member_idx'
									, N'$stock_is_proc', getdate(), N'$last_member_idx'
									, N'$modip', N'$last_member_idx'
								 
							        From DY_STOCK
								  Where stock_idx = N'$stock_idx'
							";
							$rst = parent::execSqlUpdate($qry);

						}

					}

					//발주 한 재고일 경우
					//발주건 발주취소 불가하도록 변경
					if($_view["stock_kind"] == "STOCK_ORDER") {
						$qry = "
							Update DY_STOCK_ORDER
								Set
									stock_order_is_order = N'T'
								Where stock_order_idx = N'" . $_view["stock_order_idx"] . "'
						";
						$rst = parent::execSqlUpdate($qry);
					}

					$returnValue = true;
				}
			}
		}

		if($returnValue) {
			parent::sqlTransactionCommit();     //트랜잭션 커밋
		}

		return $returnValue;
	}

	/**
	 * 지연 입고 등록
	 * @param $args
	 * @return bool|int
	 */
	public function insertStockDueDelay($args)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		$stock_idx                = "";
		$stock_due_delay_date     = "";
		$stock_due_delay_msg      = "";
		$stock_due_delay_file_idx = "";

		extract($args);

		//입고 상태 확인
		$_view = $this->getStockData($stock_idx);

		if($_view){
			//미 입고 처리 상태일 때만 지연 등록 가능
			if($_view["stock_is_proc"] == "N"){

				parent::db_connect();
				parent::sqlTransactionBegin();  //트랜잭션 시작

				//재고에 입고 지연 여부 Update
				//입고 예정일 Update
				$qry = "
					Update DY_STOCK
						Set stock_is_delay = N'Y', stock_due_date = N'$stock_due_delay_date'
						Where stock_idx = N'$stock_idx'
				";
				$tmp = parent::execSqlUpdate($qry);

				//지연 입고 내역 Insert
				$qry = "
					Insert Into DY_STOCK_DUE_DELAY
					(stock_idx, stock_due_delay_date, stock_due_delay_msg, stock_due_delay_file_idx
					  , stock_due_delay_regip, last_member_idx)
				    VALUES
					(
					 N'$stock_idx'
					 , N'$stock_due_delay_date'
					 , N'$stock_due_delay_msg'
					 , N'$stock_due_delay_file_idx'
					 , N'$modip'
					 , N'$last_member_idx'
					)
				";
				$returnValue = parent::execSqlInsert($qry);

				if($returnValue){
					parent::sqlTransactionCommit();     //트랜잭션 커밋
				}

				parent::db_close();

			}
		}

		return $returnValue;
	}

	/**
	 * 입고지연 내역 확인 처리 함수
	 * @param $stock_due_delay_idx : "입고지연내역" IDX
	 * @return int
	 */
	public function insertStockDueDelayConfirm($stock_due_delay_idx){

		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$inserted_idx = 0;

		//이미 존재 하는 지 확인
		$qry = "
			Select count(*) From DY_STOCK_DUE_DELAY_CONFIRM
			Where stock_due_delay_confirm_is_del = N'N'
				And stock_due_delay_idx = N'$stock_due_delay_idx'
				And member_idx = N'$last_member_idx'
		";
		parent::db_connect();
		$dupCnt = parent::execSqlOneCol($qry);
		parent::db_close();

		if($dupCnt == 0) {

			$qry = "
				Insert Into DY_STOCK_DUE_DELAY_CONFIRM
				(stock_due_delay_idx, member_idx, stock_due_delay_confirm_regip)
				VALUES
				(
				 N'$stock_due_delay_idx'
				 , N'$last_member_idx'
				 , N'$modip'
				)
			";

			parent::db_connect();
			$inserted_idx = parent::execSqlInsert($qry);
			parent::db_close();
		}


		return $inserted_idx;
	}

    /**
     * 일자별 해당 상품옵션, 원가 별 재고내역 목록 반환
     * @param $product_option_idx : 상품옵션 IDX
     * @param $order_by : 정렬
     * @param $confirm_date : 확정일자
     * @param $stock_is_proc : 처리 여부
     * @param $qry_where : 조건(입,출고)
     * @return array|null
     */
    public function getStockDailyRelationList($product_option_idx, $confirm_date, $stock_unit_price, $qry_where){

        $qry = "
			Select A.*, O.*,C.code_name as stock_status_name
				From DY_STOCK A
				Left Outer Join DY_CODE C On C.parent_code = N'STOCK_STATUS' And C.code = A.stock_status
				Left Outer Join DY_ORDER O On O.order_idx  = A.order_idx
			WHERE 
			    A.stock_is_del = N'N'
				And A.product_option_idx = N'$product_option_idx'
				And A.stock_unit_price = N'$stock_unit_price'
				And CONVERT(DATE,A.stock_is_confirm_date) = N'$confirm_date'
				$qry_where
			Order by stock_request_date ASC;
		";

        parent::db_connect();
        $_list = parent::execSqlList($qry);
        parent::db_close();

        return $_list;
    }

	/**
	 * 재고와 관련된 모든 재고내역 목록 반환
	 * 발주 일 경우 stock_order_idx 를 기준
	 * 주문과 관련된 경우 order_idx 를 기준
	 * @param $stock_idx : 재고 IDX
	 * @param $order_by : 정렬
	 * @param $stock_is_proc : 처리 여부
	 * @return array|null
	 */
	public function getStockOrderRelationList($stock_ref_idx, $order_by, $stock_is_proc){

		if(!$order_by)
			$order_by = "stock_request_date ASC";

		$qry = "
			Select A.*, C.code_name as stock_status_name
				, (Select save_filename From DY_FILES F Where A.stock_file_idx = F.file_idx And is_use = N'Y') as stock_file_name
				From DY_STOCK A
				Left Outer Join DY_CODE C On C.parent_code = N'STOCK_STATUS' And C.code = A.stock_status
			WHERE 
			    A.stock_is_del = N'N'
				  And A.stock_is_proc = N'$stock_is_proc'
				And A.stock_ref_idx = N'$stock_ref_idx'
			Order by $order_by
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	/**
	 * 재고 확정 처리 함수!!!!!!!!!!!!!
	 * @param $stock_idx : "재고" IDX
	 * @return bool|resource
	 */
	public function updateStockConfirm($stock_idx){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_STOCK
			Set stock_is_confirm = N'Y'
				, stock_is_confirm_date = getdate()
				, stock_is_confirm_member_idx = N'$last_member_idx'
				, stock_moddate = getdate()
				, stock_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			WHERE stock_idx = N'$stock_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

    /**
     * 재고 다중 확정 처리 함수!!!!!!!!!!!!!
     * @param $stock_idx : "재고" IDX 배열
     * @return bool|resource
     */
    public function updateStockMultiConfirm($stock_idx){
        global $GL_Member;
        $modip = $_SERVER["REMOTE_ADDR"];
        $last_member_idx = $GL_Member["member_idx"];

        $qry = "
			Update DY_STOCK
			Set stock_is_confirm = N'Y'
				, stock_is_confirm_date = getdate()
				, stock_is_confirm_member_idx = N'$last_member_idx'
				, stock_moddate = getdate()
				, stock_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			WHERE stock_idx IN ($stock_idx)
		";

        parent::db_connect();
        $rst = parent::execSqlUpdate($qry);
        parent::db_close();

        return $rst;
    }

	/**
	 * 추가입고 함수
	 * @param $stock_idx            : 추가 입고 대상 IDX
	 * @param $stock_due_date       : 입고예정일
	 * @param $stock_due_amount     : 예정수량
	 * @param $stock_msg            : 메모
	 * @return int
	 */
	public function insertStockOrderAdd($stock_idx, $stock_due_date, $stock_due_amount, $stock_msg){
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "

			Insert Into DY_STOCK
			(
			 stock_ref_idx, product_idx, product_option_idx
			 , stock_kind, order_idx, stock_order_idx, stock_order_is_ready, stock_order_msg
			 , stock_in_date, stock_due_date
			 , stock_type, stock_status
			 , stock_unit_price, stock_due_amount, stock_amount, stock_msg
			 , stock_file_idx, stock_request_date, stock_request_member_idx
			 , stock_regip, last_member_idx
			 )
			 SELECT 
			 
				stock_idx, product_idx, product_option_idx
				, stock_kind, order_idx, stock_order_idx, N'N', N''
				, N'', N'$stock_due_date'
				, N'0', N'STOCK_ORDER_ADD'
				, stock_unit_price, N'$stock_due_amount', N'0', N'$stock_msg'
				, N'0', getdate(), N'$last_member_idx'
				, N'$modip', N'$last_member_idx'
			 
		        From DY_STOCK
			  Where stock_idx = N'$stock_idx'
		";

		parent::db_connect();
		$inserted_idx = parent::execSqlInsert($qry);
		parent::db_close();

		return $inserted_idx;
	}

	/**
	 * 재고 원가(매입가) 목록
	 * @param $product_option_idx : 상품 옵션 IDX
	 * @return array
	 */
	public function getStockUnitPriceListByProductOption($product_option_idx){

		$qry = "
			Select Distinct stock_unit_price
			From DY_STOCK
			Where stock_is_del = N'N'
					And product_option_idx = N'$product_option_idx'
					And stock_kind = N'STOCK_ORDER'
			Order by stock_unit_price ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();


		return $_list;
	}

	/**
	 * 재고 타입과 단가에 따른 현 재고 수량 반환
	 * @param $product_option_idx   : 상품옵션 IDX
	 * @param $stock_status         : 재고타입
	 * @param $stock_unit_price     : 단가
	 * @return int|mixed
	 */
	public function getCurrentStockAmountByPrice($product_option_idx, $stock_status, $stock_unit_price){

		$qryAdd = "";
		if($stock_unit_price > 0) {
			$qryAdd = " And stock_unit_price = N'$stock_unit_price' ";
		}

		$qry = "
			Select 
				isNull(Sum(stock_amount * stock_type), 0) as current_stock_amount
			From DY_STOCK
			Where
			    stock_is_del = N'N'
				And product_option_idx = N'$product_option_idx'
				And stock_status = N'$stock_status'
				And stock_is_confirm = N'Y'
				$qryAdd
		";

		parent::db_connect();
		$current_stock_amount = parent::execSqlOneCol($qry);
		parent::db_close();

		return $current_stock_amount;
	}

	//todo : getCurrentAmountByPrice 와 합쳐야함, start_date 를 어떻게 할 것인지
    public function getStockAmountByPrice($productOptionIdx, $stockStatus, $stockUnitPrice, $operationDatetime) {

        $qryAdd = "";
        if($stockUnitPrice > 0) {
            $qryAdd = " And stock_unit_price = N'$stockUnitPrice' ";
        }

        $qryAdd .= " AND stock_is_confirm_date <= N'$operationDatetime'";

        $qry = "
			Select 
				isNull(Sum(stock_amount * stock_type), 0) as current_stock_amount
			From DY_STOCK
			Where
			    stock_is_del = N'N'
				And product_option_idx = N'$productOptionIdx'
				And stock_status = N'$stockStatus'
				And stock_is_confirm = N'Y'
				$qryAdd
		";

        parent::db_connect();
        $current_stock_amount = parent::execSqlOneCol($qry);
        parent::db_close();

        return $current_stock_amount;
    }

	public function getStockChartData($product_option_idx, $stock_unit_price, $date_start, $date_end)
	{

		$_search_date_ary = array();
		$_qry_date_ary = array();

		$start_date = strtotime($date_start);
		$end_date = strtotime($date_end);

		$priceQry = "";
		if($stock_unit_price)
		{
			$priceQry = "And  stock_unit_price = N'$stock_unit_price'";
		}


		$i = 0;
		do{
			$new_date = strtotime('+'.$i++.' days', $start_date);
			$_search_date_ary[] =  "" . date('Y-m-d', $new_date) . "";
			$_qry_date_ary[] = array(
				"date" => date('Y-m-d', $new_date),
				"colName" => "s".date('Ymd', $new_date)
			);
		}while ($new_date < $end_date);

		$lastCol = "";
		foreach($_qry_date_ary as $dd) {
			$lastCol .= ", STOCK_DAILY.".$dd["colName"]."_s";
			$lastCol .= ", STOCK_DAILY.".$dd["colName"]."_p";
			$lastCol .= ", STOCK_DAILY.".$dd["colName"]."_c";
		}

		//입고 확정 된 기간
		$_searchWhereStockProcDate = "	 
			And (
				stock_is_confirm_date >= '".$date_start." 00:00:00' 
				And stock_is_confirm_date <= '".$date_end." 23:59:59'
			) 
		";

		$qry = "

			Select
		        DateTable.value as dt
				, (
				  Select 
				    Sum(stock_amount * stock_type)
				  From DY_STOCK IN_S
				  Where stock_is_del = N'N' And stock_is_confirm = N'Y' And IN_S.stock_is_confirm_date <= DateTable.value + ' 23:59:59.997'
				  And stock_status IN (N'NORMAL', N'ABNORMAL', N'BAD', N'HOLD', N'DISPOSAL')
				  And product_option_idx = N'$product_option_idx' $priceQry
				) as sum_amount
			, STOCK3.*
			From 
			(
				SELECT value FROM STRING_SPLIT('".implode(",", $_search_date_ary)."', ',')
			) as DateTable

			Left Outer Join (
				Select 
					Convert(varchar(10), stock_is_confirm_date, 120) as confirm_date
					, Sum(Case When stock_kind = 'STOCK_ORDER' Then stock_amount * stock_type Else 0 End) as stock_amount_IN
					, Sum(
						Case When 
							(stock_status = 'SHIPPED') 
							OR (stock_status = 'FAC_RETURN_EXCHNAGE') 
							OR (stock_status = 'FAC_RETURN_BACK') 
							OR (stock_status = 'BAD_OUT_EXCHANGE') 
							OR (stock_status = 'BAD_OUT_RETURN') 
							OR (stock_status = 'DISPOSAL_PERMANENT') 
							OR (stock_status = 'LOSS') 
							Then stock_amount * stock_type 
							Else 0 
						End
					) as stock_amount_OUT
					, Sum(
						Case When stock_status = 'INVOICE'
							Then stock_amount * stock_type 
							Else 0 
						End
					) as stock_amount_INVOICE
					, Sum(
						Case When stock_status = 'SHIPPED'
							Then stock_amount * stock_type 
							Else 0 
						End
					) as stock_amount_SHIPPED
				From DY_STOCK ST3
					Where stock_is_del = N'N' And stock_is_confirm = N'Y'
					And product_option_idx = N'$product_option_idx' $priceQry
					$_searchWhereStockProcDate
					Group by Convert(varchar(10), stock_is_confirm_date, 120)
			) as STOCK3 On STOCK3.confirm_date = DateTable.value
		";

		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 상품 옵션 정보 Detail 반환 + 현재고수량
	 * @param $product_options_idx
	 * @return array|false|null
	 */
	public function getProductOptionDetailWithStockAmount($product_options_idx){

		$qry = "
			Select O.*, P.product_name, S.supplier_name
			, (
			  SELECT 
		          Sum(Case When stock_status = 'NORMAL' Then stock_amount * stock_type Else 0 End)
			  From DY_STOCK ST
			  Where ST.product_option_idx = O.product_option_idx
			        And ST.stock_is_del = N'N'
			        And ST.stock_is_confirm = N'Y'
			  ) as stock_amount_NORMAL
			       
			From DY_PRODUCT_OPTION O 
				Left Outer Join DY_PRODUCT P On O.product_idx = P.product_idx
				Left Outer Join DY_MEMBER_SUPPLIER S On P.supplier_idx = S.member_idx
			Where P.product_is_del = N'N' And P.product_is_use = N'Y' And O.product_option_is_del = N'N' And O.product_option_is_use = N'Y'
				And O.product_option_idx = N'$product_options_idx'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;

	}

	/**
	 * 재고 작업 함수 !!
	 * 입력된 수량 만큼
	 * 대상 재고 상태 마이너스(-) Insert
	 * 변경 될 재고 상태 플러스(+) Insert
	 * @param $product_option_idx       : 상품 옵션 IDX
	 * @param $stock_unit_price         : 재고 원가
	 * @param $stock_status_prev        : 변경 전 재고 상태
	 * @param $stock_status_next        : 변경 될 재고 상태
	 * @param $stock_amount             : 변경 될 재고 수량
	 * @param $stock_msg                : 작업 메모
	 * @return array                    :
	 */
	public function controlStockAmount($product_option_idx, $stock_unit_price, $stock_status_prev, $stock_status_next, $stock_amount, $stock_msg)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		//변경 전 상태 리스트 (가능한)
		global $GL_controlStockFromAbleStatusList;

		//변경 후 상태 리스트 (가능한)
		global $GL_controlStockToAbleStatusList;

		//
		$returnValue = array();
		$returnValue["result"] = false;
		$returnValue["msg"] = "";

		//로그 Insert IDX 보관을 위한 변수 및 배열
		$_minus_stock_idx = array();
		$_plus_stock_idx = 0;

		//현재날짜
		$nowDate = date('Y-m-d');

		//같은 상태로 변경 불가
		if($stock_status_prev == $stock_status_next){
			$returnValue["result"] = false;
			$returnValue["msg"] = "같은 상태로 변경 불가";
			return $returnValue;
		}

		//상태 가능 체크
		if(!array_key_exists($stock_status_prev, $GL_controlStockFromAbleStatusList)){
			$returnValue["result"] = false;
			$returnValue["msg"] = "허용되지 않는 변경 전 상태값";
			return $returnValue;
		}
		if(!array_key_exists($stock_status_next, $GL_controlStockToAbleStatusList)){
			$returnValue["result"] = false;
			$returnValue["msg"] = "허용되지 않는 변경 후 상태값";
			return $returnValue;
		}

		//변경 수량 검증
		if(!is_numeric($stock_amount)){
			$returnValue["result"] = false;
			$returnValue["msg"] = "변경하려는 수량이 정확하지 않습니다.";
			return $returnValue;
		}

		//상품 확인
		$C_Product = new Product();
		$_prodInfo = $C_Product -> getProductOptionData($product_option_idx);
		if(!$_prodInfo){
			$returnValue["result"] = false;
			$returnValue["msg"] = "존재하지 않는 상품";
			return $returnValue;
		}else{
			$product_idx = $_prodInfo["product_idx"];

			//현재 수량 확인
			$current_stock_amount = $this->getCurrentStockAmountByPrice($product_option_idx, $stock_status_prev, $stock_unit_price);

			if($current_stock_amount < $stock_amount){
				$returnValue["result"] = false;
				$returnValue["msg"] = "작업 가능한 재고 수량이 모자릅니다. (수량 부족)";
				return $returnValue;
			}

			parent::db_connect();
			parent::sqlTransactionBegin();  //트랜잭션 시작

			//차감되어 재고가 0 된 재고를 제외한 재고 리스트 (입고 순)
			$qry = "
				Select
				       *
						, (stock_amount + stock_minus_amount) as current_amount
				From
				(
				Select
					S.stock_idx
				    , S.stock_amount
					, (
					  Select
					         isNull(Sum(stock_amount * stock_type), 0) 
					  From DY_STOCK IN_S
					  Where IN_S.stock_is_del = N'N'
						  And IN_S.stock_ref_idx = S.stock_idx
						  And IN_S.stock_status = S.stock_status
						  And IN_S.stock_type = -1
					      And IN_S.stock_is_confirm = N'Y'
					) as stock_minus_amount
					, S.stock_in_date
					, ROW_NUMBER() Over(Order by stock_in_date ASC, stock_idx ASC) as row_num
				From DY_STOCK S
				Where 
			        S.stock_is_del = N'N'
					And S.product_option_idx = N'$product_option_idx'
					And S.stock_status = N'$stock_status_prev'
					And S.stock_type = 1
				    And S.stock_unit_price = N'$stock_unit_price'
				    And S.stock_is_confirm = N'Y'
				) as A
				Where stock_amount > abs(stock_minus_amount)
				Order by stock_in_date ASC, stock_idx ASC
			";

			$_stock_list = parent::execSqlList($qry);

			if(!$_stock_list){
				$returnValue["result"] = false;
				$returnValue["msg"] = "재고 내역이 없습니다.";
				return $returnValue;
			}else{

				//변경될 수량 (차감용)
				$remainAmount = $stock_amount;

				foreach ($_stock_list as $_stk){

					if($remainAmount == 0) break;

					$_tmp_stock_idx    = $_stk["stock_idx"];
					$_tmp_stock_amount = $_stk["current_amount"];
					$_tmp_minus_amount = 0;

					if($_tmp_stock_amount >= $remainAmount){
						//현 재고 건의 남은 수량으로 변경이 가능한 경우

						//변경될 수량 만큼 현 재고 수량을 소진
						$_tmp_minus_amount = $remainAmount;
						$remainAmount = 0;

					}else{

						//현 재고 건의 남은 수량으로 변경이 불가능한 경우

						//현재 재고건의 수량만큼 소진 후 다음 재고건으로 넘어감
						$_tmp_minus_amount = $_tmp_stock_amount;

						//변경될 수량 차감
						$remainAmount = $remainAmount - $_tmp_minus_amount;
					}

					//차감 재고 Insert (-1)
					$qry = "
							Insert Into DY_STOCK
							(
							 stock_ref_idx, product_idx, product_option_idx,
							 stock_kind, stock_in_date, stock_type,
							 stock_status, stock_unit_price, stock_amount,
							 stock_msg, 
							 stock_is_confirm, stock_is_confirm_date, stock_is_confirm_member_idx,
							 stock_regip, last_member_idx
							)
							VALUES 
							(
							 N'$_tmp_stock_idx',
							 N'$product_idx',
							 N'$product_option_idx',
							 N'MOVE',
							 N'$nowDate',
							 -1,
							 N'$stock_status_prev',
							 N'$stock_unit_price',
							 N'$_tmp_minus_amount',
							 N'$stock_msg',
							 N'Y', getdate(), N'$last_member_idx',
							 N'$modip', N'$last_member_idx'
							)
						";

					$_minus_stock_idx[] = parent::execSqlInsert($qry);

				}

				//차감 재고 Insert 된 후
				//차감 된 만큼 변경 재고 Insert (+)
				$qry = "
					Insert Into DY_STOCK
					(
					 stock_ref_idx, product_idx, product_option_idx,
					 stock_kind, stock_in_date, stock_type,
					 stock_status, stock_unit_price, stock_amount,
					 stock_msg, 
					 stock_is_confirm, stock_is_confirm_date, stock_is_confirm_member_idx,
					 stock_regip, last_member_idx
					)
					VALUES 
					(
					 N'$_tmp_stock_idx',
					 N'$product_idx',
					 N'$product_option_idx',
					 N'MOVE',
					 N'$nowDate',
					 1,
					 N'$stock_status_next',
					 N'$stock_unit_price',
					 N'$stock_amount',
					 N'$stock_msg',
					 N'Y', getdate(), N'$last_member_idx',
					 N'$modip', N'$last_member_idx'
					)
				";

				$_plus_stock_idx = parent::execSqlInsert($qry);


				if($_plus_stock_idx){

					//재고 작업 로그 Insert
					$qry = "
						Insert Into DY_STOCK_MOVE_LOG
						(
						 product_option_idx, stock_unit_price, 
						 stock_status_prev, stock_status_next, 
						 stock_move_amount, stock_move_msg, 
						 stock_move_regip, last_member_idx
						 )
						 VALUES 
						(
						 N'$product_option_idx',
						 N'$stock_unit_price',
						 N'$stock_status_prev',
						 N'$stock_status_next',
						 N'$stock_amount',
						 N'$stock_msg',
						 N'$modip',
						 N'$last_member_idx'
						)
					";

					$inserted_log_idx = parent::execSqlInsert($qry);

					//로그 정상 입력 시.. 변경된 재고들에게 log idx Update
					if($inserted_log_idx){
						$target_stock_idx = implode(",", $_minus_stock_idx) . ", " . $_plus_stock_idx;

						$qry = "
							Update DY_STOCK
							Set stock_move_idx = N'$inserted_log_idx'
							Where stock_idx in ($target_stock_idx)
						";

						$tmp = parent::execSqlUpdate($qry);
					}

					parent::sqlTransactionCommit();     //트랜잭션 커밋
					$returnValue["result"] = true;
				}else{
					parent::sqlTransactionRollback();     //트랜잭션 롤백
					$returnValue["result"] = false;
					$returnValue["msg"] = "재고 내역이 없습니다.";
				}

				return $returnValue;
			}
		}
	}

    public function changeAmount($productOptionIdx, $unitPrice, $status, $currentAmount, $newAmount, $msg, $operationDatetime) {
	    $this->db_connect();

	    if (! $productOptionIdx) return false;

	    $productIdx = $this->execSqlOneCol("SELECT product_idx FROM DY_PRODUCT_OPTION WHERE product_option_idx = N'$productOptionIdx'");
	    if (! $productIdx) return false;

        $amountDelta = $newAmount - $currentAmount;
        if ($amountDelta == 0) return false;

        $stockType = "1";
        if ($amountDelta < 0) {
            $stockType = "-1";
            $amountDelta *= -1;
        }

        global $GL_Member;
        $modIp = $_SERVER["REMOTE_ADDR"];
        $memberIdx = $GL_Member["member_idx"];

	    $query =
            "INSERT INTO DY_STOCK
            (
                product_idx
                , product_option_idx
                , stock_kind
                , stock_type
                , stock_status
                , stock_unit_price
                , stock_amount
                , stock_msg
                , stock_request_date
                , stock_is_proc
                , stock_is_confirm
                , stock_is_confirm_date
                , stock_is_confirm_member_idx
                , stock_regdate
                , stock_regip
                , last_member_idx
            )
            VALUES
            (
                N'$productIdx'
                , N'$productOptionIdx'
                , N'ETC'
                , N'$stockType'
                , N'$status'
                , N'$unitPrice'
                , N'$amountDelta'
                , N'$msg'
                , N'$operationDatetime'
                , N'Y'
                , N'Y'
                , N'$operationDatetime'
                , N'$memberIdx'
                , N'$operationDatetime'
                , N'$modIp'
                , N'$memberIdx'
            )
            ";

	    $rst = $this->execSqlInsert($query);

        $this->db_close();

        return $rst;
    }
}
?>