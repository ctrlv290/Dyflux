<?php

include_once "../_init_.php";

$gridResponse = array();
$gridResponse["page"] = "1";
$gridResponse["total"] = "1";
$clsProduct = new Product();

if ($_GET["mode"] == "sold_out") {
	$rows = array();

	$list = $clsProduct->getSoldOutList();

	$i = 0;
	foreach ($list as $row) {
		$rows[$i]["id"] = $i;
		$rows[$i]["cell"] = $row;
		$i++;
	}

	$gridResponse["rows"] = $rows;
	$gridResponse["records"] = count($rows);

} elseif ($_GET["mode"] == "shortage") {

} elseif ($_GET["mode"] == "warning") {

} else {

}

echo json_encode($gridResponse);