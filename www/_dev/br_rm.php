<?php
include_once "../_init_.php";

$C_Dbconn = new Dbconn();

$qry = "
	Select product_idx, product_name, product_supplier_name, product_supplier_option From DY_PRODUCT
	Order by product_idx asc
";

$qry = "
	Select settle_idx, product_idx, product_name, product_option_name From DY_SETTLE
	Order by product_idx asc
";

$C_Dbconn->db_connect();
$_list = $C_Dbconn->execSqlList($qry);
$C_Dbconn->db_close();

//print_r2($_list);

foreach($_list as $p)
{
	$settle_idx = $p["settle_idx"];
	$product_idx = $p["product_idx"];
	$name = $p["product_name"];
	$product_option_name = $p["product_option_name"];
	$product_supplier_name = $p["product_supplier_name"];
	$product_supplier_option = $p["product_supplier_option"];
	$name_r = $name;
	if(preg_match('/\r\n|\r|\n/', $name_r)){
		$name_r = preg_replace('/\r\n|\r|\n/',' ',$name_r);
	}

	if($name != $name_r){
		echo "<pre>" . $name . "</pre><br>";
		echo "<pre>" . $name_r . "</pre><br>";


		$qry = "
			Update DY_PRODUCT
			Set product_name = N'$name_r'
			Where settle_idx = N'$settle_idx'
		";
		echo $qry."<Br>";
//		$C_Dbconn->db_connect();
//		$C_Dbconn->execSqlUpdate($qry);
//		$C_Dbconn->db_close();

		print_r2($qry);
		echo "<br>======================================================<br>";
	}
//
//	$product_supplier_name_r = $product_supplier_name;
//	if(preg_match('/\r\n|\r|\n/', $product_supplier_name_r)){
//		$product_supplier_name_r = preg_replace('/\r\n|\r|\n/',' ',$product_supplier_name_r);
//	}
//
//	if($product_supplier_name != $product_supplier_name_r){
//		echo "<pre>공급처 상품명</pre><br>";
//		echo "<pre>" . $product_supplier_name . "</pre><br>";
//		echo "<pre>" . $product_supplier_name_r . "</pre><br>";
//
//
//		$qry = "
//			Update DY_PRODUCT
//			Set product_supplier_name = N'$product_supplier_name_r'
//			Where product_idx = N'$product_idx'
//		";
//		$C_Dbconn->db_connect();
//		$C_Dbconn->execSqlUpdate($qry);
//		$C_Dbconn->db_close();
//
//		print_r2($qry);
//		echo "<br>======================================================<br>";
//	}
//
//	$product_supplier_option_r = $product_supplier_option;
//	if(preg_match('/\r\n|\r|\n/', $product_supplier_option_r)){
//		$product_supplier_option_r = preg_replace('/\r\n|\r|\n/',' ',$product_supplier_option_r);
//	}
//
//	if($product_supplier_option != $product_supplier_option_r){
//		echo "<pre>공급처 옵션</pre><br>";
//		echo "<pre>" . $product_supplier_option . "</pre><br>";
//		echo "<pre>" . $product_supplier_option_r . "</pre><br>";
//
//
//		$qry = "
//			Update DY_PRODUCT
//			Set product_supplier_option = N'$product_supplier_option_r'
//			Where product_idx = N'$product_idx'
//		";
//		$C_Dbconn->db_connect();
//		$C_Dbconn->execSqlUpdate($qry);
//		$C_Dbconn->db_close();
//
//		print_r2($qry);
//		echo "<br>======================================================<br>";
//	}


}
?>