<?php

include_once "../_init_.php";

$kindIdx = $_POST["kind_idx"] ? $_POST["kind_idx"] : 0;

$adManager = new AdvertisingManager();
$billingTypes = $adManager->getBillingTypes();

$sellerIdx = 0;
$sellerGroupIdx = 0;

$isUse = "Y";

if ($kindIdx) {
    $kindData = $adManager->getKind($kindIdx);
    $sellerIdx = $kindData["seller_idx"];
    $isUse = $kindData["is_del"] == "N" ? "Y" : "N";
}

?>

<div class="container popup">
	<div class="content write_page">
		<div class="content_wrap">
            <form name="form_write_kind" method="post" class="">
                <input type="hidden" name="mode" value="insert_kind" />
                <input type="hidden" name="idx" value="<?=$kindIdx?>" />
                <div class="tb_wrap">
                    <table>
                        <colgroup>
                            <col width="150">
                            <col width="*">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>광고 업체</th>
                            <td class="text_left">
                                <select name="seller_group_idx" class="seller_group_idx" data-selected="<?=$sellerGroupIdx?>">
                                    <option value="0">전체그룹</option>
                                </select>
                                <select name="seller_idx" class="seller_idx" data-selected="<?=$sellerIdx?>" data-default-value="" data-default-text="판매처를 선택해주세요.">
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>이름</th>
                            <td class="text_left"><input name="kind_name" type="text" class="w100per" maxlength="20" value="<?=$kindData["kind_name"]?>"></td>
                        </tr>
                        <tr>
                            <th>방식</th>
                            <td class="text_left">
                                <select name="ad_billing_type">
                                    <?php foreach ($billingTypes as $billingType) { ?>
                                        <option value="<?=$billingType["code"]?>" <?php if($kindData["billing_type"] == $billingType["code"]) { ?> selected<?php } ?>><?=$billingType["code_name"]?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>메모</th>
                            <td class="text_left"><textarea name="memo" class="w100per h100px" maxlength="50"><?=$kindData["memo"]?></textarea></td>
                        </tr>
                        <tr>
                            <th>사용여부</th>
                            <td class="text_left">
                                <label><input type="radio" name="use_yn" value="Y" <?=$isUse == "Y" ? "checked" : ""?>/> Y</label>
                                <label><input type="radio" name="use_yn" value="N" <?=$isUse == "N" ? "checked" : ""?>/> N</label>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="btn_set">
                    <div class="center">
                        <a href="javascript:;" id="btn_save_ad_kind" class="large_btn blue_btn ">저장</a>
                        <a href="javascript:;" class="large_btn red_btn btn_close_pop">취소</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
