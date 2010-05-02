<?
echo $page;
// Если ссылка вида /news/1.html -- покажем подробно новость
if($this->DATA["HTML_FILE"]) {
    $x=intval($this->DATA["HTML_FILE"]);
    
    // Проверим корректность ссылки на новость
    if($this->DATA["HTML_FILE"]!=$x.".html") $this->err404();
    
    // Читаем и выводим новость
    $r=current($this->db->query("SELECT * FROM news WHERE id=$x")->result());
    if($r) $this->load->view("spec/news_show",$r);
    else $this->err404(); 
    
} else {
// Покажем список новостей
    $this->load->view("spec/news_list",array("LIST"=>$this->db->query("SELECT * FROM news ORDER BY dt DESC, id DESC")->result()));
}