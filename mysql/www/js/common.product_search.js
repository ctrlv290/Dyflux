var CommonProductSearch = (function() {
    var root = this;
    var init = function() {};
    var mode = "product";

    var callbackList = new Array();

    var initPopup = function() {

    };

    var refreshGrid = function() {
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
            url: "/common/" + mode + "_search_pop_grid.php",
            postData:{
                param: $("#searchFormPop").serialize()
            }
        }).trigger("reloadGrid");
    };

    var addCallback = function(callback) {
        callbackList.push(callback);
    };

    var clearCallback = function () {
        callbackList = new Array();
    };

    let test = function () {
        callbackList.push(function(itemList, _mode){console.log("qqq");});
    };

    return {
        initPopup : function(){ initPopup(); },
        addCallback : function(){ addCallback(); },
        clearCallback : function(){ clearCallback(); },
        test : function(){ test(); }
    }
})();