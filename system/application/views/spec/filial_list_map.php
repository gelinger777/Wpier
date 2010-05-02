
<h2>Наши координаты</h2>


<script>
var now=0;

function ShowRegion(id){
	$('#tr_'+now).animate({"height": "toggle"}, { duration: 1000 });

$('#tr_'+id).animate({"height": "toggle"}, { duration: 1000 });


now=id;

}

</script>

	 


<?
//$this->db->where('publ','1');
$this->db->limit(30);
//$this->db->order_by('dt');
$news=$this->db->get('filial');
$map="";
$trs="";

if($news->num_rows()>0){



  foreach($news->result() as $new){

$map.='<area shape="rect" coords="'.$new->koordinat.'" href="javascript:ShowRegion('.$new->id.')"  alt="'.$new->mesto.'" title="'.$new->mesto.'">
 ';
 $trs.='
  
  
  <tr id="tr_'.$new->id.'" style="display:none;">
				<td valign="top">

				'.$new->name.'<br> <!-- Название -->

  <p>'.$new->mesto.'<br>    '.$new->adres.'<br> <!-- Адрес -->

    тел.: '.$new->phone.'<!-- Телефоны -->

		<br>
<br>
<img src="'.$new->map.'" width="567" height="344" alt="" border="0" align="">
</p>
				</td>
			</tr>			

   
  
 

    ';

   }
   }
   ?>



	<img src="/img/cxema2.jpg" width="599" height="430" alt="" border="0" align="" USEMAP="#cxema2" style="border: 1px solid #780017;">

		<table border="0" cellspacing="1" cellpadding="3" width="100%" id="preds">
<?=$trs;?>

		</table>
		
		
		


<map name="cxema2">

<?=$map;?>
<area shape="rect" coords="77,310,149,324" href="javascript:ShowRegion(10)"  alt="Ставрополь" title="Ставрополь">
  <area shape="rect" coords="43,327,107,340" href="javascript:ShowRegion(11)"  alt="Кростнодар" title="Кростнодар">

</map>
 