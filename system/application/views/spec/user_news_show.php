<?php

if ($this->dx_auth->is_logged_in()!=false)  {
$this->db->limit(1);
$this->db->where('user',$this->session->userdata('username'));
$this->db->where('id',$my_item_id);

$mnews=$this->db->get($tbname);


if($mnews->num_rows()<1){
	echo ('новость с ID:'.$my_item_id.'  не существует');
}
else{
	$news=$mnews->row();

if($news->title){

	echo
"<h2>".$news->title."</h2>";
	"<title>".$news->title."</title>";



}
else{
	echo "<h2>"."Новость"."</h2>";
}

?></h2><!-- сюда переходит название новости, если названия нету то просто заголовок НОВОСТЬ ЭТОТЖЕ заголовок уходит в Тайтл-->

<div id="news">
<?php if($news->img AND is_file($_SERVER['DOCUMENT_ROOT'].str_replace('..','',$news->img))){
echo '<img src="/'.str_replace('/preview','',$news->img).'"  style="margin:10px;" alt="" border="0" align="left">';



}

 echo $news->ftext;
echo '</div>
<br>';
?>


<?php }}

else{

echo "Чтоб увидеть эту информацию, вам надо авторизоваться в системе.";
}

?>
