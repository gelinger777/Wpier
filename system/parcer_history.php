<?
/*DESCRIPTOR
1,1
История Изменения Цен Продуктов


group: inforcombest
version:0.1
author:Gevork Grigorian
*/

//HEAD//


$PROPERTIES=array(
"tbname"=>"parcer_history",
"pagetitle"=>"категории",
"egrid"=>1
);

$F_ARRAY=Array (
"id" => "hidden||",
"SupplierCode"=>"text| size=90 maxlength=255|GroupId",
"ParserId"=>"text| size=90 maxlength=255|SegmentId",
"ChangeText"=>"text| size=90 maxlength=255|gnt_id",
"Reason"=>"text| size=90 maxlength=255|ParcerId",
"ChangeValue"=>"text| size=90 maxlength=255|GroupName",

);

$f_array=Array(
"id" => "*hide*",

"SupplierCode"=>"SupplierCode",
"ParserId"=>"Парсер",
"ChangeText"=>"Тип Изменения",
"Reason"=>"Причина",
"ChangeValue"=>"Новая Цена",
"timestamp"=>"hidden||",
);
//ENDHEAD//


?>
