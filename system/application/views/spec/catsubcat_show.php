
<?php 
$this->db->limit(1);
$this->db->where('id',$my_item_id);

$catik=$this->db->get($tbname);


if($catik->num_rows()>0){
	
$cat=$catik->row();




}
$this->db->limit(1);
$this->db->where('id',$cat->pid);
$parentt=$this->db->get('catalog_cats');

$parent=$parentt->row();


?>

<p style="margin: 10px 0px 17px 0px;"><a href="/content/show_page/cat.html">Каталог</a>&nbsp; > &nbsp;<a href="/content/show_page/cat_cat/<?=$parent->id;?>"><?=$parent->name;?></a>&nbsp; > &nbsp;<?=$cat->name;?></p><!-- Нить присутсвия на странице-->
	

<div id="cat"> 

<h2><?=$cat->name;?></h2>


	<table cellspacing="0" cellpadding="0">
  <tr>

<?php 

$limit=12;
if(@$page!=TRUE){
	
	$page=0;
}
$this->db->select('products.img_0 as img, products.proizvoditel,products.id as prod_id,products.name,proizvoditeli.pid,strana.strana, strana.flag, products.pid,proizvoditeli.name as supplyer');
$this->db->limit($limit,$page);
$this->db->where('products.pid',$my_item_id);
$this->db->join('proizvoditeli','proizvoditeli.id=products.proizvoditel','left');
$this->db->join('strana','strana.id=proizvoditeli.pid','left');
$products=$this->db->get('products');


//echo $limit.':'.$page;

$jjj=1;

foreach($products->result() as $product){
	
	?>
	
	
	
	

     <td id="conteiner" <?if(($jjj%2)!=0){
     echo 'valign="top"';
     }?>>
	<table cellspacing="0" cellpadding="5">
  <tr valign="top">
     <td>
     
     <?php  if($product->img){?>
     <a href="/content/show_page/products/<?=$product->prod_id;?>.html" class="a1">
    <?php 
     echo '
   
     
     <img src="/'.$product->img.'" width="114"  alt="" border="0" align=""></a>
     
     ';
    }
     ?>
     
     
     </td><!-- фото -->
     <td>
	 <a href="/content/show_page/products/<?=$product->prod_id;?>.html" class="a1"><?=$product->name;?></a>

	 <br>
	 <br>
	 Производитель:	 <a href="/content/show_page/show_producer/<?=$product->proizvoditel?>.html" class="pro"><?=$product->supplyer;?></a>
	 <br>
	 <br>
	 
	 
	 <?php 
	 if($product->flag!=""){
	 echo '<img src="/'.$product->flag.'" width="24" height="16" alt="" border="0" align="absmiddle" class="bord1">
	 ';
	 
	 }
	 
	 
	echo $product->strana;
	
	?>
	
	
	
	 </td><!-- ТТХ -->
 
 
 
  </tr>

</table>



<?php 

if($jjj%2!=0){
echo '</td>
     <td class="cont_vert">&nbsp;</td>';
	
	
}
else{
	
echo '</tr>
  <tr>
     <td colspan="3" class="cont"></td>
  </tr>
    <tr>'	;
	
}	
	
	
$jjj++;	
	
}

?>

 
  </tr>
</table>

</div>


