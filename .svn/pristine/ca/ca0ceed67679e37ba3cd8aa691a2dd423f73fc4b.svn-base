<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 옵션 추가/수정 페이지
 */
//Page Info
$pageMenuIdx = 103;
//Init
include_once "../_init_.php";

$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$month =  isset($_GET['month']) ? $_GET['month'] : date('m');

?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
    <?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
    <div class="content">
        <form name="searchForm" id="searchForm" method="get">
            <div class="find_wrap">
                <div class="finder">
                    <div class="finder_set">
                        <div class="finder_col">
                            <span class="text">조회년월</span>
                            <input type="text" class="yearpicker w80px" name="year" id="year" value="<?=isset($year) ? $year : "";?>">년
                            <select name="month">
                                <option value="">선택</option>
                                <option value="01" <?=($month == "01") ? "selected" : "" ?>>01</option>
                                <option value="02" <?=($month == "02") ? "selected" : "" ?>>02</option>
                                <option value="03" <?=($month == "03") ? "selected" : "" ?>>03</option>
                                <option value="04" <?=($month == "04") ? "selected" : "" ?>>04</option>
                                <option value="05" <?=($month == "05") ? "selected" : "" ?>>05</option>
                                <option value="06" <?=($month == "06") ? "selected" : "" ?>>06</option>
                                <option value="07" <?=($month == "07") ? "selected" : "" ?>>07</option>
                                <option value="08" <?=($month == "08") ? "selected" : "" ?>>08</option>
                                <option value="09" <?=($month == "09") ? "selected" : "" ?>>09</option>
                                <option value="10" <?=($month == "10") ? "selected" : "" ?>>10</option>
                                <option value="11" <?=($month == "11") ? "selected" : "" ?>>11</option>
                                <option value="12" <?=($month == "12") ? "selected" : "" ?>>12</option>
                            </select>월
                        </div>
                        <div class="finder_col">
                            <span class="text">검색어</span>
                            <select name="search_column" id="search_column">
                                <option value="">전체</option>
                                <option value="DEST_PHONE">받는사람번호</option>
                                <option value="TEMPLATE_CODE">템플릿코드</option>
                            </select>
                        </div>
                        <div class="finder_col">
                            <input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" />
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
<script src="/js/main.js"></script>
<link href="/js/yearpicker/yearpicker.css" rel="stylesheet" type="text/css" />
<script src="/js/yearpicker/yearpicker.js"></script>
<script>
    var AlimTalkUse = (function() {
        var root = this;

        var init = function () {
        };

        var sms_error_code = [
            {code : "9903", msg :'선불사용자 사용금지'},
            {code : "9904", msg :'Block time (날짜제한)'},
            {code : "9081", msg :'선불 사용자 FAX, PHONE 발송 제한'},
            {code : "9082", msg :'발송해제'},
            {code : "9083", msg :'IP 차단 '},
            {code : "9084", msg :'DEVICE 발송 제한'},
            {code : "9085", msg :'사용금지 Callback 번호'},
            {code : "9905", msg :'Block time'},
            {code : "9011", msg :'비밀번호 틀림'},
            {code : "9012", msg :'중복 접속 량 많음 '},
            {code : "9014", msg :'알림톡/친구톡 유효하지 않은 발신프로필키'},
            {code : "9016", msg :'알림톡/친구톡 템플릿 미 입력'},
            {code : "9017", msg :'존재하지 않는 첨부파일'},
            {code : "9018", msg :'0 바이트 첨부파일'},
            {code : "9019", msg :'지원하지 않는 첨부파일'},
            {code : "9020", msg :'Wrong Data Format'},
            {code : "9022", msg :'Wrong Data Format (ex. cinfo 가 특수 문자 / , 공백 을 포함)'},
            {code : "9023", msg :'시간제한 (리포트 수신대기 timeout)'},
            {code : "9024", msg :'Wrong Data Format (ex. 메시지 본문 길이)'},
            {code : "9026", msg :'블랙리스트에 의한 차단 '},
            {code : "9027", msg :'MMS 첨부파일 이미지 사이즈 초과'},
            {code : "9080", msg :'Deny User Ack'},
            {code : "9214", msg :'Wrong Phone Num'},
            {code : "9311", msg :'Uploaded File Not Found'},
            {code : "9908", msg :'PHONE, FAX 선불사용자 제한기능'},
            {code : "9090", msg :'기타에러'},
            {code : "7000", msg :'전송완료'},
            {code : "7101", msg :'카카오 형식 오류'},
            {code : "7103", msg :'Sender key (발신프로필키) 유효하지 않음'},
            {code : "7105", msg :'Sender key (발신프로필키) 존재하지 않음'},
            {code : "7106", msg :'삭제된 Sender key (발신프로필키)'},
            {code : "7107", msg :'차단 상태 Sender key (발신프로필키)'},
            {code : "7108", msg :'차단 상태 옐로우 아이디'},
            {code : "7109", msg :'닫힌 상태 옐로우 아이디 '},
            {code : "7110", msg :'삭제된 옐로우 아이디'},
            {code : "7203", msg :'친구톡 전송 시 친구대상 아님'},
            {code : "7204", msg :'템플릿 불일치'},
            {code : "7300", msg :'기타에러'},
            {code : "7305", msg :'성공불확실(30 일 이내 수신 가능)'},
            {code : "7306", msg :'카카오 시스템 오류'},
            {code : "7308", msg :'전화번호 오류'},
            {code : "7311", msg :'메시지가 존재하지 않음'},
            {code : "7314", msg :'메시지 길이 초과 '},
            {code : "7315", msg :'템플릿 없음 '},
            {code : "7318", msg :'메시지를 전송할 수 없음 '},
            {code : "7322", msg :'메시지 발송 불가 시간'},
            {code : "7323", msg :'메시지 그룹 정보를 찾을 수 없음'},
            {code : "7324", msg :'재전송 메시지 존재하지 않음 '},
            {code : "7421", msg :'타임아웃'},
            {code : "4100", msg :'전송완료'},
            {code : "4400", msg :'음영 지역'},
            {code : "4401", msg :'단말기 전원 꺼짐'},
            {code : "4402", msg :'단말기 메시지 저장 초과'},
            {code : "4403", msg :'메시지 삭제 됨'},
            {code : "4404", msg :'가입자 위치 정보 없음'},
            {code : "4405", msg :'단말기 BUSY '},
            {code : "4410", msg :'잘못된 번호'},
            {code : "4420", msg :'기타에러'},
            {code : "4430", msg :'스팸'},
            {code : "4431", msg :'발송제한 수신거부(스팸) '},
            {code : "4411", msg :'NPDB 에러'},
            {code : "4412", msg :'착신거절'},
            {code : "4413", msg :'SMSC 형식오류'},
            {code : "4414", msg :'비가입자,결번,서비스정지'},
            {code : "4421", msg :'타임아웃'},
            {code : "4422", msg :'단말기일시정지'},
            {code : "4423", msg :'단말기착신거부'},
            {code : "4424", msg :'URL SMS 미지원폰'},
            {code : "4425", msg :'단말기 호 처리 중'},
            {code : "4426", msg :'재시도한도초과'},
            {code : "4427", msg :'기타 단말기 문제'},
            {code : "4428", msg :'시스템에러'},
            {code : "4432", msg :'회신번호 차단(개인)'},
            {code : "4433", msg :'회신번호 차단(기업)'},
            {code : "4434", msg :'회신번호 사전 등록제에 의한 미등록 차단'},
            {code : "4435", msg :'KISA 신고 스팸 회신번호 차단'},
            {code : "4436", msg :'회신번호 사전 등록제 번호규칙 위반 '},
            {code : "6600", msg :'전송완료'},
            {code : "6601", msg :'타임 아웃'},
            {code : "6602", msg :'핸드폰 호 처리 중'},
            {code : "6603", msg :'음영 지역'},
            {code : "6604", msg :'전원이 꺼져 있음'},
            {code : "6605", msg :'메시지 저장개수 초과'},
            {code : "6606", msg :'잘못된 번호'},
            {code : "6607", msg :'서비스 일시 정지'},
            {code : "6608", msg :'기타 단말기 문제'},
            {code : "6609", msg :'착신 거절'},
            {code : "6610", msg :'기타에러'},
            {code : "6611", msg :'통신사의 SMC 형식 오류'},
            {code : "6612", msg :'게이트웨이의 형식 오류'},
            {code : "6613", msg :'서비스 불가 단말기'},
            {code : "6614", msg :'핸드폰 호 불가 상태'},
            {code : "6615", msg :'SMC 운영자에 의해 삭제'},
            {code : "6616", msg :'통신사의 메시지 큐 초과'},
            {code : "6617", msg :'통신사의 스팸 처리'},
            {code : "6618", msg :'공정위의 스팸 처리'},
            {code : "6619", msg :'게이트웨이의 스팸 처리'},
            {code : "6620", msg :'발송 건수 초과'},
            {code : "6621", msg :'메시지의 길이 초과'},
            {code : "6622", msg :'잘못된 번호 형식'},
            {code : "6623", msg :'잘못된 데이터 형식'},
            {code : "6624", msg :'MMS 정보를 찾을 수 없음'},
            {code : "6625", msg :'NPDB 에러'},
            {code : "6626", msg :'080 수신거부(SPAM)'},
            {code : "6627", msg :'발송제한 수신거부(SPAM)'},
            {code : "6628", msg :'회신번호 차단(개인)'},
            {code : "6629", msg :'회신번호 차단(기업)'},
            {code : "6630", msg :'서비스 불가 번호'},
            {code : "6631", msg :'회신번호 사전 등록제에 의한 미등록 차단'},
            {code : "6632", msg :'KISA 신고 스팸 회신번호 차단'},
            {code : "6633", msg :'회신번호 사전 등록제 번호규칙 위반'},
            {code : "6670", msg :'첨부파일 사이즈 초과(60K)'},
        ];

        //템플릿 리스트 초기화
        var AlimTalkUseListInit = function() {

            //템플릿 목록 바인딩 jqGrid
            $("#grid_list").jqGrid({
                url: './sms_alimtalk_use_list_grid.php',
                mtype: "GET",
                datatype: "json",
                jsonReader: {
                    page: "page",
                    total: "total",
                    root: "rows",
                    records: "records",
                    repeatitems: true,
                    id: "idx"
                },
                colModel: [
                    {label: 'CMID', name: 'CMID', index: 'CMID', width: 150},
                    {label: 'UMID', name: 'UMID', index: 'UMID', width: 250},
                    {label: 'MSG_TYPE', name: 'MSG_TYPE', index: 'MSG_TYPE', width: 90, sortable: false,
                        formatter: function(cellvalue, options, rowobject){
                            if(cellvalue == "0") { return "SMS"};
                            if(cellvalue == "5") { return "LMS"};
                            if(cellvalue == "6") { return "알림톡"};
                        }
                    },
                    {label: 'STATUS', name: 'STATUS', index: 'STATUS', width: 70,             
                        formatter: function(cellvalue, options, rowobject){
                            if(cellvalue == "0") { return "발송대기"};
                            if(cellvalue == "1") { return "발송중"};
                            if(cellvalue == "2") { return "발송완료"};
                            if(cellvalue == "3") { return "에러"};
                        }
                    },
                    {label: 'CALL_STATUS', name: 'CALL_STATUS', index: 'CALL_STATUS', width: 150,
                        formatter: function(cellvalue, options, rowobject){
                            var msg = "";
                            $.each(sms_error_code, function(index, item){
                                if(item.code == $.trim(cellvalue)) {
                                    msg = item.msg;
                                }
                            });
                            return msg;
                        }
                    },
                    {label: 'REQUEST_TIME', name: 'REQUEST_TIME', index: 'REQUEST_TIME', width: 180, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
                    {label: 'SEND_TIME', name: 'SEND_TIME', index: 'SEND_TIME', width: 180, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
                    {label: 'DEST_PHONE', name: 'DEST_PHONE', index: 'DEST_PHONE', width: 120, sortable: false},
                    {label: 'SUBJECT', name: 'SUBJECT', index: 'SUBJECT', width: 200, sortable: false},
                    {label: 'MSG_BODY', name: 'MSG_BODY', index: 'MSG_BODY', width: 300, sortable: false, align: 'left'},
                    {label: 'TEMPLATE_CODE', name: 'TEMPLATE_CODE', index: 'TEMPLATE_CODE', width: 250, sortable: false},
                ],
                rowNum: Common.jsSiteConfig.jqGridRowList[1],
                rowList: Common.jsSiteConfig.jqGridRowList,
                pager: '#grid_pager',
                sortname: 'REQUEST_TIME',
                sortorder: "desc",
                viewrecords: true,
                autowidth: true,
                rownumbers: true,
                shrinkToFit: false,
                height: Common.jsSiteConfig.jqGridDefaultHeight,
                loadComplete: function () {


                }
            });

            //브라우저 리사이즈 시 jqgrid 리사이징
            $(window).on("resize", function(){
                Common.jqGridResize("#grid_list");
            }).trigger("resize");

            //검색 폼 Submit 방지
            $("#searchForm").on("submit", function(e){
                e.preventDefault();
            });

            //Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
            $("input.enterDoSearch").on("keyup", function(e){
                var keyCode = (event.keyCode ? event.keyCode : event.which);
                if (keyCode == 13) {
                    event.preventDefault();
                    AlimTalkUseListSearch();
                }
            });

            //검색 버튼 클릭 이벤트
            $("#btn_searchBar").on("click", function(){
                AlimTalkUseListSearch();
            });
        };

        var AlimTalkUseListSearch = function(){
            $("#grid_list").setGridParam({
                datatype: "json",
                postData:{
                    param: $("#searchForm").serialize()
                }
            }).trigger("reloadGrid");
        };

        return {
            AlimTalkUseListInit: AlimTalkUseListInit,
        }
    })();

    window.name = 'sms_alimtalk_use_list';
    AlimTalkUse.AlimTalkUseListInit();
</script>

