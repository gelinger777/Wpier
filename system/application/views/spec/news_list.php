<?

//$this->db->select("id,title,publ,img,announce,DATE_FORMAT(dt, '%e-%m-%Y') as date ");
//$this->db->where('publ','1');
//$this->db->limit(10,$page);
//$this->db->order_by('dt');
//$news=$this->db->get('news');

$mnews=$this->db->query("SELECT id,img,ftext,announce,title,DATE_FORMAT(dt, '%e-%m-%Y') as dt  from news WHERE publ=1 ORDER BY dt LIMIT 1");


if($mnews->num_rows()<1){
	echo ('новость с ID:'.$my_item_id.'  не существует');
}
else{
	$news=$mnews->row();

if($news->title){

	echo
"<h2>".$news->title."</h2>";




}


?><!-- сюда переходит название новости, если названия нету то просто заголовок НОВОСТЬ ЭТОТЖЕ заголовок уходит в Тайтл-->


<?php if($news->img AND is_file($_SERVER['DOCUMENT_ROOT'].str_replace('..','',$news->img))){
echo '<img src="/'.str_replace('/preview','',$news->img).'"  alt="" border="0" align="left" style="margin:10px;">';



}

 echo $news->ftext;
echo '
<br>';
?>


<?php }?>
