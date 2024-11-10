<?php
/**
 * 상품 관련 Class
 * User: woox
 * Date: 2018-11-10
 * 상품 IDX 범위 : 10000 ~ 29999
 */

class Product extends DBConn
{
	/**
	 * 가장 큰 상품 번호 반환
	 * out : int
	 */
	private function getMaxProductIdx()
	{
		$qry = "Select Max(product_idx) From DY_PRODUCT Where product_idx > 10000";
		parent::db_connect();
		$rst = parent::execSqlOneCol($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 상품 Insert
	 * 대상 테이블 DY_PRODUCT, DY_PRODUCT_DETAIL, DY_PRODUCT_NOTICE, DY_PRODUCT_VENDOR_SHOW
	 * $args
	 * out : int (Insert IDENTITY)
	 */
	public function insertProduct($args)
	{
		global $GL_Member;

		//#region  #변수 초기화 block
		$product_sale_type         = "";
		$supplier_idx              = "";
		$product_name              = "";
		$product_supplier_name     = "";
		$product_supplier_option   = "";
		$seller_idx                = "";
		$product_origin            = "";
		$product_manufacturer      = "";
		$product_md                = "";
		$product_delivery_fee_sale = "";
		$product_delivery_fee_buy  = "";
		$product_delivery_type     = "";
		$product_category_l_idx    = "";
		$product_category_m_idx    = "";
		$product_sales_date        = "";
		$product_tax_type          = "";
		$product_notice_idx        = "";
		$product_notice_1_content  = "";
		$product_notice_2_content  = "";
		$product_notice_3_content  = "";
		$product_notice_4_content  = "";
		$product_notice_5_content  = "";
		$product_notice_6_content  = "";
		$product_notice_7_content  = "";
		$product_notice_8_content  = "";
		$product_notice_9_content  = "";
		$product_notice_10_content = "";
		$product_notice_11_content = "";
		$product_notice_12_content = "";
		$product_notice_13_content = "";
		$product_notice_14_content = "";
		$product_notice_15_content = "";
		$product_notice_16_content = "";
		$product_notice_17_content = "";
		$product_notice_18_content = "";
		$product_notice_19_content = "";
		$product_notice_20_content = "";
		$product_img_main          = 0;
		$product_img_1             = "";
		$product_img_2             = "";
		$product_img_3             = "";
		$product_img_4             = "";
		$product_img_5             = "";
		$product_img_6             = "";
		$product_desc              = "";
		$product_vendor_show       = "";
		$product_vendor_show_list  = "";
		$product_detail_mall_name  = array();
		$product_detail_url        = array();
		$product_is_use            = "";
		$product_is_del            = "";
		$product_is_trash          = "";
		$product_regdate           = "";
		$product_regip             = "";
		$product_moddate           = "";
		$product_modip             = "";
		$last_member_idx           = "";
		//endregion

		extract($args);

		//상품 IDX 생성
		$product_idx_inserted = $this->getMaxProductIdx();
		if(!$product_idx_inserted) $product_idx_inserted = 10000;
		$product_idx_inserted = $product_idx_inserted + 1;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//상품 기본 정보 Insert..
		$qry = "
			Insert Into DY_PRODUCT
			(
			product_idx, product_sale_type, supplier_idx, product_name, product_supplier_name, product_supplier_option
			, seller_idx, product_origin, product_manufacturer, product_md, product_delivery_fee_sale, product_delivery_fee_buy
			, product_delivery_type, product_category_l_idx, product_category_m_idx, product_sales_date, product_tax_type
			, product_notice_idx, product_notice_1_content, product_notice_2_content, product_notice_3_content, product_notice_4_content
			, product_notice_5_content, product_notice_6_content, product_notice_7_content, product_notice_8_content
			, product_notice_9_content, product_notice_10_content, product_notice_11_content, product_notice_12_content
			, product_notice_13_content, product_notice_14_content, product_notice_15_content, product_notice_16_content
			, product_notice_17_content, product_notice_18_content, product_notice_19_content, product_notice_20_content
			, product_img_main, product_img_1, product_img_2, product_img_3, product_img_4, product_img_5, product_img_6
			, product_desc, product_vendor_show, product_regdate, product_regip, last_member_idx
			)
			VALUES 
			(
				N'$product_idx_inserted' 
				, N'$product_sale_type' 
				, N'$supplier_idx' 
				, N'$product_name' 
				, N'$product_supplier_name' 
				, N'$product_supplier_option' 
				, N'$seller_idx' 
				, N'$product_origin' 
				, N'$product_manufacturer' 
				, N'$product_md' 
				, N'$product_delivery_fee_sale' 
				, N'$product_delivery_fee_buy' 
				, N'$product_delivery_type' 
				, N'$product_category_l_idx' 
				, N'$product_category_m_idx' 
				, N'$product_sales_date' 
				, N'$product_tax_type' 
				, N'$product_notice_idx' 
				, N'$product_notice_1_content' 
				, N'$product_notice_2_content' 
				, N'$product_notice_3_content' 
				, N'$product_notice_4_content' 
				, N'$product_notice_5_content' 
				, N'$product_notice_6_content' 
				, N'$product_notice_7_content' 
				, N'$product_notice_8_content' 
				, N'$product_notice_9_content' 
				, N'$product_notice_10_content' 
				, N'$product_notice_11_content' 
				, N'$product_notice_12_content' 
				, N'$product_notice_13_content' 
				, N'$product_notice_14_content' 
				, N'$product_notice_15_content' 
				, N'$product_notice_16_content' 
				, N'$product_notice_17_content' 
				, N'$product_notice_18_content' 
				, N'$product_notice_19_content' 
				, N'$product_notice_20_content' 
				, N'$product_img_main' 
				, N'$product_img_1' 
				, N'$product_img_2' 
				, N'$product_img_3' 
				, N'$product_img_4' 
				, N'$product_img_5' 
				, N'$product_img_6' 
				, N'$product_desc' 
				, N'$product_vendor_show' 
				, NOW() 
				, N'".$_SERVER["REMOTE_ADDR"]."' 
				, N'".$GL_Member["member_idx"]."' 
			);
		";

		$tmp = parent::execSqlInsert($qry);

		//상품이 정상 입력 되었을 경우
		if($product_idx_inserted)
		{
			//쇼핑몰 상세페이지 Insert
			if(count($product_detail_mall_name) > 0) {
				for($i = 0; $i < count($product_detail_mall_name);$i++)
				{
					if($product_detail_mall_name[$i] && $product_detail_url[$i]) {
						$qry = "
							Insert Into DY_PRODUCT_DETAIL
							(product_idx, product_detail_mall_name, product_detail_url, product_detail_regdate, product_detail_regip, last_member_idx)
							VALUES 
							(
								N'$product_idx_inserted'
								, N'" . $product_detail_mall_name[$i] . "'
								, N'" . $product_detail_url[$i] . "'
								, NOW()
								, N'" . $_SERVER["REMOTE_ADDR"] . "'
								, N'" . $GL_Member["member_idx"] . "'
							)
						";

						$rst2 = parent::execSqlInsert($qry);
					}
				}
			}

			//벤더사 노출 상태가 : 선택 노출 일 경우
			if($product_vendor_show == "SELECTED")
			{
				if(count($product_vendor_show_list) > 0) {
					foreach($product_vendor_show_list as $vendor_idx) {
						$qry = "
							Insert Into DY_PRODUCT_VENDOR_SHOW
							(product_idx, vendor_idx, product_vendor_show_regdate, product_vendor_show_regip, last_member_idx) 
							VALUES 
							(
								N'$product_idx_inserted'
								, N'$vendor_idx'
								, NOW()
								, N'" . $_SERVER["REMOTE_ADDR"] . "' 
								, N'" . $GL_Member["member_idx"] . "' 
							)
						";

						$rst3 = parent::execSqlInsert($qry);
					}
				}
			}

			parent::sqlTransactionCommit();     //트랜잭션 커밋
		}else{
			$product_idx_inserted = 0;
			parent::sqlTransactionRollback();     //트랜잭션 롤백
		}

		parent::db_close();
		return $product_idx_inserted;
	}

	/**
	 * 상품 휴지통으로 이동
	 * @param $product_idx : 상품 코드 (DY_PRODUCT 테이블 IDX)
	 * @return int
	 */
	public function gotoTrashProduct($product_idx)
	{
		global $GL_Member;

		$product_modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_PRODUCT
			Set product_is_trash = N'Y', product_is_trash_date = NOW()
			, product_moddate = NOW(), product_modip = N'$product_modip'
			, last_member_idx = N'$last_member_idx'
			Where product_idx = N'$product_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 상품 휴지통에서 복구
	 * @param $product_idx_list : 콤마(,) 로 구분된 상품 코드 리스트 (DY_PRODUCT 테이블 IDX)
	 * @return int
	 */
	public function restoreTrashProduct($product_idx_list)
	{
		global $GL_Member;

		$product_modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_PRODUCT
			Set product_is_trash = N'N', product_is_trash_date = NOW()
			, product_moddate = NOW(), product_modip = N'$product_modip'
			, last_member_idx = N'$last_member_idx'
			Where product_idx in ($product_idx_list)
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 상품 Update
	 * 대상 테이블 DY_PRODUCT, DY_PRODUCT_DETAIL, DY_PRODUCT_NOTICE, DY_PRODUCT_VENDOR_SHOW
	 * @param $args
	 * @return int
	 */
	public function updateProduct($args)
	{
		global $GL_Member;

		//#region  #변수 초기화 block
		$product_idx               = "";
		$product_sale_type         = "";
		$supplier_idx              = "";
		$product_name              = "";
		$product_supplier_name     = "";
		$product_supplier_option   = "";
		$seller_idx                = "";
		$product_origin            = "";
		$product_manufacturer      = "";
		$product_md                = "";
		$product_delivery_fee_sale = "";
		$product_delivery_fee_buy  = "";
		$product_delivery_type     = "";
		$product_category_l_idx    = "";
		$product_category_m_idx    = "";
		$product_sales_date        = "";
		$product_tax_type          = "";
		$product_notice_idx        = "";
		$product_notice_1_content  = "";
		$product_notice_2_content  = "";
		$product_notice_3_content  = "";
		$product_notice_4_content  = "";
		$product_notice_5_content  = "";
		$product_notice_6_content  = "";
		$product_notice_7_content  = "";
		$product_notice_8_content  = "";
		$product_notice_9_content  = "";
		$product_notice_10_content = "";
		$product_notice_11_content = "";
		$product_notice_12_content = "";
		$product_notice_13_content = "";
		$product_notice_14_content = "";
		$product_notice_15_content = "";
		$product_notice_16_content = "";
		$product_notice_17_content = "";
		$product_notice_18_content = "";
		$product_notice_19_content = "";
		$product_notice_20_content = "";
		$product_img_main          = 0;
		$product_img_1             = "";
		$product_img_2             = "";
		$product_img_3             = "";
		$product_img_4             = "";
		$product_img_5             = "";
		$product_img_6             = "";
		$product_desc              = "";
		$product_vendor_show       = "";
		$product_vendor_show_list  = array();
		$product_detail_idx        = array();
		$product_detail_mall_name  = array();
		$product_detail_url        = array();
		$product_is_use            = "";
		$product_is_del            = "";
		$product_is_trash          = "";
		$product_regdate           = "";
		$product_regip             = "";
		$product_moddate           = "";
		$product_modip             = "";
		$last_member_idx           = "";
		//endregion

		extract($args);
		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		//상품 기본 정보 Update..
		$qry = "
			Update DY_PRODUCT
			Set
				product_sale_type = N'$product_sale_type'
				, supplier_idx = N'$supplier_idx'
				, product_name = N'$product_name'
				, product_supplier_name = N'$product_supplier_name'
				, product_supplier_option = N'$product_supplier_option'
				, seller_idx = N'$seller_idx'
				, product_origin = N'$product_origin'
				, product_manufacturer = N'$product_manufacturer'
				, product_md = N'$product_md'
				, product_delivery_fee_sale = N'$product_delivery_fee_sale'
				, product_delivery_fee_buy = N'$product_delivery_fee_buy'
				, product_delivery_type = N'$product_delivery_type'
				, product_category_l_idx = N'$product_category_l_idx'
				, product_category_m_idx = N'$product_category_m_idx'
				, product_sales_date = N'$product_sales_date'
				, product_tax_type = N'$product_tax_type'
				, product_notice_idx = N'$product_notice_idx'
				, product_notice_1_content = N'$product_notice_1_content'
				, product_notice_2_content = N'$product_notice_2_content'
				, product_notice_3_content = N'$product_notice_3_content'
				, product_notice_4_content = N'$product_notice_4_content'
				, product_notice_5_content = N'$product_notice_5_content'
				, product_notice_6_content = N'$product_notice_6_content'
				, product_notice_7_content = N'$product_notice_7_content'
				, product_notice_8_content = N'$product_notice_8_content'
				, product_notice_9_content = N'$product_notice_9_content'
				, product_notice_10_content = N'$product_notice_10_content'
				, product_notice_11_content = N'$product_notice_11_content'
				, product_notice_12_content = N'$product_notice_12_content'
				, product_notice_13_content = N'$product_notice_13_content'
				, product_notice_14_content = N'$product_notice_14_content'
				, product_notice_15_content = N'$product_notice_15_content'
				, product_notice_16_content = N'$product_notice_16_content'
				, product_notice_17_content = N'$product_notice_17_content'
				, product_notice_18_content = N'$product_notice_18_content'
				, product_notice_19_content = N'$product_notice_19_content'
				, product_notice_20_content = N'$product_notice_20_content'
				, product_img_main = N'$product_img_main'
				, product_img_1 = N'$product_img_1'
				, product_img_2 = N'$product_img_2'
				, product_img_3 = N'$product_img_3'
				, product_img_4 = N'$product_img_4'
				, product_img_5 = N'$product_img_5'
				, product_img_6 = N'$product_img_6'
				, product_desc = N'$product_desc'
				, product_vendor_show = N'$product_vendor_show'
				, product_moddate = NOW()
				, product_modip = N'".$_SERVER["REMOTE_ADDR"]."'
				, last_member_idx = N'".$GL_Member["member_idx"]."' 
			WHERE product_idx = N'$product_idx'
		";
		$rst = parent::execSqlUpdate($qry);

		//쇼핑몰 상세페이지 Delete All
		//또는 사용자가 삭제한 상세페이지 데이터만 삭제
		$qry2 = "";
		if(count($product_detail_idx) > 0){
			$qry2 = " And product_detail_idx not in (".join(',', $product_detail_idx).")";
		}
		$qry = "Delete From DY_PRODUCT_DETAIL Where product_idx = N'$product_idx'" . $qry2;
		$rst2 = parent::execSqlUpdate($qry);

		//쇼핑몰 상세페이지 Insert
		if(count($product_detail_mall_name) > 0) {
			for($i = 0; $i < count($product_detail_mall_name);$i++)
			{
				if($product_detail_mall_name[$i] && $product_detail_url[$i]) {

					if($product_detail_idx[$i] == "") {
						//product_detail_idx 가 없는 경우
						$qry = "
							Insert Into DY_PRODUCT_DETAIL
							(product_idx, product_detail_mall_name, product_detail_url, product_detail_regdate, product_detail_regip, last_member_idx)
							VALUES 
							(
								N'$product_idx'
								, N'" . $product_detail_mall_name[$i] . "'
								, N'" . $product_detail_url[$i] . "'
								, NOW()
								, N'" . $_SERVER["REMOTE_ADDR"] . "' 
								, N'" . $GL_Member["member_idx"] . "' 
							)
						";
					}else{
						$qry = "
							Update DY_PRODUCT_DETAIL
							Set 
								product_detail_mall_name = N'".$product_detail_mall_name[$i]."', 
								product_detail_url = N'".$product_detail_url[$i]."',
								product_detail_moddate = NOW(),
								product_detail_modip = N'".$_SERVER["REMOTE_ADDR"]."',
								last_member_idx = N'".$GL_Member["member_idx"]."'
							Where
								product_detail_idx = N'".$product_detail_idx[$i]."'
						";
					}

					$rst2 = parent::execSqlInsert($qry);
				}
			}
		}

		//벤더사 노출 리스트 모두 삭제
		//또는 넘겨 받은 값들 중 없는 리스트 삭제
		$qry3 = "";
		$product_vendor_show_list = array_filter($product_vendor_show_list, function($value) { return $value !== ''; });
		if(count($product_vendor_show_list) > 0){
			$qry3 = " And vendor_idx not in (". join(',', $product_vendor_show_list) .")";
		}
		$qry = "Delete From DY_PRODUCT_VENDOR_SHOW Where product_idx = N'$product_idx'" . $qry3;


		$rst3 = parent::execSqlUpdate($qry);

		//현재 벤더사 리스트 가져오기
		$qry33 = "Select vendor_idx From DY_PRODUCT_VENDOR_SHOW Where product_idx = N'$product_idx'";
		$rstVendorList = parent::execSqlList($qry33);

		$qry4 = "";
		$existsVendorIdx = array();
		if($rstVendorList){
			foreach($rstVendorList as $vv) {
				$existsVendorIdx[] = $vv["vendor_idx"];
			}
		}

		//벤더사 노출 상태가 : 선택 노출 일 경우
		if($product_vendor_show == "SELECTED")
		{
			if(count($product_vendor_show_list) > 0) {
				foreach($product_vendor_show_list as $vendor_idx) {
					//현재 입력되어 있는 벤더사 이외의 벤더사만 추가
					if(!in_array($vendor_idx, $existsVendorIdx)) {
						$qry = "
							Insert Into DY_PRODUCT_VENDOR_SHOW
							(product_idx, vendor_idx, product_vendor_show_regdate, product_vendor_show_regip, last_member_idx) 
							VALUES 
							(
								N'$product_idx'
								, N'$vendor_idx'
								, NOW()
								, N'" . $_SERVER["REMOTE_ADDR"] . "' 
								, N'" . $GL_Member["member_idx"] . "' 
							)
						";

						$rst3 = parent::execSqlInsert($qry);
					}
				}
			}
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		parent::db_close();
		return $product_idx;
	}

	/**
	 * 상품 정보 리턴
	 * @param $product_idx : 상품 IDX (DY_PRODUCT 테이블 IDX)
	 * @return array (ONE ROW)
	 **/
	public function getProductData($product_idx)
	{
		$qry = "
			Select P.*
		        , (Select manage_group_idx From DY_MANAGE_GROUP MG Where MG.manage_group_idx = SP.manage_group_idx) as supplier_group_idx
		        , (Select manage_group_idx From DY_MANAGE_GROUP MG Where MG.manage_group_idx = S.manage_group_idx) as seller_group_idx
			From DY_PRODUCT P 
				Left Outer Join DY_MEMBER_SUPPLIER SP On SP.member_idx = P.supplier_idx
				Left Outer Join DY_SELLER S On S.seller_idx = P.seller_idx
			Where P.product_idx = N'$product_idx'
				And P.product_is_del = N'N'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 상품 삭제를 위한 정보 리턴
	 * @param $product_idx : 상품 IDX (DY_PRODUCT 테이블 IDX)
	 * @return array (ONE ROW)
	 **/
	public function getProductDataByDelete($product_idx)
	{
		$qry = "
			Select P.*
		        , (Select manage_group_idx From DY_MANAGE_GROUP MG Where MG.manage_group_idx = SP.manage_group_idx) as supplier_group_idx
		        , (Select manage_group_idx From DY_MANAGE_GROUP MG Where MG.manage_group_idx = S.manage_group_idx) as seller_group_idx
				, IFNULL((Select Sum(stock_amount * stock_type) From DY_STOCK T Where T.stock_is_del = N'N' And T.stock_is_confirm = N'Y' And T.stock_status = N'NORMAL' And T.product_idx = P.product_idx), 0) as current_stock_count
				, (Select count(*) From DY_ORDER_PRODUCT_MATCHING M Where M.order_matching_is_del = N'N' And M.product_idx = P.product_idx) as product_option_matching_count
				, (Select count(*) From DY_PRODUCT_MATCHING_LIST ML Where ML.matching_list_is_del= N'N' And ML.product_idx = P.product_idx) as matching_info_count
			From DY_PRODUCT P 
				Left Outer Join DY_MEMBER_SUPPLIER SP On SP.member_idx = P.supplier_idx
				Left Outer Join DY_SELLER S On S.seller_idx = P.seller_idx
			Where P.product_idx = N'$product_idx'
				And P.product_is_del = N'N'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * @param $product_idx : 상품 IDX (DY_PRODUCT 테이블 IDX)
	 * @return array (List)
	 */
	public function getProductDetailList($product_idx)
	{
		$qry = "
			Select PD.*
			From DY_PRODUCT_DETAIL PD
			Where PD.product_idx = N'$product_idx'
		";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 상품 벤더사 노출이 특정업체 노출일 경우 선택된 벤더사 리스트 반환
	 * @param $product_idx : 상품 IDX (DY_PRODUCT 테이블 IDX)
	 * @return array (List)
	 */
	public function getProductVendorSelectedList($product_idx)
	{
		$qry = "
			Select V.member_idx as vendor_idx, V.vendor_name
			From DY_MEMBER_VENDOR  V
			Where V.member_idx in (Select PVS.vendor_idx From DY_PRODUCT_VENDOR_SHOW PVS Where PVS.product_idx = N'$product_idx') 
		";
		parent::db_connect();
		$rst = parent::execSqlList($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 상품 옵션 추가
	 * @param $args
	 * @return int : product_option_idx
	 */
	public function insertProductOption($args)
	{
		global $GL_Member;

		$product_idx                   = "";
		$product_option_idx            = "";
		$product_option_name           = "";
		$product_option_purchase_price = "";
		$product_option_sale_price     = "";
		$product_option_sale_price_A   = "";
		$product_option_sale_price_B   = "";
		$product_option_sale_price_C   = "";
		$product_option_sale_price_D   = "";
		$product_option_sale_price_E   = "";
		$product_option_warning_count  = 0;
		$product_option_danger_count   = 0;
		$product_option_soldout        = "N";
		$product_option_soldout_temp   = "N";

		$product_option_regip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];
		extract($args);

		$qry = "
			Insert Into DY_PRODUCT_OPTION
			(
			product_idx, product_option_name, product_option_purchase_price, product_option_sale_price,
			product_option_sale_price_A, product_option_sale_price_B, product_option_sale_price_C, 
			product_option_sale_price_D, product_option_sale_price_E, product_option_warning_count, 
			product_option_danger_count, product_option_soldout, product_option_soldout_temp, 
			product_option_is_use, product_option_regip, last_member_idx
			) 
			VALUES 
			(
				N'$product_idx'
				, N'$product_option_name'
				, N'$product_option_purchase_price' 
				, N'$product_option_sale_price'
				, N'$product_option_sale_price_A'
				, N'$product_option_sale_price_B' 
				, N'$product_option_sale_price_C' 
				, N'$product_option_sale_price_D' 
				, N'$product_option_sale_price_E' 
				, N'$product_option_warning_count' 
				, N'$product_option_danger_count' 
				, N'$product_option_soldout'
				, N'$product_option_soldout_temp'
				, N'Y'
				, N'$product_option_regip'
				, N'$last_member_idx'
			)
		";

		parent::db_connect();
		$idx = parent::execSqlInsert($qry);
		parent::db_close();

		return $idx;
	}

	/**
	 * 상품 옵션 수정
	 * @param $args
	 * @return int : product_option_idx
	 */
	public function updateProductOption($args){
		global $GL_Member;

		$product_idx                   = "";
		$product_option_idx            = "";
		$product_option_name           = "";
		$product_option_purchase_price = "";
		$product_option_sale_price     = "";
		$product_option_sale_price_A   = "";
		$product_option_sale_price_B   = "";
		$product_option_sale_price_C   = "";
		$product_option_sale_price_D   = "";
		$product_option_sale_price_E   = "";
		$product_option_warning_count  = 0;
		$product_option_danger_count   = 0;

		$product_option_modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];
		extract($args);

		$qry = "
			Update DY_PRODUCT_OPTION
			Set product_option_name = N'$product_option_name'
				, product_option_purchase_price = N'$product_option_purchase_price'
				, product_option_sale_price = N'$product_option_sale_price'
				, product_option_sale_price_A = N'$product_option_sale_price_A'
				, product_option_sale_price_B = N'$product_option_sale_price_B'
				, product_option_sale_price_C = N'$product_option_sale_price_C'
				, product_option_sale_price_D = N'$product_option_sale_price_D'
				, product_option_sale_price_E = N'$product_option_sale_price_E'
				, product_option_warning_count = N'$product_option_warning_count'
				, product_option_danger_count = N'$product_option_danger_count'
				, product_option_moddate = NOW()
				, product_option_modip = N'$product_option_modip'
				, last_member_idx = N'$last_member_idx'
			Where
				product_option_idx = N'$product_option_idx'
		";

		parent::db_connect();
		$idx = parent::execSqlUpdate($qry);
		parent::db_close();

		return $idx;
	}

	/**
	 * 상품 옵션 품절/일시품절 업데이트
	 * @param $product_option_idx : 상품옵션 IDX
	 * @param $soldout_type : 품절/일시품절
	 * @param $change_value : Y/N
	 * @return int
	 */
	public function ProductOptionSoldOutUpdate($product_option_idx, $soldout_type, $change_value)
	{
		global $GL_Member;
		$product_option_modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$able_field = array("product_option_soldout", "product_option_soldout_temp");

		if(in_array($soldout_type, $able_field) && in_array($change_value, array("Y", "N")))
		{
			$qry = "
				Update DY_PRODUCT_OPTION
				Set
					$soldout_type = N'$change_value',
					product_option_soldout_date = NOW(),
					product_option_soldout_ip = N'$product_option_modip',
					last_member_idx = N'$last_member_idx'
				Where
					product_option_idx = N'$product_option_idx'
			";

			parent::db_connect();
			$rst = parent::execSqlUpdate($qry);
			parent::db_close();
		}else{
			$rst = 0;
		}

		return $rst;
	}

	/**
	 * 상품 옵션 전체 품절/판매가능 업데이트
	 * @param $product_idx : 상품 IDX
	 * @param $change_value : Y/N
	 * @return int
	 */
	public function ProductOptionSoldOutAllUpdate($product_idx, $change_value)
	{
		global $GL_Member;
		$product_option_modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update DY_PRODUCT_OPTION
			Set 
				product_option_soldout = N'$change_value',
				product_option_soldout_date = NOW(),
				product_option_soldout_ip = N'$product_option_modip',
				last_member_idx = N'$last_member_idx'
			Where
				product_idx = N'$product_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();
		return $rst;
	}

	/**
	 * 상품 옵션 정보 리턴
	 * @param $product_options_idx : 상품 옵션 IDX (DY_PRODUCT_OPTION 테이블 IDX)
	 * @return array (ONE ROW)
	 **/
	public function getProductOptionData($product_options_idx)
	{
		$qry = "
			Select O.*, P.product_name
			From DY_PRODUCT_OPTION O 
				Left Outer Join DY_PRODUCT P On O.product_idx = P.product_idx
			Where P.product_is_del = N'N' And P.product_is_use = N'Y' And O.product_option_is_del = N'N' And O.product_option_is_use = N'Y'
				And O.product_option_idx = N'$product_options_idx'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 상품 옵션 Detail 정보 리턴
	 * @param $product_options_idx : 상품 옵션 IDX (DY_PRODUCT_OPTION 테이블 IDX)
	 * @return array (ONE ROW)
	 **/
	public function getProductOptionDataDetail($product_options_idx)
	{
		$qry = "
			Select O.*, P.product_name, P.supplier_idx, S.supplier_name
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
	 * 상품 옵션 삭제를 위한 정보 리턴
	 * @param $product_options_idx : 상품 옵션 IDX (DY_PRODUCT_OPTION 테이블 IDX)
	 * @return array (ONE ROW)
	 **/
	public function getProductOptionDataByDelete($product_options_idx)
	{
		$qry = "
			Select P.*
		        , (Select manage_group_idx From DY_MANAGE_GROUP MG Where MG.manage_group_idx = SP.manage_group_idx) as supplier_group_idx
		        , (Select manage_group_idx From DY_MANAGE_GROUP MG Where MG.manage_group_idx = S.manage_group_idx) as seller_group_idx
				, IFNULL((Select Sum(stock_amount * stock_type) From DY_STOCK T Where T.stock_is_del = N'N' And T.stock_is_confirm = N'Y' And T.stock_status = N'NORMAL' And T.product_option_idx = PO.product_option_idx), 0) as current_stock_count
				, (Select count(*) From DY_ORDER_PRODUCT_MATCHING M Where M.order_matching_is_del = N'N' And M.product_option_idx = PO.product_option_idx) as product_option_matching_count
				, (Select count(*) From DY_PRODUCT_MATCHING_LIST ML Where ML.matching_list_is_del= N'N' And ML.product_option_idx = PO.product_option_idx) as matching_info_count
			From DY_PRODUCT_OPTION PO 
				Inner Join DY_PRODUCT P On P.product_idx = PO.product_idx
				Left Outer Join DY_MEMBER_SUPPLIER SP On SP.member_idx = P.supplier_idx
				Left Outer Join DY_SELLER S On S.seller_idx = P.seller_idx
			Where PO.product_option_idx = N'$product_options_idx'
				And P.product_is_del = N'N'
				And PO.product_option_is_del = N'N'
		";
		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;

		return $rst;
	}

	/**
	 * 상품 옵션 삭제
	 * @param $product_options_idx : 상품 옵션 IDX (DY_PRODUCT_OPTION 테이블 IDX)
	 * @return array (ONE ROW)
	 **/
	public function getProductOptionDelete($product_options_idx)
	{
		global $GL_Member;
		$product_option_modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];
		$qry = "
			Update DY_PRODUCT_OPTION 
			Set product_option_is_del = N'Y'
				, product_option_moddate = NOW()
				, product_option_modip = N'$product_option_modip'
				, last_member_idx = N'$last_member_idx'
			WHERE product_option_idx = N'$product_options_idx'
		";
		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 상품 일괄 선택 수정 시 사용되는 Update
	 * @param $target       : 대상 (product, product_option)
	 * @param $target_idx   : 대상 IDX
	 * @param $target_field : 대상 컬럼명
	 * @param $val          : 수정 값
	 * @return bool|resource
	 */
	public function updateProductXlsSelected($target, $target_idx, $target_field, $val)
	{
		global $GL_Member;

		$product_modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$table_name = "";
		$table_key_field = "";
		$require_update_field = "";
		if($target == "product") {
			$table_name = "DY_PRODUCT";
			$table_key_field = "product_idx";
			$require_update_field = ", product_moddate = NOW(), product_modip = N'$product_modip', last_member_idx = N'$last_member_idx'";
		}elseif($target == "product_option"){
			$table_name = "DY_PRODUCT_OPTION";
			$table_key_field = "product_option_idx";
			$require_update_field = ", product_option_moddate = NOW(), product_option_modip = N'$product_modip', last_member_idx = N'$last_member_idx'";
		}else{
			return false;
		}

		$qry = "
			Update $table_name
			Set $target_field = N'$val'
			$require_update_field
			Where $table_key_field = N'$target_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

	/**
	 * 상품 매칭 정보 저장
	 * @param $seller_idx               : 판매처 IDX
	 * @param $market_product_no        : 판매처 상품 코드
	 * @param $market_product_name      : 판매처 상품명
	 * @param $market_product_option    : 판매처 상품 옵션
	 * @param $product_list             : 매칭 상품 목록 (array) [{product_idx, product_option_idx, product_option_cnt}]
	 * @return int                      : 매칭된 상품 수
	 */
	public function insertProductMatchingInfo($seller_idx, $market_product_no, $market_product_name, $market_product_option, $product_list)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$inserted = 0;
		$isExists = 0;

		$resultInfoIdx = 0;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		if(count($product_list) > 0){

			//이미 존재 하는 매칭 정보 인지 확인
			$qry = "
				Select count(*)
				From DY_PRODUCT_MATCHING_INFO
				Where matching_info_is_del = N'N'
						And seller_idx = N'$seller_idx'
						And market_product_no = N'$market_product_no'
						And market_product_name = N'$market_product_name'
						And market_product_option = N'$market_product_option'
			";
			$resultInfoIdx = parent::execSqlOneCol($qry);

			//매칭정보가 없을 경우에만 저장
			if($resultInfoIdx == 0) {
				//매칭 정보 저장 (판매처 상품코드, 상품명, 상품옵션)
				$qry = "
					Insert Into DY_PRODUCT_MATCHING_INFO
					(
					 seller_idx, market_product_no, market_product_name, market_product_option
					, member_idx, matching_info_regip, last_member_idx
					) 
					VALUES 
					(
					 N'$seller_idx', 
					 N'$market_product_no',
					 N'$market_product_name',
					 N'$market_product_option',
					 N'$last_member_idx',
					 N'$modip',
					 N'$last_member_idx'
					)
				";
				$resultInfoIdx = parent::execSqlInsert($qry);

				if ($resultInfoIdx) {
					foreach ($product_list as $prd) {
						$product_idx        = $prd["product_idx"];
						$product_option_idx = $prd["product_option_idx"];
						$product_option_cnt = $prd["product_option_cnt"];

						//매칭 상품 저장
						//상품코드, 상품옵션코드, 상품수량 숫자형인지 확인
						if (is_numeric($product_idx)
							&& is_numeric($product_option_idx)
							&& is_numeric($product_option_cnt)
						) {

							$qry = "
								Insert Into DY_PRODUCT_MATCHING_LIST
								(
								 matching_info_idx
								 , product_idx, product_option_idx, product_option_cnt
								 , matching_list_regip, last_member_idx
								)
								VALUES
								(
								 N'$resultInfoIdx',
								 N'$product_idx',
								 N'$product_option_idx',
								 N'$product_option_cnt',
								 N'$modip',
								 N'$last_member_idx'
								)
							";

							$rst = parent::execSqlInsert($qry);
							if ($rst) {
								$inserted++;
							}
						}
					}
				}
			}
		}

		//전달 받은 상품목록 개수와 저장된 개수가 같으면
		//Commit
		if($isExists == 0 && count($product_list) > 0 && count($product_list) == $inserted) {
			//Commit
			parent::sqlTransactionCommit();     //트랜잭션 커밋
		}else {
			//Rollback
			parent::sqlTransactionRollback();     //트랜잭션 롤백
		}
		parent::db_close();

		return $resultInfoIdx;
	}

	/**
	 * 상품 매칭 정보 삭제
	 * @param $matching_info_idx : 매칭 정보 IDX
	 * @return bool|resource
	 */
	public function deleteProductMatchingInfo($matching_info_idx)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$qry = "
			Update
			DY_PRODUCT_MATCHING_INFO
			Set
			    matching_info_is_del = N'Y'
				, matching_info_moddate = NOW()
				, matching_info_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			Where
				matching_info_idx = N'$matching_info_idx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

    /**
     * 상품 매칭 정보 다중 삭제
     * @param $matching_info_idx : 매칭 정보 IDX
     * @return bool|resource
     */
    public function multiDeleteProductMatchingInfo($matching_info_idx_arr){
        global $GL_Member;
        $modip = $_SERVER["REMOTE_ADDR"];
        $last_member_idx = $GL_Member["member_idx"];

        $qry = "
			Update
			DY_PRODUCT_MATCHING_INFO
			Set
			    matching_info_is_del = N'Y'
				, matching_info_moddate = NOW()
				, matching_info_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			Where
		        matching_info_idx IN ($matching_info_idx_arr)
		";

        parent::db_connect();
        $rst = parent::execSqlUpdate($qry);
        parent::db_close();

        return $rst;
    }

	/**
	 * 매칭 정보 중복 체크 (중복이 있을 경우 false)
	 * @param $seller_idx             : 판매처 IDX
	 * @param $market_product_no      : 판매처 상품코드
	 * @param $market_product_name    : 판매처 상품명
	 * @param $market_product_option  : 판매처 옵션
	 * @return bool (중복이 있을 경우 false)
	 */
	public function dupCheckProductMatchingInfo($seller_idx, $market_product_no, $market_product_name, $market_product_option)
	{

		parent::db_connect();
			//이미 존재 하는 매칭 정보 인지 확인
			$qry = "
				Select count(*)
				From DY_PRODUCT_MATCHING_INFO
				Where matching_info_is_del = N'N'
						And seller_idx = N'$seller_idx'
						And market_product_no = N'$market_product_no'
						And market_product_name = N'$market_product_name'
						And market_product_option = N'$market_product_option'
			";
			$isExists = parent::execSqlOneCol($qry);
		parent::db_close();

		return ($isExists == 0) ? true : false;
	}

	/**
	 * 매칭 정보 반환 - 판매처코드, 판매처 상품코드, 판매처 상품명, 판매처 옵션명 으로 검색
	 * @param $seller_idx
	 * @param $market_product_no
	 * @param $market_product_name
	 * @param $market_product_option
	 * @return array|false|null
	 */
	public function getProductMatchingInfo($seller_idx, $market_product_no, $market_product_name, $market_product_option)
	{
		parent::db_connect();
		$qry = "
				Select I.*, M.member_id
				From DY_PRODUCT_MATCHING_INFO I
					Left Outer Join DY_MEMBER M On I.last_member_idx = M.idx
				Where I.matching_info_is_del = N'N'
						And I.seller_idx = N'$seller_idx'
						And I.market_product_no = N'$market_product_no'
						And I.market_product_name = N'$market_product_name'
						And I.market_product_option = N'$market_product_option'
			";
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();

		return $_view;
	}

	/**
	 * 수수료관리 - 판매처상품코드 중복확인
	 * False 시 중복!
	 * @param $seller_idx
	 * @param $market_product_no
	 * @return bool
	 */
//	public function dupCheckProductCommissionMarketProductNo($seller_idx, $market_product_no)
	public function dupCheckProductCommissionMarketProductNo($seller_idx, $comm_type, $market_commission, $delivery_commission, $event_unit_price)
	{
		$return = false;
		if($comm_type == 'EVENT') {
            $qry = "
			Select count(*) From DY_MARKET_COMMISSION 
			Where seller_idx = N'$seller_idx' 
			And comm_type = N'$comm_type' 
			And market_commission = N'$market_commission'
			And delivery_commission = N'$delivery_commission'
			And event_unit_price = N'$event_unit_price'
			And comm_is_del = N'N'
		";
        }else{
            $qry = "
			Select count(*) From DY_MARKET_COMMISSION 
			Where seller_idx = N'$seller_idx' 
			And comm_type = N'$comm_type' 
			And market_commission = N'$market_commission'
			And delivery_commission = N'$delivery_commission'
			And comm_is_del = N'N'
		";
        }
		parent::db_connect();
		$cnt = parent::execSqlOneCol($qry);
		parent::db_close();

		if($cnt == 0)
		{
			$return = true;
		}

		return $return;
	}

	/**
	 * 수수료 등록
	 * @param $seller_idx
	 * @param $market_product_no
	 * @param $market_commission
	 * @param $delivery_commission
	 * @param $product_idx_list
	 * @param $product_option_idx_list
	 * @return bool
	 */
//	public function insertProductCommission($seller_idx, $market_product_no, $market_commission, $delivery_commission, $product_idx_list, $product_option_idx_list)
    public function insertProductCommission($seller_idx, $comm_type, $market_commission, $delivery_commission, $product_idx_list, $product_option_idx_list, $event_unit_price)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		//중복확인
//		$dup = $this->dupCheckProductCommissionMarketProductNo($seller_idx, $market_product_no);
//		if(!$dup){
//			$returnValue = false;
//			return $returnValue;
//		}

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		if(count($product_idx_list) == 0 || count($product_idx_list) != count($product_option_idx_list)){
			return $returnValue;
		}


		//수수료정보입력
        if($comm_type == 'EVENT') {
            $qry = "
			Insert Into DY_MARKET_COMMISSION
			(seller_idx, comm_type, market_commission, delivery_commission, comm_regip, last_member_idx, event_unit_price)
			VALUES 
			(
			 N'$seller_idx'
			 , N'$comm_type'
			 , N'$market_commission'
			 , N'$delivery_commission'
			 , N'$modip'
			 , N'$last_member_idx'
			 , N'$event_unit_price'
			)
		";
        } else{
            $qry = "
			Insert Into DY_MARKET_COMMISSION
			(seller_idx, comm_type, market_commission, delivery_commission, comm_regip, last_member_idx)
			VALUES 
			(
			 N'$seller_idx'
			 , N'$comm_type'
			 , N'$market_commission'
			 , N'$delivery_commission'
			 , N'$modip'
			 , N'$last_member_idx'
			)
		";
		}

		$inserted_idx = parent::execSqlInsert($qry);

		if($inserted_idx){

			//상품 입력
			foreach ($product_idx_list as $key => $product_idx)
			{
				$product_option_idx = $product_option_idx_list[$key];

				$qry = "
					Insert Into DY_MARKET_COMMISSION_PRODUCT
					(comm_idx, product_idx, product_option_idx, comm_product_regip, last_member_idx)
					VALUES 
					(
					 N'$inserted_idx'
					 , N'$product_idx'
					 , N'$product_option_idx'
					 , N'$modip'
					 , N'$last_member_idx'
					)
				";

				$tmp = parent::execSqlInsert($qry);
			}

		}else{
			parent::sqlTransactionRollback();     //트랜잭션 롤백
			return $returnValue;
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;

	}

	/**
	 * 수수료 수정
	 * @param $seller_idx
	 * @param $market_product_no
	 * @param $market_commission
	 * @param $delivery_commission
	 * @param $product_idx_list
	 * @param $product_option_idx_list
	 * @return bool
	 */
	public function updateProductCommission($comm_idx, $comm_type, $market_commission, $delivery_commission, $product_idx_list, $product_option_idx_list, $event_unit_price)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		if(count($product_idx_list) == 0 || count($product_idx_list) != count($product_option_idx_list)){
			return $returnValue;
		}

        if($comm_type == 'EVENT') {
            //수수료정보입력
            $qry = "
			Update DY_MARKET_COMMISSION
			Set 
			    market_commission = N'$market_commission'
			    , delivery_commission = N'$delivery_commission'
				, comm_moddate = NOW()
				, comm_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
				, event_unit_price = N'$event_unit_price'
		    Where comm_idx = N'$comm_idx'
		";
        }else{
            $qry = "
			Update DY_MARKET_COMMISSION
			Set 
			    market_commission = N'$market_commission'
			    , delivery_commission = N'$delivery_commission'
				, comm_moddate = NOW()
				, comm_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
		    Where comm_idx = N'$comm_idx'
		";
        }
		$tmp = parent::execSqlUpdate($qry);

		//기존 상품 제거
		$qry = "Delete From DY_MARKET_COMMISSION_PRODUCT Where comm_idx = N'$comm_idx'";
		$tmp = parent::execSqlUpdate($qry);

		//상품 입력
		foreach ($product_idx_list as $key => $product_idx)
		{
			$product_option_idx = $product_option_idx_list[$key];

			$qry = "
				Insert Into DY_MARKET_COMMISSION_PRODUCT
				(comm_idx, product_idx, product_option_idx, comm_product_regip, last_member_idx)
				VALUES 
				(
				 N'$comm_idx'
				 , N'$product_idx'
				 , N'$product_option_idx'
				 , N'$modip'
				 , N'$last_member_idx'
				)
			";

			$tmp = parent::execSqlInsert($qry);
		}

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;

	}


	/**
	 * 수수료 삭제
	 * @param $comm_idx
	 * @return bool
	 */
	public function deleteProductCommission($comm_idx)
	{
		global $GL_Member;
		$modip = $_SERVER["REMOTE_ADDR"];
		$last_member_idx = $GL_Member["member_idx"];

		$returnValue = false;

		parent::db_connect();
		parent::sqlTransactionBegin();  //트랜잭션 시작

		$qry = "
			Update DY_MARKET_COMMISSION
			Set comm_is_del = N'Y'
				, comm_moddate = NOW()
				, comm_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			Where comm_idx = N'$comm_idx'
		";
		$tmp = parent::execSqlUpdate($qry);

		$qry = "
			Update DY_MARKET_COMMISSION_PRODUCT
			Set comm_product_is_del = N'N'
				, comm_product_moddate = NOW()
				, comm_product_modip = N'$modip'
				, last_member_idx = N'$last_member_idx'
			Where comm_idx = N'$comm_idx'
		";
		$tmp = parent::execSqlUpdate($qry);

		parent::sqlTransactionCommit();     //트랜잭션 커밋
		$returnValue = true;

		return $returnValue;
	}

	/**
	 * 수수료 정보 얻기
	 * @param $comm_idx
	 * @return array|false|null
	 */
	public function getProductCommissionInfo($comm_idx){
		$qry = "
			Select C.*, S.seller_name 
			From DY_MARKET_COMMISSION C
			Left Outer Join DY_SELLER S On C.seller_idx = S.seller_idx
			Where C.comm_idx = N'$comm_idx' And C.comm_is_del = N'N'
		";

		parent::db_connect();
		$_view = parent::execSqlOneRow($qry);
		parent::db_close();

		return $_view;
	}

	/**
	 * 수수료 정보 얻기
	 * @param $comm_idx
	 * @return array|false|null
	 */
	public function getProductCommissionProductInfo($comm_idx){
		$qry = "
			Select 
			CP.product_idx, CP.product_option_idx, P.product_name, PO.product_option_name
			From DY_MARKET_COMMISSION C
			Inner Join DY_MARKET_COMMISSION_PRODUCT CP On C.comm_idx = CP.comm_idx
			Left Outer Join DY_PRODUCT P On CP.product_idx = P.product_idx
			Left Outer Join DY_PRODUCT_OPTION PO On CP.product_option_idx = PO.product_option_idx
			Where C.comm_idx = N'$comm_idx' And comm_is_del = N'N' And P.product_is_del = N'N' And PO.product_option_is_del = N'N'
			Order by CP.comm_product_regdate ASC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}


	function getSoldOutList() {
		$qry = "
			SELECT
				P.product_idx, P.product_name, PO.product_option_idx, PO.product_option_name,
				IFNULL(product_option_soldout_date, N'') AS product_option_soldout_date,
				IFNULL(product_option_soldout_memo, N'') AS product_option_soldout_memo
			FROM DY_PRODUCT AS P
				LEFT OUTER JOIN DY_PRODUCT_OPTION AS PO ON P.product_idx = PO.product_idx
			WHERE PO.product_option_soldout = N'Y'
			ORDER BY product_option_soldout_date DESC
		";

		parent::db_connect();
		$_list = parent::execSqlList($qry);
		parent::db_close();

		return $_list;
	}

	function updateOptionSoldOutMemo($optIdx, $memo) {
		$qry = "
			UPDATE DY_PRODUCT_OPTION
			SET product_option_soldout_memo = N'$memo'
			WHERE product_option_idx = N'$optIdx'
		";

		parent::db_connect();
		$rst = parent::execSqlUpdate($qry);
		parent::db_close();

		return $rst;
	}

    function updateOptionBarcodeGTIN($optIdx, $num) {
        $qry = "
			UPDATE DY_PRODUCT_OPTION
			SET product_option_barcode_GTIN = N'$num'
			WHERE product_option_idx = N'$optIdx'
		";

        parent::db_connect();
        $rst = parent::execSqlUpdate($qry);
        parent::db_close();

        return $rst;
    }

    function getSpecialSaleDataByIndex($idx) {
		$qry = "
			SELECT SV.*, S.seller_name, P.product_name, PO.product_option_name
			FROM DY_PRODUCT_SPECIAL_VALUE SV
				JOIN DY_PRODUCT P ON SV.product_idx = P.product_idx
				JOIN DY_PRODUCT_OPTION PO ON SV.product_option_idx = PO.product_option_idx
				JOIN DY_SELLER S ON SV.seller_idx = S.seller_idx
			WHERE
				SV.idx = N'$idx'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}

	function getSpecialSaleData($seller_idx, $product_option_idx) {
		$qry = "
			SELECT SV.*, S.seller_name, P.product_name, PO.product_option_name
			FROM DY_PRODUCT_SPECIAL_VALUE SV
				JOIN DY_PRODUCT P ON SV.product_idx = P.product_idx
				JOIN DY_PRODUCT_OPTION PO ON SV.product_option_idx = PO.product_option_idx
				JOIN DY_SELLER S ON SV.seller_idx = S.seller_idx
			WHERE
				SV.seller_idx = N'$seller_idx' AND SV.product_option_idx = N'$product_option_idx' AND SV.is_del = 'N'
		";

		parent::db_connect();
		$rst = parent::execSqlOneRow($qry);
		parent::db_close();

		return $rst;
	}
}
?>