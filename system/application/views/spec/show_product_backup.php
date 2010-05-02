<?php $this->db->select('products.img_0, 
products.img_1,
products.img_2,
products.img_3,
products.img_4,    
catalog_cats.id as cat_id,
catalog_cats.name as cat_name,
products.pid as subcat_id,
 catalog_subcats.name as subcat_name,
 products.id as prod_id,products.pid,products.right_descr,products.img_bot,products.img_right, products.botom_descr,products.top_descr,products.name,proizvoditel,proizvoditeli.pid,strana.strana, strana.flag, products.pid,proizvoditeli.name as supplyer');
$this->db->limit(1);
$this->db->where('products.id',$my_item_id);

$this->db->join('catalog_subcats','catalog_subcats.id=products.pid','left');
$this->db->join('catalog_cats','catalog_subcats.pid=catalog_cats.id','left');
$this->db->join('proizvoditeli','proizvoditeli.id=products.proizvoditel','left');
$this->db->join('strana','strana.id=proizvoditeli.pid','left');
$products=$this->db->get($tbname);

$product=$products->row();
?>


<p style="margin: 10px 0px 17px 0px;"><a href="/content/show_page/cat.html">Каталог</a>&nbsp; > &nbsp;
<a href="/content/show_page/cat_cat/<?=$product->cat_id?>.html"><?=$product->cat_name;?></a>&nbsp; > &nbsp;<a href="/content/show_page/cat_subcat/<?=$product->subcat_id?>"><?=$product->subcat_name;?></a></p><!-- Нить присутсвия на странице-->
	

<div id="cat"> 




<h2><?=$product->name;?> </h2>
<table cellspacing="0" cellpadding="0">
  <tr>
     <td >


<style type="text/css">

#gallery_wrap {
	position: relative;
	width: 469px;
	height: 452px;
	padding: 84px 70px 177px 61px;
	background: url(/img/polaroid_back.png) top left no-repeat;
}
.panel {
	margin-bottom: 10px;
}
a:link,a:visited {
	color: #ddd !important;
	text-decoration: underline;
}
a:hover {
	text-decoration: none;
}
h3 {
	border-bottom-color: white;
}
#polaroid_overlay {
	background: url(/img/polaroid_front.png) top left no-repeat;
	position: absolute;
	top: 82px;
	left: 59px;
	width: 474px;
	height: 458px;
	z-index: 2000;
}
</style>







  <?
	  
	  $img_arr=$desc_arr=FALSE;
	  	  
if(@$product->img_0){
$img[]=$product->img_0;
}
if(@$product->img_1){
	  	 $img[]=$product->img_1;

}
if(@$product->img_2){
	  $img[]=$product->img_2;
}
if(@$product->img_3){
	  $img[]=$product->img_3;
}
if(@$product->img_4){
	  $img[]=$product->img_4;
	  }

//print_r($img);

if(@$img){
$lim=count($img);

$img=array_values($img);
//print_r($img);	  
	  $i=0;
	  

	  
$gal_text='<div id="photos" class="galleryview">

	';
	$gal_text2='<ul class="filmstrip">
	';
	

	foreach ($img as $key=>$pic){
	
	$pic=str_replace('../','',$pic);
	$gal_text .='
	
	<div class="panel">

     <img src="/'.str_replace('/preview','',$pic).'" /> 
    <div class="panel-overlay">
      <h2>'.$product->name.'</h2>
      
    </div>
  </div>
	
	'
	;
	$gal_text2 .=' <li><img src="/'.$pic.'" width=114 alt="Effet du soleil" title="Effet du soleil" /></li>
	';
	
	}
	
		

	  
$gal_text2.='</ul>';	  
	  
	  
}	 
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  


 if(count($img)>0 ){  

 echo $gal_text;
echo $gal_text2;
echo '</div>';
    

  }
  
  else{
 
echo '<img src="/img/no_photo.gif"  width="300" height="400" alt="" class="b1"  >';




}
  //echo '<img src="'.$img[0].'"  width="300" height="400" alt="" class="b1"  >';
  ?>   
    

</td>
     <td class="cont_vert">&nbsp;</td>
     <td id="conteiner" valign="top">
	 Производитель:	 <a href="/content/show_page/show_producer/<?=$product->proizvoditel;?>.html" class="pro"><?=$product->supplyer;?></a>
	 <br>
	 <br>

	 <img src="/<?=$product->flag;?>" width="24" height="16" alt="" border="0" align="absmiddle" class="bord1"><?=$product->strana;?><br>
<br>
<?=$product->right_descr;?>

<div align="right">
<?php if(@$product->img_right){
echo '<img src="/'.str_replace('../','',$product->img_right).'" width="261"  alt="" border="0" align="">';
}
?>
</div>
<!-- таблица в виде картинки (Опционально)-->

</td>
  </tr>
  <tr>
     <td colspan="3" class="cont"></td>
  </tr>

</table>
<br>

<?php 

echo $product->top_descr.'<br>';
if($product->img_bot){

echo'<img src="/'.str_replace('../',''.$product->img_bot).'" width="502" аlt="" border="0" align="">';
}


echo "<br>";


echo $product->botom_descr;
?>



</div>

<br>

<?php 


$this->db->limit(1);
$this->db->order_by('id');
$this->db->where('pid',$product->pid);
$this->db->where('id >',$product->prod_id);
$sledd=$this->db->get('products');
if($sledd->num_rows()>0){
$sled=$sledd->row();
}

$this->db->limit(1);
$this->db->order_by('id','desc');
$this->db->where('pid',$product->pid);
$this->db->where('id <',$product->prod_id);
$predd=$this->db->get('products');
if($predd->num_rows()>0){
$pred=$predd->row();
}
?>


<table cellspacing="0" cellpadding="0">
  <tr><!-- Прокрутка событий -->
     <td width="200"><img src="/img/left.gif" align="textbottom">
     <?php 
     if(@$pred){
     	echo anchor("/content/show_page/products/".$pred->id, 'предыдущая'); 
     }
     else{
     	
     	
     	echo 'предыдущая';
     }
     ?>
     
    </td>
     <td width="240" align="center"><img src="/img/up.gif" align="bottom"><a href="/content/show_page/cat_subcat/<?=$product->pid;?>.html">вернутся к списку</a></td>
     <td width="200" align="right">   <?php 
     if(@$sled){
     	echo anchor("/content/show_page/products/".$sled->id, 'следующая'); 
     }
     else{
     	
     	
     	echo 'следующая';
     }
     ?>
     <img src="/img/right.gif" align="absmiddle"></td>
  </tr>

</table>




 
     

























