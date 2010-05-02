<?

if ($this->dx_auth->is_logged_in()!=false)  {
//$this->db->select("id,title,publ,img,announce,DATE_FORMAT(dt, '%e-%m-%Y') as date ");
//$this->db->where('publ','1');
//$this->db->limit(10,$page);
//$this->db->order_by('dt');
//$news=$this->db->get('news');

$mnews=$this->db->query("SELECT id,img,ftext,announce,title,DATE_FORMAT(dt, '%e-%m-%Y') as dt  from user_news WHERE publ=1 and `user`='".$this->session->userdata('username')."' ORDER BY dt ");

echo $this->session->userdata('username');
if($mnews->num_rows()<1){
	echo ('Новостей Нет');
}
else{
	foreach($mnews->result() as $news){


	echo
"<h2>".anchor('/content/show_page/cabinet/'.$news->id,$news->title)."</h2><br>";





?><!-- сюда переходит название новости, если названия нету то просто заголовок НОВОСТЬ ЭТОТЖЕ заголовок уходит в Тайтл-->

<div id="news">


</div>
<br><br><br>



<?php }
}


}
else{


	echo "Эта страница закрыта паролем. Пожалуйста залогинтесь чтоб увидеть ";}
?>
