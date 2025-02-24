<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 자금일보 입금/출금 팝업 페이지
 */
//Page Info
$pageMenuIdx = 242;
//Init
include_once "../_init_.php";


$mode = $_GET["mode"];
$tran_date = $_GET["tran_date"];
$tran_type = $_GET["tran_type"];
$tran_inout = $_GET["tran_inout"];
$target_idx = $_GET["target_idx"];
$tran_idx = $_GET["tran_idx"] ? $_GET["tran_idx"] : 0;

//창 타이틀 설정
switch ($tran_type){
	case "CASH_IN" :
		$pageMenuIdx = 243;
		break;
	case "CASH_OUT" :
		$pageMenuIdx = 244;
		break;
    case "CASH_CUSTOMER_IN" :
        $pageMenuIdx = 309;
        break;
    case "CASH_CUSTOMER_OUT" :
        $pageMenuIdx = 310;
        break;
	case "BANK_CUSTOMER_IN" :
		$pageMenuIdx = 245;
		break;
	case "BANK_CUSTOMER_OUT" :
		$pageMenuIdx = 246;
		break;
	case "BANK_ETC_IN" :
		$pageMenuIdx = 247;
		break;
	case "BANK_ETC_OUT" :
		$pageMenuIdx = 248;
		break;
	case "TRANSFER_IN" :
		$pageMenuIdx = 249;
		break;
	case "TRANSFER_OUT" :
		$pageMenuIdx = 250;
		break;
}

$use_target = false;
$is_card = false;

if($tran_type == "BANK_CUSTOMER_IN" || $tran_type == "BANK_CUSTOMER_OUT" || $tran_type == "CASH_CUSTOMER_IN" || $tran_type == "CASH_CUSTOMER_OUT"){
    if($tran_type == "BANK_CUSTOMER_IN" || $tran_type == "CASH_CUSTOMER_IN" ){
        $seller_cal = 1;
        $supplier_cal = -1;
    } else if($tran_type == "BANK_CUSTOMER_OUT" || $tran_type == "CASH_CUSTOMER_OUT"){
        $seller_cal = -1;
        $supplier_cal = 1;
    }
	$use_target = true;
	$C_Seller = new Seller();
    $C_Supplier = new Supplier();
	$seller_target_list = $C_Seller->getSellerList();
    $supplier_target_list = $C_Supplier->getUseSupplierList();
    $seller_select_option ='';
    $supplier_select_option = '';
    foreach($seller_target_list as $t) {

        $_target_idx = $t["seller_idx"];
        $_target_name = $t["seller_name"];

        $seller_select_option .= '<option value="' . $_target_idx . '">' . $_target_name . '</option>';
    }

    foreach($supplier_target_list as $t) {

        $_target_idx = $t["member_idx"];
        $_target_name = $t["supplier_name"];

        $supplier_select_option .= '<option value="' . $_target_idx . '">' . $_target_name . '</option>';
    }

}elseif($tran_type == "CARD_OUT"){
    $is_card = true;
}

$C_Report = new Report();

$_account_list = $C_Report->getAccountCodeList($tran_inout);

if($mode == "update"){
    $C_Report = new Report();
	$_list = $C_Report->getReportDataByDate($tran_date, $tran_type, $tran_inout, "day", "N");

	if (count($_list) && $tran_idx != 0) {
		$list_temp = array();

		foreach ($_list as $row) {
			if ($row["tran_idx"] == $tran_idx) {
				$list_temp[] = $row;
				break;
			}
		}

		$_list = $list_temp;
	}

    $list_type = array();
    foreach ($_list as $type) {
        $get_type = $C_Report->findSellerSupplierUnionType($type["target_idx"]);
        $list_type = array_merge($list_type, $get_type);
    }
}
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<style>
				.search-txt {ime-mode: active !important;}
				label {cursor: pointer;}
			</style>
			<form name="dyFormPop" id="dyFormPop" method="post" class="<?=$mode?>" action="report_proc.php">
				<input type="hidden" name="mode" value="<?=$mode?>" />
				<!--<input type="hidden" name="tran_date" value="<?=$tran_date?>" />-->
				<input type="hidden" name="tran_type" value="<?=$tran_type?>" />
				<input type="hidden" name="tran_inout" value="<?=$tran_inout?>" />
				<div class="tb_wrap" style="height: 100%;overflow-x: visible !important;overflow-y: visible;">
					<?php if($tran_type == "TRANSFER_IN" || $tran_type == "TRANSFER_OUT") {?>
					<p>
						<label><input type="checkbox" name="tran_is_sync" value="Y"/>계좌간이체 <?=($tran_type == "TRANSFER_IN") ? "지출" : "수입"?>에도 등록</label>
					</p>
					<?php } ?>
					<table>
						<colgroup>
							<?php if($is_card == true){ ?>
							<col width="180">
							<col width="180">
							<col width="180">
							<?php } ?>
							<col width="220">
							<?php if($use_target){ ?>
							<col width="220">
                            <col width="220">
							<?php } ?>
							<col width="100">
                            <col width="150">
                            <col width="*">
						</colgroup>
						<thead>
						<tr>
							<?php if($is_card == true){ ?>
							<th>사용자</th>
							<th>카드번호</th>
							<th>지출처</th>
							<?php } ?>
							<th>계정과목 <a href="javascript:;" class="xsmall_btn btn-account-sync">일괄선택</a></th>
							<?php if($use_target){ ?>
                            <th>판매처</th>
                            <th>공급처</th>
                            <?php }?>
							<th>일자</th>
                            <th>금액</th>
							<th>적요</th>
						</tr>
						</thead>
						<tbody>
						<?php
						$limit = 10;
						if($mode == "update"){
							$limit = count($_list);
						}
						for($i=0;$i<$limit;$i++){
							$row = "";
							if($mode == "update") {
								$row = $_list[$i];
							}

							if($row["tran_date"]){
								$tran_date = $row["tran_date"];
							}

							//계좌간이체 수입/지출 등록 일 경우
							if($mode == "add"){
								if($tran_type == "TRANSFER_IN"){
									$row = array();
									$row["account_idx"] = "75"; //수입-계좌간이체
								}elseif($tran_type == "TRANSFER_OUT"){
									$row = array();
									$row["account_idx"] = "7";  //지출-계좌간이체
								}
							}
						?>
						<tr>
							<?php if($is_card == true){ ?>
								<td><input type="text" name="tran_user[]" class="w100per" value="<?=$row["tran_user"]?>" /></td>
								<td><input type="text" name="tran_card_no[]" class="w100per" value="<?=$row["tran_card_no"]?>" /></td>
								<td><input type="text" name="tran_purpose[]" class="w100per" value="<?=$row["tran_purpose"]?>" /></td>
							<?php } ?>
							<td class="text_left">
								<?php
								if($mode == "update") {
									echo '<input type="hidden" name="tran_idx[]" value="'.$row["tran_idx"].'" />';
								}
								?>

								<select name="account_idx[]" class="sel-account-idx">
									<?php
									if($mode == "add") {
										echo '<option value="">계정과목을 선택해주세요.</option>';
									}
									?>
									<?php
									foreach($_account_list as $ac){
										$selected = ($row["account_idx"] == $ac["account_idx"]) ? "selected" : "";
										echo '<option value="'.$ac["account_idx"].'" '.$selected.'>'.$ac["account_name"].'</option>';
									}
									?>
								</select>
							</td>
							<?php if($use_target){ ?>
                                <?php
                                if($mode == "add") { ?>
                                <input type="hidden" name="target_cal[]" class="target_cal" value="" />
                                <span>
                                    <td class="text_left">
                                        <select name="target_idx[]" class="sel-seller-target-idx">
                                            <?php
                                                echo '<option value="">판매처를 선택해주세요.</option>';
                                                echo $seller_select_option
                                            ?>
                                        </select>
                                    </td>
                                </span>
                                <span>
                                    <td class="text_left">
                                        <select name="" class="sel-supplier-target-idx">
                                            <?php
                                                echo '<option value="">공급처를 선택해주세요.</option>';
                                                echo $supplier_select_option
                                            ?>
                                        </select>
                                    </td>
                                </span>

                                <?php } ?>
                                <?php if($mode == "update") { ?>
                                    <?php if($list_type[$i]["type"] == 'seller') {?>
                                        <input type="hidden" name="target_cal[]" class="target_cal" value="<?=$seller_cal?>" />
                                        <span>
                                         <td class="text_left">
                                        <select name="target_idx[]" class="sel-seller-target-idx">
                                        <?php
                                            echo '<option value="">판매처를 선택해주세요.</option>';
                                            foreach($seller_target_list as $t) {

                                                $_target_idx = $t["seller_idx"];
                                                $_target_name = $t["seller_name"];

                                                if ($row["target_idx"]) {
                                                    $selected = ($row["target_idx"] == $_target_idx) ? "selected" : "";
                                                } else {
                                                    if ($target_idx) {
                                                        $selected = ($target_idx == $_target_idx) ? "selected" : "";
                                                    }
                                                }
                                                echo '<option value="' . $_target_idx . '" ' . $selected . '>' . $_target_name . '</option>';
                                            } ?>
                                        </span>
                                        <span>
                                            <td class="text_left">
                                            <select name="" class="sel-supplier-target-idx">
                                                <?php
                                                echo '<option value="">공급처를 선택해주세요.</option>';
                                                echo $supplier_select_option
                                                ?>
                                            </select>
                                             </td>
                                        </span>
                                        <?php
                                    } elseif ($list_type[$i]["type"] == 'supplier') { ?>
                                        <input type="hidden" name="target_cal[]" class="target_cal" value="<?=$supplier_cal?>" />
                                        <span>
                                        <td class="text_left">
                                            <select name="" class="sel-seller-target-idx">
                                                <?php
                                                echo '<option value="">판매처를 선택해주세요.</option>';
                                                echo $seller_select_option
                                                ?>
                                            </select>
                                        </td>
                                        </span>
                                        <span>
                                        <td class="text_left">
                                        <select name="target_idx[]" class="sel-supplier-target-idx">
                                        <?php
                                            echo '<option value="">공급처를 선택해주세요.</option>';
                                            foreach($supplier_target_list as $t) {

                                                $_target_idx = $t["member_idx"];
                                                $_target_name = $t["supplier_name"];

                                                if ($row["target_idx"]) {
                                                    $selected = ($row["target_idx"] == $_target_idx) ? "selected" : "";
                                                } else {
                                                    if ($target_idx) {
                                                        $selected = ($target_idx == $_target_idx) ? "selected" : "";
                                                    }
                                                }
                                                echo '<option value="' . $_target_idx . '" ' . $selected . '>' . $_target_name . '</option>';
                                            }
                                        } ?>
                                        </select>
                                        </td>
                                    </span>
                                <?php } ?>
							<?php } ?>
							<td><input type="text" name="tran_date[]" class="w100per jqDate" value="<?=$tran_date?>" readonly="readonly" /></td>
                            <?php if($mode == "update") { ?>
							<td><input type="text" name="tran_amount[]" class="w100per money" value="<?= abs($row["tran_amount"])?>" /></td>
                            <?php } else { ?>
                            <td><input type="text" name="tran_amount[]" class="w100per money" value="<?= $row["tran_amount"]?>" /></td>
                            <?php } ?>
                            <td><input type="text" name="tran_memo[]" class="w100per" value="<?=$row["tran_memo"]?>" /></td>
						</tr>
						<?php
						}
						?>
						<tr>
							<?php if($is_card == true){ ?>
							<th></th>
							<th></th>
							<th></th>
							<?php } ?>
							<th></th>
							<?php if($use_target){ ?>
                            <th></th>
                            <th></th>
							<?php } ?>
                            <th class="w100per">합계</th>
                            <th class="w100per text_right"><span class="money_total">0</span></th>
							<th></th>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save-pop" class="large_btn blue_btn  ">저장</a>
						<a href="javascript:self.close();" class="large_btn red_btn btn-common-pop-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/settle.report.js?v=200526"></script>
<script>
	SettleReport.ReportPopInit();

	$('.money').keyup(function(){
		calculateTotal();
	});

	function calculateTotal() {
		var total = 0;
		
		$(".money").each(function(){
			var val = $(this).val().replace(/,/gi, '');
			if (val == "") {
				val = 0;
			}

			total += Number(val);
		});

		$('.money_total').text(Common.addCommas(total));
	}

	window.onload = function () {
		calculateTotal();
	};

	var flag = 0;
    $(".sel-seller-target-idx").on("change", function(){
        if (flag) {
            flag = 0;
            return;
        }
        flag = 1;
        var target_sel = $(".sel-seller-target-idx").index(this);
        $(".sel-seller-target-idx").eq(target_sel).attr("name", "target_idx[]");
        $(".sel-supplier-target-idx").eq(target_sel)[0].sumo.selectItem (0);
        $(".sel-supplier-target-idx").eq(target_sel).attr("name", "");
        $(".target_cal").eq(target_sel).val(<?= $seller_cal?>);
        flag = 0;
    });
    $(".sel-supplier-target-idx").on("change", function(){
        if (flag) {
            flag = 0;
            return;
        }
        flag = 1;
        var target_sel = $(".sel-supplier-target-idx").index(this);
        $(".sel-supplier-target-idx").eq(target_sel).attr("name", "target_idx[]");
        $(".sel-seller-target-idx").eq(target_sel)[0].sumo.selectItem (0);
        $(".sel-seller-target-idx").eq(target_sel).attr("name", "");
        $(".target_cal").eq(target_sel).val(<?= $supplier_cal?>);
        flag = 0;
    });

</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

