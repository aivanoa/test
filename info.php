<?php
	if(empty($salonID)) {
		$salonID = $GLOBALS['arSalon']['ID'];
	}
	
	/* Проверки */
	$showElement = 0;
	$showPanel = 0;
	$showUser = 0;

	/* Счетчики */
	$girlsCount = 0;
	$newsCount = 0;
	$vacancyCount = 0;
	$commentsCount = 0;
	$programmsCount = 0;
	
	$maxClick = 0;
	
	$resU = CUser::GetByID($USER->GetID());
	if($lotU = $resU->Fetch()) {
		if($lotU['ACTIVE'] == 'Y') {
			$showUser = 1;
		}
	}
	
	$salonData = array(
		'ID' => 0,
		'IBLOCK_ID' => 0,
		'ACTIVE' => 'N',
		'NAME' => '',
		'PREVIEW_TEXT' => '',
		'PREVIEW_PICTURE' => '',
		'PROPERTIES' => array(
			'SUMM' => 0,
			'CLICK' => 0,
			'SORT' => 0,
			'PLACE' => 0,
			'PHONE' => '',
			'CITY' => 0,
			'U_POSITION_COUNT' => 0,
			'ADDRESS' => '',
			'WWW' => '',
			'REKLAMA_OFF' => '',
			'PHONEAUTHCHECK' => '',
			'PHONEAUTH' => '',
			'ACTIVE_ADMINISTRATOR' => false
		)
	);
	
	$resS = CIBlockElement::GetList(array(), array('ID' => $salonID), false, false, array('ID', 'IBLOCK_ID', 'ACTIVE', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE', 'PROPERTY_CITY', 'PROPERTY_SUMM', 'PROPERTY_CLICK', 'PROPERTY_U_POSITION_COUNT', 'PROPERTY_SORT', 'PROPERTY_PHONE', 'PROPERTY_ADRESS', 'PROPERTY_WWW', 'PROPERTY_REKLAMA_OFF', 'PROPERTY_PHONEAUTHCHECK', 'PROPERTY_PHONEAUTH', 'PROPERTY_ACTIVE_ADMINISTRATOR'));
	if($lotS = $resS->Fetch()) {
		
		$salonData = array(
			'ID' => $lotS['ID'],
			'IBLOCK_ID' => $lotS['IBLOCK_ID'],
			'ACTIVE' => $lotS['ACTIVE'],
			'NAME' => $lotS['NAME'],
			'PREVIEW_TEXT' => $lotS['PREVIEW_TEXT'],
			'PREVIEW_PICTURE' => !empty($lotS['PREVIEW_PICTURE']) ? CFile::GetPath($lotS['PREVIEW_PICTURE']) : '',
			'PROPERTIES' => array(
				'SUMM' => (int)$lotS['PROPERTY_SUMM_VALUE'],
				'CLICK' => (int)$lotS['PROPERTY_CLICK_VALUE'],
				'SORT' => (int)$lotS['PROPERTY_SORT_VALUE'],
				'PLACE' => 0,
				'PHONE' => $lotS['PROPERTY_PHONE_VALUE'],
				'CITY' => $lotS['PROPERTY_CITY_VALUE'],
				'ADDRESS' => $lotS['PROPERTY_ADRESS_VALUE'],
				'WWW' => $lotS['PROPERTY_WWW_VALUE'],
				'U_POSITION_COUNT' => $lotS['PROPERTY_U_POSITION_COUNT_VALUE'],
				'REKLAMA_OFF' => $lotS['PROPERTY_REKLAMA_OFF_ENUM_ID'],
				'PHONEAUTHCHECK' => $lotS['PROPERTY_PHONEAUTHCHECK_VALUE'],
				'PHONEAUTH' => $lotS['PROPERTY_PHONEAUTH_VALUE'],
				'ACTIVE_ADMINISTRATOR' => !empty($lotS['PROPERTY_ACTIVE_ADMINISTRATOR_VALUE'])
			)
		);
		
		if($lotS['ACTIVE'] == 'Y' || (isset($GLOBALS['arSalon']['ID']) && $GLOBALS['arSalon']['ID'] == $lotS['ID'])) {
			$showElement = 1;
		}
		if(isset($GLOBALS['arSalon']['ID']) && $GLOBALS['arSalon']['ID'] == $lotS['ID']) {
			$showPanel = 1;
		}
		
		$arFilter = array('PROPERTY_SALON' => $lotS['ID']);
		if(!$showPanel) {
			$arFilter['ACTIVE'] = 'Y';
		}
		/* Девушки */
		$arFilter['IBLOCK_ID'] = 3;	
		$girlsCount = CIBlockElement::GetList(false, $arFilter, array());
		/* Новости */
		$arFilter['IBLOCK_ID'] = 6;	
		$newsCount = CIBlockElement::GetList(false, $arFilter, Array());
		/* Вакансии */
		$arFilter['IBLOCK_ID'] = 11;
		$vacancyCount = CIBlockElement::GetList(false, $arFilter, Array());
		/* Комментарии */
		$arFilter['IBLOCK_ID'] = 4;	
		$commentsCount = CIBlockElement::GetList(false, $arFilter, Array());
		/* Программы */
		$arFilter['IBLOCK_ID'] = 12;
		$programmsCount = CIBlockElement::GetList(false, $arFilter, Array());

		$arFilter = array(
			'IBLOCK_ID' => 1,
			'ACTIVE' => 'Y',
			'!ID' => $lotS['ID'],
			'PROPERTY_CITY' => $GLOBALS['arCity']['ID'],
			'<=PROPERTY_CLICK' => $lotS['PROPERTY_CLICK_VALUE']
		);
		$resC = CIBlockElement::GetList(array('PROPERTY_CLICK' => 'DESC'), $arFilter, false, false, array('PROPERTY_CLICK'));
		if($lotC = $resC->Fetch()) {
			$maxClick = $lotC['PROPERTY_CLICK_VALUE'];
		}
		if($maxClick <= 0) {
			$maxClick = 4;
		}
		else {
			$maxClick++;			
		}
		
		$arFilter = array(
			'IBLOCK_ID' => 1,
			'ACTIVE' => 'Y',
			'PROPERTY_CITY' => $GLOBALS['arCity']['ID'],
			'>=PROPERTY_SORT' => (int)$lotS['PROPERTY_SORT_VALUE']);
		$salonData['PROPERTIES']['PLACE'] = (int)CIBlockElement::GetList(false, $arFilter, array());
	}


	$this->SetViewTarget('top_text_salon');
	echo '<div class="topTextSalon">Салон эротического массажа ' . $salonData['NAME'];
	$this->EndViewTarget();

	if($showElement) {
		//Карточка салона - в теге <EXTRA>, чтобы затем разобрать закешированный вывод и выполнить отложенные функции
		?>
		
			<EXTRA>
				<div class="b-einfo">
					<div class="m-img" style="background:url(<?php echo resizeImgCrop($salonData['PREVIEW_PICTURE'], 174, 100); ?>);"></div>
					<p class="m-title">Салон эротического массажа «<?php echo $salonData['NAME']; ?>». Девушки, отзывы, акции.</p>
					<p class="m-descr"><?php echo $salonData['PREVIEW_TEXT']; ?></p>
					<div class="m-meta">
						<?php if(!empty($salonData['PROPERTIES']['PHONE'])) { ?>
							<p class="m-phone"><?php echo str_replace('(', '<span>(', str_replace(')', ')</span>', $salonData['PROPERTIES']['PHONE'])); ?></p>
						<?php } ?>
						<?php if(!empty($salonData['PROPERTIES']['ADRESS']) || !empty($salonData['PROPERTIES']['WWW'])) { ?>
							<p class="m-links">
								<?php if(!empty($salonData['PROPERTIES']['ADRESS'])) { ?>
									Адрес: <a href="/personal/salon/#map" title="Посмотреть на карте" class="m-address"><?php echo $salonData['PROPERTIES']['ADRESS']; ?></a>
								<?php } ?>
								<?php if(!empty($salonData['PROPERTIES']['ADRESS'])) { ?>
									<?php if(!empty($salonData['PROPERTIES']['WWW'])) { ?>
										<br/>
									<?php } ?>
									Сайт: <a href="http://<?php echo $salonData['PROPERTIES']['WWW']; ?>" terget="_blank" title="Перейти на сайт" class="m-site">http://<?php echo $salonData['PROPERTIES']['WWW']; ?></a>
								<?php } ?>
							</p>
						<?php } ?>
					</div>
				</div>
				<div class="l-extra-top"></div>
				<div class="l-extra-bottom"></div>
			</EXTRA>
			
		<?php
	}

	if($showElement){
		//Первые 5-20 салонов
		$arSelect = array('ID', 'IBLOCK_ID', 'PROPERTY_CLICK', 'PROPERTY_SORT', 'PROPERTY_SUMM');
		$arFilter = array('IBLOCK_ID' => 1, 'ACTIVE' => 'Y', 'PROPERTY_CITY' => $GLOBALS['arCity']['ID']);
		$result = CIBlockElement::GetList(array('PROPERTY_SORT' => 'DESC'), $arFilter, false, array('nPageSize' => 20), $arSelect);
		$i = 1;
		$currentClick = DEFAULT_CLICK_VALUE;
		while($arFields = $result->GetNext()) {
			if($arFields['PROPERTY_SUMM_VALUE'] >= $arFields['PROPERTY_CLICK_VALUE']) {
				$currentClick = $arFields['PROPERTY_CLICK_VALUE'];
			}
			if($i == 1)//Первое место
				$place1 = intval($currentClick) + 1;
			if($i == 5)//Пятое место
				$place5 = intval($currentClick) + 1;
			if($i == 20)//Двадцатое место
				$place20 = intval($currentClick) + 1;
			$i++;
		}

	$dataTo   = date($DB->DateFormatToPHP(CLang::GetDateFormat()), mktime(0,0,0,date("m"),date("d")+1 , date("Y")));
	$dataFrom = date($DB->DateFormatToPHP(CLang::GetDateFormat()), mktime(0,0,0,date("m"),date("d"),date("Y")));

    $arrFilterStat = array(
        "PROPERTY_SALON" => $arResult["ID"],
        "IBLOCK_ID" => 9,
        ">=DATE_CREATE" => $dataFrom,
        "<DATE_CREATE" => $dataTo
    );
    $resultStat = CIBlockElement::GetList(array(), $arrFilterStat, false, false);
    $revCont = 0;
    while($_revCont = $resultStat->GetNext()) {
			$revCont++;
		}
    $resultStat = $revCont;
		?>
					<div class="salon-info-line">
					<div class="container">
						<div class="info-line_right">
							статус:   
							<?php if($arResult["PROPERTIES"]["ACTIVE_ADMINISTRATOR"]["VALUE"]!="") { ?>
								<span class="inactive">Не активен</span>
							<?php } else { ?>
								<span class="active">Активен</span>
							<?php } ?>
						</div>
						<div class="info-line_left">
							<a href="/personal/salon/" class="info-line_info active">
								информация о салоне
							</a>
							<a href="/personal/promo/" class="info-line_advertise">
								реклама на сайте
							</a>
						</div>
					</div><!-- end container -->
				</div><!-- end salon-info-line -->
		  
		 
		<?php if($showUser == 0) { ?>
			<div class="kr_user_auth_letter">
				<p><span>Необходимо подтвердить электронную почту</span></p>
				<p><a href="#" onclick="resendUserNotification(<?php echo $USER->GetID(); ?>); return false;">Выслать письмо повторно</a></p>
				<p class="kr_user_auth_letter_result">Письмо отправлено. Проверьте почту.</p>
			</div>
		<?php } else 
		{ ?>

			<section class="enter-block lk-1">
					<div class="container">
						<!-- <p class="notice-msg">
							Убедительная просьба не размещать в тексте программ телефоны, ссылки и адреса
						</p> -->
						<?php if($arResult['ACTIVE'] == 'N') { ?>
							<p class="note-msg">
								<span>Емайл подтвержден!</span> Салон начнет отображаться в каталоге сразу после проверки модератором.
							</p>
						<?php } ?>
					</div>
					<div class="lk-wrap">
					<div class="container">
					<!--DEBUG-->
						<div class="lk-wrap_aside">
							<?php
								$rk_is_active = $salonData['PROPERTIES']['SUMM'] > 0 && $salonData['PROPERTIES']['SUMM'] >= $salonData['PROPERTIES']['CLICK'];
								$rk_is_pause = $salonData['PROPERTIES']['REKLAMA_OFF'] == 8;
								$rk_class_bg = $rk_is_active ? ($rk_is_pause ? 'pause' : 'active') : 'inactive';
							?>
							<div class="adv-company <?php echo $rk_class_bg; ?>">
								<span>
									Рекламная кампания
								</span>
								<?php if($rk_is_active) { ?>
									<?php if($rk_is_pause) { ?>
										<span class="status-adv">
											Остановлена
										</span>
									<?php } else { ?>
										<span class="status-adv">
											Активна
										</span>
									<?php } ?>
								<?php } else {?>
									<span class="status-adv">
										Неактивна
									</span>
								<?php } ?>

								<div class="control-block" action="<?php echo $rk_is_pause ? 'on' : 'off'; ?>">
									<i class="fa fa-play <?php echo $rk_is_pause ? 'inactive' : 'active'; ?>" style="cursor: pointer;" aria-hidden="true"></i>
									<i class="fa fa-pause <?php echo $rk_is_pause ? 'active' : 'inactive'; ?>" style="cursor: pointer;"  aria-hidden="true"></i>
								</div>

								<div class="count-view <?php echo $rk_class_bg; ?>">
									Показов сегодня: <span><?=$resultStat?></span>
								</div>
							</div>
					
							<script>
								$(function() {
									$('.control-block').click(function() {
										var action = $(this).attr('action');
										$.ajax({
											type: 'POST',
											url: '/ajax/pauseRK.php',
											data: {action: action, salon: '<?php echo $salonData['ID']; ?>'},
											dataType: 'json',
											success: function(answer) {
												if(answer.status == 'ok') {
													if(answer.is_active == 0) {
														$('.adv-company').removeClass('active pause').addClass('inactive');
														$('.count-view').removeClass('active pause').addClass('inactive');
														$('.status-adv').text('Неактивна');
													}
													else {
														if(answer.is_pause == 0) {
															$('.adv-company').removeClass('inactive pause').addClass('active');
															$('.count-view').removeClass('inactive pause').addClass('active');
															$('.status-adv').text('Активна');
														}
														else {
															$('.adv-company').removeClass('inactive active').addClass('pause');
															$('.count-view').removeClass('inactive active').addClass('pause');
															$('.status-adv').text('Остановлена');
														}
													}
													if(answer.is_pause == 0) {
														$('.fa-play').removeClass('inactive').addClass('active');
														$('.fa-pause').removeClass('active').addClass('inactive');
														$('.control-block').attr('action', 'off');
													}
													else {
														$('.fa-play').removeClass('active').addClass('inactive');
														$('.fa-pause').removeClass('inactive').addClass('active');
														$('.control-block').attr('action', 'on');
													}
												}
											}
										});
									});
								});
							</script>
							
							<?/*
							<div class="title">Статистика</div>
							<div class="stat">
								За сутки: 12 / 120 руб.<br />
								За неделю: 120 / 1200 руб.<br />
								За месяц: 1200 / 12000 руб.<br />
								Всего: 12000 / 120000 руб.
							</div>
							*/?>
							<?//var_dump($salonData['PROPERTIES']);
							/*?><span style="display:none;"><?var_dump ($salonData);?></span><?*/
							$b=1;
							//echo($salonData['PROPERTIES']['REKLAMA_OFF']);
							$arFilter = Array("IBLOCK_ID"=>1, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
							$res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), Array());
							$stack = array();
							while($ob = $res->GetNextElement())
							{
								$arFields = $ob->GetFields();
								$arProps = $ob->GetProperties();
								//echo $arFields['NAME'];
								//print_r($arProps);
								//echo $salonData['PROPERTIES']['CITY'];								
								if($salonData['PROPERTIES']['REKLAMA_OFF']!=8){
									if($arProps['REKLAMA_OFF']['VALUE']!="Y"){
										if($arFields['ID']!=$salonData['ID']){
											if($arProps['CITY']['VALUE']==$salonData['PROPERTIES']['CITY']){												
												//if($arProps['U_POSITION_COUNT']['VALUE']!=""){
													//if($salonData['PROPERTIES']['U_POSITION_COUNT'] == $arProps['U_POSITION_COUNT']['VALUE']){
													if($arProps['SUMM']['VALUE'] > $arProps['CLICK']['VALUE']){														
														if($salonData['PROPERTIES']['CLICK'] < $arProps['CLICK']['VALUE']){
															$b++;
															//array_push($stack, $arProps['CLICK']['VALUE']);
															//echo $arProps['CLICK']['VALUE'].' - '.$arFields['NAME'].'<BR/>';
														}elseif($salonData['PROPERTIES']['CLICK'] == $arProps['CLICK']['VALUE']){
															if($salonData['PROPERTIES']['SUMM'] < $arProps['SUMM']['VALUE']){
																$b++;																
																//echo 'условие 2<BR/>';
															}
															array_push($stack, $arProps['CLICK']['VALUE']);
															/*echo $arFields['NAME'].' - ';
															echo $arProps['SUMM']['VALUE'].' - ';
															echo $arProps['CLICK']['VALUE'].'<BR/>';*/
														}
														else{array_push($stack, $arProps['CLICK']['VALUE']);}
													}
												//}
											}
										}
									}
								}else{									
									if($arProps['CITY']['VALUE']==$salonData['PROPERTIES']['CITY']){
										array_push($stack, $arProps['CLICK']['VALUE']);
										if($arProps['REKLAMA_OFF']['VALUE']!="Y"){
											if($arProps['SUMM']['VALUE'] > $arProps['CLICK']['VALUE']){
											$b++;
											/*echo $arFields['NAME'].' - ';
											echo $arProps['SUMM']['VALUE'].' - ';
											echo $arProps['REKLAMA_OFF']['VALUE'].' - ';
											echo $arProps['CLICK']['VALUE'].'<BR/>';*/
											}
										}else{										
											if($arFields['ID']!=$salonData['ID']){
												//if($arProps['U_POSITION_COUNT']['VALUE']!=""){
													//if($salonData['PROPERTIES']['U_POSITION_COUNT'] == $arProps['U_POSITION_COUNT']['VALUE']){
												if($arProps['SUMM']['VALUE'] > $arProps['CLICK']['VALUE']){
													if($salonData['PROPERTIES']['CLICK'] < $arProps['CLICK']['VALUE']){
															$b++;
															//array_push($stack, $arProps['CLICK']['VALUE']);
															//echo $arProps['CLICK']['VALUE'].' - '.$arFields['NAME'].'<BR/>';
													}elseif($salonData['PROPERTIES']['CLICK'] == $arProps['CLICK']['VALUE']){
														if($salonData['PROPERTIES']['SUMM'] < $arProps['SUMM']['VALUE']){
																$b++;																
																//echo 'условие 2<BR/>';
															}
															array_push($stack, $arProps['CLICK']['VALUE']);
															/*echo $arFields['NAME'].' - ';
															echo $arProps['SUMM']['VALUE'].' - ';
															echo $arProps['CLICK']['VALUE'].'<BR/>';*/
														}else{array_push($stack, $arProps['CLICK']['VALUE']);}
													}
												//}
											}
										}
									}
								}
								/*if($b==1) print_r($arProps);
								$b++;	*/							
							}//echo $salonData['PROPERTIES']['CLICK'];
							?> 
							<div class="lk-wrap_aside__salon">
								<div class="aside_salon__item">
									Ваш салон занимает:
									<div class="place">
									<span class="num" > <?php echo $b; ?> </span><span>место</span>
									</div>
								</div>
								<div class="aside_salon__item">
									Остаток средств:
									<div class="place">
									<span class="summ"><?php echo $salonData['PROPERTIES']['SUMM']; ?></span><span>руб.</span>
									</div>
									<a href="<? $_SERVER['HTTP_HOST']; ?>/personal/promo/?type=payment" class="add-money">Пополнить</a>
									<a href="<? $_SERVER['HTTP_HOST']; ?>/personal/promo/?type=history" class="history">
										История пополнений
									</a>
								</div>
							</div>
							<div class="lk_rate">
								<div class="lk_rate_item rate-block">
									Ставка
									<form action="" method="">
										<input id="rateValue" type="text" value="<?php echo $salonData['PROPERTIES']['CLICK']; ?>" data-current="<?php echo $salonData['PROPERTIES']['CLICK']; ?>" name="rateValue" placeholder="4">
										<a href="/ajax/ajaxSaveRate.php?salon=<?=$salonData['ID']?>" title="Сохранить ставку" class="hide saveRate">сохранить</a>
									</form>
									руб.
								</div>
								<script type="text/javascript">
									$(document).ready(function() {
										$('body').on('click','.saveRate',function(){
											var href=$(this).attr('href');
											var rate = $("#rateValue").val();
											$.ajax({
												url: href,
												type: 'POST',
												 dataType: 'json',
												data: {'rate': rate},
											})
											.done(function(response) {
													
												if( !response.error){
													location.reload();
													//$('.place .num').html(response.place);
													//$('.lk_rate_item .rate').html(response.click);
												}
												
												if(response.active == 1)
												{
													$(".adv-company").removeClass("inactive");
													$(".adv-company").addClass("active");
													$(".fa-play").removeClass('active');
													$(".fa-play").addClass('inactive');
													$(".fa-pause").removeClass('inactive');
													$('.fa-pause').addClass('active');
													$(".status-adv").text("Активна");
													$(".control-block").attr('action','off');
													$(".count-view").addClass('active');
												}
												else
												{
													$(".adv-company").addClass("inactive");
													$(".adv-company").removeClass("active");
													$(".fa-play").removeClass('inactive');
													$(".fa-play").addClass('active');
													$('.fa-pause').removeClass('active');
													$('.fa-pause').addClass('inactive');
													$(".status-adv").text("Неактивна");
													$(".count-view").removeClass('active');
													$(".control-block").attr('action','on');
												}
												
												if(response.error){
													alert(response.out);
												}
											})
											.fail(function() {
											})
											.always(function() {
											});
											
											return false;
										});
									});
								</script>

								
								
								<?$temp=max($stack);
								if($salonData['PROPERTIES']['CLICK']> $temp){
									$temp = $temp+1;
								}elseif($salonData['PROPERTIES']['CLICK']=='' || !$salonData['PROPERTIES']['CLICK'] || $salonData['PROPERTIES']['CLICK']==0){
									
									$temp = 4;
								}else{
									
									$temp = $salonData['PROPERTIES']['CLICK'];
								}?>
								<div class="lk_rate_item">
									Актуальная ставка <span class="rate"><?echo $temp;?></span> руб. <a href="#actual" id='title_for_first_link'>Что это такое?</a>
									<script type="text/javascript">
										
										$(document).ready(function() {
											$('body').on('mouseover', 'a#title_for_first_link', function(event) {
												event.preventDefault();

												$("div#title_for_first_link").show();
												return false;
											});
											$('body').on('mouseleave', 'a#title_for_first_link', function(event) {
												event.preventDefault();

												$("div#title_for_first_link").hide();
												return false;
											});

										});
									</script>
									<div id="title_for_first_link" class="titleV" style="display: none;">
										<u>Актуальная ставка</u> - это стоимость, которая списывается со счета салона за один переход.
										Если, к примеру, у ближайшего конкурента снизу ставка 4 руб., а у Вашего салона 20 руб.,
										то <u>актуальная ставка</u> для Вас будет 5 руб. Столько и будет стоить для Вас один переход.
										<img src="<?=SITE_TEMPLATE_PATH?>/images/chat.png" class="chatPng">
									</div>
								</div>
								<div class="lk_rate_item">
									Вы можете установить любую ставку (целое число от 4 руб.)
								</div>
							</div>
						</div>
						
            <script>

						
                $(document).ready(function(){

                    // При наведении на ссылку
                    $(".link_test").mouseover
                    (function(){

                        // Получаем ID блока, который нужно показать
                        var title = $(this).attr("title2");

                        // Показываем блок

										// Получаем ID блока, который нужно показать
										var title = $(this).attr("title2");

										// Показываем блок

										$(title).fadeIn();
								});

								// При уходе мышки со ссылки
								$(".link_test").mouseout
								(function(){

										// Получаем ID блока, который нужно показать
										var title = $(this).attr("title2");

										// Скрываем блок
										$(title).fadeOut();

								});
							});
            </script>
	<?php } ?>

<?php if($showUser == 1) { ?>
<?//Вывод основного контента?>

<?php if(isset($_REQUEST["strIMessage"])) { ?>
	<p class="notetext"><?=$_REQUEST["strIMessage"]?></p>
<?php } ?>

<?if(empty($salonData['PROPERTIES']['PHONEAUTH']) && empty($salonData['PROPERTIES']['PHONEAUTHCHECK'])){
	
	CIBlockElement::SetPropertyValues($salonData['ID'], $salonData['IBLOCK_ID'], 15, 'PHONEAUTHCHECK');
?>


<div class="pass-popup offer-popup">
          <div class="offer-popup-bgr" onclick="$('.pass-popup').fadeOut();"></div>
          <div class="offer-popup-inner">
				<a href="javascript:void(0);" onclick="$('.pass-popup').fadeOut();" class="close"></a>
				
				<h3>Вы можете привязать к личному кабинету номер телефона.
					Это позволит защитить доступ к данным салона. <br>
					<b style='font-size:14px;'>Номер телефона не будет отображаться на страницах каталога.</b>
				</h3>
					
					<style>
						input[name="PHONEAUTH"] {
							margin-top: 15px;
						}
								
						.form-msg-error{
							border: 2px solid #FF000C;
							padding: 10px;
							margin-top: 15px;
							border-radius: 5px;
							color: #fff;
							font-weight: normal;
						}
						
						.form-msg-sussecc{
							border: 2px solid #10CE10;
							padding: 10px;
							margin-top: 15px;
							border-radius: 5px;
							color: #fff;
							font-weight: normal;
						}
						
					</style>
					<div id="form">
						
						<form action="/ajax/editPhoneForRestore.php" class="editPhoneForRestore" style="padding-left:0px;">
							
							
							<div class="form-msg-error" style="display:none;">
								<b>ОШИБКА</b> - {{:msg}}
							</div>
							
							<div class="form-msg-sussecc" style="display:none;">
								Телефон <b>{{:msg}}</b> успешно сохранен.<br>
								Изменить номер Вы можете в любой момент в описание салона, поле - "Телефон для восстановления доступа".
							</div>
							
							<input type="hidden" size="30" name="salon" class="input"  value="<?=$salonData["ID"]?>">
							<input type="text" size="30" name="PHONEAUTH" class="input"  placeholder="+79009999999"><br><br>
							<input type="submit" value="Сохранить" class="input button">
						</form>
					</div>
			</div>
</div>

		<script>
                $(document).ready(function(){
					$("form.editPhoneForRestore input").click(function(){
						$(this).attr("placeholder","");
					});
                    $("#form").on('submit','.editPhoneForRestore',function(){
						$.ajax({
								url:     $(this).attr("action"), 
								type:     "POST", 
								dataType: "json", 
								data: $(this).serialize(), 
								success: function(response) { 
									
									if ( response.error ){										
										$('.editPhoneForRestore .form-msg-error').show().html('Ошибка - ' + response.msg);
										$('.editPhoneForRestore .form-msg-sussecc').hide();																			
									}
									else{
										$('.editPhoneForRestore .form-msg-error').hide();
										$('.editPhoneForRestore .form-msg-sussecc').show().html('Телефон <b>' + response.msg +' </b> успешно сохранен.<br>Изменить номер Вы можете в любой момент в описание салона, поле - "Телефон для восстановления доступа".');
									}
								}
						});


						return false;
                	});
			
				});
            </script>
<?}?>

<?if($showElement) { ?>

					<div class="lk-wrap_content">
						<div class="lk_tabs">
							<?$URL = parse_url($_SERVER["REQUEST_URI"])?>
		<?$URL["path"] = str_replace("index.php", "", $URL["path"])?>

		<?if($URL["path"]=="/personal/salon/") {?>
		<input id="tab_lk1" type="radio" name="tabs" <?if(!isset($_GET['type'])) {?> checked <? }?> >
							<label for="tab_lk1">Описание</label>
		<? }?>

	
		<input id="tab_lk2" type="radio" name="tabs" <?if(isset($_GET['type']) && $_GET['type']=='girls') { ?> checked <? } ?> >
							<label for="tab_lk2">Девушки <span><?=$girlsCount?></span></label>

		
         <input id="tab_lk3" type="radio" name="tabs" <?if(isset($_GET['type']) && $_GET['type']=='programms') { ?> checked <? } ?>>
							<label for="tab_lk3">Программы <span><?=$programmsCount?></span></label>
     

        	
			<input id="tab_lk4" type="radio" name="tabs" <?if(isset($_GET['type']) && $_GET['type']=='news') { ?> checked <? } ?>>
							<label for="tab_lk4">Акции <span><?=$newsCount?></span></label>
			<input id="tab_lk5" type="radio" name="tabs" <?if(isset($_GET['type']) && $_GET['type']=='vacancy') {?> checked <? } ?>>
							<label for="tab_lk5">Вакансии <span><?=$vacancyCount?></span></label>
			<input id="tab_lk6" type="radio" name="tabs" <?if(isset($_GET['type']) && $_GET['type']=='comments') { ?> checked <? } ?>>
							<label for="tab_lk6">Отзывы <span><?=$commentsCount?></span></label>

							

<? } ?>
<? } ?>
<? } ?>