<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품목록 페이지
 */
//Page Info
$pageMenuIdx = 101;
//Init
include_once "../_init_.php";
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
                            <span class="text">상태</span>
                            <select name="tp_use">
                                <option value="">전체</option>
                                <option value="Y">사용</option>
                                <option value="N">미사용</option>
                            </select>
                        </div>
                        <div class="finder_col">
                            <span class="text">검색어</span>
                            <select name="search_column" id="search_column">
                                <option value="">전체</option>
                                <option value="tp_name">템플릿명</option>
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

        <div class="grid_btn_set_top">
            <a href="javascript:;" class="btn btn-template-write-pop">템플릿 신규등록</a>
            <a href="./bizppurio_KAKAOTLAK_v1.2.pdf" target="_blank" class="btn btn-manage-group-pop">알림톡 사용방법 가이드</a>
        </div>
        <div class="tb_wrap grid_tb">
            <table id="grid_list">
            </table>
            <div id="grid_pager"></div>
        </div>
    </div>
</div>
    <script src="/js/main.js"></script>
    <script>
        var Template_Setup = (function() {
            var root = this;

            var init = function () {
            };

            //판매처 등록 수정 팝업 함수
            var TemplateWritePopup = function(idx) {
                var url = '/sms/sms_template_write_pop.php';
                url += (idx != '') ? '?idx=' + idx : '';
                Common.newWinPopup(url, 'template_write_pop', 700, 720, 'yes');
            };

            //템플릿 리스트 초기화
            var TemplateListInit = function(){
                //신규등록 팝업
                $(".btn-template-write-pop").on("click", function(){
                    TemplateWritePopup('');
                });

                //템플릿 목록 바인딩 jqGrid
                $("#grid_list").jqGrid({
                    url: './sms_template_list_grid.php',
                    mtype: "GET",
                    datatype: "json",
                    jsonReader : {
                        page: "page",
                        total: "total",
                        root: "rows",
                        records: "records",
                        repeatitems: true,
                        id: "idx"
                    },
                    colModel: [
                        { label: 'No', name: 'idx', index: 'idx', width: 50, hidden:true},
                        { label:'수정', name: '수정', width: 100,formatter: function(cellvalue, options, rowobject){
                                //console.log(rowobject);
                                return '<a href="javascript:;" class="xsmall_btn btn-template-modify-pop" data-idx="'+rowobject.idx+'">수정</a>';
                            }, sortable: false
                        },
                        { label:'삭제', name: '삭제', width: 100,formatter: function(cellvalue, options, rowobject){
                                //console.log(rowobject);
                                return '<a href="javascript:;" class="xsmall_btn btn-template-delete-pop" data-idx="'+rowobject.idx+'">삭제</a>';
                            }, sortable: false
                        },
                        { label: '템플릿코드', name: 'tp_code', index: 'tp_code', width: 250, sortable: false},
                        { label: '템플릿명', name: 'tp_name', index: 'tp_name', width: 200},
                        { label: '템플릿내용', name: 'tp_con', index: 'tp_con', width: 400, align: 'left'},
                        { label: '치환코드', name: 'tp_replace_code', index: 'tp_replace_code', width: 400, sortable: false},
                        { label: '사용여부', name: 'tp_use', index: 'tp_use', width: 80, sortable: false},
                        { label: '등록일', name: 'tp_regdate', index: 'tp_regdate', width: 200,formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }}
                    ],
                    rowNum: Common.jsSiteConfig.jqGridRowList[1],
                    rowList: Common.jsSiteConfig.jqGridRowList,
                    pager: '#grid_pager',
                    sortname: 'tp_regdate',
                    sortorder: "desc",
                    viewrecords: true,
                    autowidth: true,
                    rownumbers: true,
                    shrinkToFit: false,
                    height: Common.jsSiteConfig.jqGridDefaultHeight,
                    loadComplete: function(){
                        //수정 팝업
                        $(".btn-template-modify-pop").on("click", function(){
                            TemplateWritePopup($(this).data("idx"));
                        });

                        $(".btn-template-delete-pop").on("click", function(){
                            TemplateDelete($(this).data("idx"));
                        });
                    }
                });
                //$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});


                //판매처 등록 수정 팝업 함수
                var TemplateDelete = function(idx) {

                    var p_url = "sms_send_proc.php";
                    var dataObj = new Object();
                    dataObj.mode = "SMS_TEMPLATE_DEL";
                    dataObj.idx = idx;

                    if(!confirm('삭제 하시겠습니까?')){
                    	return false;
                    }


                    showLoader();
                    $.ajax({
                        type: 'POST',
                        url: p_url,
                        dataType: "json",
                        data: dataObj
                    }).done(function (response) {
                        if(response.result)
                        {
                            alert("삭제 되었습니다.");
                            location.reload();
                        }else{
                            alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
                        }
                        hideLoader();
                    }).fail(function(jqXHR, textStatus){
                        alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
                        hideLoader();
                    });
                };

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
                        TemplateListSearch();
                    }
                });

                //검색 버튼 클릭 이벤트
                $("#btn_searchBar").on("click", function(){
                    TemplateListSearch();
                });
            };

            //판매처 목록/검색
            var TemplateListSearch = function(){
                $("#grid_list").setGridParam({
                    datatype: "json",
                    postData:{
                        param: $("#searchForm").serialize()
                    }
                }).trigger("reloadGrid");
            };

            var TemplateListReload = function(){
                TemplateListSearch();
            };

            return {
                TemplateListReload : TemplateListReload,
                TemplateListInit: TemplateListInit,
            }
        })();

        window.name = 'sms_template_setup';
        Template_Setup.TemplateListInit();
    </script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>