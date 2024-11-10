<?php
if (DY_DEV_ING) {
	?>
	<link rel="stylesheet" href="<?=DY_URL?>/css/dev.css" />
	<script type="text/javascript">
		//<![CDATA[
		var dev_box = false;
		$(function(){
			$(".dev_toggle_btn").click(function(){
				if(!dev_box)
				{
					devBoxOpen();
				}else{
					devBoxClose();
				}
			});

			$(".dev_box_fix_btn").click(function(){
				var tp = $(this).data("type");

				if(tp != "")
				{
					devBoxOpen();
					if(tp == 9)
					{
						window.open("<?=DY_MYSQL_ADMIN?>");
					}else{
						$(".div_console").addClass("hide");
						$(".div_console").eq((tp-1)).removeClass("hide");
					}
				}
			});

			function devBoxOpen()
			{
				dev_box = true;
				$(".dev_toggle_btn").text("닫기");
				$("#dev_box").stop().animate({height:'300px'});
			}

			function devBoxClose()
			{
				dev_box = false;
				$(".dev_toggle_btn").text("열기");
				$("#dev_box").stop().animate({height:'30px'});
			}
		});
		//]]>
	</script>
	<div id="dev_box" class="console">
		<div class="fix">
			<span class="dev_box_fix_btn" data-type="1">Const WBOARD</span>
			<span class="dev_box_fix_btn" >|</span>
			<span class="dev_box_fix_btn" data-type="2">Global WBOARD</span>
			<span class="dev_box_fix_btn" >|</span>
			<span class="dev_box_fix_btn" data-type="3">Member Info</span>
			<span class="dev_box_fix_btn" >|</span>
			<span class="dev_box_fix_btn" data-type="4">Board Info</span>
			<span class="dev_box_fix_btn" >|</span>
			<span class="dev_box_fix_btn" data-type="5">Session Info</span>
			<span class="dev_box_fix_btn" >|</span>
			<span class="dev_box_fix_btn" data-type="6">POST Info</span>
			<span class="dev_box_fix_btn" >|</span>
			<span class="dev_box_fix_btn" data-type="7">GET Info</span>
			<span class="dev_box_fix_btn" >|</span>
			<span class="dev_box_fix_btn" data-type="8">Query Log</span>
					</div>
		<div class="div_console" id="div_console">
			<?=$_const_info;?>
		</div>
		<div class="div_console" id="div_console2 hide">
			<?=_show_global();?>
		</div>
		<div class="div_console" id="div_console3 hide">
			<?print_r2($member);?>
		</div>
		<div class="div_console" id="div_console4 hide">
			<?print_r2($board);?>
		</div>
		<div class="div_console" id="div_console5 hide">
			<?print_r2($_SESSION);?>
		</div>
		<div class="div_console" id="div_console6 hide">
			<?print_r2($_POST);?>
		</div>
		<div class="div_console" id="div_console7 hide">
			<?print_r2($_GET);?>
		</div>
		<div class="div_console hide" id="div_console8 hide">
			<table id="query_log_table" border="1" width="100%">
				<?=$_dev_query_table?>
			</table>
		</div>


	</div>
	<?php
}
?>