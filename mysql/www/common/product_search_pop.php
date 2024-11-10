<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 팝업 창에서 주문 생성 시 상품 검색 팝업
 */
//Page Info
$pageMenuIdx = 305;
//Init
include_once "../_init_.php";

$idx = $_GET["idx"] ? $_GET["idx"] : "0";
$mode = $_GET["mode"] ? $_GET["mode"] : "product_option";
$type = $_GET["type"] ? $_GET["type"] : "multiple";
$callback = $_GET["callback"] ? $_GET["callback"] : "";
$product_sale_type = $_GET["product_sale_type"] ? $_GET["product_sale_type"] : null;
$auto_close = true;
if($_GET["auto_close"]) {
    if ($_GET["auto_close"] == "true") $auto_close = true;
	if ($_GET["auto_close"] == "false") $auto_close = false;
}

?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
    <?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
    <div class="content write_page">
        <div class="content_wrap">
            <form name="searchFormPop" id="searchFormPop" method="get">
                <input type="hidden" name="mode" value="<?=$mode?>">
                <?php if ($product_sale_type != null) { ?>
                <input type="hidden" name="product_sale_type" value="<?=$product_sale_type?>">
                <?php } ?>
                <div class="find_wrap">
                    <div class="finder">
                        <div class="finder_set">
                            <div class="finder_col">
                                <span class="text">상품명</span>
                                <input type="text" name="product_name" class="w100px enterDoSearchPop" placeholder="상품명" />
                            </div>
                            <?php if($mode == "product_option") { ?>
                            <div class="finder_col">
                                <span class="text">옵션명</span>
                                <input type="text" name="product_option_name" class="w100px enterDoSearchPop" placeholder="옵션명" />
                            </div>
                            <div class="finder_col">
                                <span class="text">옵션코드</span>
                                <input type="text" name="product_option_idx" class="w100px enterDoSearchPop" placeholder="옵션코드" />
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="find_btn">
                        <div class="table">
                            <div class="table_cell">
                                <a href="javascript:;" id="btn_searchBar_pop" class="wide_btn btn_default">검색</a>
                            </div>
                        </div>
                    </div>
                    <a href="javascript:;" class="find_hide_btn">
                        <i class="fas fa-angle-up up_btn"></i>
                        <i class="fas fa-angle-down dw_btn"></i>
                    </a>
                </div>
            </form>
            <div class="tb_wrap grid_tb">
                <table id="grid_list_pop">
                </table>
                <div id="grid_pager_pop"></div>
            </div>

            <div class="btn_set">
                <div class="center">
                    <a href="javascript:;" class="large_btn blue_btn btn_add_search_product">추가</a>
                    <a href="javascript:self.close();" class="large_btn red_btn">닫기</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script>
    window.name = "product_search_pop";
    let commonProductSearchPopMode = "<?=$mode?>";
    let commonProductSearchPopCallback = new Array();

    function commonProductSearchInitPop() {
        $(".btn_add_search_product").on("click", function(){
            let selRowId = $("#grid_list_pop").getGridParam('selarrrow');

            if(selRowId == null || selRowId.length == 0){
                alert('선택된 내용이 없습니다.');
                return;
            }

            let itemList = [];

            $.each(selRowId, function(i, o){
                let rowData = $("#grid_list_pop").getRowData(o);
                if (commonProductSearchPopMode === "product") {
                	itemList.push({
                        product_idx: rowData.product_idx,
                        product_name: rowData.product_name
                    });
                } else if(commonProductSearchPopMode === "product_option") {
					itemList.push({
						product_idx: rowData.product_idx,
						product_name: rowData.product_name,
                        product_option_idx: rowData.product_option_idx,
                        product_option_name: rowData.product_option_name
					});
                }
            });

            <?php if ($callback != "") { ?>
            let callbackArgs = {};
            callbackArgs.list = itemList;
            callbackArgs.type = commonProductSearchPopMode;
            callbackArgs.idx = <?=$idx?>;

            window.opener.<?=$callback?>(callbackArgs);
            <?php } ?>

            <?php if ($auto_close) { ?>
			self.close();
			<?php } ?>
        });

        let colModel;
        if (commonProductSearchPopMode === "product") {
            colModel = [
                {label: '상품코드', name: 'product_idx', index: 'product_idx', width: 100},
                {label: '상품명', name: 'product_name', index: 'product_name', width: 200, sortable: true},
                {label: '판매타입', name: 'code_name', index: 'code_name', width: 100, sortable: true},
                {label: '공급업체', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: false, align: 'left'}
            ];
        } else if (commonProductSearchPopMode === "product_option") {
            colModel = [
                {label: '상품코드', name: 'product_idx', index: 'product_idx', width: 0, hidden: true},
                {label: '옵션코드', name: 'product_option_idx', index: 'product_option_idx', width: 80, is_use: true},
                {label: '상품명', name: 'product_name', index: 'product_name', width: 140, sortable: true},
                {label: '판매타입', name: 'code_name', index: 'code_name', width: 100, sortable: true},
                {label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 110, sortable: true},
                {label: '공급업체', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: false, align: 'left'}
            ];
        }

        //Grid 초기화
        $("#grid_list_pop").jqGrid({
            url: "/common/" + commonProductSearchPopMode + "_search_pop_grid.php",
            mtype: "GET",
            datatype: "local",
            postData:{
                param: $("#searchFormPop").serialize()
            },
            jsonReader : {
                page: "page",
                total: "total",
                root: "rows",
                records: "records",
                repeatitems: true,
                id: "idx"
            },
            colModel: colModel,
            rowNum: Common.jsSiteConfig.jqGridRowList[1],
            pager: '#grid_pager_pop',
            sortname: "A." + commonProductSearchPopMode + "_idx",
            sortorder: "asc",
            viewrecords: true,
            autowidth: false,
            rownumbers: true,
            shrinkToFit: true,
            height: 150,
            multiselect: true,
            loadComplete: function(){
                //Grid 사이즈 reSize
                Common.jqGridResize("#grid_list_pop");
            },
            <?php if($type == "single"){ ?>
			beforeSelectRow: function() {
				$("#grid_list_pop").jqGrid('resetSelection');
				return true;
			}
			<?php } ?>
        });

        //브라우저 리사이즈 시 jqgrid 리사이징
        $(window).on("resize", function(){
            Common.jqGridResizeWidthByTarget("#grid_list_pop", $(".container.popup .tb_wrap"));
        }).trigger("resize");

        //검색 폼 Submit 방지
        $("#searchFormPop").on("submit", function(e){
            e.preventDefault();
        });

        //Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
        $("input.enterDoSearchPop").on("keyup", function(e){
            var keyCode = (event.keyCode ? event.keyCode : event.which);
            if (keyCode == 13) {
                event.preventDefault();
                refreshProductGrid();
            }
        });

        //검색 버튼 클릭 이벤트
        $("#btn_searchBar_pop").on("click", function(){
            refreshProductGrid();
        });
    }

    function refreshProductGrid() {
        let txt1 = $("form[name='searchFormPop'] input[name='product_name']").val();
        let txt2 = $("form[name='searchFormPop'] input[name='product_option_name']").val();
        let txt3 = $("form[name='searchFormPop'] input[name='product_option_idx']").val();

        if($.trim(txt1) == "" && $.trim(txt2) == "" && $.trim(txt3) == ""){
            alert('검색어를 입력해주세요.');
            return;
        }

        $("#grid_list_pop").setGridParam({
            datatype: "json",
            page: 1,
            url: "/common/" + commonProductSearchPopMode + "_search_pop_grid.php",
            postData:{
                param: $("#searchFormPop").serialize()
            }
        }).trigger("reloadGrid");
    }

    commonProductSearchInitPop();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
