<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 리스트 테이블 항목 설정 창
 */

//Page Info
$pageMenuIdx = 175;
//Init
include_once "../_init_.php";

//사용자 항목 설정 가져오기
$C_ColumnModel = new ColumnModel();
$userColumnList = $C_ColumnModel -> getUserColumn($_GET["target"], $GL_Member["member_idx"]);

$xls_hide_list = array(
		"LOSS_LIST"
);

?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm" method="post">
				<input type="hidden" name="target" id="column_setting_target" value="<?=$_GET["target"]?>" />
				<input type="hidden" name="mode" id="column_setting_mode" value="<?=$_GET["mode"]?>" />
			</form>
			<div class="sub_desc_border">
				<a href="column_setting_pop.php?target=<?=$_GET["target"]?>&mode=list" class="btn <?=$_GET["mode"] == "list" ? "on" : ""?> large_btn">조회설정</a>
				<?php if(!in_array($_GET["target"], $xls_hide_list)){ ?>
				<a href="column_setting_pop_xls.php?target=<?=$_GET["target"]?>&mode=xls" class="btn <?=$_GET["mode"] == "xls" ? "on" : ""?> large_btn">엑셀설정</a>
				<?php } ?>
			</div>
			<div class="tb_wrap grid_tb">
				<table id="grid_list">
				</table>
				<div id="grid_pager"></div>
			</div>
			<div class="btn_set">
				<div class="center">
					<a href="javascript:;" id="btn-save" class="large_btn blue_btn ">저장</a>
					<a href="javascript:self.close();" class="large_btn red_btn">닫기</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/column_const.js"></script>
<script src="/js/page/common.column_setting.js"></script>

<script>
	//사용자 항목설정을 불러오기전에 초기화
	var user_column_list = [];
</script>
<script src="/common/column_load_js.php?target=<?=$_GET["target"]?>"></script>

<script>
	var default_list = columnModel.<?=$_GET["target"]?>;

	var grid_data = [];
	if(user_column_list.length > 0)
	{
		//존재하는 항목 삽입
		$.each(user_column_list, function(i, o){
			var tmp  = default_list.filter(function(col) { return col.name == o.column_name });
			if(tmp.length == 1)
			{
				is_readonly = false;
				try {
					if(typeof tmp[0].is_readonly != "undefined"){
						is_readonly = tmp[0].is_readonly;
					}
				}catch(e){}
				var tmp = {
					"is_use": o.is_use,
					"default_name": tmp[0].label,
					"visible_name": (!is_readonly) ? o.visible_name : tmp[0].label ,
					"column_name": o.column_name,
					"is_readonly": is_readonly
				};

				grid_data.push(tmp);
			}
		});

		//존재하지 않는 항목 체크
		$.each(default_list, function(i, o){

			var tmp  = user_column_list.filter(function(col) { return col.column_name == o.name });
			if(tmp.length ==0)
			{
				var tmp = {
					"is_use": default_list[i].is_use,
					"default_name": default_list[i].label,
					"visible_name": default_list[i].label,
					"column_name": default_list[i].name,
					"is_readonly" : (typeof default_list[i].is_readonly == "undefined") ? false : default_list[i].is_readonly
				};

				if(typeof default_list[i].hidden == "undefined" || default_list[i].hidden == false) {
					grid_data.push(tmp);
				}
			}

		});

	}else {
		for (var i = 0; i < default_list.length; i++) {
			var tmp = {
				"is_use": default_list[i].is_use,
				"default_name": default_list[i].label,
				"visible_name": default_list[i].label,
				"column_name": default_list[i].name,
				"is_readonly" : (typeof default_list[i].is_readonly == "undefined") ? false : default_list[i].is_readonly
			};

			if(typeof default_list[i].hidden == "undefined" || default_list[i].hidden == false) {
				grid_data.push(tmp);
			}
		}
	}

	ColumnSetting.ColumnSettingListInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

