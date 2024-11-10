<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공통으로 사용되는 정보 변경이력 팝업
 */
//Page Info
$pageMenuIdx = 208;
//Init
include_once "../_init_.php";

$tel = $_GET["mobile"];
$order_pack_idx = $_GET["order_pack_idx"];
$order_idx = $_GET["order_idx"];
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
    <?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
    <div class="content write_page">
        <div class="content_wrap">
            <div class="m-t-20">
	            <input type="hidden" name="order_idx" id="order_idx" value="<?=$order_idx?>" />
	            <input type="hidden" name="order_pack_idx" id="order_pack_idx" value="<?=$order_pack_idx?>" />
                <div class="smsSendWrap">
                    <div class="innerTop advertise">
                        <div class="inner">
                            <ul class="type_area">
                                <li id="" class="on"><a href="javascript:;">SMS</a></li>
                            </ul>
                            <div class="text_tit">
                                <input type="text" id="sms_title" name="sms_title" placeholder="제목을 입력하세요." title="sms 전송제목 입력">
                            </div>
                            <div class="inputBox">
                                <div class="msgInput">
                                    <textarea name="sms_con" id="sms_con" rows="5" cols="20"  title="sms내용 입력"></textarea>
                                </div>
                                <div class="txt_num">
                                    <span class="right"><label class="point" id="r_cnt">0</label>/<label id="m_cnt">90</label>byte</span>
                                </div>
                            </div>
                            <!-- 자동 LMS전환 시작 -->
                            <div class="mailMg">
                                <label for="autoLMS">90byte 초과시 자동 LMS전환</label>
                                <a class="toolTipTrigger" title="입력 내용이 90byte를 넘을경우 LMS로 자동전환됩니다."><img src="/images/sms/icoguide_W.gif" alt="자동 LMS전환 안내글"></a>
                            </div>
                            <div style="margin-left: 20px; margin-top: 10px;">
                                <label>보내는 사람 :</label>
                                <select name="sms_sender" id="sms_sender">
                                    <option value="">선택</option>
                                    <?php
                                    foreach($GL_SmsSendPhone as $k => $v) {
                                        ?>
                                        <option value="<?=$k?>"><?=$v?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <!-- 자동 LMS전환 끝 -->
                            <div style="margin-left: 20px; margin-top: 10px;">
                                <label>받는 사람 :</label>
                                <input type="text" id="reciver_phone" name="reciver_phone" value="<?=$tel?>" title="받는사람" placeholder="받는사람" class="w120px">
                            </div>
                            <p style="text-align: center;">
                                <a href="javascript:;" class="btn" id="sms_mecro">매크로 보기</a>
                                <a href="javascript:;" class="btn" id="sms_mecro_save">매크로 저장</a>
                            </p>
                            <p class="txtBtnArea">
                                <a href="javascript:" id="send_btn">메시지 발송</a> <a href="javascript:location.reload();">새로쓰기 </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="" style="float:left; margin-left: 10px; width:70%;">
                <form name="searchForm" id="searchForm" method="get">
                    <input type="hidden" name="view" value="<?=$_GET["view"]?>" />
                    <div class="find_wrap">
                        <div class="finder">
                            <div class="finder_set">
                                <div class="finder_col">
                                    <input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " readonly="readonly" />
                                    ~
                                    <input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " readonly="readonly" />
                                    <select class="sel_period_preset" id="period_preset_select">

                                    </select>
                                </div>
                                <div class="finder_col">
                                    <span class="text">작업자</span>
                                    <select name="search_column">
                                        <option value="">전체</option>
                                        <option value="A.sms_receive_num">받는사람 연락처</option>
                                        <option value="A.sms_msg">보낸 내용</option>
                                    </select>
                                    <input type="text" name="search_keyword" class="w150px enterDoSearch" placeholder="검색어" />
                                </div>
                            </div>
                        </div>
                        <div class="find_btn">
                            <div class="table">
                                <div class="table_cell">
                                    <a href="javascript:;" id="btn_searchBar" class="wide_btn btn_default">검색</a>
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
                    <table id="grid_list">
                    </table>
                    <div id="grid_pager"></div>
                </div>
            </div>
        </div>

        <div id="modal_sms_mecro_list" title="매크로 정보" class="red_theme" style="display: none;"></div>
        <input type="hidden" name="mecro_idx" id="mecro_idx" value="" />
    </div>
</div>
<script src="/js/page/common.function.js"></script>
<script src="/js/main.js"></script>
<script src="/js/page/sms.send.js"></script>
<script>
   $(function(){
	   SMS.PersonalSendListInit();
   });
</script>
<style>
    .m-t-10 { margin-top: 10px;}
    .m-t-20 { margin-top: 20px;}
    .p-l-10 { padding-left: 10px;}
    .p-l-20 { padding-left: 20px;}
    .p-l-30 { padding-left: 30px;}
    .smsSendWrap {float:left;width:250px; margin:0 20px 0 0;}
    .smsSendWrap .innerTop{padding-top:35px;background:url(../images/sms/bgPhoneTop.gif) no-repeat left top}
    .smsSendWrap .innerTop .inner{background:url(../images/sms/bgPhoneBody.gif) repeat-y left top}
    .smsSendWrap .innerTop .inputBox {height:155px;padding:40px 35px 20px;background:url(../images/sms/bgSmsInputA.gif) no-repeat left top}
    .smsSendWrap .innerTop .inputBox span{display:block;margin-top:10px;text-align:right;}
    .smsSendWrap .innerTop .addInputBox{margin-top:-4px;*margin-top:-8px;padding-left:20px;}
    .smsSendWrap .innerTop .addInputBox .addInner{position:relative;width:150px;padding:25px 35px 20px 25px;margin:0 atuo;background:url(../images/sms/bgSmsInputAdd.gif) no-repeat left top}
    .smsSendWrap .innerTop .addInputBox span{display:block;width:170px;margin-top:10px;text-align:right;}
    .smsSendWrap .innerTop .addInputBox .btnDelet{position:absolute;bottom:20px;left:25px;}
    .smsSendWrap .innerTop .inputWrap{width:210px;hegiht:30x;margin:10px auto 0;background:url(../images/sms/bgSmsInput.gif) no-repeat center top;}
    .smsSendWrap .innerTop .inputWrap input {border:0px;width:186px;padding:8px 12px;color:#727272;background-color:transparent}
    .smsSendWrap .innerTop .txtBtnArea{height:90px;padding-top:20px;text-align:center;background:url(../images/sms/bgPhoneBottom.gif) no-repeat left bottom;}
    .smsSendWrap .innerTop .txtBtnArea .inner {width:205px;margin:0 auto;background-image:none;}
    .smsSendWrap .innerTop .txtBtnArea a,
    .smsSendWrap .innerTop .txtBtnArea button {display:inline-block;float:left;width:90px;height:14px;padding:0 10px 0 17px;color:#505050; font-size:13px; text-align:center;font-weight:bold;background:url(../images/sms/bgBar.gif) no-repeat 6px center; line-height:14px;}/* [150629] sms전송 버튼 */
    .smsSendWrap .innerTop .txtBtnArea a:first-child,
    .smsSendWrap .innerTop .txtBtnArea button:first-child{width:90px; background-image:none; padding:0 0 0 10px;}/* [150629] sms전송 버튼 */

    .smsSendWrap .reNumList{width:210px;margin:0 auto;}
    .smsSendWrap .reNumList .inputPnum{width:208px;height:130px;margin-top:5px;border:1px solid #d5d5d5;border-radius:1px;overflow-y:scroll;background-color:#fff;}
    .smsSendWrap .reNumList .inputPnum li{border-bottom:1px solid #efefef}
    .smsSendWrap .reNumList .inputPnum li span,
    .smsSendWrap .reNumList .inputPnum li .inputText{display:inline-block;}
    .smsSendWrap .reNumList .inputPnum li span{padding:5px 13px 4px;text-align:center;border-right:1px solid #efefef}
    .smsSendWrap .reNumList .inputPnum li .inputText{width:140px;padding:0 2px;border:0px;}
    .smsSendWrap .reNumList .topArea{margin-top:5px;*zoom:1}
    .smsSendWrap .reNumList .topArea h3 img{margin-top:7px;}
    .smsSendWrap .reNumList .topArea > p:first-child {margin-top:4px;}
    .smsSendWrap .reNumList .topArea:after{content:"";display:block;clear:both;}
    .smsSendWrap .reNumList .btnArea{margin:5px 0 10px;font-size:11px;font-family:Dotum,"돋움";*zoom:1}
    .smsSendWrap .reNumList .btnArea:after{content:"";display:block;clear:both;}
    .smsSendWrap .reNumList .btnArea .btn{float:left;margin-right:2px;}
    .smsSendWrap .reNumList .btnLarge{display:block;padding:7px 0;margin:8px 0;color:#4f4f4f;text-align:center;font-weight:bold;border:1px solid #c5c5c5;border-radius:2px;background-color:#fff;}
    .smsSendWrap .reNumList .btnArea .guide{float:right;color:#919191;padding-top:4px;letter-spacing:-1px;}

    .smsSendWrap .reNumList .nearNum{margin-top:5px;border:1px solid #c5c5c5;border-radiul:2px;background-color:#fff;*zoom:1}
    .smsSendWrap .reNumList .nearNum .inputText{float:left;width:135px;padding:4px;border:1px solid #f0f0f0;border-width:1px 0 0 0;color:#818181;}
    .smsSendWrap .reNumList .nearNum .btn{display:inline-block;float:right;width:60px;height:28px;vertical-align:middle;color:#616161;line-height:28px;text-align:center;border-left:1px solid #c5c5c5}
    .smsSendWrap .reNumList .nearNum:after{content:"";display:block;clear:both;}
    .smsSendWrap .reNumList .multiDel{margin-top:10px;font-size:11px;font-family:Dotum;color:#747474}
    .smsSendWrap .reNumList textarea{width:165px;height:110px;overflow-y:scroll;border:0px}

    /* saveMessageResistration */
    .smsSendWrap .smsSendBottom{height:60px;background:url(../images/sms/bgBodyBottom01.gif) no-repeat left top;}
    .smsSendWrap .innerTop .registLmsInner .inputBox{height:302px;background:url(../images/sms/bgPhoneTopMiddleLms.gif) no-repeat left top}
    .smsSendWrap .innerTop .callback .inputBox{height:206px;background:url(../images/sms/bgPhoneTopMiddleCallback.gif) no-repeat left top}
    .smsSendWrap .innerTop .callback .inputBox textarea{height:30px;width:145px;padding:10px;overflow:hidden;border:1px solid #d5d5d5}
    .smsSendWrap .innerTop .callback .inputBox span.left{margin-bottom:70px}

    .smsSendWrap .innerTop.advertise {padding-top:35px;}
    .smsSendWrap .innerTop.advertise .inner {background:url(../images/sms/bgSmsInputA2.gif) no-repeat left top}
    .smsSendWrap .innerTop.advertise .type_area {padding:0 20px 15px; height:26px;}
    .smsSendWrap .innerTop.advertise .type_area li {width:210px; height:26px; float:left; text-align: center; background-color: #B9B9B9; padding-top: 10px; padding-bottom: 20px; }
    .smsSendWrap .innerTop.advertise .type_area li a{ color:#ffffff; }
    .smsSendWrap .innerTop.advertise .type_area li.on {background-position:left 0px; background-color: #727683; }
    .smsSendWrap .innerTop.advertise .text_tit input{margin:0px 20px 2px; padding:2px 5px 0 12px; width:193px; height:30px; border:none; background:url(../images/sms/text_tit_on.gif) no-repeat left top;}/* [0225] */
    @media \0screen { .smsSendWrap .innerTop.advertise .text_tit input{padding:7px 5px 0 12px; height:25px;} }
    *:first-child+html .smsSendWrap .innerTop.advertise .text_tit input {padding:7px 5px 0 12px; height:25px;}
    .smsSendWrap .innerTop.advertise .text_tit input:disabled {background:url(../images/sms/text_tit_off.gif) no-repeat left top;}
    .smsSendWrap .innerTop.advertise .inputBox {margin:0px 20px 10px; padding:5px 0 0; background:url(../images/sms/bg_txtTop.gif) no-repeat left top}
    .smsSendWrap .innerTop.advertise .inputBox .msgInput {padding:10px 15px 0px; border-left:1px solid #c5c5c5; border-right:1px solid #c5c5c5; background:#fff;}
    .smsSendWrap .innerTop.advertise .inputBox .msgInput textarea {padding:0;width:100%; height:140px;}
    .smsSendWrap .innerTop.advertise .inputBox .txt_num {padding:0 0 5px; background:#fff url(../images/sms/bg_txtBottom.gif) no-repeat left bottom}
    .smsSendWrap .innerTop.advertise .inputBox .right {margin:0; padding:10px 10px 2px; border-left:1px solid #c5c5c5; border-right:1px solid #c5c5c5; background:#fff;}
    .smsSendWrap .innerTop.advertise .mailMg {margin:40px 0 0px 20px;}
    .smsSendWrap .innerTop.advertise .inner p.center {margin-top:5px;}
    .smsSendWrap .innerTop.advertise .inner .txtBtnArea {padding-top:14px; padding:14px 0 0 26px;}/* [150629] sms전송 버튼 */
    .smsSendWrap .innerTop.advertise .addInputBox {margin:-10px 20px 0; padding:5px 0 0; background:url(../images/sms/bg_txtTop.gif) no-repeat left top}
    .smsSendWrap .innerTop.advertise .addInputBox .addInner {width:210px; padding:0 0 5px; background:#fff url(../images/sms/bg_txtBottom.gif) no-repeat left bottom}
    .smsSendWrap .innerTop.advertise .addInputBox .msgInput {padding:10px 15px 0px; border-left:1px solid #c5c5c5; border-right:1px solid #c5c5c5; background:#fff;}
    .smsSendWrap .innerTop.advertise .addInputBox textarea {padding:0;width:100%; height:108px;}
    .smsSendWrap .innerTop.advertise .addInputBox .right {margin:0; padding:10px 10px 2px; width:188px; border-left:1px solid #c5c5c5; border-right:1px solid #c5c5c5; background:#fff;}
    .smsSendWrap .innerTop.advertise .addInputBox .btnDelet {left:15px; bottom:8px;}
    .smsSendWrap .innerTop.advertise .inner .txtBtnArea .inner {background:none; text-align:center;}
    .smsSendWrap .innerTop.advertise .inner .txtBtnArea .inner button + button {width:115px; padding-left:20px;}
</style>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
