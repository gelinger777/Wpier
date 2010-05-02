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
     <td id="foto" align="center">





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
$img[5]=False;
$img=array_values($img);
//print_r($img);
	  $i=0;

	  while($i<=$lim){

//echo $i."<br>";


if($img["$i"]){

//echo $img["$i"];








//$img_add=str_replace("./","/",$img[$i]);
$img_add=str_replace("../","/",$img[$i]);
$img_add=str_replace('/preview','',$img_add);
$img_arr[]=$img_add;
$desc_arr[]="-";


}

$i++;
}


 if($img_arr ){
$nkarner="'".join("','",$img_arr)."'";
$nkarner=str_replace(",''v","",$nkarner);?>

<script>
var myImages = new Array(<?=$nkarner;?>);
var myDescs = new Array('<?=join("','",$desc_arr);?>');
var imagesDir = 'http://<?=$_SERVER['HTTP_HOST'];?>';
var thumbsDir = 'http://<?=$_SERVER['HTTP_HOST'];?>';
var countThumbs = myImages.length;
var bigImg=document.getElementById('bigImg');

if(countThumbs>5){countThumbs=5;}

function PreloadImages(a){
    var d = document;
    d.imgs = new Array();
    k = 0;
    for (var i = 0; i < a.length; i++){
        d.imgs[k] = new Image;
        d.imgs[k].src = thumbsDir + a[i];
        d.imgs[(k+1)] = new Image;
        d.imgs[(k+1)].src = imagesDir + a[i];
        k += 2;
    }
}
                                                                           //РІСЃРµ С‡С‚Рѕ РЅРёР¶Рµ РЅРµ С‚СЂРѕРіР°Р№
function goodIndex(idx){
    if(idx >= 0){
       idx = idx % myImages.length;
    }else{
       idx = Math.abs(idx % myImages.length);
       if(idx == 0) idx = 13;
       idx = myImages.length - idx;
    }
    return idx;
}
var offsetThumb = 0;
function thumbScroll(c){
    offsetThumb += c;
    for(i=0; i<countThumbs; i++){
        ind = goodIndex(i+offsetThumb);
        img = document.getElementById("thumb_" + i + "_");
        aimg = document.getElementById("aimg_" + i + "_");
        img.src = thumbsDir + myImages[ind];
        //elem_desc.value = myDescs[ind];
        elem_desc2.innerHTML = myDescs[ind];
    }
}

var offsetImg = 0;
function imgScroll(c){
    offsetImg += c;


    var bigImg=document.getElementById('bigImg');
    bigImg.src= imagesDir + myImages[offsetImg];
    //elem_desc.value = myDescs[offsetImg];
   // elem_desc2.innerHTML = myDescs[offsetImg];
}

function switchImg(i){
	var bigImg=document.getElementById('bigImg');
    ind = goodIndex(offsetThumb + i);
    bigImg.src= imagesDir + myImages[ind];
    //elem_desc.value = myDescs[ind];
    //elem_desc2.innerHTML = myDescs[ind];
    offsetImg = ind;
    imgScroll(0);
}


function showTable(){
	PreloadImages(myImages);
    textHtml = " <table cellspacing=0 celpadding=0>";
    textHtml = textHtml + "<tr>";
    textHtml = textHtml + "<td id=foto align=center  valign=middle>";
    textHtml = textHtml + "<div><img name=\"bigImg\" id=\"bigImg\" src=\"" + imagesDir + myImages[0] + "\" class=\"f1\"></div>";

    textHtml = textHtml + "</td></tr><tr><td><table cellspacing=\"4\" cellpadding=\"0\" id=\"foto_small\" valign=\"bottom\"><tr align=\"center\">";
    for(i=0; i<countThumbs; i++){
        textHtml = textHtml + "<td width=\"100\">";
        textHtml = textHtml + "<a href=\"javascript:void(0);\" id=\"aimg_" + i + "_\" onclick=\"switchImg(" + i + ");\"><img id=\"thumb_" + i + "_\" width=60 height=60 src=\"" + thumbsDir + myImages[i] + "\" class=\"f1\" border=\"0\"></a>";
        textHtml = textHtml + "</td>";
    }
    textHtml = textHtml + "</tr></table></table>";

    textHtml = textHtml + "</center>";

    document.write(textHtml);
    imgScroll(0);
}


//РµСЃРё С€Рѕ РЅРµРїРѕРЅСЏС‚РЅРѕ РїРёС€Рё ;)   <textarea id=\"elem_desc\" rows=3 cols=100 readonly class=\"toform\"></textarea>
</script>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
        <script>
  			showTable();
  		</script>

		</td>
	</tr>

</table>



  <?}
  }

  else{

echo '<img src="/img/no_photo.gif"  width="300" height="400" alt="" class="b1"  >';




}
  //echo '<img src="'.$img[0].'"  width="300" height="400" alt="" class="b1"  >';
  ?>


























</div>

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
echo '<img src="/'.$product->img_right.'" width="261"  alt="" border="0" align="">';
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

echo'<img src="/'.$product->img_bot.'" width="502" аlt="" border="0" align="">';
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

