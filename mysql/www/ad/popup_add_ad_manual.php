<?php

include_once "../_init_.php";

$mode = $_POST["mode"];
$adIdx = $_POST["idx"];

$adManager = new AdvertisingManager();
$kinds = $adManager->getKinds();
$adRepDatum = $adManager->getAdDatum($adIdx);

$productGroup = array();

$adProductType = "product";

if ($adRepDatum) {
    $productManager = new Product();

    if ($adRepDatum["product_group"]) {
        $productIdxGroup = explode(",", $adRepDatum["product_group"]);

        foreach ($productIdxGroup as $productIdx) {
            $product = $productManager->getProductData($productIdx);
            $productGroup[$product["product_idx"]] = $product["product_name"];
        }
    }

    if ($adRepDatum["product_option_group"]) {
        $adProductType = "product_option";

        $productOptionIdxGroup = explode(",", $adRepDatum["product_option_group"]);

        foreach ($productOptionIdxGroup as $productOptionIdx) {
            $productOption = $productManager->getProductOptionData($productOptionIdx);
            $productGroup[$productOption["product_option_idx"]] = $productOption["product_option_name"];
        }
    }
}

if ($mode == "mod_ad_manual" && !$adRepDatum) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: text/html; charset=UTF-8');
    die("Error");
}

?>
<style>
    .selectize-input { height: auto; }
</style>
<div class="container popup">
    <div class="content write_page">
        <div class="content_wrap">
            <form name="form_ad_group" method="post" class="">
                <input type="hidden" name="mode" value="<?=$mode?>" />
                <input type="hidden" name="group_idx" value="<?=$adIdx?>" />
                <div class="tb_wrap">
                    <table>
                        <colgroup>
                            <col width="150">
                            <col width="*">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>광고 종류 선택 <span class="lb_red">필수</span></th>
                            <td class="text_left">
                                <select name="kind_idx">
                                    <?php foreach ($kinds as $kind) { ?>
                                        <option name="kind_idx" value="<?=$kind["idx"]?>" <?php if($adRepDatum["kind_idx"] == $kind["idx"]) { ?> selected<?php } ?>><?=$kind["kind_full_name"]?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>광고 이름</th>
                            <td class="text_left">
                                <input name="ad_name" type="text" class="w100per" maxlength="20" value="<?=$adRepDatum["ad_name"]?>">
                            </td>
                        </tr>
                        <tr>
                            <th>방식 <span class="lb_red">필수</span></th>
                            <td class="text_left">
                                <p>
                                    <label>
                                        <input type="radio" name="ad_group_product_type" value="product" <?php if($adProductType == "product") { ?> checked="checked" <?php } ?>/>상품
                                    </label>
                                    <label>
                                        <input type="radio" name="ad_group_product_type" value="product_option" <?php if($adProductType == "product_option") { ?> checked="checked" <?php } ?>/>옵션
                                    </label>
                                </p>
                            </td>
                        </tr>
                        <tr id="tr_product_group">
                            <th>광고 타겟 상품 <span class="lb_red">필수</span></th>
                            <td class="text_left td_product_group">
                                <input name="product_group" type="text" class="w100per" id="product_selectize" value="" readonly>
                            </td>
                        </tr>
                        <tr id="tr_product_option_group">
                            <th>광고 타겟 상품 옵션 <span class="lb_red">필수</span></th>
                            <td class="text_left td_product_option_group">
                                <input name="product_option_group" type="text" class="w100per" id="product_option_selectize" value="" readonly>
                            </td>
                        </tr>
                        <tr>
                            <th>광고 내역 <span class="lb_red">필수</span></th>
                            <td class="text_left" style="display: block; max-height: 400px; overflow: scroll;">
                                <div>
                                    <table id="ad_spec_list">
                                        <colgroup>
                                            <col width="30">
                                            <col width="150">
                                            <col width="100">
                                            <col width="80">
                                            <col width="80">
                                            <col width="100">
                                            <col width="*">
                                        </colgroup>
                                        <tbody>
                                        <tr idx="0">
                                            <th></th>
                                            <th>키워드 <span class="lb_red">필수</span></th>
                                            <th>총 비용 <span class="lb_red">필수</span></th>
                                            <th>노출 수 </th>
                                            <th>접근 수 </th>
                                            <th>실행일 <span class="lb_red">필수</span></th>
                                            <th>비고</th>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="btn_set">
                    <div class="center">
                        <a href="javascript:;" id="btn_add_product" class="large_btn green_btn ">상품 추가</a>
                        <a href="javascript:;" id="btn_add_product_option" class="large_btn green_btn ">옵션 추가</a>
                        <a href="javascript:;" id="btn_add_spec" class="large_btn green_btn ">내역 추가</a>
                        <a href="javascript:;" id="btn_save_ad_group" class="large_btn blue_btn ">저장</a>
                        <a href="javascript:;" class="large_btn red_btn btn_close_pop">취소</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function initAddAdManualPop() {
        $(".btn_close_pop").on("click", function() {
            closeModalPop("#modal_add_ad_manual");
        });

        //상품 검색 팝업
        $("#btn_add_product").on("click", function(){
            Common.newWinPopup("/common/product_search_pop.php?mode=product&callback=addListToSelectize", 'product_search_pop', 600, 700, 'yes');
        });

        //상품 옵션 팝업
        $("#btn_add_product_option").on("click", function(){
            Common.newWinPopup("/common/product_search_pop.php?mode=product_option&callback=addListToSelectize", 'product_search_pop', 800, 700, 'yes');
        });

        //내역 추가
        $("#btn_add_spec").on("click", function(){
            appendSpecInput(null);
        });

        $("#product_selectize").selectize({
            plugins: ['remove_button'],
            delimiter: ',',
            persist: false,
            readOnly: true
        });

        $("#product_option_selectize").selectize({
            plugins: ['remove_button'],
            delimiter: ',',
            persist: false,
            readOnly: true
        });

        //저장
        $("#btn_save_ad_group").on("click", function(){
            let select = $("#product_selectize").selectize();
            let selectize = select[0].selectize;
            let products = Object.keys(selectize.options);

            select = $("#product_option_selectize").selectize();
            selectize = select[0].selectize;
            let productOptions = Object.keys(selectize.options);

            if (products.length == 0 && productOptions.length == 0) {
                alert("선택된 상품이나 옵션이 없습니다.");
                return;
            }

            let specList = $("#ad_spec_list > tbody:last > tr");

            if (specList.length < 2) {
                alert("광고 내역이 없습니다. 광고 내역을 추가해 주세요.");
                return;
            }

            let specListValid = true;
            let specListExist = false;

            specList.each(function(index, e) {
                if (index == 0) return true;
                let trIdx = index - 1;

                let keyword = $(e).find('input[name="ad_spec_keyword[]"]').val();
                let cost = $(e).find('input[name = "ad_spec_cost[]"]').val();
                let dpCnt = $(e).find('input[name = "ad_spec_dp_cnt[]"]').val();
                let opCnt = $(e).find('input[name = "ad_spec_op_cnt[]"]').val();
                let opDate = $(e).find('input[name = "ad_spec_op_date[]"]').val();

                if (keyword == '' && cost == '' && opDate == '') {
                    return true;
                }

                if (keyword == '' || cost == '' || opDate == '') {
                    alert('내역 리스트 ' + index + '행에 필수 값이 빠져있습니다.');
                    specListValid = false;
                } else {
                    specListExist = true;
                }
            });

            if (!specListValid) {
                return;
            }

            if (!specListExist) {
                alert("광고 내역에 정보를 입력해 주세요.");
                return;
            }

            showLoader();

            $.ajax({
                type: 'POST',
                url: '/ad/ad_proc.php',
                dataType: "json",
                data: $("form[name='form_ad_group']").serialize()
            }).done(function (response) {
                if(response.rst) {
                    alert('저장되었습니다.');
                    closeModalPop("#modal_add_ad_manual");
                    adListRefresh();
                }else{
                    alert(response.msg);
                }
            }).fail(function(jqXHR, textStatus){
                alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
            }).always(function(){
                hideLoader();
            });
        });

        $(":input:radio[name=ad_group_product_type]").on("change", function(){
            let productType = $(":input:radio[name=ad_group_product_type]:checked").val();
            toggleAdGroupProductType(productType);
        });

        <?php if (!$adIdx) { ?>
        appendSpecInput(null);
        <?php } ?>

        initAddAdManualPopProductData();
    }

    function initAddAdManualPopProductData() {
        toggleAdGroupProductType("<?=$adProductType?>");

        let list = new Array();
        <?php foreach ($productGroup as $productIdx => $productName) { ?>
        list.push(["<?=$productIdx?>", "<?=$productName?>"]);
        <?php } ?>

        console.log("asdf");

        let args = {};
        args.list = list;
        args.type = "<?=$adProductType?>";
        addListToSelectize(args);

        <?php if ($mode == "mod_ad_manual") {
		    $adDatum = $adManager->getAdDatum($adIdx);
            if ($adDatum) { ?>
        var adDatum = [
            "<?=$adDatum["idx"]?>",
            "<?=$adDatum["keyword"]?>",
            "<?=$adDatum["cost"]?>",
            "<?=$adDatum["display_count"]?>",
            "<?=$adDatum["operation_count"]?>",
            "<?=$adDatum["operation_date"]?>",
            "<?=$adDatum["memo"]?>"
        ];

        appendSpecInput(adDatum);
            <?php }
        }
        ?>
    }

    function appendSpecInput(adDatum) {
        let lastTrIdx = Number($('table[id="ad_spec_list"] > tbody:last > tr:last').attr('idx'));
        let trIdx = lastTrIdx + 1;

        let inputHiddenIdx = '';
        let tdKeyword = '<td><input type="text" name="ad_spec_keyword[]" class="w100per"></td>';
        let tdCost = '<td><input type="text" name="ad_spec_cost[]" class="w100per money"></td>';
        let tdDpCnt = '<td><input type="text" name="ad_spec_dp_cnt[]" class="w100per money"></td>';
        let tdOpCnt = '<td><input type="text" name="ad_spec_op_cnt[]" class="w100per money"></td>';
        let tdOpDate = '<td><input type="text" name="ad_spec_op_date[]" class="w100per jqDateDynamic" readonly="readonly"></td>';
        let tdMemo = '<td><input type="text" name="ad_spec_memo[]" class="w100per"></td>';

        if (adDatum) {
            inputHiddenIdx = '<input type="hidden" name="ad_spec_idx[]" value="' + adDatum[0] + '">';
            tdKeyword = '<td><input type="text" name="ad_spec_keyword[]" class="w100per" value="' + adDatum[1] + '"></td>';
            tdCost = '<td><input type="text" name="ad_spec_cost[]" class="w100per money" value="' + adDatum[2] + '"></td>';
            tdDpCnt = '<td><input type="text" name="ad_spec_dp_cnt[]" class="w100per money" value="' + adDatum[3] + '"></td>';
            tdOpCnt = '<td><input type="text" name="ad_spec_op_cnt[]" class="w100per money" value="' + adDatum[4] + '"></td>';
            tdOpDate = '<td><input type="text" name="ad_spec_op_date[]" class="w100per jqDateDynamic" readonly="readonly" value="' + adDatum[5] + '"></td>';
            tdMemo = '<td><input type="text" name="ad_spec_memo[]" class="w100per" value="' + adDatum[6] + '"></td>';
        }

        let trHtml = '<tr name="ad_spec_' + trIdx + '" idx="' + trIdx + '">' +
            '<td style="padding: 5px;">' +
            inputHiddenIdx +
            '<a href="javascript:;" id="btn_del_ad_spec_' + trIdx + '" class=""><i class="far fa-times-circle fa-1x"></i></a>' +
            '</td>' +
            tdKeyword +
            tdCost +
            tdDpCnt +
            tdOpCnt +
            tdOpDate +
            tdMemo +
            '</tr>';

        $('table[id="ad_spec_list"] > tbody:last').append(trHtml);

        $('#btn_del_ad_spec_' + trIdx).on("click", function(){
            removeSpecInput(trIdx);
        });

        $("#modal_add_ad_manual").dialog({
            position: {my:"center", at:"center", of: window }
        });

        $(".money").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 0, autoGroup: true, rightAlign: true});

        Common.setDatePickerForDynamicElement($('tr[name="ad_spec_' + trIdx + '"] .jqDateDynamic'));
    }

    function removeSpecInput(trIdx) {
        $('#btn_del_ad_spec_' + trIdx).off();
        $('table[id="ad_spec_list"] > tbody:last > tr[name="ad_spec_' + trIdx + '"]').remove();

        $("#modal_add_ad_manual").dialog({
            position: {my:"center", at:"center", of: window }
        });
    }

    function addListToSelectize(args) {
        let $select = $("#" + args.type + "_selectize").selectize();
        let selectize = $select[0].selectize;

        args.list.forEach(function(val){
        	if (args.type === "product") {
				selectize.addOption({value:val.product_idx, text:val.product_name});
				selectize.addItem(val.product_idx);
            } else if (args.type === "product_option") {
				selectize.addOption({value:val.product_option_idx, text:val.product_name + '-' + val.product_option_name});
				selectize.addItem(val.product_option_idx);
            }
        });
    }

    function toggleAdGroupProductType(type) {
        let revType = "product";
        if (type == "product") revType = "product_option";

        $("#btn_add_" + type).show();
        $("#btn_add_" + revType).hide();

        $("#tr_" + type + "_group").show();
        $("#tr_" + revType + "_group").hide();

        let $select = $("#" + revType + "_selectize").selectize();
        let selectize = $select[0].selectize;
        selectize.clear();
    }

    initAddAdManualPop();
</script>