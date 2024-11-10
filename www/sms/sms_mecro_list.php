<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 옵션 추가/수정 페이지
 */
//Page Info
$pageMenuIdx = 1203;
//Init
include_once "../_init_.php";

$C_SmsMecro = new SmsMecro();

$mode = isset($_POST['mode']) ? $_POST['mode'] : "MECRO_LIST";
$member_idx = $_SESSION['dy_member']['member_idx'];

if($mode == "MECRO_LIST") {
    $data = $C_SmsMecro->getSmsMecroList($member_idx);
}else{
    $data = $C_SmsMecro->getSmsMecroTop10List($member_idx);
}
?>
<div class="container popup">
    <div class="content write_page">
        <div class="content_wrap">
            <form name="searchForm" id="searchForm" method="get">
                <input type="hidden" name="mode" value="<?=$mode?>" />
                <div>
                    <div class="div_tab">
                        <a href="javascript:;" class="btn <?=($mode == "MECRO_LIST") ? "btn_sel" : "";?> mecro_reg">등록매크로</a>
                        <a href="javascript:;" class="btn <?=($mode == "MECRO_USE") ? "btn_sel" : "";?> mecro_use">최근사용</a>
                    </div>

                    <div class="mecro_div">
                        <ul class="mecro_ul">
                            <?php
                                if(count($data) > 0) {
                                    for($i=0; $i<count($data); $i++){
                                        $idx = $data[$i]['idx'];
                                        $msg = $data[$i]['mecro_msg'];
                                        $use_date = $data[$i]['mecro_usedate'];
                            ?>
                            <li>
                                <div class="speech-bubble <?=($mode == "MECRO_USE") ? "mecro_used" : "";?>">
                                    <p id="msg_<?=$idx?>"><?=$msg?></p>
                                    <div class="div_center">
                                        <a href="javascript:;" class="btn xsmall_btn btn_mecro_sel" data-idx="<?=$idx?>">선택</a>
                                        <a href="javascript:;" class="btn xsmall_btn btn_mecro_del" data-idx="<?=$idx?>">삭제</a>
                                    </div>
                                    <?php
                                    if($mode != 'MECRO_LIST') {
                                        echo "<span class='mecro_label'>" . date('Y-m-d H:i:s', strtotime($use_date)) . "</span>";
                                    }
                                    ?>
                                </div>
                            </li>
                            <?php      }
                                }else{
                            ?>
                            <li>
                                <div class="speech-bubble">
                                    <p>등록된 매크로가 없습니다.</p>
                                </div>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="btn_mecro_close ">
                    <div class="center">
                        <a href="javascript:;" class="large_btn red_btn btn-mecro-close">닫기</a>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
<style>

    .btn_sel {
        background-color: #49697f;
        color: #fff !important;
    }

    .mecro_label {
        color: #fff;
        padding-top: 10px;
        padding-left: 10px;
    }
    .mecro_div {
        margin: 10px;
    }
    .mecro_ul li {
        float: left;
        margin-right: 20px;
    }
    .clearfix {
        clear: both;
    }
    .btn_mecro_close{
        margin-top: 50px;
        text-align: center;
    }
    .speech-bubble {
        position: relative;
        background: #239aeb;
        border-radius: .4em;
        height: 120px;
        width:120px;
    }
    .mecro_used {
        height: 145px;
        width:145px;
    }
    .speech-bubble p {
        color: #fff;
        padding: 10px;
        text-align: center;
        height:82px;
        overflow: auto;
    }
</style>
<script>

    $(".btn-mecro-close").on("click", function(){
        $("#modal_sms_mecro_list").dialog( "close" );
    });

    $('.btn_mecro_sel').on("click", function(){
       var idx = $(this).attr('data-idx');
       var con = $('#msg_' + idx).text();
       $('#mecro_idx').val('');
       $('#mecro_idx').val(idx);
       $('#sms_con').val('');
       $('#sms_con').val(con);
       $("#modal_sms_mecro_list").dialog( "close" );
    });

    $('.btn_mecro_del').on("click", function(){
        var idx = $(this).attr('data-idx');

        var dataObj = new Object();
        dataObj.mode = "MECRO_DEL";
        dataObj.idx = idx;

        if(confirm('선택하신 매크로를 삭제하시겠습니까?')) {
            showLoader();
            $.ajax({
                type: 'POST',
                url: 'sms_send_proc.php',
                dataType: "json",
                data: dataObj
            }).done(function (response) {
                if (response.result) {
                    alert('삭제 되었습니다');
                    $('#sms_mecro').trigger('click');
                } else {
                    alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
                }
                hideLoader();
            }).fail(function (jqXHR, textStatus) {
                alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
                hideLoader();
            });
        }
    });

    $('.mecro_use').on("click", function(){
        $('#lmode').val('MECRO_USE');
        $('#sms_mecro').click();
    });

    $('.mecro_reg').on("click", function(){
        $('#lmode').val('MECRO_LIST');
        $('#sms_mecro').click();
    });
</script>

