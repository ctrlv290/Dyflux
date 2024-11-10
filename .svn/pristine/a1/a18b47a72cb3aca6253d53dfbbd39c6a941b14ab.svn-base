<?php
/**
 * User: ysh
 * Date: 2020-04-27
 * Desc: 주문다운로드 포맷설정 팝업 창
 */
//Page Info
$pageMenuIdx = 78;
//Init
include_once "../_init_.php";


$mode                   = "save";

$C_Supplier = new Supplier();
$C_Order = new Order();

$supplier_info = $C_Supplier->getUseSupplierData($supplier_idx);
$supplier_format_rst = $C_Order->getOrderDownloadFormatSupplierList($supplier_idx);

//포맷설정 default 불러오기
$format_rst = $C_Order->getOrderDownloadFormatList();
$format_list = '';
foreach($format_rst as $format){
    $format_list .= '<option value="'.$format["order_download_format_default_idx"].'">'.$format["order_download_format_default_header_name"].'</option>';
}

?>

    <div class="container popup">
        <div class="content write_page">
            <div class="content_wrap">
                <form name="dyForm2" method="post" class="<?php echo $mode?>">
                    <input type="hidden" name="mode" value="<?php echo $mode?>" />
                    <input type="hidden" name="supplier_idx" value="<?php echo $supplier_idx?>" />
                    <div class="tb_wrap" style="height: 560px">
                        <table>
                            <colgroup>
                                <col width="150">
                                <col width="*">
                            </colgroup>
                            <tbody>
                            <tr>
                                <th>공급처코드</th>
                                <td class="text_left"><?=$supplier_idx?></td>
                            </tr>
                            <tr>
                                <th>공급처명</th>
                                <td class="text_left"><?=$supplier_info["supplier_name"]?></td>
                            </tr>
                            </tbody>
                        </table>
                        <br>
                        <table>
                            <colgroup>
                                <col width="25">
                                <col width="25">
                                <col width="180">
                                <col width="*">
                                <col width="100">
                            </colgroup>
                            <tbody>
                            <thead>
                            <tr>
                                <th colspan="2">순서</th>
                                <th>기준헤더</th>
                                <th>발주서 헤더</th>
                                <th><a href="javascript:;" class="btn blue_btn add_format">추가</a></th>
                            </tr>
                            </thead>
                            <tr class="format_tr"></tr>
                            <?php
                            if($supplier_format_rst) {
                            foreach($supplier_format_rst as $sfr){
                                echo '<tr class="format_tr">
                                    <td colspan="2">
								    <a href="javascript:;" class="btn-category-move-up" onclick="moveUp(this)" ><i class="fas fa-arrow-alt-circle-up"></i></a>
								    <a href="javascript:;" class="btn-category-move-dn" onclick="moveDown(this)" ><i class="fas fa-arrow-alt-circle-down"></i></a>
							        </td>
                                     <td><select name="order_download_format_default_idx[]" class="format_default_list">';
                                 foreach($format_rst as $fr){
                                     $selected = ($sfr["order_download_format_default_idx"] == $fr["order_download_format_default_idx"]) ? "selected" : "";
                                     echo '<option value="' . $fr["order_download_format_default_idx"] . '" ' . $selected . '>' . $fr["order_download_format_default_header_name"] . '</option>';
                                     }
                                     echo '</select></td>
                                    <td class="text_left">
                                    <input type="text" name="order_download_format_header_name[]" class="w100per" value="'.$sfr["order_download_format_header_name"].'" />
                                    </td>
                                    <td><a href="javascript:;" class="btn red_btn del_format">삭제</a></td>
                                </tr>';
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="btn_set">
                        <div class="center">
                            <a href="javascript:;" id="btn-save-format" class="large_btn blue_btn ">저장</a>
                            <a href="javascript:;" class="large_btn red_btn btn-order-download-format-pop-close">취소</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="/js/jquery.sumoselect.min.js"></script>
    <script type="text/javascript" src="/js/jquery-ui.min.js"></script>
    <style>
        .SumoSelect {
            width: 160px;
        }
    </style>
    <script>
        OrderShipped.OrderDownloadFormatPopInit();

        $(".add_format").on("click", function(){
            var add_format_html =
                '<tr class="format_tr">' +
                '<td colspan="2">' +
                '<a href="javascript:;" class="btn-category-move-up" onclick="moveUp(this)" ><i class="fas fa-arrow-alt-circle-up"></i></a> '+
                '<a href="javascript:;" class="btn-category-move-dn" onclick="moveDown(this)" ><i class="fas fa-arrow-alt-circle-down"></i></a>' +
                '</td>' +
                '<td><select name="order_download_format_default_idx[]" class="format_default_list">' +
                '<?=$format_list?>' +
                '</select></td>' +
                '<td class="text_left">' +
                '<input type="hidden" name="order_format_default_idx[]" class="w100per" value="" />' +
                '<input type="hidden" name="order_format_seller_idx[]" class="w100per" value="" />' +
                '<input type="text" name="order_download_format_header_name[]" class="w100per" value="" />' +
                '</td>' +
                '<td><a href="javascript:;" class="btn red_btn del_format">삭제</a></td>' +
                '</tr>';
            var trHtml = $(".format_tr:last");
            trHtml.after(add_format_html);

            $(".format_default_list").SumoSelect({
                search: true,
                searchText: '검색',
                noMatch : '검색결과가 없습니다.',
            });

        });

        $(document).on("click", ".del_format", function() {
            var trHtml = $(this).parent().parent();
            trHtml.remove();
        });

        function moveUp(el){
            var $tr = $(el).closest('tr');
            $tr.prev().before($tr);
        }

        function moveDown(el){
            var $tr = $(el).closest('tr');
            $tr.next().after($tr);
        }

    </script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>