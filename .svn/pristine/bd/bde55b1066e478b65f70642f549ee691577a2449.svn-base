<?php
// DB 접속
include_once "../_init_.php";
include_once $GL_class_path . 'Dbconn.php';
$C_db = new Dbconn();
$C_db -> db_connect();

$serverName = "192.168.1.16";
$connectionInfo = array( "Database"=>"DYFLUX", "UID"=>"sa", "PWD"=>"d!@#y!@#");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

$target_table_name = "DY_ORDER_GIFT";          //트리거 대상 테이블 명
$target_table_idx_column = "gift_idx";   //트리거 대상 테이블의 PK
$history_table_name = "DY_ORDER_GIFT_HISTORY"; //로그를 삽입할 HISTORY 테이블 명

$qry = "
		SELECT 
		        c.Name AS Field_Name,
		        t.Name AS Data_Type,
		        t.max_length AS Length_Size,
		        t.precision AS Precision,
		        cast(p.value as varchar(4000)) As 'Desc'
		FROM sys.columns c 
		     INNER JOIN sys.objects o ON o.object_id = c.object_id
		     LEFT JOIN  sys.types t on t.user_type_id  = c.user_type_id   
		     LEFT OUTER JOIN sys.extended_properties p on p.major_id = o.object_id and p.minor_id  = c.column_id and p.name = 'MS_Description' 
		WHERE o.type = 'U'
		And o.Name = '$target_table_name'
		ORDER BY o.Name, c.column_id asc
	";

$fields = $C_db->execSqlList($qry);


//print_r2($fields);



echo "

============ UPDATE =============

";

foreach($fields as $row)
{
	$column_name = $row["Field_Name"];
	$column_desc = $row["Desc"];

	echo "
		INSERT INTO $history_table_name ([table_nm],[table_idx1],[column_mn],[before_data],[after_data],[member_idx],[dml_flag],[memo]) 
		(
		SELECT '$target_table_name', I.[$target_table_idx_column],'$column_name', D.$column_name, I.$column_name, I.[gift_modidx], 'U', '$column_desc' 
		FROM inserted I, deleted D 
		WHERE I.[$target_table_idx_column] = D.[$target_table_idx_column] AND I.[$column_name] <> D.[$column_name]
		);
	";


}

echo "

============ INSERT =============

";

foreach($fields as $row) {

	$column_name = $row["Field_Name"];
	$column_desc = $row["Desc"];

	echo "
		INSERT INTO $history_table_name ([table_nm],[table_idx1],[column_mn],[before_data],[after_data],[member_idx],[dml_flag],[memo]) 
		(SELECT '$target_table_name', I.[$target_table_idx_column], '$column_name', '', I.[$column_name], I.[last_member_idx], 'I','$column_desc' FROM inserted I WHERE ISNULL(I.[$column_name], '') <> '' ); 
	";
}

echo "

============ DELETE =============

";

foreach($fields as $row) {

	$column_name = $row["Field_Name"];
	$column_desc = $row["Desc"];

	echo "
		INSERT INTO $history_table_name ([table_nm],[table_idx1],[column_mn],[before_data],[after_data],[member_idx],[dml_flag],[memo])
		(SELECT '$target_table_name', [$target_table_idx_column],'$column_name',D.[$column_name],'', D.[last_member_idx], 'D','$column_desc' FROM deleted D WHERE ISNULL(D.[$column_name], '') <> '' ); 
	";
}

?>