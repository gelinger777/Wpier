
    aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
<?
$img='/img/flashjpg.jpg';
if($this->uri->segment(3)==TRUE){
$uri=$this->uri->segment(3);

$this->db->where('page',$uri)
$pic=$this->db->get('headfoto');

if($pic->num_rows()>0){  $imd=$pic->row();

  $img=str_replace('..','',$imd->img);
  $img=str_replace('/preview','',$img);


}


}
echo '<img src="'.$img.'" width="710" height="236" alt="" border="0" align="">';
?>

