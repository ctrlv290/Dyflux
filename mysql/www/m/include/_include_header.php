</head>
<body>
<div class="wrap">
	<div class="wrap_header">
		<button class="menu"><img src="/m/images/menu.png" alt="" /></button>
		<a href="javascript:;" class="logo"><img src="/m/images/logo_header.png" alt="dyflux" /></a>
	</div>

	<div class="wrap_menu">
		<div class="navigation">
			<div class="top_logo">
				<p><span class="name"><?=$_SESSION["dy_member_mobile"]["member_name"]?></span>님 안녕하세요</p>
				<button class="close_btn"><img src="/m/images/close.png" alt="" /></button>
			</div>
			<div class="gnb_set">
				<ul class="gnb">
					<?php
					if(!isset($pageMenuNo_L)) $pageMenuNo_L = 0;
					if(!isset($pageMenuNo_M)) $pageMenuNo_M = 0;
					foreach($GL_Mobile_Menu_Ary as $MenuL){
						$menuNo = (int) $MenuL["no"];
						$menuName = $MenuL["name"];
						$menuUrl = $MenuL["url"];
						$menuSub = $MenuL["submenu"];
						$hasSub = false;
						$menuClass = "home";
						if(is_array($menuSub) && count($menuSub) > 0){
							$hasSub = true;
							$menuClass = "";
							$menuUrl = "javascript:;";
						}

						$menuL_Open = false;
						if($pageMenuNo_L == $menuNo){
							$menuClass .= " on";
							$menuL_Open = true;
						}
					?>
						<li>
							<a href="<?=$menuUrl?>" class="<?=$menuClass?>"><?=$menuName?></a>
							<?php
							if($hasSub){
							?>
								<ul class="depth" style="<?=($menuL_Open) ? "display: block;" : ""?>">
									<?php
									foreach ($menuSub as $MenuM)
									{
										$menuName = $MenuM["name"];
										$menuUrl = $MenuM["url"];
									?>
										<li><a href="<?=$menuUrl?>"><?=$menuName?></a></li>
									<?php
									}
									?>
								</ul>
							<?php
							}
							?>
						</li>
					<?php
					}
					?>
				</ul>
				<div class="btn_set">
					<a href="/" class="btn" target="_blank">PC사이트보기</a>
					<a href="/m/logout.php" class="btn">로그아웃</a>
				</div>
			</div>
		</div>
	</div>
	<div class="wrap_bg"></div>