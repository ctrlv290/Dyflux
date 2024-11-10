<?php
//Init
include_once "../_init_.php";


$filename = $_POST["filename"];

$path = "../_logs/".$filename;
$_list = array();
if(file_exists($path)){
	$fp = fopen($path,"r");
	if($fp) {
		while (!feof($fp)) {
			$line = fgets($fp);

			if($line) {
				$rst = explode(" [[^^]] ", $line);
				$rst["date"] = $rst[0];
				$rst["type"] = $rst[1];
				$rst["msg"] = $rst[2];
				$rst["context"] = $rst[3];

				$_list[] = $rst;
			}
		}
	}
	fclose($fp);
}else{
	echo "파일 없어!";
}
?>
<table class="log_list">
	<colgroup>
		<col width="180" />
		<col width="100" />
		<col width="*" />
	</colgroup>
	<thead>
	<th>일시</th>
	<th>타입</th>
	<th>에러내용</th>
	</thead>
	<tbody>
	<?php foreach($_list as $l){?>
		<tr>
			<td><?=$l["date"]?></td>
			<td><?=$l["type"]?></td>
			<td class="text_left">
				<a href="javascript:;" class="log_detail">
					<i class="far fa-plus-square plus"></i>
					<i class="far fa-minus-square minus"></i>
				</a>
				<?=$l["msg"]?>
				<div class="json dis_none"><?=$l["context"]?></div>
			</td>
		</tr>
	<?php }?>
	</tbody>
</table>