<?php

include_once "../_init_.php";
header("Content-Type: application/json; charset=EUC-KR");
header("Cache-Control:no-cache");
header("Pragma:no-cache");

$crm = new CustomReportManager();

$data = $_POST;

foreach ($data as &$val){
    $val = urldecode($val);
}

if($data['mode'] == 'createReport'){

    parse_str($data["report_data"], $output);
    unset($output["user_template"]);
    unset($output["user_template_name"]);
    foreach ($output as &$out){
        if(is_array($out) == 1){
            $out = implode( ',', $out);
        }
    }

	$report = $crm->createReport($output);

	$report_new = [];
	$header = [];

	$j = 0;
	foreach ($report as $key => $val) {
		$i = 0;

        foreach ($val as $key_c => $val_c) {
			if($j == 0) {
				$report_new["header"]["id_".$i] = $key_c;
            }

			$report_new[$j]["id_".$i] = $val_c;

			$i++;
		}

		$j++;
	}

	echo json_encode($report_new,true);

}elseif ($data['mode'] == 'saveTemplate'){

    $idx = $data["user_template_idx"];
    $name = $data["user_template_name"];

    parse_str($data["report_data"], $output);
    unset($output["user_template"]);
    unset($output["user_template_name"]);
    foreach ($output as &$out){
        if(is_array($out) == 1){
            $out = implode( ',', $out);
        }
    }
    $report = $crm->saveTemplate($name, $output, $idx);
    echo json_encode($report,true);

}elseif($data['mode'] == 'deleteTemplate'){
    $idx = $data['idx'];
    $report = $crm->deleteTemplate($idx);
    echo json_encode($report,true);

}elseif($data['mode'] == 'selectTemplate'){
    $qry = "Select idx, name from dy_custom_report_template where is_del =N'N' order by name ASC";

    $template_list = $crm->getConnect()->execSqlList($qry);

    echo json_encode($template_list,true);

}elseif($data['mode'] == 'searchTemplate'){
    $idx = $data['idx'];
    $qry = "Select items from dy_custom_report_template where idx =N'$idx' AND is_del =N'N'";
    $template_item = $crm->getConnect()->execSqlOnecol($qry);

    echo json_encode($template_item,true);
}
