<?php

//Page Info
$pageMenuIdx = 294;

//Init
include_once "../_init_.php";

$mode = "amount_change";
$_sample_filename = "재고_수량_일괄_조정.xlsx";

$operationDate = date('Y-m-d');
$operationTime = date("H:i");

?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
    <?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
    <div class="content">
        <form name="searchForm" id="searchForm" method="post" enctype="multipart/form-data" action="/proc/_xls_upload.php" target="xls_hidden_frame">
            <input type="hidden" name="mode" id="xls_write_mode" value="<?=$mode?>" />
            <input type="hidden" name="act" id="xls_write_act" value="grid" />
            <input type="hidden" name="xls_type" value="stock_move" />
            <div class="find_wrap">
                <div class="finder">
                    <div class="finder_set">
                        <div class="finder_col">
                            <input type="file" name="xls_file" />
                        </div>
                        <div class="finder_col">
                            <a href="javascript:" class="btn green_btn btn-upload">업로드</a>
                        </div>
                    </div>
                    <div class="finder_set">
                        <div class="finder_col">
                            <span class="text">작업 날짜</span>
                            <input type="text" name="operation_date" id="operation_date" class="w80px jqDate " value="<?=$operationDate?>" readonly="readonly" />
                            <span class="text">작업 시간</span>
                            <input type="time" name="operation_time" id="operation_time" class="" value="<?=$operationTime?>"/>
                            <span class="info_txt col_red">입력받은 날짜와 시간을 기준으로 해당 시간까지의 재고 수량을 변경합니다. IE, safari 브라우저에서는 동작하지 않습니다. </span>
                        </div>
                    </div>
                </div>
                <div class="find_btn empty">
                    <div class="table">
                        <div class="table_cell">
                        </div>
                    </div>
                </div>
                <a href="javascript:" class="find_hide_btn">
                    <i class="fas fa-angle-up up_btn"></i>
                    <i class="fas fa-angle-down dw_btn"></i>
                </a>
            </div>
        </form>

        <p class="sub_desc">
            샘플 파일 다운로드 <a href="/_xls_sample/<?=$_sample_filename?>" class="btn blue_btn">다운로드</a>
            * 샘플파일을 다운로드하여 포맷을 확인하시고 등록해 주세요. 양식에 맞지 않으면 정상적으로 등록되지 않습니다.
        </p>
        <div class="grid_btn_set_top">
            <a href="javascript:" class="large_btn red_btn btn-xls-insert">&nbsp;&nbsp;&nbsp;적용&nbsp;&nbsp;&nbsp;</a>
            <div class="right">
            </div>
        </div>
        <div class="tb_wrap grid_tb">
            <table id="grid_list">
            </table>
            <div id="grid_pager"></div>
        </div>
    </div>
</div>
<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/main.js"></script>
<script src="/js/page/stock.product.js?v=191030"></script>
<script>
    StockProduct.changeAmountInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>
