
						<h3> Nachrichten</h3>
						

<?
//echo $current_page_id;

//$this->db->select("id,announce,title ,");
//$this->db->where('publ',1);
//$this->db->limit(5);
//$this->db->order_by('dt');


//$news=$this->db->get('news');
$news=$this->db->query("SELECT id,announce,title,dt as datum ,DATE_FORMAT(dt, '%e-%m-%Y') as dt  from news WHERE publ=1 ORDER BY datum DESC LIMIT 5");
echo '';
if($news->num_rows()>0){



  foreach($news->result() as $new){


  echo '

 '.$new->dt.' <br>

<a href="/content/show_page/show_news/'.$new->id.'.html">'.$new->title.'!</a><br>
'.$new->announce.'
<br><br>

    ';

   }
   }
   ?>



