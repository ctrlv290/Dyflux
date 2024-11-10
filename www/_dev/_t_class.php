<?php
// DB 접속
include_once "../_init_.php";
include_once $GL_class_path . 'Dbconn.php';
$C_db = new Dbconn();
$C_db -> db_connect();

$serverName = "192.168.1.16";
$connectionInfo = array( "Database"=>"DYFLUX", "UID"=>"sa", "PWD"=>"d!@#y!@#");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

// 파라미터
$db_value 	= $_GET['db_name'];
$tb_value 	= $_GET['tb_name'];
$type 			= $_GET['type'];

// Common Select Box
function commSel($keyArr,$selName,$selectedVal,$option,$selDefault)
{
	$selString = "<select name='$selName' $option>\n";
	if($selDefault == 0) $selString .= "<option value=''>:: Select ::</option>\n";
	if(count($keyArr)>0)
	{
		foreach ($keyArr as $key => $value)
		{
			$selected = ($selectedVal == $key)? "selected" : "";
			$selString .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>' . "\n";
		}
	}
	$selString .= "</select>\n";

	return $selString;
}

// DB list 가져오기
$db_name = "DYFLUX";

// Table list 가져오기
$tb_nameArr = array();
$i = 1;
if($db_name)
{
	$sql = "select table_name from information_schema.tables Order by table_name ASC";
	$rst = $C_db->execSqlList($sql);

	foreach($rst as $item)
	{
		$tb_nameArr[$i++] = $item["table_name"];
	}
}

$tb_nameSel = commSel($tb_nameArr,"tb_name",$tb_value,"onchange='reLocation()'",0);
$tb_name = $tb_nameArr[$tb_value];

?>
<script>
	function reLocation(type)
	{
		//var db_name = document.all.db_name.value;
		var db_name = "DYFLUX";
		var tb_name = document.all.tb_name.value;

		if(type == undefined || type == '' ) type = '<?=$type?>';
		if(type == '') type = "classes";
		location.href="<?=$GL_page_nm?>?db_name="+db_name+"&tb_name="+tb_name+"&type="+type;

	}
</script>
<?=$db_nameSel?>
<?=$tb_nameSel?><br>
<a href="javascript:reLocation('classes')">[CLASSES]</a>
<a href="javascript:reLocation('query')">[QUERY]</a>
<br>
<style>
	textarea {height: 400px;}
</style>
<?php
if($tb_name)
{
	$qry = "
		SELECT 
		        c.Name AS Field_Name,
		        t.Name AS Data_Type,
		        t.max_length AS Length_Size,
		        t.precision AS Precision
		FROM sys.columns c 
		     INNER JOIN sys.objects o ON o.object_id = c.object_id
		     LEFT JOIN  sys.types t on t.user_type_id  = c.user_type_id   
		WHERE o.type = 'U'
		And o.Name = '$tb_name'
		ORDER BY o.Name, c.column_id asc
	";

	$fields = $C_db->execSqlList($qry);
	$columns = count($fields);

	function Classes($fields,$columns,$tb_name)
	{
		echo ("<br><B><font color='red'>CLASSES</font></B><br><table width='100%' border='0'>
            <tr><td width='34%'>
            <textarea style='width:100%;border:1 solid #CCCCCC;overflow:visible;text-overflow:ellipsis;' onfocus='this.select()'>");
		ClassesEntity($fields,$columns,$tb_name);
		echo ("</textarea></td></tr>");
		echo ("</table>");
	}

	function Querys($fields,$columns,$tb_name)
	{
		// SELECT
		echo ("<br><B><font color='red'>SELECT QUERY</font></B><br><table width='100%' border='0'>
            <tr><td width='34%'>
            <textarea style='width:100%;border:1 solid #CCCCCC;overflow:visible;text-overflow:ellipsis;' onfocus='this.select()'>");
		echo SelectQuery($fields,$columns,$tb_name);
		echo ("</textarea></td></tr>");
		echo ("</table>");

		// INSERT
		echo ("<br><B><font color='red'>INSERT QUERY</font></B><br><table width='100%' border='0'>
            <tr><td width='34%'>
            <textarea style='width:100%;border:1 solid #CCCCCC;overflow:visible;text-overflow:ellipsis;' onfocus='this.select()'>");
		echo InsertQuery($fields,$columns,$tb_name);
		echo ("</textarea></td></tr>");
		echo ("</table>");

		// UPDATE
		echo ("<br><B><font color='red'>UPDATE QUERY</font></B><br><table width='100%' border='0'>
            <tr><td width='34%'>
            <textarea style='width:100%;border:1 solid #CCCCCC;overflow:visible;text-overflow:ellipsis;' onfocus='this.select()'>");
		echo UpdateQuery($fields,$columns,$tb_name);
		echo ("</textarea></td></tr>");
		echo ("</table>");

		// DELETE
		echo ("<br><B><font color='red'>DELETE QUERY</font></B><br><table width='100%' border='0'>
            <tr><td width='34%'>
            <textarea style='width:100%;border:1 solid #CCCCCC;overflow:visible;text-overflow:ellipsis;' onfocus='this.select()'>");
		echo DeleteQuery($fields,$columns,$tb_name);
		echo ("</textarea></td></tr>");
		echo ("</table>");

	}

	$tab = '     ';
	$tab = '	';
	function SelectQuery($fields,$columns,$tb_name)
	{
		global $tab;
		$str = "\n" . $tab . $tab . $tab . "SELECT \n";
		$str .= $tab . $tab . $tab .$tab .  "* \n";
		$str .= $tab . $tab . $tab . "FROM\n    ";
		$str .= $tab . $tab . $tab . "$tb_name \n";
		$str .= $tab . $tab . $tab . "\$where";

		return $str;
	}

	function InsertQuery($fields,$columns,$tb_name)
	{
		global $tab;
		$str = "\n" . $tab . $tab . $tab . "INSERT INTO \n" . $tab . $tab . $tab . "$tb_name\n" . $tab . $tab . $tab . "(\n";
		for ($i = 0; $i < $columns; $i++) {
			$filesName = $fields[$i]["Field_Name"];
			$bar = ", ";

			$str .= ($i != ($columns-1))?  $tab . $tab . $tab . $filesName . $bar . " \n" :  $tab . $tab . $tab . $filesName . " \n";
		}
		$str .= "" . $tab . $tab . $tab . ") \n" . $tab . $tab . $tab . "VALUES\n" . $tab . $tab . $tab . "(\n";

		for ($i = 0; $i < $columns; $i++) {
			$filesName = $fields[$i]["Field_Name"];
			$filesType = $fields[$i]["Data_Type"];

			$bind = "'";
			//if($filesType != "int") $bind = "'";
			$bar = ", ";

			if($i == 0)
			{
				$filesName = "";
				$bar = "";
			}

			if($filesType == "date")
				$str .= $tab . $tab . $tab . $bar . " NOW()\n";
			else
			{
				if($i == 0)
					$str .= $tab . $tab . $tab . $bar . "$bind" . $filesName . "$bind \n" ;
				else
					$str .= $tab . $tab . $tab . $bar . "N$bind$" . $filesName . "$bind \n" ;
			}
		}
		$str .= $tab . $tab . $tab ."\n" . $tab . $tab . $tab . ")";

		return $str;
	}

	function UpdateQuery($fields,$columns,$tb_name)
	{
		global $tab;
		$str = "\n";
		$str .= $tab . $tab . $tab . "UPDATE $tb_name SET \n";
		for ($i = 0; $i < $columns; $i++) {
			$filesName = $fields[$i]["Field_Name"];
			$filesType = $fields[$i]["Data_Type"];

			$bind = "'";
			//if($filesType != "int") $bind = "'";
			$bar = ($i == 0 || $i == 1)? "":", ";
			if($i)
			{
				if($filesType == "date" || $filesType == "datetime")
					$str .= "";
				else
					$str .=  $tab . $tab . $tab . $bar . $filesName . " = $bind$" . $filesName . "$bind \n";
			}
		}
		$str .= $tab . $tab . $tab . "WHERE ".$fields[$i]["Field_Name"]." = $".$fields[$i]["Field_Name"];

		return $str;
	}

	function DeleteQuery($fields,$columns,$tb_name)
	{
		$str = "DELETE FROM $tb_name";
		$str .= " WHERE ".$fields[0]["Field_Name"]." = '$".$fields[0]["Field_Name"]."'";

		return $str;
	}

	function ClassesEntity($fields,$columns,$tb_name)
	{
		global $tab;
		$str = "CLASS {$tb_name} extends Dbconn \n";
		$str .= "{ \n";

		// insert function
		$str .= $tab . "function {$tb_name}Insert(\$args) \n";
		$str .= $tab . "{ \n";
		$str .= $tab . "\n";
		for ($i = 0; $i < $columns; $i++) {
			$filesName = $fields[$i]["Field_Name"];
			$filesType = $fields[$i]["Data_Type"];

			$str .= $tab . "\$" . $filesName . " = \"\";\n";
		}
		$str .= $tab . "\n";
		$str .= $tab . $tab . "extract(\$args); \n";
		$str .= $tab . $tab . "\$qry = \"".InsertQuery($fields,$columns,$tb_name)."\";\n";
		$str .= $tab . $tab . "parent::db_connect();\n";
		$str .= $tab . $tab . "\$rst = parent::execSqlInsert(\$qry);\n";
		$str .= $tab . $tab . "parent::db_close();\n";
		$str .= $tab . $tab . "return \$rst;\n";
		$str .= $tab . "} \n\n";

		// modify function
		$str .= $tab . "function {$tb_name}Modify(\$args) \n";
		$str .= $tab . "{ \n";
		$str .= $tab . $tab . "extract(\$args); \n";
		$str .= $tab . $tab . "\$qry = \"".UpdateQuery($fields,$columns,$tb_name)."\";\n";
		$str .= $tab . $tab . "parent::db_connect();\n";
		$str .= $tab . $tab . "\$rst = parent::execSqlUpdate(\$qry);\n";
		$str .= $tab . $tab . "parent::db_close();\n";
		$str .= $tab . $tab . "return \$rst;\n";
		$str .= $tab . "} \n\n";

		// select function
		$str .= $tab . "function {$tb_name}Select(\$args) \n";
		$str .= $tab . "{ \n";
		$str .= $tab . $tab . "extract(\$args); \n";
		$str .= $tab . $tab . "\$qry = \"".SelectQuery($fields,$columns,$tb_name)."\";\n";
		$str .= $tab . $tab . "parent::db_connect();\n";
		$str .= $tab . $tab . "switch (\$type) {\n";
		$str .= $tab . $tab . $tab . "case 'one_row' :\n";
		$str .= $tab . $tab . $tab . $tab . "\$rst = parent::execSqlOneRow(\$qry);\n";
		$str .= $tab . $tab . $tab . "break;\n";
		$str .= $tab . $tab . $tab . "default :\n";
		$str .= $tab . $tab . $tab . $tab . "\$rst = parent::execSqlList(\$qry);\n";
		$str .= $tab . $tab . $tab . "break;\n";
		$str .= $tab . $tab . "}\n";
		$str .= $tab . $tab . "parent::db_close();\n";
		$str .= $tab . $tab . "return \$rst;\n";
		$str .= $tab . "} \n\n";

		// delete function
		$str .= $tab . "function {$tb_name}Delete(\$args) \n";
		$str .= $tab . "{ \n";
		$str .= $tab . $tab . "extract(\$args); \n";
		$str .= $tab . $tab . "\$qry = \"".DeleteQuery($fields,$columns,$tb_name)."\";\n";
		$str .= $tab . $tab . "parent::db_connect();\n";
		$str .= $tab . $tab . "\$rst = parent::execSqlUpdate(\$qry);\n";
		$str .= $tab . $tab . "parent::db_close();\n";
		$str .= $tab . $tab . "return \$rst;\n";
		$str .= $tab . "} \n";
		$str .= "}";

		echo $str;
	}

	switch ($type)
	{
		case "classes":
			Classes($fields,$columns,$tb_name);
			break;
		case "query":
			Querys($fields,$columns,$tb_name);
			break;
	}

	$C_db = "";
}
?>

foreach ($_POST as $name=>$value)<br>
{<br>
$$name = $value;<br>
}<br>
foreach ($_GET as $name=>$value)<br>
{<br>
$$name = $value;<br>
}<br>

<?php
foreach($fields as $col)
{
	echo "$" . $col["Field_Name"] . " = \$_POST[\"" . $col["Field_Name"] . "\"]; <br>";
}
?>
<br><br>
<?php
echo "\$args = array();<br>";
foreach($fields as $col)
{
	echo "\$args[\"" . $col["Field_Name"] . "\"] = \$" . $col["Field_Name"] . "; <br>";
}
?>
