<?php
//Функция перевода цифр в сумму прописью. Подаете цифру (разделитель рублей и копеек - точка или запятая, максимальная сумма - миллиард рублей), на выходе у функции - сумма прописью.
//Юрий Денисенко, denik@aport.ru  http://poligraf.h1.ru

function Number($c)
{
$c=str_pad($c,3,"0",STR_PAD_LEFT);
//---------сотни
switch  ($c[0])
{
case 0:
$d[0]="";
break;
case 1:
$d[0]="сто";
break;
case 2:
$d[0]="двести";
break;
case 3:
$d[0]="триста";
break;
case 4:
$d[0]="четыреста";
break;
case 5:
$d[0]="пятьсот";
break;
case 6:
$d[0]="шестьсот";
break;
case 7:
$d[0]="семьсот";
break;
case 8:
$d[0]="восемьсот";
break;
case 9:
$d[0]="девятьсот";
break;
}
//--------------десятки
switch  ($c[1])
{
case 0:
$d[1]="";
break;
case 1:
{
$e=$c[1].$c[2];
switch ($e)
{
case 10:
$d[1]="десять";
break;
case 11:
$d[1]="одиннадцать";
break;
case 12:
$d[1]="двенадцать";
break;
case 13:
$d[1]="тринадцать";
break;
case 14:
$d[1]="четырнадцать";
break;
case 15:
$d[1]="пятнадцать";
break;
case 16:
$d[1]="шестнадцать";
break;
case 17:
$d[1]="семнадцать";
break;
case 18:
$d[1]="восемнадцать";
break;
case 19:
$d[1]="девятнадцать";
break;
};
}
break;
case 2:
$d[1]="двадцать";
break;
case 3:
$d[1]="тридцать";
break;
case 4:
$d[1]="сорок";
break;
case 5:
$d[1]="пятьдесят";
break;
case 6:
$d[1]="шестьдесят";
break;
case 7:
$d[1]="семьдесят";
break;
case 8:
$d[1]="восемьдесят";
break;
case 9:
$d[1]="девяносто";
break;
}
//--------------единицы
$d[2]="";
if ($c[1]!=1):
switch  ($c[2])
{
case 0:
$d[2]="";
break;
case 1:
$d[2]="один";
break;
case 2:
$d[2]="два";
break;
case 3:
$d[2]="три";
break;
case 4:
$d[2]="четыре";
break;
case 5:
$d[2]="пять";
break;
case 6:
$d[2]="шесть";
break;
case 7:
$d[2]="семь";
break;
case 8:
$d[2]="восемь";
break;
case 9:
$d[2]="девять";
break;
}
endif;

return $d[0].' '.$d[1].' '.$d[2];

}
//---------------------------------------
function SumProp($sum)
{

// Проверка ввода
            $sum=str_replace(' ','',$sum);
            $sum = trim($sum);
            if ((!(@eregi('^[0-9]*'.'[,\.]'.'[0-9]*$', $sum)||@eregi('^[0-9]+$', $sum)))||($sum=='.')||($sum==',')) :
                return "Это не деньги: $sum";
                endif;
// Меняем запятую, если она есть, на точку
   $sum=str_replace(',','.',$sum);
   if($sum>=1000000000):
   return "Максимальная сумма &#151 один миллиард рублей минус одна копейка";
   endif;
// Обработка копеек
     $rub=floor($sum);
     $kop=100*round($sum-$rub,2);
     $kop.=" коп.";
      if (strlen($kop)==6):
      $kop="0".$kop;
      endif;
// Выясняем написание слова 'рубль'
$one = substr($rub, -1);
$two = substr($rub, -2);
if ($two>9&&$two<21):
 $namerub="рублей";

elseif ($one==1):
  $namerub="рубль";

elseif ($one>1&&$one<5):
  $namerub=" рубля";

else:
  $namerub="рублей";

endif;
if($rub=="0"):
return "Ноль рублей $kop";
endif;
//----------Сотни
$sotni= substr($rub, -3);
$nums=Number($sotni);
if ($rub<1000):
return ucfirst(trim("$nums $namerub $kop"));
endif;
//----------Тысячи
if ($rub<1000000):
$ticha=substr(str_pad($rub,6,"0",STR_PAD_LEFT),0,3);
else:
$ticha=substr($rub,strlen($rub)-6,3);
endif;
$one = substr($ticha, -1);
$two = substr($ticha, -2);
if ($two>9&&$two<21):

 $name1000=" тысяч";
elseif ($one==1):

  $name1000=" тысяча";
elseif ($one>1&&$one<5):

  $name1000=" тысячи";
else:

  $name1000=" тысяч";
endif;
$numt=Number($ticha);
if ($one==1&&$two!=11):
$numt=str_replace('один','одна',$numt);
endif;
if ($one==2):
$numt=str_replace('два','две',$numt);
$numt=str_replace('двед','двад',$numt);
endif;
if  ($ticha!='000'):
$numt.=$name1000;
endif;
if ($rub<1000000):
return ucfirst(trim("$numt $nums $namerub $kop"));
endif;
//----------Миллионы
$million=substr(str_pad($rub,9,"0",STR_PAD_LEFT),0,3);
$one = substr($million, -1);
$two = substr($million, -2);
if ($two>9&&$two<21):

 $name1000000=" миллионов";
elseif ($one==1):

  $name1000000=" миллион";
elseif ($one>1&&$one<5):

  $name1000000=" миллиона";
else:

  $name1000000=" миллионов";
endif;
$numm=Number($million);
$numm.=$name1000000;

return ucfirst(trim("$numm $numt $nums $namerub $kop"));

}
?>