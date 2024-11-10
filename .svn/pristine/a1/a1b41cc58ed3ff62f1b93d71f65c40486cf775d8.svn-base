<?php
/**
 * User: ysh
 * Date: 2020-02-10
 * Desc: 판매일보 업로드 팝업 페이지
 */

//Page Info
$pageMenuIdx = 122;
//Init
include_once "../_init_.php";

$mode = "transaction_upload";
$_sample_filename = "판매일보_업로드_엑셀.xlsx";
?>
<div class="container popup transaction_pop">
    <div class="content">
        <form name="uploadForm" id="uploadForm" method="post" enctype="multipart/form-data" action="/proc/_xls_upload.php" target="xls_hidden_frame">
            <input type="hidden" name="mode" id="xlswrite_mode" value="<?=$mode?>" />
            <input type="hidden" name="act" id="xlswrite_act" value="info" />
            <input type="hidden" name="xls_type" value="transaction_upload" />
            <div class="find_wrap">
                <div class="finder">
                    <div class="finder_set">
                        <div class="finder_col">
                            <input type="file" name="xls_file" />
                        </div>
                        <a href="javascript:;" class="btn btn_default btn-product-search-pop" >상품검색</a>
                    </div>
                </div>
                <div class="find_btn empty">
                    <div class="table">
                        <div class="table_cell">
                        </div>
                    </div>
                </div>
                <a href="javascript:;" class="find_hide_btn">
                    <i class="fas fa-angle-up up_btn"></i>
                    <i class="fas fa-angle-down dw_btn"></i>
                </a>
            </div>
        </form>
        <p class="sub_desc">
            샘플 파일 다운로드 <a href="/_xls_sample/<?=$_sample_filename?>" class="btn blue_btn">다운로드</a>
            * 샘플파일을 다운로드하여 포맷을 확인하시고 등록해 주세요.<br>
            양식에 맞지 않으면 정상적으로 등록되지 않습니다.
        </p>
        <div class="tb_wrap">
            <table autofocus="autofocus">
                <colgroup>
                    <col width="150">
                    <col width="*">
                </colgroup>
                <tbody>
                <tr>
                    <th>총 행수</th>
                    <td class="text_left" id="total_rows">
                    </td>
                </tr>
                <tr>
                    <th>정상 행수</th>
                    <td class="text_left" id="normal_count">
                    </td>
                </tr>
                <tr>
                    <th>오류 행수</th>
                    <td class="text_left" id="error_count">
                    </td>
                </tr>
                <tr>
                    <th>오류 행</th>
                    <td class="text_left" id="error_rows_td"style="word-break:break-all; height:200px; table-layout: fixed; ">
                        <div style="width: 100%; height: 100%; overflow: auto;">
                            <p id="error_rows">

                            </p>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="btn_set">
            <div class="center">
                <a href="javascript:;" id="" class="large_btn blue_btn btn-xls-insert">적용</a>
                <a href="javascript:;" class="large_btn red_btn btn-common-pop-close">취소</a>
            </div>
        </div>
    </div>
</div>
<script>
    SettleTransaction.TransactionUploadInit();
</script>

