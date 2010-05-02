

<?
$this->db->where('spec','1');
$this->db->limit(6);
//$this->db->order_by('dt');
$news=$this->db->get('products');

echo '
	 
 <h2>Спецпредложения</h2>
 <table cellpadding="0" cellspacing="0">
  <tr>
     <td colspan="2" class="td_col"></td>
  </tr>
  <tr>

     <td class="td_news_pos">
	 <ul id="catalog_1_lvl">';
if($news->num_rows()>0){


  
  foreach($news->result() as $new){
  
  	
  	if($new->novinka=='1'){
  		
  		
  		$nov='<div><img src="/img/new.gif"></div>';
  	}
  	else{
  		$nov="";
  	}  	
  	
     
  echo ' 
  

    <li>
	'.$nov.'
	<a href="/content/show_page/products/'.$new->id.'.html">
	<img src="/'.$new->img_0.'" width="60" height="60" alt="" border="0" align=""><br>'.$new->name.'</a>

	</li>
  
  
    ';
  
   }
   }
   ?>
  
  </ul>
</td>
   <td class="TD_col2"></td>
  </tr>

</table>

