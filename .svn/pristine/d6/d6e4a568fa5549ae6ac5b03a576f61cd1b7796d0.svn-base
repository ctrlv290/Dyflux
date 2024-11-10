<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 회수 팝업 페이지
 */
//Page Info
$pageMenuIdx = 307;
//Init
include_once "../_init_.php";

$C_Order = new Order();
$C_CS = new CS();

$order_idx              = $_GET["order_pack_idx"];
$cs_status              = $_GET["cs_status"];
//$order_pack_idx         = $_POST["order_pack_idx"];
$_order                 = $C_CS -> getOrderDetail($order_idx);

if(!$_order){
	//header('HTTP/1.1 500 Internal Server Error');
	//header('Content-Type: text/html; charset=UTF-8');
	//die("Error");
}else{
	extract($_order);
}

$send_name = $_order["receive_name"];
$send_tel_num = $_order["receive_tp_num"];
$send_hp_num = $_order["receive_hp_num"];
$send_zipcode = $_order["receive_zipcode"];
$send_address = $_order["receive_addr1"];
if($_order["receive_addr2"]){
	$send_address .= " " . $_order["receive_addr2"];
}
$send_tel_num1 = "";
$send_tel_num2 = "";
$send_tel_num3 = "";
$send_hp_num1 = "";
$send_hp_num2 = "";
$send_hp_num3 = "";

$send_tel_num_ary = explode("-", add_hyphen(str_replace("-", "", $send_tel_num)));
$send_tel_num1 = $send_tel_num_ary[0];
$send_tel_num2 = $send_tel_num_ary[1];
$send_tel_num3 = $send_tel_num_ary[2];

$send_hp_num_ary = explode("-", add_hyphen(str_replace("-", "", $send_hp_num)));
$send_hp_num1 = $send_hp_num_ary[0];
$send_hp_num2 = $send_hp_num_ary[1];
$send_hp_num3 = $send_hp_num_ary[2];
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup cs_order_return_popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormReturn" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="order_idx" id="return_order_idx" value="<?php echo $order_idx?>" />
				<input type="hidden" name="order_pack_idx" id="return_order_pack_idx" value="<?php echo $order_pack_idx?>" />
			</form>
			<table class="tb_wrap">
				<colgroup>
					<col width="100">
					<col width="*">
					<col width="100">
				</colgroup>
				<tbody>
				<tr>
					<th>관리번호</th>
					<td class="text_left"><?=$order_idx?></td>
				</tr>
				</tbody>
			</table>
			<div class="tb_wrap">
				<table>
					<tr>
						<td class="text_left">
							<label>
							재고회수 된 상품은 입고예정으로 등록됩니다.
							</label>
						</td>
					</tr>
				</table>
			</div>
			<div class="tb_wrap grid_tb">
				<table id="grid_return_order_list">
				</table>
				<div id="grid_return_order_pager"></div>
            </div>
            <div class="tb_wrap grid_tb">
                <table id="grid_return_call_list">
                </table>
                <div id="grid_return_call_pager"></div>
            </div>
			<div class="tb_wrap">
                <table>
                    <colgroup>
                        <col width="120">
                        <col width="*">
                    </colgroup>
                    <tbody>
                    <tr>
                        <th>CS</th>
                        <td class="text_left">
                            <textarea name="cs_msg" class="w100per h100px commonCsContent"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <a href="javascript:;" id="btn-stock-return-insert" class="large_btn blue_btn">재고회수</a>
                            <a href="javascript:self.close();" class="large_btn red_btn">취소</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
			</div>
		</div>
	</div>
</div>
<script>
	jqgridDefaultSetting = false;
</script>
<script src="/js/main.js"></script>
<script src="/js/page/cs.cs.js?v=200518"></script>
<script>
	CSPopup.CSPopupStockReturnInit(<?=$order_idx?>,'<?=$cs_status?>');
</script>

<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

