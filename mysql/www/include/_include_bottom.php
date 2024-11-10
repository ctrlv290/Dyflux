</div>

<div class="loading_dimmer">
	<div class="lds-css ng-scope">
<!--		<div style="width:100%;height:100%" class="lds-wedges">-->
<!--			<div>-->
<!--				<div>-->
<!--					<div></div>-->
<!--				</div>-->
<!--				<div>-->
<!--					<div></div>-->
<!--				</div>-->
<!--				<div>-->
<!--					<div></div>-->
<!--				</div>-->
<!--				<div>-->
<!--					<div></div>-->
<!--				</div>-->
<!--			</div>-->
<!--		</div>-->

		<div class="lds-roller"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
	</div>
</div>
<?php
if($_SERVER["SCRIPT_NAME"] != "/login.php" && $GL_dev_ing){
	include_once DY_INCLUDE_PATH."/_debug_console.php";
}
?>

<script>
<?php
if($pageMenuIdx){
	echo 'var pageMenuIdx = '.$pageMenuIdx . PHP_EOL;
?>

	if(!$("body>.wrap").hasClass("popup")) {
		var lpm_idx = $.cookie("last_page_menu_idx");
		var lpm_ary = new Array();
		var lpm_ary_result = new Array();
		if (typeof lpm_idx == undefined || typeof lpm_idx == "undefined" || lpm_idx == null || lpm_idx == "") {
			lpm_ary_result.push(pageMenuIdx);
		} else {
			lpm_ary = lpm_idx.split('|');

			$.each(lpm_ary, function (i, o) {
				var o_ary = o.split('^');
				if (o_ary[0] != pageMenuIdx) {
					lpm_ary_result.push(o);
				}
			});
			lpm_ary_result.push(pageMenuIdx  + '^' + moment().unix());

			if (lpm_ary_result.length > 10) {
				while (true) {
					lpm_ary_result.shift();
					if (lpm_ary_result.length == 10) {
						break;
					}
				}
			}
		}
		var lpm_idx_return = lpm_ary_result.join('|');
		$.cookie("last_page_menu_idx", lpm_idx_return, {path: "/", expires: 30});
	}
<?php
}
?>
</script>
<iframe src="about:blank" id="hidden_ifrm_common_filedownload" name="hidden_ifrm_common_filedownload" frameborder="0" style="width: 0;height: 0;display: none;"></iframe>
</body>
</html>