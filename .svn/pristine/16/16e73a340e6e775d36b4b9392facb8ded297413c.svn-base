<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처등록/수정 페이지
 */
//Page Info
$pageMenuIdx = 101;

//Init
include_once "../_init_.php";


$mode = "SMS_TEMPLATE_ADD";
$idx             = $_GET["idx"];

$C_SmsMecro = new SmsMecro();

if($idx)
{
    $_view = $C_SmsMecro->getSmsTemplateInfo($idx);
    if($_view)
    {
        $mode = "SMS_TEMPLATE_MOD";
        extract($_view);
    }else{
        put_msg_and_back("존재하지 않는 판매처입니다.");
    }
}
//print_r2($_view);
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<script type='text/javascript'>
    $(document).ready(function () {
        $("#btn-save").on("click", function(e){
            e.preventDefault ? e.preventDefault() : (e.returnValue = false);

            $("form[name='dyForm']").submit();
        });

        //폼 Submit 이벤트
        $("form[name='dyForm']").submit(function(){
            var returnType = false;        // "" or false;
            var valForm = new FormValidation();
            var objForm = this;

            try{

                if(objForm.tp_code.value == "")
                {
                    alert("템플릿코드를 입력해주세요");
                    objForm.tp_code.focus();
                    return false;
                }

                if(objForm.tp_name.value == "")
                {
                    alert("템플릿명을 입력해주세요");
                    objForm.tp_name.focus();
                    return false;
                }

                if(objForm.tp_con.value == "")
                {
                    alert("템플릿 내용을 입력해주세요");
                    objForm.tp_con.focus();
                    return false;
                }

                if(objForm.tp_replace_code.value == "")
                {
                    alert("템플릿 치환코드를 입력해주세요");
                    objForm.tp_replace_code.focus();
                    return false;
                }

                if(objForm.tp_use.value == "")
                {
                    alert("템플릿 사용여부를 선택해주세요");
                    objForm.tp_use.focus();
                    return false;
                }

                this.action = "sms_send_proc.php";
                $("#btn-save").attr("disabled", true);

            }catch(e){
                alert(e);
                return false;
            }
        });
    });



</script>
<div class="container popup">
    <?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
    <div class="content write_page">
        <div class="content_wrap">
            <form name="dyForm" method="post" id="dyForm" >
                <input type="hidden" name="mode" value="<?php echo $mode?>" />
                <input type="hidden" name="idx" value="<?=isset($idx) ? $idx : ""?>" />
                <div class="tb_wrap">
                    <table>
                        <colgroup>
                            <col width="150">
                            <col width="*">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>템플릿 코드 <span class="lb_red">필수</span></th>
                            <td class="text_left">
                                <input type="text" name="tp_code" id="tp_code" class="w400px" value="<?=isset($tp_code) ? $tp_code : "" ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th>템플릿 명 <span class="lb_red">필수</span></th>
                            <td class="text_left">
                                <input type="text" name="tp_name" id="tp_name" class="w400px" value="<?=isset($tp_name) ? $tp_name : "" ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th>템플릿 내용 <span class="lb_red">필수</span></th>
                            <td class="text_left">
                                <textarea name="tp_con" id="tp_con" class="w100per h100px"><?=isset($tp_con) ? $tp_con : "" ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>템플릿 치환 <span class="lb_red">필수</span></th>
                            <td class="text_left">
                                <input type="text" name="tp_replace_code" id="tp_replace_code" class="w400px" value="<?=isset($tp_replace_code) ? $tp_replace_code : "" ?>" />
                                <span class="info_txt col_red">(구분은 , 로 해주세요)</span>
                            </td>
                        </tr>
                        <tr>
                            <th>사용여부</th>
                            <td class="text_left">
                                <select name="tp_use" id="tp_use">
                                    <option value="">선택</option>
                                    <option value="Y" <?= (isset($tp_use) && $tp_use == "Y") ? "selected" : ""?>>사용</option>
                                    <option value="N" <?= (isset($tp_use) && $tp_use == "N") ? "selected" : ""?>>미사용</option>
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="btn_set">
                    <div class="center">
                        <a href="javascript:;" id="btn-save" class="large_btn blue_btn ">저장</a>
                        <a href="javascript:self.close();" class="large_btn red_btn">취소</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script>


</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
