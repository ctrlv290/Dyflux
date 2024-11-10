<?php
if($PageNavIsFav)
{
	$fav_a_class = "fav_menu";
	$fav_i_class = "fas";
}else{
	$fav_a_class = "";
	$fav_i_class = "far";
}
?>
<div class="con_tit">
	<p class="title">
		<?=($PageNavAry[0]) ? end($PageNavAry) : "HOME";?>
		<a href="javascript:;" class="btn-fav <?=$fav_a_class?>" data-idx="<?=end($PageNavNoAry)?>"><i class="<?=$fav_i_class?> fa-star"></i></a>
	</p>
	<ul class="page_location">
		<li><a href="javascript:;">HOME</a></li>
		<?php
		if($PageNavAry[0]) {
			foreach ($PageNavAry as $_pageTitle) {
				?>
				<li><a href="javascript:;"><?php echo $_pageTitle; ?></a></li>
				<?php
			}
		}
		?>
	</ul>
</div>