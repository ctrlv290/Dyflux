<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 거래형황 페이지
 */
//Page Info
$pageMenuIdx = 311;
//Init
include_once "../_init_.php";

$date = date('Y-m-d');

$C_Dbconn = new DBConn();

$qry_report_item = "Select * from dy_custom_report_item where is_hidden =N'N' order by sort ASC";
$qry_seller_list = "Select S.seller_idx as idx, S.seller_name as name 
                    From DY_SELLER S
                    WHERE 1=1 
	                AND S.seller_is_use = N'Y' AND S.seller_is_del = N'N'";
$qry_supplier_list = "Select M.idx as idx, S.supplier_name as name From DY_MEMBER M Left Outer Join DY_MEMBER_SUPPLIER S On M.idx = S.member_idx Where M.is_del = N'N' And M.is_use = N'Y' And M.member_type = N'SUPPLIER'";
$C_Dbconn->db_connect();
$custom_report_item = $C_Dbconn->execSqlList($qry_report_item);
$seller_list = $C_Dbconn->execSqlList($qry_seller_list);
$supplier_list = $C_Dbconn->execSqlList($qry_supplier_list);
$C_Dbconn->db_close();

if ($period == "week") {
    $prev_date = date('Y-m-d', strtotime("-6 days", strtotime($date)));
}

?>
<?php include_once DY_INCLUDE_PATH . "/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container">
<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
<div class="content">
    <form name="searchForm" id="searchForm" method="post" action="custom_report_test.php">
        <div class="find_wrap" style="min-width: 1100px; margin-bottom: 0;">
            <div class="finder">
                <div class="finder_set">
                    &nbsp
                    <div class="finder_col">
                        <a href="#" class="all_menu_btn">
                            <i class="fas fa-minus-square"></i>
                        </a>
                    </div>
                    <div class="finder_col" id="sumo_user_template">
                        <select name="user_template" id="user_template">
                        </select>
                        <input type="text" name="user_template_name" id="user_template_name">
                        <a href="javascript:;" id="user_template_save" class="btn">저장</a>
                        <a href="javascript:;" id="user_template_del" class="btn">삭제</a>
                    </div>
                    <div class="find_btn">
                        <div class="table">
                            <div class="table_cell">
                                <a href="javascript:;" id="btn_searchBar" class="wide_btn btn_default">적용</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="top_menu">
                <span class="axis_group_span">
                    <p>분류 <a href="javascript:;" id="btn_reset" class="btn_item_reset"><i class="fas fa-trash-alt"></i></a></p>
                    <div id="axis_group">

                    </div>
                </span>
                <span class="search_condition_span">
                    <p>검색조건 <a href="javascript:;" id="btn_reset" class="btn_item_reset"><i class="fas fa-trash-alt"></i></a></p>
                    <div class="" style="margin-bottom: 5px" id="item_1">
                            <span class="select_item">
                                <span class="point green"></span>
                                <span class="text">시작일</span>
                                <input type="text" name="1" id="1" class="w80px jqDate" value="<?=$date?>" readonly="readonly">
                                <select class="sel_period_preset" id="period_preset_select"></select>
                            </span>
                        </div>
                        <div class="" style="margin-bottom: 5px" id="item_2">
                            <span class="select_item">
                                <span class="point green"></span>
                                <span class="text">종료일</span>
                                <input type="text" name="2" id="2" class="w80px jqDate" value="<?=$date?>" readonly="readonly">
                            </span>
                        </div>
                    <div id="search_condition">
                    </div>
                </span>
              <span class="display_group_span">
                    <p>노출그룹 <a href="javascript:;" id="btn_reset" class="btn_item_reset"><i class="fas fa-trash-alt"></i></a></p>
                    <div id="display_group" >
                    </div>
                </span>
                <span class="display_span">
                    <p>노출항목 <a href="javascript:;" id="btn_reset" class="btn_item_reset"><i class="fas fa-trash-alt"></i></a></p>
                    <div id="display" >
                    </div>
                </span>
                <div class="top_menu_close_btn">

                    <i class="fas fa-chevron-up" aria-hidden="true"></i>
                </div>
        </div>
    </form>
    <div class="grid_btn_set_top" style="min-width: 1100px; margin-top: 20px;">
        <span>&nbsp</span>
        <div class="right">
            <a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>
<!--            <a href="custom_report_xls_down.php" target="_blank">다운로드</a>-->
        </div>
    </div>
    <div class="tb_wrap grid_tb" style="position:relative; min-width: 1100px; z-index: 1;">
        <table id="grid_list">
        </table>
        <div id="grid_pager"></div>
    </div>

</div>
<div class="report_footer">
<!--    <div class="bottom_menu_open_btn bottom_menu_btn">-->
<!--            <i class="fa fa-chevron-up fa-2x" aria-hidden="true"></i>-->
<!--    </div>-->
    <div class="bottom_menu">
        <div class="bottom_menu_close_btn bottom_menu_btn">
                <i class="fa fa-chevron-down" aria-hidden="true"></i>
        </div>
        <div style="height: 100%; margin-top: 30px; min-width: 600px;">
            <span class="axis_group_span">
                <p>분류</p>
                <?php foreach ($custom_report_item as $row) {
                    if ($row["type"] == "AXIS_GROUP") {
                        echo '<div class="check_item"><label><input type="checkbox" class="axis_group_item" 
                            name="' . $row["col_name"] . '" id="item_' . $row["idx"] . '"value="' . $row["idx"] . '"data-ui-type="'.$row["ui_type"].'"><span class="text">' . $row["name"] . '</span></label></div>
                            ';
                    }
                }
                ?>
            </span>
            <span class="search_condition_span">
                <p>검색조건</p>
                    <?php foreach ($custom_report_item as $row) {
                        if ($row["type"] == "SEARCH_CONDITION") {
                            echo '<div class="check_item"><label><input type="checkbox" class="search_condition_item"
                            name="' . $row["col_name"] . '" id="item_' . $row["idx"] . '" value="' . $row["idx"] . '"data-ui-type="'.$row["ui_type"].'"><span class="text">' . $row["name"] . '</span></label></div>
                            ';
                        }
                    }
                    ?>
            </span>
            <span class="display_group_span">
                <p>노출그룹</p>
                <?php foreach ($custom_report_item as $row) {
                    if ($row["type"] == "DISPLAY_GROUP") {
                        echo '<div class="check_item"><label><input type="checkbox" class="display_group_item"
                            name="' . $row["idx"] . '" id="item_' . $row["idx"] . '"value="' . $row["idx"] . '"data-ui-type="'.$row["ui_type"].'"><span class="text">' . $row["name"] . '</span></label></div>
                            ';
                    }
                }
                ?>
            </span>
            <span class="display_span">
                <p>노출항목</p>
                <?php foreach ($custom_report_item as $row) {
                    if ($row["type"] == "DISPLAY") {
                        echo '<div class="check_item"><label><input type="checkbox" class="display_item"
                            name="' . $row["idx"] . '" id="item_' . $row["idx"] . '"value="' . $row["idx"] . '"data-ui-type="'.$row["ui_type"].'"><span class="text">' . $row["name"] . '</span></label></div>
                            ';
                    }
                }
                ?>
            </span>
        </div>
    </div>
</div>


<div id="modal_common" title="" class="red_theme" style="display: none;"></div>

<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/page/common.function.js"></script>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/info.category.js"></script>
<script>
    $(document).ready(function(){
        $("#axis_group").sortable();
        $("#display").sortable();
        $("#display_group").sortable();
        selectTemplate();
    });

    window.name = 'settle_report';
    Common.setDateTimePreSetSelectbox("period_preset_select", '1', '2', 'period_preset_start_time_input', 'period_preset_end_time_input',  "1");
    let item_arr = <?= json_encode($custom_report_item) ?>;

    $("input:checkbox").on('click', function () {

        if ($(this).prop('checked')) {
            add_item(this)
        } else {
            remove_div($(this).val())
        }
    });

    function add_item(obj) {

        let obj_class = $(obj).attr('class');
        let ui_type = $(obj).data('ui-type');
        let sub_item_html = '<input type="hidden" name="' + obj.value + '" id="' + obj.value + '" value="" />';
        if(obj_class == 'search_condition_item') {
            if (ui_type == 'DATE') {
                sub_item_html = '<input type="text" name="' + obj.value + '" id="' + obj.value + '" class="w80px jqDate " value="<?=$date?>" readonly="readonly" />'
            } else if (ui_type == 'TEXT') {
                sub_item_html = '<input type="text" name="' + obj.value + '" id="' + obj.value + '" class="ui_type_text" value="" />'
            } else if (ui_type == 'NUMBER') {
                sub_item_html = '<input type="text" name="' + obj.value + '" id="' + obj.value + '" class="ui_type_number" value="" />'
            } else if (ui_type == 'COMBOBOX_M') {
                switch (obj.value) {
                    case "29":
                        let seller_list = <?= json_encode($seller_list) ?>;
                        sub_item_html = '<select id="' + obj.value + '" name="' + obj.value + '[]"multiple>';
                        for (let i = 0; i < seller_list.length; i++) {
                            sub_item_html += '<option value="' + seller_list[i].idx + '">' + seller_list[i].name + '</option>';
                        }
                        sub_item_html += '</select>';
                        break;
                    case "30":
                        let supplier_list = <?= json_encode($supplier_list) ?>;
                        sub_item_html = '<select id="' + obj.value + '" name="' + obj.value + '[]"multiple>';
                        for (let i = 0; i < supplier_list.length; i++) {
                            sub_item_html += '<option value="' + supplier_list[i].idx + '">' + supplier_list[i].name + '</option>';
                        }
                        sub_item_html += '</select>';
                        break;
                    default:
                        sub_item_html = '<select id="' + obj.value + '" name="' + obj.value + '[]"multiple>';
                        for (let i = 0; i < item_arr.length; i++) {
                            if (item_arr[i].parent_idx) {
                                sub_item_html += '<option value="' + item_arr[i].idx + '">' + item_arr[i].name + '</option>';
                            }
                        }
                        sub_item_html += '</select>';
                }
            }
        }

        let html = '<div class="move" style="margin-bottom: 5px" id="item_' + obj.value + '">' +
                        '<span class="select_item">' +
                            '<span class="point"></span>' +
                            '<span class="text">' + $(obj).parent().text() + '</span>' + sub_item_html +
                        '</span>' +
                        '<span class="btn_del">' +
                            '<a href="javascript:;" class="btn_item_delete" data-idx="'+ obj.value + '" onclick="remove_div($(this).data(\'idx\'))">' +
                                '<i class="far fa-times-circle"></i>' +
                             '</a>' +
                        '</span>' +
                  '</div>';
        if(obj_class == 'search_condition_item'){
            $("#search_condition").append(html);
            $("#search_condition").find($(".move")).removeClass('move');
            $("#search_condition").find($(".point")).addClass('green');

        }else if(obj_class == 'axis_group_item'){

            $("#axis_group").append(html);
            $("#axis_group").find($(".point")).addClass('orange');
        }else if(obj_class == 'display_item'){

            $("#display").append(html);
            $("#display").find($(".point")).addClass('blue');
        }else if(obj_class == 'display_group_item'){

            $("#display_group").append(html);
            $("#display_group").find($(".point")).addClass('blue');
        }

        if(ui_type == 'DATE') {
            $(".jqDate").datepicker();
        }else if(ui_type == 'COMBOBOX_M') {

            let idx = '#' + obj.value + "";
            $(idx).SumoSelect({
                selectAll:true,
                placeholder: '선택해주세요',
                captionFormatAllSelected : '{0}개 모두 선택됨',
                search: true,
                searchText: '검색',
                noMatch : '검색결과가 없습니다.',
            });

            $(idx).on('sumo:opening', function () {
                $('.select-all').css('height', '35px');
                $('.select-all').css('padding-top', '0px');
                $('.select-all').children('label').text('전체 선택');
            });
        }
    }


    function remove_div(obj) {

        let idx = '#item_' + obj + "";
        $(".top_menu").find($(idx)).remove();
        // $("#item_3").attr( 'checked', false );
        $(".bottom_menu").find($(idx)).prop( 'checked', false );
    }

    $(".btn_item_delete").on("click", function () {
        remove_div(this)
    });


    $(".btn_item_reset").on("click", function () {
        // $(this).parent().nextAll().remove();
        let obj_class = $(this).parent().parent().attr('class');
        if(obj_class == 'search_condition_span'){
            $("input:checkbox[class='search_condition_item']").prop( 'checked', false );
            $("#search_condition").children().remove();
        }
        if(obj_class == 'axis_group_span'){
            $("input:checkbox[class='axis_group_item']").prop( 'checked', false );
            $("#axis_group").children().remove();
        }
        if(obj_class == 'display_span'){
            $("input:checkbox[class='display_item']").prop( 'checked', false );
            $("#display").children().remove();
        }

    });


    $(".top_menu_close_btn").click(function (e) {
        e.preventDefault();

        $(".top_menu").slideToggle();
        $(".top_menu").addClass("open");
        $(".all_menu_btn").toggleClass("open");
        $(".all_menu_btn").find("i").attr("class", "fas fa-plus-square");

    });

    $(".bottom_menu_btn").click(function (e) {
        e.preventDefault();
        $(".bottom_menu").slideToggle();
        $(".bottom_menu").toggleClass("open");

    });

    $(".all_menu_btn").click(function (e) {

        $(".all_menu_btn").toggleClass("open");

        if ($(".all_menu_btn").hasClass("open")) {
            $(".all_menu_btn").find("i").attr("class", "fas fa-plus-square");

            $(".top_menu").addClass("open");
            $(".top_menu").slideToggle();

            if(!$(".bottom_menu").hasClass("open")){
                $(".report_footer").toggleClass("open");
                $(".bottom_menu").toggleClass("open");
                $(".bottom_menu").slideToggle();
            }

        } else {
            $(".all_menu_btn").find("i").attr("class", "fas fa-minus-square");

            $(".top_menu").removeClass("open");
            $(".top_menu").slideToggle();

            if($(".bottom_menu").hasClass("open")){
                $(".bottom_menu").toggleClass("open");
                $(".bottom_menu").slideToggle();
            }
        }
    });

    $("#searchForm").on("submit", function(e){
        e.preventDefault();
    });

    // 검색 버튼 클릭 이벤트
    $("#btn_searchBar").on("click", function(){
        showLoader();
        if (!$(".top_menu").hasClass("open")) {
            $(".all_menu_btn").find("i").attr("class", "fas fa-plus-square");
            $(".all_menu_btn").addClass("open");
            $(".top_menu").addClass("open");
            $(".top_menu").slideToggle();
        }
        if(!$(".bottom_menu").hasClass("open")) {
            $(".bottom_menu").toggleClass("open");
            $(".bottom_menu").slideToggle();
        }
        setTimeout(formSubmit, 1000);

    });

    function formSubmit() {
        let dataObj = {};
        dataObj.mode = "createReport";
        dataObj.report_data = $("#searchForm").serialize();
        $.ajax({
            url:"./custom_report_proc.php",
            type: 'POST',
            data: dataObj,
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            dataType: 'json',
            success : function(data, status, xhr) {
                if(Common.isEmpty(data)){
                    alert("검색결과가 없습니다.");
                }else{
                    createColmodel(data);
                }
                hideLoader();
                },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('오류가 발생했습니다.');
                hideLoader();
            }

        });
    }

    function createColmodel(data) {
        let header = data.header;
        let obj_keys = Object.keys(header);
        delete data.header;
        data = Object.values(data);
        let colModel = [];
        let total_obj = [];

        for(let i=0; i < obj_keys.length; i++){
            let regexp_num = /^[+-]?\d*(\.?\d*)$/; // 정수 실수 소수
            let regexp_str = /idx/; //idx 합 예외처리
            let check_num = (data[0][obj_keys[i]]);
            let odj_col;
            let temp_sum = 0;
            // //숫자만 있을경우 천단위 콤마
            let label = obj_keys[i].replace(/_/g," ");
            if(regexp_num.test(check_num) && !regexp_str.test(obj_keys[i])){
                odj_col = {label:header["id_"+i], name:obj_keys[i], index:obj_keys[i], align: 'right', search : false, sortable: true ,
                             formatter: 'integer', formatoptions:{thousandsSeparator:","}};
                    //숫자만 있는 컬럼 합계 구하기
                for (let j = 0; j < data.length; j++) {

                    temp_sum += Number(data[j][obj_keys[i]]);
                }
                    total_obj.push({name:obj_keys[i], total:temp_sum});
            }else{
                odj_col = {label:header["id_"+i], name:obj_keys[i], index:obj_keys[i], sortable: true , search : false};
            }
            colModel.push(odj_col);
        }

        //그리드 초기화
        $.jgrid.gridUnload("#grid_list");

        //그리드 생성
        $("#grid_list").jqGrid({
            datatype: 'local',
            data: data,
            colModel: colModel,
            pager: '#grid_pager',
            rowNum: 100,
            viewrecords: true,
            autowidth: true,
            rownumbers: true,
            shrinkToFit: false,
            height: Common.jsSiteConfig.jqGridDefaultHeight,
            loadComplete: function(){
                //합계 금액 추가

                if(total_obj.length > 0) {
                    $("#grid_list").jqGrid('addRow', {
                        rowID: 'total',
                        position: "last",
                    });
                    for (let i = 0; i < total_obj.length; i++) {
                        $("#grid_list").jqGrid('setCell', 'total', total_obj[i].name, total_obj[i].total);
                    }
                    $("#grid_list").jqGrid("filterToolbar",{});
                    var total_row_data = $("#grid_list").jqGrid("getRowData", "total");
                    $("#grid_list").jqGrid("delRowData", "total");

                    for(let i=0; i < total_obj.length; i++ ) {
                        $("#gsh_grid_list_" + total_obj[i].name).text(Common.addCommas(total_row_data[total_obj[i].name]));
                    }
                }

                Common.jqGridResize("#grid_list");

                // TODO : 컬럼 넓이 자동 조절 - css 문제로 보류
                // $("#grid_list").bind("jqGridAfterLoadComplete", function () {
                //     var $this = $(this), iCol, iRow, rows, row, cm, colWidth,
                //         $cells = $this.find(">tbody>tr>td"),
                //         $colHeaders = $(this.grid.hDiv).find(">.ui-jqgrid-hbox>.ui-jqgrid-htable>thead>.ui-jqgrid-labels>.ui-th-column>div"),
                //         colModel = $this.jqGrid("getGridParam", "colModel"),
                //         n = $.isArray(colModel) ? colModel.length : 0,
                //         idColHeadPrexif = "jqgh_" + this.id + "_";
                //
                //     $cells.wrapInner("<span class='mywrapping'></span>");
                //     $colHeaders.wrapInner("<span class='mywrapping'></span>");
                //
                //     for (iCol = 0; iCol < n; iCol++) {
                //         cm = colModel[iCol];
                //         colWidth = $("#" + idColHeadPrexif + $.jgrid.jqID(cm.name) + ">.mywrapping").outerWidth() + 25; // 25px for sorting icons
                //         for (iRow = 0, rows = this.rows; iRow < rows.length; iRow++) {
                //             row = rows[iRow];
                //             if ($(row).hasClass("jqgrow")) {
                //                 colWidth = Math.max(colWidth, $(row.cells[iCol]).find(".mywrapping").outerWidth());
                //             }
                //         }
                //         $this.jqGrid("setColWidth", iCol, colWidth);
                //     }
                // });
            }

        });
    }

    $("#user_template_save").on("click", function(){
        if(Common.isEmpty($("#user_template_name").val())){
            alert("저장할 이름을 입력해주세요.")
        } else {
            showLoader();
            let dataObj = {};
            dataObj.mode = "saveTemplate";
            dataObj.user_template_idx = $("#user_template").val();
            dataObj.user_template_name = $("#user_template_name").val();
            dataObj.report_data = $("#searchForm").serialize();
            let msg = '저장';
            userTemplate(dataObj, msg)
        }
    });

    $("#user_template_del").on("click", function(){
        showLoader();
        let dataObj = {};
        dataObj.mode = "deleteTemplate";
        dataObj.idx = $("#user_template").val();
        let msg = '삭제';
        userTemplate(dataObj,msg);

    });

    $('#user_template').on('change', function(){
        showLoader();
        searchTemplate()
    });

    function userTemplate(dataObj,msg) {
        $.ajax({
            url:"./custom_report_proc.php",
            type: 'POST',
            data: dataObj,
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            dataType: 'json',
            success : function(data, status, xhr) {

                if(data.result == true){
                    alert(msg + "완료되었습니다.");
                    if(msg == '삭제'){
                        $("#user_template_name").val("");
                        for(let i=0; i < item_arr.length; i++) {
                            remove_div(item_arr[i].idx);
                        }
                    }
                    selectTemplate();
                }else if(data.msg){
                    alert(data.msg);
                }
                hideLoader();
            },
            error: ajaxFailWithHideLoader
        });
    }

    function selectTemplate() {
        let dataObj = new Object();
        dataObj.mode = "selectTemplate";
        $.ajax({
            url:"./custom_report_proc.php",
            type: 'POST',
            data: dataObj,
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            dataType: 'json',
            success : function(data, status, xhr) {

                if(data){
                    $("#user_template").children().remove();
                    let html = '<option value="">선택하세요.</option>';
                    for (let i = 0; i < data.length; i++) {
                        html += '<option value="' + data[i].idx + '">' + data[i].name + '</option>';
                    }
                    $("#user_template").append(html);

                    $("#user_template").SumoSelect({
                        placeholder: '전체',
                        captionFormatAllSelected : '{0}개 모두 선택됨',
                        search: true,
                        searchText: '검색',
                        noMatch : '검색결과가 없습니다.',
                    });
                    $('#user_template')[0].sumo.reload();
                }
                hideLoader();
            },
            error: ajaxFailWithHideLoader
        });
    }

    function searchTemplate() {
        let dataObj = {};
        dataObj.mode = "searchTemplate";
        dataObj.idx = $("#user_template").val();
        $.ajax({
            url:"./custom_report_proc.php",
            type: 'POST',
            data: dataObj,
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            dataType: 'json',
            success : function(data, status, xhr) {
                if(data){
                    let obj = JSON.parse(data);
                    let slice = data.slice(1, -1);
                    let splitArr = slice.split(",");
                    let dummyArr = [];
                    let keys = [];

                    for(let i = 0;  i < splitArr.length; i++){
                        dummyArr.push(splitArr[i].split(":"));
                        keys.push(dummyArr[i][0].slice(1, -1));
                    }
                    function remove() {
                        return new Promise(function(resolve, reject){
                            for(let i=0; i < item_arr.length; i++) {
                                remove_div(item_arr[i].idx);

                            }
                            resolve();
                        })
                    }
                    function checked() {
                        return new Promise(function(resolve, reject){
                            for(let i=0; i < keys.length; i++) {
                                let idx = "item_" + keys[i];
                                $("input:checkbox[id='" + idx + "']").trigger("click");
                            }
                            resolve();
                        })
                    }
                    function value() {
                        return new Promise(function(resolve, reject){
                            for(let i=0; i < keys.length; i++) {
                                if(keys[i] == 29){
                                    let strArray = obj[29].split(",");
                                    for(let j=0; j < strArray.length; j++){
                                        $('#29')[0].sumo.selectItem(strArray[j])
                                    }
                                }else if(keys[i] == 30){
                                    let strArray = obj[30].split(",");
                                    for(let j=0; j < strArray.length; j++){
                                        $('#30')[0].sumo.selectItem(strArray[j])
                                    }
                                }else{
                                    $("input[id=" + keys[i] + "]").val(obj[keys[i]]);
                                }
                            }
                            resolve();
                        })
                    }
                    async function end() {
                        await remove();
                        await checked();
                        await value();
                    }
                    end();
                    $("#user_template_name").val($("#user_template option:selected").text())
                }else{
                    for(let i=0; i < item_arr.length; i++) {
                        remove_div(item_arr[i].idx);
                    }
                    $("#user_template_name").val("");
                }
                hideLoader();
            },
            error: ajaxFailWithHideLoader
        });
    }


    $(".btn-xls-down").on("click", function(){
        if(Common.isEmpty($("#searchForm").serialize())){
            alert("다운로드 받을 데이터가 없습니다.")
        }else{
            // exportExcel();
            customReportXlsDown();
        }
    });

    let xlsDownIng = false;
    let xlsDownInterval;

    let customReportXlsDown = function(obj){
        if(xlsDownIng) return;
        xlsDownIng = true;

        let dataObj = {
            param: [$("#searchForm").serialize()]
        };

        let url = "custom_report_xls_down.php?"+$.param(dataObj);
        showLoader();
        $("#hidden_ifrm_common_filedownload").attr("src", url);

        clearInterval(xlsDownInterval);
        xlsDownInterval = setInterval(function(){
            Common.checkXlsDownWait("XLS_CUSTOM_REPORT", function(){
                customReportXlsDownComplete();
            });
        }, 500);
    };

    let customReportXlsDownComplete = function(){
        xlsDownIng = false;
        clearInterval(xlsDownInterval);
        hideLoader();
    };

</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

