<?php
include_once "./_init_.php";
set_time_limit(0);

exit;

$C_Dbconn = new Dbconn();
$C_API_CJ_Invoice = new API_CJ_Invoice();

$qry = "
Select CJ.*, O.receive_name, O.receive_zipcode, O.receive_addr1 + ' ' + O.receive_addr2 + '[' + CJ.p_etcaddr + ']' as receive_addr, O.receive_memo, O.receive_hp_num, O.receive_tp_num
, O.delivery_fee
From DY_INVOICE_CJ CJ
	Inner Join DY_ORDER O On CJ.order_pack_idx = O.order_idx
Where CJ.invoice_regdate >= '2019-06-24 00:00:00'
And CJ.invoice_no not in (
	Select invoice_no From DY_INVOICE_PRINT_LOG
)
And O.order_is_del = N'N'
";

$C_Dbconn->db_connect();
$_list = $C_Dbconn->execSqlList($qry);
$C_Dbconn->db_close();

$qry = "
Select Max(print_date_count)+1 as print_date_max From DY_INVOICE_PRINT_LOG
Where print_date = 20190624
";
$C_Dbconn->db_connect();
$max_print_date_count = $C_Dbconn->execSqlOneCol($qry);
$C_Dbconn->db_close();

foreach ($_list as $item) {

	$order_pack_idx = $item["order_pack_idx"];
	$product_name_ary = array();

	$qry = "
		Select 
			P.product_name + PO.product_option_name + ' - ' + convert(varchar(5), OPM.product_option_cnt) + '개' as nm
		From DY_ORDER O
		Inner Join DY_ORDER_PRODUCT_MATCHING OPM On O.order_idx = OPM.order_idx
		Left Outer Join DY_PRODUCT P On OPM.product_idx = P.product_idx
		Left Outer Join DY_PRODUCT_OPTION PO On OPM.product_option_idx = PO.product_option_idx
		Where O.order_pack_idx = N'$order_pack_idx'
	";

	$C_Dbconn->db_connect();
	$_tmp_product_list = $C_Dbconn->execSqlList($qry);
	$C_Dbconn->db_close();

	foreach ($_tmp_product_list as $p)
	{
		$product_name_ary[] = $p["nm"];
	}

	$product_name_inline = implode(PHP_EOL, $product_name_ary);

	$invoice_idx     = $item["invoice_idx"];
	$invoice_no      = $item["invoice_no"];
	$receive_name    = $item["receive_name"];
	$receive_zipcode = $item["receive_zipcode"];
	$receive_addr    = $item["receive_addr"];
	$receive_memo    = $item["receive_memo"];
	$receive_hp_num  = $item["receive_hp_num"];
	$receive_tp_num  = $item["receive_tp_num"];
	$delivery_fee    = $item["delivery_fee"];


	$p_clsfcd            = $item["p_clsfcd"];
	$p_subclsfcd         = $item["p_subclsfcd"];
	$p_clsfaddr          = $item["p_clsfaddr"];
	$p_clldlcbranshortnm = $item["p_clldlcbranshortnm"];
	$p_clldlvempnm       = $item["p_clldlvempnm"];
	$p_clldlvempnicknm   = $item["p_clldlvempnicknm"];
	$p_prngdivcd         = $item["p_prngdivcd"];
	$p_farediv           = "03";
	$p_boxtyp            = "01";
	$send_name           = "덕윤";
	$send_phone1         = "031-811-5500";
	$send_phone2         = "";
	$send_add            = "경기 고양시 덕양구 성사동 1-2  성원영업소내 덕윤";
	$qry = "
		Insert Into DY_INVOICE_PRINT_LOG
		(print_date, print_date_count, invoice_idx, order_pack_idx, invoice_no, print_type, product_option_names, receive_name, receive_zipcode, receive_addr, receive_memo, receive_hp_num, receive_tp_num
		, delivery_code, delivery_name, delivery_fee
		, p_clsfcd, p_subclsfcd, p_clsfaddr, p_clldlcbranshortnm, p_clldlvempnm, p_clldlvempnicknm, p_prngdivcd, p_farediv, p_boxtyp
		, send_name, send_phone1, send_phone2, send_add, last_member_idx
		)
		VALUES 
		(
		 N'20190624', 
		 $max_print_date_count, 
		 N'$invoice_idx', 
		 N'$order_pack_idx', 
		 N'$invoice_no', 
		 N'F', 
		 N'$product_name_inline',
		 N'$receive_name', 
		 N'$receive_zipcode', 
		 N'$receive_addr', 
		 N'$receive_memo', 
		 N'$receive_hp_num', 
		 N'$receive_tp_num', 
		 N'CJGLS', 
		 N'CJ대한통운', 
		 N'$delivery_fee', 
		 N'$p_clsfcd', 
		 N'$p_subclsfcd', 
		 N'$p_clsfaddr', 
		 N'$p_clldlcbranshortnm', 
		 N'$p_clldlvempnm', 
		 N'$p_clldlvempnicknm', 
		 N'$p_prngdivcd', 
		 N'$p_farediv', 
		 N'$p_boxtyp', 
		 N'$send_name', 
		 N'$send_phone1', 
		 N'$send_phone2', 
		 N'$send_add',
		 0
		)
	";

	//echo $qry . "<br>";

	$C_Dbconn->db_connect();
	$inserted_idx = $C_Dbconn->execSqlInsert($qry);
	$C_Dbconn->db_close();


	// CJ OPEN DB 에 추가 하여 대한통운에 배송 요청 보냄
	$_deliver = array(
		'RCPT_DV' => "01",    // 접수구분 : 01 : 일반,  02 : 반품 (ex : 01)

		'SENDR_NM' => $send_name,    // 송화인명 : 보내는분 성명 (ex : XXX기업㈜)
		'SENDR_TEL_NO' => $send_phone1,    // 송화인전화번호 : '0'으로 시작할 것. (국번은 0으로 시작) (ex : 02)
		'SENDR_ADDR' => $send_add,    // 송화인주소 : 송화인 주소
		'RCVR_NM' => $receive_name,    // 수화인명 :  (ex : 홍길동)
		'RCVR_TEL_NO' => ($receive_hp_num != "" ? $receive_hp_num : $receive_tp_num),    // 수화인전화번호1 : 수화인전화번호 항목은 '0'으로 시작할 것. (국번은 0으로 시작) (ex : 031)
		'RCVR_ADDR' => $receive_addr,    // 수화인주소 : 수화인 주소 (

		'INVC_NO' => $invoice_no,    // 운송장번호 : 12자리, 운송장번호 채번 로직 : 3~11 범위의 수를 MOD(7) 한 결과가 12번째 수와 같아야 한다. Ex: 운송장번호 301100112233 의 경우, 3~11의 수(110011223) 을 MOD(7)한 결과가 3 이기에 적합한운송장번호이다. (ex : 301100112233)"
		'GDS_NM' => $product_name_inline,    // 상품명 :  (ex : 사과쥬스1박스)
	);
	$_ret = $C_API_CJ_Invoice->insertCJInvoice($_deliver);
	if(!$_ret["result"]) {
		echo $order_pack_idx . ":" . $invoice_no . "<br>";
	}

}

?>