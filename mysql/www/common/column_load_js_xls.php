<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 개인화 항목설정 js 형태로 불러오기
 */
//Init
include_once "../_init_.php";

//사용자 항목 설정 가져오기
$C_ColumnModel = new ColumnModel();
$userColumnList = $C_ColumnModel -> getUserColumnXls($_GET["target"], $GL_Member["member_idx"]);
?>
var _gridColModel = [];
var user_column_list = [];

<?php
foreach($userColumnList as $col)
{
	echo '
			var tmp = {
				"is_use" : ' . (($col["col_user_is_use"] == "Y") ? 'true' : 'false') . ',
				"column_name" : "' . $col["col_field_name"] . '",
				"visible_name" : "' . $col["col_user_visible_name"] . '",
			};
			user_column_list.push(tmp);
		';
}
?>

if(user_column_list.length > 0)
{
	//존재하는 항목 삽입
	$.each(user_column_list, function(i, o){
		var tmp  = default_list.filter(function(col) { return col.name == o.column_name });
		if(tmp.length == 1)
		{
			var is_readonly = false;
			try {
				if(typeof tmp[0].is_readonly != "undefined"){
					is_readonly = tmp[0].is_readonly;
				}
			}catch(e){}

			var row = tmp[0];
			if(!is_readonly){
				row.label = o.visible_name;
			}
			if(o.is_use)
			{
				_gridColModel.push(row);
			}
		}
	});

	//존재하지 않는 항목 체크
	$.each(default_list, function(i, o){
		var tmp  = user_column_list.filter(function(col) { return col.column_name == o.name });
		if(tmp.length ==0)
		{
			_gridColModel.push(o);
		}
	});
}else {
	_gridColModel = default_list;
}