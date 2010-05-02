<?
$this->db->select('proizvoditeli.name, strana.strana ,strana.flag ,proizvoditeli.descr');
$this->db->limit(1);
$this->db->join('strana','strana.id=proizvoditeli.pid','left');
$producerr=$this->db->get_where($tbname,array($tbname.'.id'=>$my_item_id));

$producer=$producerr->row();

$this->db->select('products.id as id, products.name as name,products.pid,products.pid as pid,catalog_subcats.name as cat_name ');
//$this->db->limit($limit,$page);
$this->db->order_by('products.pid');
$this->db->where('products.proizvoditel',$my_item_id);
$this->db->join('catalog_subcats','catalog_subcats.id=products.pid','left');
//$this->db->join('strana','strana.id=proizvoditeli.pid','left');
$products=$this->db->get('products');
?>
<p style="margin: 10px 0px 17px 0px;"><a href="/content/show_page/cat.html">Каталог</a>&nbsp;  > &nbsp;<?php echo $producer->name;?></p><!-- Нить присутсвия на странице-->
	
<div id="cat"> 

<h2>Производитель: <?php echo $producer->name;?></h2>

 <div class="about">
<div align="right"> 	 <img src="/<?=$producer->flag?>" width="24" height="16" alt="" border="0" align="absmiddle" class="bord1"><?=$producer->strana?></div><br>
	 <?=$producer->descr;?>
<br>
<br>

<?
$last_cat=0;
$cikl=0;

foreach($products->result() as $product){

	if($last_cat !=$product->pid){
	
		if($cikl!=0){
		echo '</ul>
<p class="hr4"></p><!-- разделитель--> ';
		}
		
		
echo '<p><span>'.$product->cat_name.'</span></p><!-- название группы слева каталгога 1 уровня-->
<ul>';
	}
echo '<li style="list-style-image: url(/img/cat23.gif)"><a href="/content/show_page/products/'.$product->id.'.html">'.$product->name.'</a></li>';



$cikl++;
$last_cat=$product->pid;
}
?>
	 </div>

</div>



