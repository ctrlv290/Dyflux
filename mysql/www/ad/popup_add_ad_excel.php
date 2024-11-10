<?php

include_once "../_init_.php";

$adManager = new AdvertisingManager();
$kinds = $adManager->getKinds();

?>

<div class="container popup">
    <div class="content write_page">
        <div class="content_wrap">
            <form name="form_add_ad_excel" method="post" enctype="multipart/form-data" action="/proc/_xls_upload.php" target="xls_hidden_frame">
                <input type="hidden" name="mode" value="grid" />
                <input type="hidden" name="xls_type" value="add_ad_excel" />
                <div class="tb_wrap">
                    <table>
                        <colgroup>
                            <col width="150">
                            <col width="200">
                            <col width="150">
                            <col width="*">
                            <col width="100">
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
                            <th>엑셀 파일</th>
                            <td><input type="file" name="xls_file" /></td>
                            <td><a href="javascript:;" class="btn green_btn btn-upload">업로드</a></td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <div class="tb_wrap grid_tb">
                                    <table id="grid_list_pop">
                                    </table>
                                    <div id="grid_pager_popup"></div>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="btn_set">
                    <div class="center">
                        <a href="javascript:;" id="btn_save_ad_excel" class="large_btn blue_btn ">저장</a>
                        <a href="javascript:;" class="large_btn red_btn btn_close_pop">취소</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script>
var prevGroup = {groupIdx: undefined, startRowNo: undefined, endRowNo: undefined};

function initAddAdExcelPop() {
    $("#grid_list_pop").jqGrid({
        url: '/ad/popup_add_ad_excel_grid.php',
        mtype: "POST",
        datatype: "local",
        jsonReader : {
            page: "page",
            total: "total",
            root: "rows",
            records: "records",
            repeatitems: true,
            id: "idx"
        },
        colModel: [
            { label: '그룹 번호', name: 'group_idx', index: 'group_idx', width: 100, sortable: true, hidden:true},
            { label: '그룹 광고 수', name: 'group_count', index: 'group_count', width:100, hidden:true},
            { label: '대표 행', name: 'rep_row', index: 'rep_row', width:100, hidden:true, formatter: function(cv, opt, ro){
                    if (prevGroup.groupIdx != undefined && Number(opt.rowId) <= prevGroup.endRowNo) {
                        return false;
                    } else {
                        prevGroup.startRowNo = Number(opt.rowId);
                        prevGroup.endRowNo = Number(opt.rowId) + ro.group_count - 1;
                        prevGroup.groupIdx = ro.group_idx;
                        return true;
                    }
                }},
            { label: '광고 이름', name: 'ad_name', index: 'ad_name', width: 160, sortable: false, cellattr: function(rowId, tv, ro, cm, rd) {
                    if (prevGroup.startRowNo == Number(rowId)) {
                        return 'rowspan="' + ro.group_count + '" style="vertical-align:top;"';
                    } else {
                        return 'style="display:none;"';
                    }
                }},
            { label: '광고 상품명', name: 'ad_product_name', index: 'ad_product_name', width: 160, sortable: false, cellattr: function(rowId, tv, ro, cm, rd) {
                    if (prevGroup.startRowNo == Number(rowId)) {
                        return 'rowspan="' + ro.group_count + '" style="vertical-align:top;"';
                    } else {
                        return 'style="display:none;"';
                    }
                }},
            { label: '타겟 상품 선택', name: 'select_product', index: 'select_product', width: 160, sortable: false, cellattr: function(rowId, tv, ro, cm, rd) {
                    if (prevGroup.startRowNo == Number(rowId)) {
                        return 'rowspan="' + ro.group_count + '" style="vertical-align:top;"';
                    } else {
                        return 'style="display:none;"';
                    }
                }, formatter: function(cv, opt, ro){
                    if (prevGroup.startRowNo == Number(opt.rowId)) {
                        return '<a href="javascript:;" class="xsmall_btn btn_select_excel_grid_product" data-idx="'+ Number(opt.rowId) +'">상품 선택</a> <a href="javascript:;" class="xsmall_btn btn_select_excel_grid_product_option" data-idx="'+ Number(opt.rowId) +'">옵션 선택</a>';
                    } else {
                        return '';
                    }
                }},
            { label: '타겟 상품 리스트', name: 'product_list', index: 'product_list', width: 150, sortable: false, cellattr: function(rowId, tv, ro, cm, rd) {
                    if (prevGroup.startRowNo == Number(rowId)) {
                        return 'id="cell_product_list" data-idx="' + Number(rowId) + '" rowspan="' + ro.group_count + '" style="vertical-align:top;"';
                    } else {
                        return 'style="display:none;"';
                    }
                }, formatter: function(cv, opt, ro){
                    if (prevGroup.startRowNo == Number(opt.rowId)) {
                        if ($(ro.product_name_list).length > 0) {
							let pText = '<div class="selected_products" id="selected_products_' + Number(opt.rowId) + '" data-idx="'+ Number(opt.rowId) +'">';
							$.each(ro.product_name_list, function(k, v){
								pText += '<p ' + ro.product_type + '="' + k + '">' + v + '</p><br/>';
							});
							pText += '</div>' +
                                '<a href="javascript:;" class="xsmall_btn btn_delete_selected_product" id="btn_delete_selected_product_' + Number(opt.rowId) + '" data-idx="'+ Number(opt.rowId) +'">상품 삭제</a>';
							return pText;
                        } else {
							return '<div class="selected_products" id="selected_products_' + Number(opt.rowId) + '" data-idx="'+ Number(opt.rowId) +'">' +
								'<p class="selected_product_empty">선택된 상품이 없습니다</p>' +
								'</div>' +
								'<a href="javascript:;" class="xsmall_btn btn_delete_selected_product" id="btn_delete_selected_product_' + Number(opt.rowId) + '" data-idx="'+ Number(opt.rowId) +'" style="display:none;">상품 삭제</a>';
                        }
                    } else {
                    	return "";
                    }
                }},
            { label: '상품 타입', name: 'product_type', index: 'product_type', width: 100, sortable: false, hidden:true},
            { label: '상품 리스트', name: 'product_group', index: 'product_group', width: 100, sortable: false, hidden:true},
            { label: '상품 옵션 리스트', name: 'product_option_group', index: 'product_option_group', width: 100, sortable: false, hidden:true},
            { label: '키워드', name: 'ad_keyword', index: 'ad_keyword', width: 100, sortable: false},
            { label: '총 비용', name: 'ad_cost', index: 'ad_cost', width: 100, sortable: false},
            { label: '노출 수', name: 'ad_display_count', index: 'ad_display_count', width: 100, sortable: false},
            { label: '접근 수', name: 'ad_click_count', index: 'ad_click_count', width: 100, sortable: false},
            { label: '실행 일', name: 'ad_operation_date', index: 'ad_operation_date', width: 100, sortable: false},
            { label: '비고', name: 'valid', index: 'valid', width: 100, sortable: false, cellattr: function(rowId, tv, ro, cm, rd){
                if (!ro.valid) {
                    return 'style="color:red;"';
                }
            }, formatter:function(cv, opt, ro) {
            	if (cv) {
            		return "";
                } else {
            		return ro.valid_text;
                }
            }}
        ],
        rowNum: 1000,
        rowList: 1000,
        pager: '#grid_pager_popup',
        sortable: false,
        viewrecords: true,
        autowidth: true,
        rownumbers: true,
        shrinkToFit: true,
        loadonce: true,
        height: Common.jsSiteConfig.jqGridDefaultHeight,
        loadComplete: function(completeData){
            if (completeData.rst) {
                //상품 검색 팝업
                $(".btn_select_excel_grid_product").on("click", function(){
                    Common.newWinPopup("/common/product_search_pop.php?mode=product&callback=addProductListFromSearchPop&idx=" + $(this).data("idx"), 'product_search_pop', 600, 700, 'yes');
                });

                //상품 옵션 팝업
                $(".btn_select_excel_grid_product_option").on("click", function(){
                    Common.newWinPopup("/common/product_search_pop.php?mode=product_option&callback=addProductListFromSearchPop&idx=" + $(this).data("idx"), 'product_search_pop', 800, 700, 'yes');
                });

                //상품 삭제
                $(".btn_delete_selected_product").on("click", function(){
                    clearSelectedProductList($(this).data("idx"));
                });
            }
        }
    });

    $(".btn-upload").on("click", function(){
        if($("input[name='xls_file']").val() == "")
        {
            alert("업로드 할 파일을 선택해주세요.");
            return false;
        }

        showLoader();
        $('form[name="form_add_ad_excel"]').submit();
    });

    $("#btn_save_ad_excel").on("click", function(){
        executeAdExcelData();
    });
}

function addAdExcelReadFile(filename) {
    hideLoader();
    prevGroup = {groupIdx: undefined, startRowNo: undefined, endRowNo: undefined};

    //업로드된 엑셀 바인딩 jqGrid
    $("#grid_list_pop").setGridParam({
        datatype: "json",
        page: 1,
        postData:{
            mode: 'grid',
            kind_idx: $('select[name="kind_idx"] option:selected').val(),
            xls_filename: filename
        }
    }).trigger("reloadGrid");
}

function executeAdExcelData() {
	console.log("a");
    if ($('.selected_product_empty:first').length) {
        let nonProcessedItem = $('.selected_product_empty:first').parent().attr("data-idx");
        alert("아직 상품 매칭이 되지 않은 광고 그룹이 존재합니다. 상품을 매칭해주세요.");
        $('#grid_list_pop > tbody > tr:eq(' + nonProcessedItem + ')').focus();
        return;
    }

    let dataObj = {};
    dataObj.list = $("#grid_list_pop").jqGrid("getRowData");
    dataObj.mode = "execute";
    dataObj.kind_idx = $('select[name="kind_idx"] option:selected').val();

    let isValid = true;
	$.each(dataObj.list, function(idx, item){
		if (item.valid != "") {
			isValid = false;
			return;
		}
    });

	if (!isValid) {
		alert("오류가 있는 데이터가 존재합니다. 확인해주시고 수정해 다시 업로드해야 합니다.");
		return;
    }

    $.ajax({
    type: 'POST',
    url: '/ad/popup_add_ad_excel_grid.php',
    dataType: "json",
    data: dataObj,
    }).done(function (response) {
        if(response.rst) {
            //success
            alert('저장되었습니다.');
            closeModalPop("#modal_add_ad_excel");
            adListRefresh();
        }else{
            alert(response.msg);
        }

    }).fail(function(jqXHR, textStatus){
        alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
    });
}

function addProductToGridRow(rowIdx, productType, productIdx, productName) {
	let rowData = $('#grid_list_pop').jqGrid('getRowData', rowIdx);

	let html = '';
	let pGroup = '';

	if ($('#selected_products_' + rowIdx + ' .selected_product_empty').length == 0) {
		html = $('#selected_products_' + rowIdx).html();

		if (productType == "product") {
			pGroup = rowData.product_group;
		} else if (productType == "product_option") {
			pGroup = rowData.product_option_group;
		}
	}

	html += '<p ' + productType + '="' + productIdx + '">' + productName + '</p><br/>';

	$('#selected_products_' + rowIdx).html(html);
	$('#btn_delete_selected_product_' + rowIdx).css('display', 'inline-block');

	$('#grid_list_pop').jqGrid('setCell', rowIdx, productType + '_group', pGroup);
	$('#grid_list_pop').jqGrid('setCell', rowIdx, 'product_type', productType);
}

function addProductListFromSearchPop(args) {
    args.list.forEach(function(val){
		addProductToGridRow(args.idx, args.type, val[0], val[1]);
    });
}

function clearSelectedProductList(rowId) {
    $('#selected_products_' + rowId).html('<p class="selected_product_empty">선택된 상품이 없습니다</p>');
    $('#btn_delete_selected_product_' + rowId).css('display', 'none');

    $('#grid_list_pop').jqGrid('setCell', rowId, 'product_type', '');
    $('#grid_list_pop').jqGrid('setCell', rowId, 'product_group', '');
    $('#grid_list_pop').jqGrid('setCell', rowId, 'product_option_group', '');
}

</script>