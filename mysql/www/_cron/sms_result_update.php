<?php
include_once "../_init_.php";

$table_month = date('Ym');
$now_date = date('Y-m-d H:i');
//$before_date =  $date = date("Y-m-d H:i", strtotime($start_date . "-50 minutes"));
$before_date =  $date = date("Y-m-d H:i", strtotime($start_date . "-1 day"));

$C_Dbconn = new DBConn();

$qry = "
    select idx, tp_code, send_phone, al_date from DY_SMS_AL_SEND_RESULT where DATE_FORMAT(al_date, '%Y-%m-%d %H:%i:%s') between '$before_date' AND '$now_date' 
";
echo $qry . "<br>";
$C_Dbconn->db_connect();
$data = $C_Dbconn->execSqlList($qry);

for($i=0; $i<count($data); $i++) {
	$idx = $data[$i]['idx'];
	$tp_code = $data[$i]['tp_code'];
	$send_phone = $data[$i]['send_phone'];
	$al_date = $data[$i]['al_date'];

	$qry = "
        select count(*) as cnt from DYFLUX_SMS.BIZ_LOG_" . $table_month . " where REQUEST_TIME='$al_date' and TEMPLATE_CODE='$tp_code' and SEND_PHONE='$send_phone' AND CALL_STATUS='7000' 
    ";
	echo $qry . "<br>";
	$succ_cnt = $C_Dbconn->execSqlOneCol($qry);

	$qry = "
        select count(*) as cnt from DYFLUX_SMS.BIZ_LOG_" . $table_month . " where REQUEST_TIME='$al_date' and TEMPLATE_CODE='$tp_code' and SEND_PHONE='$send_phone' AND CALL_STATUS !='7000' 
    ";
	echo $qry . "<br>";
	$fail_cnt = $C_Dbconn->execSqlOneCol($qry);

	$qry = "
        update
            DY_SMS_AL_SEND_RESULT
        set
          send_succ_cnt = '$succ_cnt',
          send_fail_cnt = '$fail_cnt'
        where   
          idx = '$idx'
    ";
	echo $qry . "<br>";
	$C_Dbconn->execSqlUpdate($qry);
}
$C_Dbconn->db_close();
echo "OK";
?>