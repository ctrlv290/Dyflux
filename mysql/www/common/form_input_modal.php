<?php
include_once "../_init_.php";

$postData = array();

foreach ($_POST["data"] as $key => $datum) {
    $postData[$key] = array();

    foreach ($datum as $row) {
        $postData[$key][$row[0]] = $row[1];
    }
}

?>

<div class="container popup">
    <div class="content_wrap">
        <form name="frm_common_modal_pop" method="post">
            <input type="hidden" id="frm_url" value="<?= $_POST["url"] ?>">
            <input type="hidden" name="mode" value="<?= $_POST["mode"] ?>"/>

            <?php foreach ($postData as $datum) {
                if ($datum["type"] == "hidden") {
            ?>
            <input type="hidden" name="<?= $datum["column_name"] ?>" value="<?= $datum["value"] ?>">
            <?php }} ?>


            <div class="tb_wrap">
                <table>
                    <colgroup>
                        <col width="120">
                        <col width="*">
                    </colgroup>
                    <tbody>
                    <?php foreach ($postData as $datum) {
                    if ($datum["type"] == "text") {
                    ?>
                    <tr>
                        <th><?= $datum["name"] ?></th>
                        <td class="text_left">
                            <input type="text" class="input_common_modal_pop input_<?= $datum["column_name"] ?> w100per" name="<?= $datum["column_name"] ?>" value="<?= $datum["value"]; ?>" maxlength="50"/>
                        </td>
                    </tr>
                    <?php }} ?>
                    </tbody>
                </table>
            </div>

            <div class="btn_set">
                <div class="center">
                    <a href="javascript:;" class="large_btn btn_common_input_modal_confirm">변경</a>
                    <a href="javascript:;" class="large_btn red_btn btn_common_input_modal_cancel">취소</a>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
</script>


<?php
include_once DY_INCLUDE_PATH . "/_include_bottom.php";
?>
