<?php
/********************************************
* RussianTypography mambot for Mambo/Joomla *
* Copyright (C) 2006-2007 by Denis Ryabov   *
* Homepage   : http://sanrsu.org.ru/        *
* Version    : 2.0.2                        *
* License    : Released under GPL           *
********************************************/

// Метод "выкусывания" тегов взят из статьи http://www.softportal.com/articles/item_txt.php?id=208.
// В ней указан источник http://spectator.ru/, но на этом сайте данная статья обнаружена не была...

// Метод расстановки кавычек — ©spectator.ru


//defined( '_VALID_MOS' ) or die( 'Доступ запрещен.' );

//$_MAMBOTS->registerFunction( 'onPrepareContent', 'botRusTypo' );


define('TAGBEGIN', "\x01");
define('TAGEND',   "\x02");
$Refs = array(); // буфер для хранения тегов
$RefsCntr = 0;   // счётчик буфера
function putTag($x)
{
	global $Refs, $RefsCntr;
	$Refs[] = $x[0];
	return TAGBEGIN.($RefsCntr++).TAGEND;
}
function getTag($x)
{
	global $Refs;
	return $Refs[$x[1]];
}


define('NOBRSPACE',  "\x03");
define('NOBRHYPHEN', "\x04");
define('THINSP',     "\x05");
define('DASH',       "\x06");
define('NUMDASH',    "\x07");

function Proof( $text)
{

return $text;

   global $Refs, $RefsCntr;

    $htmlents = array(
		'&#8222;'=>'„','&#8219;'=>'“','&#8220;'=>'”','&#8216;'=>'‘','&#8217;'=>'’',
		'&laquo;'=>'«','&raquo;'=>'»','&hellip;'=>'…','&euro;'=>'€','&permil;'=>'‰',
		'&bull;'=>'•','&middot;'=>'·','&ndash;'=>'–','&mdash;'=>'—','&nbsp;'=>' ',
		'&trade;'=>'™','&copy;'=>'©','&reg;'=>'®','&sect;'=>'§','&#8470;'=>'№',
		'&plusmn;'=>'±','&deg;'=>'°');
	$text = strtr( $text, $htmlents ); // Делаем замены html entity на символы из cp1251

// РАБОТА С ТЕГАМИ. ЧАСТЬ 1
	$text = preg_replace( '/(?> | )+(?=$|<br|<\/p)/', '', $text ); // Убираем лишние пробелы перед концом строки
	$text = preg_replace( '/<a +href([^>]*)> *(?:"|&quot;)([^<"]*)(?:"|&quot;) *<\/a>/', '"<a href\\1>\\2</a>"', $text ); // Выносим кавычки из ссылок
	$text = preg_replace( '/([а-яА-Яa-zA-Z]) ([а-яА-Яa-zA-Z]{1,5}(?>[.!?…]*))(?=$|<\/p>|<\/div>|<br>|<br \/>)/','\\1'.NOBRSPACE.'\\2', $text); // Последнее короткое слово в абзаце привязывать к предыдущему

//ПРЯМАЯ РЕЧЬ
	$text = preg_replace( '/(^|<p>|<br>|<br \/>)[  ]?- /','\\1— ', $text ); // Прямая речь - дефис в начале строки и после тегов <p>, <br> и <br />

// ВЫРЕЗАЕМ ТЕГИ
	$Refs = array();
	$RefsCntr = 0;
	$text = preg_replace_callback('/<!--.*?-->/s', 'putTag', $text); // комментарии
	$text = preg_replace_callback('/< *(script|style|pre|code|textarea).*?>.*?< *\/ *\1 *>/is', 'putTag', $text); // теги, которые вырезаются вместе с содержимым
	$text = preg_replace_callback('/<(?:[^\'"\>]+|".*?"|\'.*?\')+>/s', 'putTag', $text); // обычные теги

	//$text = strtr( $text, "\t\n\r", '   ' ); // Заменяем табулюцию и перевод строки на пробел
	$text = preg_replace( '/ +/', ' ', $text ); // Убираем лишние пробелы

	$text = str_replace( '&quot;','"', $text ); // Заменяем &quot на "
	$text = str_replace( '&#39;',"'", $text ); // Заменяем &#39 на '

	// УГЛЫ (ГРАДУСЫ, МИНУТЫ И СЕКУНДЫ)
	$text = preg_replace( '/((?>\d{1,3})) ?° ?((?>\d{1,2})) ?\' ?((?>\d{1,2})) ?"/','\\1° \\2&prime; \\3&Prime;', $text ); // 10° 11' 12"
	$text = preg_replace( '/((?>\d{1,3})) ?° ?((?>\d{1,2})) ?\'/','\\1° \\2&prime;', $text ); // 10° 11'
	$text = preg_replace( '/((?>\d{1,3})) °(?![^CcСсF])/','\\1°', $text ); // 10°, но не 10 °C
	$text = preg_replace( '/((?>\d{1,2})) ?\' ?((?>\d{1,2})) ?"/','\\1&prime; \\2&Prime;', $text ); // 11' 12"
	$text = preg_replace( '/((?>\d{1,2})) \'/','\\1&prime;', $text ); // 11'
	

// РАССТАВЛЯЕМ КАВЫЧКИ
	$text = preg_replace( '/(['.TAGEND.'\(  ]|^)"([^"]*)([^  "\(])"/', '\\1«\\2\\3»', $text ); // Расстановка кавычек-"елочек"
	if( stristr( $text, '"' ) ) // Если есть вложенные кавычки
	{
		$text = preg_replace( '/(['.TAGEND.'(  ]|^)"([^"]*)([^  "(])"/', '\\1«\\2\\3»', $text );
		while( preg_match( '/«[^»]*«/', $text ) )
			$text = preg_replace( '/«([^»]*)«([^»]*)»/', '«\\1„\\2“', $text );
	}
	

// ДЕЛАЕМ ЗАМЕНЫ
//	$text = str_replace( '• ','•'.NOBRSPACE, $text ); // Пункт (для списков)
	 // Тире
	$text = str_replace( ' - ',NOBRSPACE.DASH.' ', $text );
	$text = str_replace( ' - ',NOBRSPACE.DASH.' ', $text );
	
	$text = str_replace( '...','…', $text ); // Многоточие
	$text = str_replace( '+/-','±', $text ); // плюс-минус

	$text = str_replace( '(r)','<sup>®</sup>', $text );
	$text = str_replace( '(R)','<sup>®</sup>', $text ); // registered
	
	$text = preg_replace( '/\((c|C|с|С)\)/','©', $text );
	$text = str_replace( '© ','©'.NOBRSPACE, $text ); // copyright
	$text = str_replace( '(tm)','™', $text );
	$text = str_replace( '(TM)','™', $text );

	
	$text = str_replace( '&lt;=','&le;', $text ); // Меньше/равно
	$text = str_replace( '&gt;=','&ge;', $text ); // Больше/равно

// ДРОБИ
	$text = preg_replace( '/(^|[  ("«„])1\/2(?=$|[  )"»“.,!?:;…])/','\\1&frac12;', $text);
	$text = preg_replace( '/(^|[  ("«„])1\/4(?=$|[  )"»“.,!?:;…])/','\\1&frac14;', $text);
	$text = preg_replace( '/(^|[  ("«„])3\/4(?=$|[  )"»“.,!?:;…])/','\\1&frac34;', $text);

	$text = preg_replace( '/(^|[  ("«„])1\/3(?=$|[  )"»“.,!?:;…])/','\\1&#8531;', $text);
	$text = preg_replace( '/(^|[  ("«„])2\/3(?=$|[  )"»“.,!?:;…])/','\\1&#8532;', $text);
	$text = preg_replace( '/(^|[  ("«„])1\/8(?=$|[  )"»“.,!?:;…])/','\\1&#8539;', $text);
	$text = preg_replace( '/(^|[  ("«„])3\/8(?=$|[  )"»“.,!?:;…])/','\\1&#8540;', $text);
	$text = preg_replace( '/(^|[  ("«„])5\/8(?=$|[  )"»“.,!?:;…])/','\\1&#8541;', $text);
	$text = preg_replace( '/(^|[  ("«„])7\/8(?=$|[  )"»“.,!?:;…])/','\\1&#8542;', $text);
	

// ИНИЦИАЛЫ И ФАМИЛИИ
	$text = preg_replace( '/(?<=[^а-яА-ЯёЁa-zA-Z][А-ЯЁA-Z]\.|^[А-ЯЁA-Z]\.) ?([А-ЯЁA-Z]\.) ?(?=[А-ЯЁA-Z][а-яА-ЯёЁa-zA-Z])/', THINSP.'\\1'.NOBRSPACE, $text ); // Инициалы + фамилия
	$text = preg_replace( '/((?>[А-ЯЁA-Z][а-яА-ЯёЁa-zA-Z]+)) ([А-ЯЁA-Z]\.) ?(?=[А-ЯЁA-Z]\.)/', '\\1'.NOBRSPACE.'\\2'.THINSP, $text ); // Фамилия + инициалы
	$text = preg_replace( '/(?<=[^а-яА-ЯёЁa-zA-Z][А-ЯЁA-Z]\.|^[А-ЯЁA-Z]\.) ?(?=[А-ЯЁA-Z][а-яА-ЯёЁa-zA-Z])/', NOBRSPACE, $text ); // Инициал + фамилия
	$text = preg_replace( '/((?>[А-ЯЁA-Z][а-яА-ЯёЁa-zA-Z]+)) (?=[А-ЯЁA-Z]\.)/', '\\1'.NOBRSPACE, $text ); // Фамилия + инициал
	

// СОКРАЩЕНИЯ
	$text = preg_replace( '/([^а-яА-ЯёЁa-zA-Z]|^)(г\.|ул\.|пер\.|пл\.|пос\.|р\.|проф\.|доц\.|акад\.|гр\.) ?(?=[А-ЯЁ])/', '\\1\\2'.NOBRSPACE, $text ); // Сокращения
	$text = preg_replace( '/([^а-яА-ЯёЁa-zA-Z]|^)(с\.|стр\.|рис\.|гл\.|илл\.|табл\.|кв\.|дом|д.\|офис|оф\.|ауд\.) ?(?=\d)/', '\\1\\2'.NOBRSPACE, $text ); // Сокращения
	$text = preg_replace( '/([^а-яА-ЯёЁa-zA-Z]|^)(см\.|им\.|каф\.) ?(?=[а-яА-ЯёЁa-zA-Z\d])/', '\\1\\2'.NOBRSPACE, $text ); // Сокращения
	
	
// ЕДИНИЦЫ ИЗМЕРЕНИЯ
	$text = preg_replace( '/([а-яёa-z\d\.]) (?=экз\.|тыс\.|млн\.|млрд\.|руб\.|коп\.|у\.е\.|\$|€)/', '\\1'.NOBRSPACE, $text ); // Единицы измерения
	$text = preg_replace( '/([а-яёa-z\d\.]) (?=евро([ \.,!\?:;]|$))/', '\\1'.NOBRSPACE, $text ); // Евро
	

// ПРИВЯЗЫВАЕМ КОРОТКИЕ СЛОВА
	$text = preg_replace( '/([^а-яА-ЯёЁa-zA-Z0-9])(я|ты|мы|вы|он|не|ни|на|но|в|во|до|от|и|а|с|со|о|об|ну|к|ко|за|их|из|ее|её|ей|ой|ай|у) (?=[а-яА-ЯёЁa-zA-Z]{3})/', '\\1\\2'.NOBRSPACE, $text ); // Короткие слова прикрепляем к следующим (если те сами не короткие)
	$text = preg_replace( '/([а-яА-ЯёЁ]) (?=(же|ж|ли|ль|бы|б|ка|то)([\.,!\?:;])?( |$))/', '\\1'.NOBRSPACE, $text ); // Частицы
	$text = preg_replace( '/([.!?…] [А-ЯЁA-Z][а-яА-ЯёЁa-zA-Z]{0,3}) /', '\\1'.NOBRSPACE, $text ); // Слова от 1 до 3 букв в начале предложения
	

// И Т.Д., И Т.П., Т.К., ...
	$text = preg_replace( '/([^а-яА-ЯёЁa-zA-Z]и) (д|п)(?=р\.)/', '\\1'.NOBRSPACE.'\\2', $text ); // и др., и пр.
	$text = preg_replace( '/([^а-яА-ЯёЁa-zA-Z]и) т\. ?(?=(д|п)\.)/', '\\1'.NOBRSPACE.'т.'.THINSP, $text ); // и т.д., и т.п.
	$text = preg_replace( '/([^а-яА-ЯёЁa-zA-Z]в) т\. ?(?=ч\.)/', '\\1'.NOBRSPACE.'т.'.THINSP, $text ); // в т.ч.
	$text = preg_replace( '/([^а-яА-ЯёЁa-zA-Z]т\.) ?(?=(к|н|е)\.)/', '\\1'.THINSP, $text ); // т.к., т.н., т.е.
	$text = preg_replace( '/([^а-яА-ЯёЁa-zA-Z](к|д)\.) ?(ф\.-м|х|б|т|ф|п)\. ?(?=н\.)/', '\\1'.THINSP.'\\2.'.THINSP, $text ); // к.т.н., д.ф.-м.н., ...
	

// ИСПРАВЛЕНИЕ ГРАММАТИЧЕСКИХ ОШИБОК
	$text = preg_replace( '/\( *([^)]+?) *\)/', '(\\1)', $text ); // удаляем пробелы после открывающей скобки и перед закрыващей скобкой
	$text = preg_replace( '/([а-яА-ЯёЁa-zA-Z.,!?:;…])\(/', '\\1 (', $text ); // добавляем пробел между словом и открывающей скобкой, если его нет (отключите, если у Вас на сайте есть формулы)
	$text = preg_replace( '/([а-яА-ЯёЁa-zA-Z]),(?=\d)/','\\1, ', $text); // Делает проверку в расстановке запятых и меняет слово,число на слово, число (ул. Дружбы, 46)
	$text = str_replace( ','.NOBRSPACE.DASH.' ',','.DASH.' ', $text );
	$text = str_replace( '.'.NOBRSPACE.DASH.' ','.'.DASH.' ', $text );

	$text = str_replace( '!?','?!', $text ); // Правильно в таком порядке
	$text = preg_replace( '/(!|\?)(?:…|\.\.\.)/','\\1..', $text ); // Убираем лишние точки
	$text = preg_replace( '/ (?=[.,!?:;])/','', $text ); // Убираем пробелы перед знаками препинания
	$text = preg_replace( '/(№|§) ?(?=\d)/', '\\1 ', $text ); // пробел между знаком "№" или "§" и числом.
	$text = str_replace( '№ №', '№№', $text ); // слитное написание "№№"
	$text = str_replace( '§ §', '§§', $text ); // слитное написание "§§"
	//TODO: Выносим знаки препинания (.,:;) вне кавычек, а (!?…) в кавычки

// ВСЁ О ЧИСЛАХ
	$text = preg_replace( '/(\d) *(?:\*|х|x|X|Х) *(?=\d)/', '\\1&times;', $text ); // обрабатываем размерные конструкции (200x500)
	// Делает неразрывными номера телефонов
	$text = preg_replace( '/(\+7|8) ?(\(\d+\)) ?(\d+)-(\d{2})-(?=\d{2})/','\\1'.NOBRSPACE.'\\2'.NOBRSPACE.'\\3'.NOBRHYPHEN.'\\4'.NOBRHYPHEN, $text );
	$text = preg_replace( '/(\(\d+\)) ?(\d+)-(\d{2})-(?=\d{2})/','\\1'.NOBRSPACE.'\\2'.NOBRHYPHEN.'\\3'.NOBRHYPHEN, $text );
	$text = preg_replace( '/(\d+)-(\d{2})-(?=\d{2})/','\\1'.NOBRHYPHEN.'\\2'.NOBRHYPHEN, $text );

	$text = preg_replace( '/((?>\d+))-(?=(?>\d+)([ .,!?:;…]|$))/','\\1'.NUMDASH, $text );
	$text = preg_replace( '/((?>[IVXLCDM]+))-(?=(?>[IVXLCDM]+)([ .,!?:;…]|$))/','\\1'.NUMDASH, $text );

	$text = preg_replace( '/ -(?=\d)/',' &minus;', $text ); // Минус перед цифрами
	$text = preg_replace( '/([ '.DASH.NUMDASH.']|^)((?>\d+)) /','\\1\\2'.NOBRSPACE, $text ); // Неразрывный пробел после арабских цифр
	$text = preg_replace( '/([ '.DASH.NUMDASH.']|^)((?>[IVXLCDM]+)) /','\\1\\2'.NOBRSPACE, $text ); // Неразрывный пробел после римских цифр
	//TODO: Неразрывный пробел в конструкциях вида 10 кг и т.д. (если предыдущее правило отключено)
	//TODO: Вставлять неразрывный пробел между числом и сокращением размерностью, чтобы не было 1кг (причем только для общепринятых сокращений размерностей...)
	$text = preg_replace( '/([-+]?(?>\d+)(?:[.,](?>\d*))?)[  '.NOBRSPACE.']?[CС]\b/','\\1&deg; C', $text); // Заменяет C в конструкциях градусов на °C
	$text = preg_replace( '/(\d)[  '.NOBRSPACE.'](?=%|‰)/','\\1', $text); // Знаки процента (%) и промилле (‰) прикреплять к числам, к которым они относятся
	$text = preg_replace( '/(\d) (?=\d)/','\\1'.NOBRSPACE, $text ); // Не разрывать 25 000

// РАЗНОЕ
	$text = preg_replace( '/(ООО|ОАО|ЗАО|ЧП) ?(?="|«)/','\\1'.NOBRSPACE, $text); // Делает неразрывными названия организаций и абревиатуру формы собственности
	$text = preg_replace( '/([^а-яА-ЯёЁa-zA-Z][а-яА-ЯёЁa-zA-Z]{1,8})-(?=[а-яА-ЯёЁa-zA-Z]{1,8}[^а-яА-ЯёЁa-zA-Z])/','\\1'.NOBRHYPHEN, $text); // Делает неразрывными двойные слова (светло-красный, фамилии Иванов-Васильев)
	$text = strtr( $text, array_flip( $htmlents ) ); // Делаем обратные замены на html-entity
	$text = str_replace( '"','&quot;', $text ); // Заменяем " на &quot;
	$text = str_replace( "'",'&#39;', $text ); // Заменяем ' на &#39;
	

// КОРОТКИЙ ПРОБЕЛ
	$text = str_replace( THINSP,NOBRSPACE, $text ); //break;
	/*case 2:
		$text = str_replace( THINSP,' ', $text ); break;
	case 3:
		$text = str_replace( THINSP,'&thinsp;', $text ); break;
	case 0:
	default:
		$text = str_replace( THINSP,'', $text );
	} */
	
// НЕРАЗРЫВНЫЙ ПРОБЕЛ
	/*switch( $botParams->get( 'typenbsp' ) )
	{
	case 1:
		$text = preg_replace( '/(^| |'.TAGEND.')([^ '.TAGBEGIN.TAGEND.NOBRSPACE.NOBRHYPHEN.DASH.NUMDASH.']+['.NOBRSPACE.NOBRHYPHEN.DASH.NUMDASH.'][^ '.TAGBEGIN.']*)(?=$| |'.TAGBEGIN.')/','\\1<nobr>\\2</nobr>', $text );
		$text = str_replace( NOBRSPACE,' ', $text );
		break;
	case 2:
		$text = preg_replace( '/(?<=^| |'.TAGEND.')([^ '.TAGBEGIN.TAGEND.NOBRSPACE.NOBRHYPHEN.DASH.NUMDASH.']+['.NOBRSPACE.NOBRHYPHEN.DASH.NUMDASH.'][^ '.TAGBEGIN.']*)(?=$| |'.TAGBEGIN.')/','<span style="white-space:nowrap">\\1</span>', $text );
		$text = str_replace( NOBRSPACE,' ', $text );
		break;
	case 0:
	default:*/
		$text = str_replace( NOBRSPACE,' ', $text );
	//}

// НЕРАЗРЫВНЫЕ ТИРЕ И ДЕФИС (ЕСЛИ NOBRSPACE=&nbsp;)
	/*$text = preg_replace( '/(?<=^| |'.TAGEND.')([^ '.TAGBEGIN.TAGEND.NOBRHYPHEN.DASH.NUMDASH.']+['.NOBRHYPHEN.DASH.NUMDASH.'][^ '.TAGBEGIN.']+)(?=$| |'.TAGBEGIN.')/','<nobr>\\1</nobr>', $text );*/
//	$text = preg_replace( '/(?<=^| |'.TAGEND.')([^ '.TAGBEGIN.TAGEND.NOBRHYPHEN.DASH.']+['.NOBRHYPHEN.DASH.'][^ '.TAGBEGIN.']+)(?=$| |'.TAGBEGIN.')/','<span style="white-space:nowrap">\\1</span>', $text );

// НЕРАЗРЫВНЫЙ ДЕФИС
	$text = str_replace( NOBRHYPHEN,'-', $text );

// ТИРЕ
	/*switch( $botParams->get( 'typedash' ) )
	{
	case 1:
		$text = str_replace( DASH,'–', $text ); break; // ndash
	case 2:
		$text = str_replace( DASH,'&minus;', $text ); break; // minus
	case 3:
		$text = str_replace( DASH,'-', $text ); break; // hyphen
	case 0:
	default:  */
		$text = str_replace( DASH,'—', $text ); // mdash
	//}
// ТИРЕ В ДИАПАЗОНЕ
	/*switch( $botParams->get( 'typenumdash' ) )
	{
	case 0:
		$text = str_replace( NUMDASH,'—', $text ); break; // mdash
	case 2:
		$text = str_replace( NUMDASH,'&minus;', $text ); break; // minus
	case 3:
		$text = str_replace( NUMDASH,'-', $text ); break; // hyphen
	case 1:
	default:*/
		$text = str_replace( NUMDASH,'–', $text ); // ndash
	//}
	
// ВОЗВРАЩАЕМ ТЕГИ НА МЕСТО
	while(preg_match('/'.TAGBEGIN.'\d+'.TAGEND.'/', $text))
		$text = preg_replace_callback('/'.TAGBEGIN.'(\d+)'.TAGEND.'/', 'getTag', $text);

// РАБОТА С ТЕГАМИ. ЧАСТЬ 2
	//Начальные и конечные пробелы и знаки препинания внутри текста ссылки выносить за пределы ссылки.
		$text = preg_replace( '/<a +href([^>]*)>([ .,!?:;…]+)/', '\\2<a href\\1>', $text );
		$text = preg_replace( '/(!\.\.|\?\.\.)<\/a>/', '</a>\\1', $text );
		$text = preg_replace( '/([ .,!?:;…]+)<\/a>/', '</a>\\1', $text );
	
	$text = str_replace( ' <su', '<su', $text ); // Не отрывать верхние и нижние индексы от предыдущих символов

	return trim($text);
}

?>