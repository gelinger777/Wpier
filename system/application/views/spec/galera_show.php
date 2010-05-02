















<?
$this->db->where('id',$my_item_id);
//$this->db->limit(10,$page);
//$this->db->order_by('dt');
$news=$this->db->get('galera');
$new=$news->row();


echo '<center>';


echo '<img src="/'.str_replace('/preview','',$new->img).'"><br>';
echo $new->descr;
echo "</center>";
?>


  



