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

$member_idx = $_SESSION['dy_member']['member_idx'];

$data = $C_SmsMecro->getSmsTemplateList();
?>
<div class="container popup">
    <div class="content write_page">
        <div class="content_wrap">
            <form name="searchForm" id="searchForm" method="get">
                <div>
                    <div class="mecro_div">
                        <ul class="mecro_ul">
                            <?php
                            if(count($data) > 0) {
                                for($i=0; $i<count($data); $i++){
                                    $idx = $data[$i]['idx'];
                                    $tp_con = $data[$i]['tp_con'];
                                    $tp_code = $data[$i]['tp_code'];
                                    $tp_replace_code = $data[$i]['tp_replace_code'];
                                    ?>
                                    <li>
                                        <div class="speech-bubble">
                                            <p id="msg_<?=$tp_code?>"><?=nl2br($tp_con)?></p>
                                            <div class="div_center">
                                                <a href="javascript:;" class="btn xsmall_btn btn_template_sel" data-idx="<?=$tp_code?>" data-value="<?=$tp_replace_code?>">선택</a>
                                            </div>
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
                        <a href="javascript:;" class="large_btn red_btn btn-al-close">닫기</a>
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
        height: 240px;
        width:240px;
    }
    .mecro_used {
        height: 145px;
        width:145px;
    }
    .speech-bubble p {
        color: #fff;
        padding: 10px;
        text-align: left;
        height: 200px;
        overflow: auto;
    }

	.btn_template_sel{
		position: absolute;
		bottom: 0;
		left: 50%;
		transform: translate(-50%, -50%);
	}

</style>
<script>

    $(".btn-al-close").on("click", function(){
        $("#modal_sms_template_list").dialog( "close" );
    });

    $('.btn_template_sel').on("click", function(){
        var idx = $(this).attr('data-idx');
        var cde = $(this).attr('data-value');
        var con = $('#msg_' + idx).text();
        $('#tp_code').val('');
        $('#tp_code').val(idx);
        $('#tp_replace_code').val('');
        $('#tp_replace_code').val(cde);
        $('#tp_replace_ex_code').val('');
        $('#sms_con').val('');
        $('#sms_con').val(con);
        $('#al_template_match').show();
        $("#modal_sms_template_list").dialog( "close" );
    });
</script>

