
 <h2>НОВОСТИ</h2>


<table border="0" collspan="0">	
<tr align="left"><td>
<?
//echo $current_page_id;

//$this->db->select("id,announce,title ,");
//$this->db->where('publ',1);
//$this->db->limit(5);
//$this->db->order_by('dt');


//$news=$this->db->get('news');
$news=$this->db->query("SELECT id,announce,title,DATE_FORMAT(dt, '%e-%m-%Y') as dt  from news WHERE publ=1 ORDER BY dt LIMIT 5");
echo '
	 
<ul id="cont">';
if($news->num_rows()>0){


  
  foreach($news->result() as $new){
  
     
  echo ' 
  
<p> '.$new->dt.' <br>

<a href="/content/show_page/show_news/'.$new->id.'.html">'.$new->title.'!</a><br>
'.$new->announce.'
</p>
  
    ';
  
   }
   }
   ?>
  
  </ul>
<td>
</tr>
<tr valign="top">
<td class="bott" align="right">
<p class="niz"><a href="news.html">
<img src="/img/all_news.gif" alt="Все новости"></a></p>

</td>

</table>
