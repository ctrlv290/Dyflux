<?php

include_once "../_init_.php";

$kindIdx = $_POST["kind_idx"];

$adManager = new AdvertisingManager();
$kindData = $adManager->getKind($kindIdx);
$kindFormat = $adManager->getKindFormatData($kindIdx);

$idx = 0;
if ($kindFormat["idx"]) $idx = $kindFormat["idx"];

$sellerManager = new Seller();
$sellerData = $sellerManager->getAllSellerData($kindData["seller_idx"]);

if (!$kindData || !$sellerData) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: text/html; charset=UTF-8');
    die("Error");
}

?>

<div class="container popup">
    <div class="content write_page">
        <div class="content_wrap">
            <form name="form_kind_format" method="post" class="">
                <input type="hidden" name="mode" value="kind_format" />
                <input type="hidden" name="idx" value="<?=$idx?>" />
                <input type="hidden" name="kind_idx" value="<?=$kindIdx?>" />
                <div class="tb_wrap">
                    <table>
                        <colgroup>
                            <col width="150">
                            <col width="*">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>광고 업체</th>
                            <td class="text_left"><?=$sellerData["seller_name"]?></td>
                        </tr>
                        <tr>
                            <th>광고 이름</th>
                            <td class="text_left"><?=$kindData["kind_name"]?></td>
                        </tr>
                        </tbody>
                    </table>
                    <br>
                    <table>
                        <colgroup>
                            <col width="150">
                            <col width="*">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>기준헤더</th>
                            <th>광고 파일 헤더</th>
                        </tr>
                        <tr>
                            <th>광고 이름</th>
                            <td class="text_left">
                                <input name="ad_name" type="text" class="w100per" maxlength="22" value="<?=$kindFormat["ad_name"]?>"></td>
                            </td>
                        </tr>
                        <tr>
                            <th>광고 타겟 상품명</th>
                            <td class="text_left">
                                <input name="ad_product_name" type="text" class="w100per" maxlength="22" value="<?=$kindFormat["ad_product_name"]?>"></td>
                            </td>
                        </tr>
                        <tr>
                            <th>광고 키워드</th>
                            <td class="text_left">
                                <input name="ad_keyword" type="text" class="w100per" maxlength="22" value="<?=$kindFormat["ad_keyword"]?>"></td>
                            </td>
                        </tr>
                        <tr>
                            <th>광고 비용</th>
                            <td class="text_left">
                                <input name="ad_cost" type="text" class="w100per" maxlength="22" value="<?=$kindFormat["ad_cost"]?>"></td>
                            </td>
                        </tr>
                        <tr>
                            <th>광고 입찰일</th>
                            <td class="text_left">
                                <input name="ad_winning_bid_date" type="text" class="w100per" maxlength="22" value="<?=$kindFormat["ad_winning_bid_date"]?>"></td>
                            </td>
                        </tr>
                        <tr>
                            <th>광고 실행일</th>
                            <td class="text_left">
                                <input name="ad_operation_date" type="text" class="w100per" maxlength="22" value="<?=$kindFormat["ad_operation_date"]?>"></td>
                            </td>
                        </tr>
                        <tr>
                            <th>광고 노출수</th>
                            <td class="text_left">
                                <input name="ad_display_count" type="text" class="w100per" maxlength="22" value="<?=$kindFormat["ad_display_count"]?>"></td>
                            </td>
                        </tr>
                        <tr>
                            <th>광고 클릭(접근)수</th>
                            <td class="text_left">
                                <input name="ad_click_count" type="text" class="w100per" maxlength="22" value="<?=$kindFormat["ad_click_count"]?>"></td>
                            </td>
                        </tr>
                        <tr>
                            <th>광고 메모</th>
                            <td class="text_left">
                                <input name="ad_memo" type="text" class="w100per" maxlength="22" value="<?=$kindFormat["ad_memo"]?>"></td>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="btn_set">
                    <div class="center">
                        <a href="javascript:;" id="btn_save_ad_kind_format" class="large_btn blue_btn ">저장</a>
                        <a href="javascript:;" class="large_btn red_btn btn_close_pop">취소</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>