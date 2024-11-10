<?php
    include_once "../_init_.php";
    $test = $_POST;
    $C_Dbconn = new DBConn();
    $C_Dbconn -> db_connect();

    $qry = 'select * from dy_settle limit 100;';
    $result = $C_Dbconn->execSqlList($qry);
    $C_Dbconn -> db_close();
    $test = $_POST;

    echo json_encode($result,true);

?>