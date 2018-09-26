<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
// Используется именно клик, т.к. только по переменной $_SERVER
// нельзя достоверно определить источник захода на сайт

// Поэтому, пользователю формируется уникальная ссылка, содержащая куку
// а в скрипте клика сравнивается входящий идентификатор и идентификатор в куке

// Таким образом, подтверждается, что до клика пользователь находился на странице сайта и получил куку, а потом уже кликнул
// а не пришел на обработчик клика из поиска, рекламы, из избранного и т.д. 

// Если не передана кука или кука пустая
CModule::IncludeModule("iblock");
if(isset($_REQUEST["s"]))
{
    if($_REQUEST["s"]!="")
    {
        echo "===1";
	$ip = $_SERVER['REMOTE_ADDR'];
    // $ip_data = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
	       $ip_data = json_decode(file_get_contents("http://api.sypexgeo.net/ajh71/json/{$ip}"));
    	if ( isset($ip_data->country->iso) && $ip_data->country->iso != 'RU' )
    	{	
		  echo "0";exit;
        }

        // Получаем переменную "s" (session)
        $S_REQUEST = $_REQUEST["s"];

        global $APPLICATION;
        $S_COOKIE = $APPLICATION->get_cookie("PROMO");

        // Сравниваем сессию из $_REQUEST и сессию из $_COOKIE
        if($S_REQUEST==$S_COOKIE)
        {
            echo "===2";
            // Задаем нулевые балансы, на случай, если ничего не будет найдено
            $BALANCE = 0;
            $CLICK = 0;
			
			//===============DEBUG===========================
			 // Получаем баланс салона по символьному коду
            $arSelect = Array("ID", "IBLOCK_ID", "NAME","PROPERTY_SUMM", "PROPERTY_CLICK", "PROPERTY_USER","PROPERTY_SORT","PROPERTY_CITY", "PROPERTY_USER", "PROPERTY_U_POSITION_DOWN");
            $arFilter = Array("IBLOCK_ID"=>1, "CODE"=>$_REQUEST["ELEMENT_CODE"]);
            $result = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
            if($arFields = $result->GetNext())
            {
				$rsk = CIBlockElement::GetList(Array("PROPERTY_SORT"=>"DESC"), array("IBLOCK_ID"=>1,"<PROPERTY_SORT"=>$arFields["PROPERTY_SORT_VALUE"]), false, Array("nPageSize"=>1), $arSelect);
				$arRsk = $rsk->GetNext();
                $rsk = CIBlockElement::GetList(Array("PROPERTY_SORT"=>"DESC"), array("IBLOCK_ID"=>1,">PROPERTY_SORT"=>$arFields["PROPERTY_SORT_VALUE"]), false, Array("nPageSize"=>1), $arSelect);
                $arRsk2 = $rsk->GetNext();

                $BALANCE = intval($arFields["PROPERTY_SUMM_VALUE"]);
                $CLICK = intval($arFields["PROPERTY_CLICK_VALUE"]);
                $USER_ID = intval($arFields["PROPERTY_USER_VALUE"]);
                $SALON = intval($arFields["ID"]);


            }
			/*echo $arFields["NAME"].'<br>';
			echo 'Город: '.$arFields["PROPERTY_CITY_VALUE"].'<br>';
			echo $arFields["ID"].'<br>';
			echo 'Гарант.ставка: '.$CLICK.'<br><br> Первая компания за PLAY BOY у которой меньше гарант.ставка<br>';*/
			if($CLICK>0)
            {	$arSort = array("PROPERTY_CLICK"=>"DESC","PROPERTY_SUMM"=>"DESC");
				$arFilter = array("ACTIVE"=>"Y","IBLOCK_ID"=>1,"!ID"=>$arFields["ID"],"PROPERTY_CITY"=>$arFields["PROPERTY_CITY_VALUE"],"<=PROPERTY_CLICK"=>$CLICK,"!=PROPERTY_76"=>8,"!=PROPERTY_81"=>12,">=PROPERTY_SUMM"=>$CLICK);
				$arSelect = array("NAME","PROPERTY_CLICK","PROPERTY_SUMM");
				//$arSelect = array("PROPERTY_*");
				
                $maxClickRes = CIBlockElement::GetList($arSort, $arFilter, false,Array("nPageSize"=>5),$arSelect);
                
			  
				while($ar_fields = $maxClickRes->GetNext())
					{
					 if($ar_fields['PROPERTY_SUMM_VALUE']>=$ar_fields['PROPERTY_CLICK_VALUE']){
							$arrR[] = $ar_fields;
						}
					}
					//echo '<pre>';
					//print_r ($arrR);
					//echo '</pre>';
				}
				if($arrR[0]['PROPERTY_CLICK_VALUE'] < $CLICK){
					$CLICK2 = $arrR[0]['PROPERTY_CLICK_VALUE']+1;
				}elseif($CLICK=='' || !$CLICK || $CLICK==0){
					
					$CLICK2 = 4;
				}else{
					
					$CLICK2 = $CLICK;
				}
				
			//====================================

            // Получаем баланс салона по символьному коду
           /* $arSelect = Array("ID", "IBLOCK_ID", "NAME","PROPERTY_SUMM", "PROPERTY_CLICK", "PROPERTY_USER","PROPERTY_SORT","PROPERTY_CITY", "PROPERTY_USER", "PROPERTY_U_POSITION_DOWN");
            $arFilter = Array("IBLOCK_ID"=>1, "CODE"=>$_REQUEST["ELEMENT_CODE"]);
            $result = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), $arSelect);
            if($arFields = $result->GetNext())
            {
				$rsk = CIBlockElement::GetList(Array("PROPERTY_SORT"=>"DESC"), array("IBLOCK_ID"=>1,"<PROPERTY_SORT"=>$arFields["PROPERTY_SORT_VALUE"]), false, Array("nPageSize"=>1), $arSelect);
				$arRsk = $rsk->GetNext();
                $rsk = CIBlockElement::GetList(Array("PROPERTY_SORT"=>"DESC"), array("IBLOCK_ID"=>1,">PROPERTY_SORT"=>$arFields["PROPERTY_SORT_VALUE"]), false, Array("nPageSize"=>1), $arSelect);
                $arRsk2 = $rsk->GetNext();

                $BALANCE = intval($arFields["PROPERTY_SUMM_VALUE"]);
                $CLICK = intval($arFields["PROPERTY_CLICK_VALUE"]);
                $USER_ID = intval($arFields["PROPERTY_USER_VALUE"]);
                $SALON = intval($arFields["ID"]);


            }

            if($CLICK>0)
            {
                $maxClickRes = CIBlockElement::GetList(array("PROPERTY_CLICK"=>"DESC"), array("ACTIVE"=>"Y","IBLOCK_ID"=>10,"!ID"=>$arFields["ID"],"PROPERTY_CITY"=>$arFields["PROPERTY_CITY_VALUE"],"<=PROPERTY_CLICK"=>$CLICK), false,false,array("PROPERTY_CLICK"))->Fetch();
                $CLICK2 = $maxClickRes["PROPERTY_CLICK_VALUE"];
                if(!$CLICK2)
                    $CLICK2=4;
                else
                    $CLICK2++;
            }*/


            // Если баланс салона и ставка позволяет провести списание
            if($CLICK>0 && ($BALANCE-$CLICK2)>=0)
            {
				echo "===3";
                // Определяем IP
                $IP = "";
                if (!empty($_SERVER['HTTP_CLIENT_IP']))
                    $IP = $_SERVER['HTTP_CLIENT_IP'];
                elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
                    $IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
                else
                    $IP = $_SERVER['REMOTE_ADDR'];

                // Устанавливаем флаг по умолчанию, позволяющий произвести списание
                $spendDo = 1;

                // Проверяем, если пользователь авторизован, то это владелец или конкурент, не списываем
                if($USER->IsAuthorized())
                    $spendDo = 0;
                else
                {
                    echo "===4";
                    // Проверяем, списания по данной сессии или IP и данному салону за последнее время (2 часа)
                    $arSpendFilter = Array(
                        "IBLOCK_ID"=>9,
                        "PROPERTY_SALON"=>$SALON,
                        ">DATE_CREATE"=>date($DB->DateFormatToPHP(CLang::GetDateFormat()), (time()-2*3600)),
                        Array("LOGIC" => "OR",
                            array("=PROPERTY_SESSION" => $S_COOKIE),
                            array("=PROPERTY_IP" => $IP),
                        ),
                    );
                    $spendCount = CIBlockElement::GetList(false, $arSpendFilter, Array());

echo " - speedcount=".$spendCount." ";
                    if($spendCount>0)
                        $spendDo = 0;

                }

                if($spendDo)
                {
                    // Добавляем списание
                    $el = new CIBlockElement;
                    $arLoadProductArray = Array(
                        "IBLOCK_ID"      => 9,
                        "NAME"           => "Клик",
                        "ACTIVE"         => "Y",
                        "PROPERTY_VALUES"=> array(
                            "46" => $CLICK2,
                            "47" => $USER_ID,
                            "48" => $SALON,
                            "49" => array("VALUE" => 4),
                            "50" => $IP,
                            "55" => $S_COOKIE,
                            "56" => $_SERVER["HTTP_USER_AGENT"],
                            "111" => $_REQUEST['CITY'],
                            "112" => $_REQUEST['UTM'] ? $_REQUEST['UTM'] : null,
                        ),
                    );

                    if($SPENDING = $el->Add($arLoadProductArray))
                    {
                        echo '-ЕСТЬ СПИСАНИЕ на - '.$CLICK2;
                        // Уменьшаем баланс салона на ставку
                        CIBlockElement::SetPropertyValues($arFields["ID"], $arFields["IBLOCK_ID"], ($BALANCE-$CLICK2), "SUMM");

                        // Если следующее списание возможно, устанавливаем одну цифру сортировки, с учетом клика
                        if(($BALANCE-$CLICK2*2)>=0)
                        {
                            // Пересчитываем сортировку салона
                            // Сортировку устанавливаем по формуле
                            // Ставка*10000000000+Баланс*100000+ID пользователя
                            // Для того чтобы исключить салоны с одинаковой сортировкой
                            // Чтобы можно было точно определить место салона
                            $NEW_SORT = $CLICK*10000000000 + ($BALANCE-$CLICK)*100000 + $arFields["ID"];
                        }
                        else
                        {
                            // Если следующее списание НЕвозможно, устанавливаем другую цифру сортировки, без учета клика
                            // Пересчитываем сортировку салона
                            // Сортировку устанавливаем по формуле
                            // Баланс*100000+ID пользователя
                            // Для того чтобы исключить салоны с одинаковой сортировкой
                            // Чтобы можно было точно определить место салона
                            $NEW_SORT = ($BALANCE-$CLICK)*100000 + $arFields["ID"];
                        }
                        // Устанавливаем новую сортировку
                        CIBlockElement::SetPropertyValues($arFields["ID"], $arFields["IBLOCK_ID"], $NEW_SORT, "SORT");
					
                    }
                    else
                    {
                        // Отправяем предупреждение админу
                        SendError($el->LAST_ERROR);
                    }
                }
            }
        }

    }
}
?>
