<?php

class Content extends Controller {

	function Content()
	{
		parent::Controller();
	}

	function index()
	{
		$this->load->view('welcome_message');

	}





function show_page($page_id=FALSE,$mod_item_id=FALSE,$mod_page_id=FALSE){
	$this->load->plugin('captcha');
	//$this->load->plugin('captcha');
$this->load->helper('form');
       
	$this->load->library('email');


// load captcha model with default database connection
$this->load->model('Capcha_model', '', 'default');
//loadim helper s funkcijami kotorie mogut ponadobitsja....

	$this->load->helper('wpier');
	//paru veshej bistrenko peredaem , konretno dlja proekta...

$cat_menu=$this->load->view('cat_menu','',TRUE);


if($page_id!=TRUE){
exit('Page Id is not Defined');

}

$this->db->select('catalogue_fin.id as id,title,pid,dir,attr,menu,tpl,spec,wintitle,windescript,
winkeywords,
tmpfile ');
$this->db->where(array('dir'=>$page_id));
$this->db->join('templates','catalogue_fin.tpl=templates.id','left');
$page=$this->db->get_where('catalogue_fin');
//echo $page->num_rows();
if($page->num_rows()==0){

exit("ERROR 404: Page Not Found");

}
     else{


//berem dannie iz bazi i pokazivaem fajl-shablon
    $page_data=$page->row_array();

    //print_r($page_data);

//berem iz bazi bloki

$this->db->where('catalogue_id',$page_data['id']);

   $this->db->where('cpid','0');
$bloks=$this->db->get('content_fin');

$i=0;



//esli bloki generalnie sushestvujurt
if( $bloks->num_rows()>0){



foreach ($bloks->result_array() as $blok){

	//print_r($blok);

	//esli blok prosto HTMLovij
if($blok['spec']!=TRUE){

$page_data["BLOCK".$i]=$blok['text'];


}
else
{
$specName=$blok['spec'];
$privat="./webadmin/extensions/$specName.php";

  $fp=fopen($privat,"r");
  $seval=fread($fp,filesize($privat));
  fclose($fp);
  $spos1=strpos($seval,"//HEAD//");
  $spos2=strpos($seval,"//ENDHEAD//");
  $seval=substr($seval,$spos1,$spos2-$spos1);
  //if(isset($MAINFRAME) && $MAINFRAME)  eval($seval);// or write2errorLog ($privat);
  //else
//echo $seval."<hr>";
  eval($seval) ;

                  if(!trim($PROPERTIES["tbname"])) {

                                   $page_data["BLOCK".$i]="";
                                         }
                                      else{


  	                                                 //print_r($PROPERTIES);

  	                                                     if(!$mod_item_id){
  	                                                      $mod_temp=$PROPERTIES['template_list'];
  	                                                          }
                                                                	else{

  	                                                                   $mod_temp=$PROPERTIES['template_row'];
  	                                                                }

  	                                        $PROPERTIES['page']=$mod_page_id;
  	                                     	$PROPERTIES['my_item_id']=$mod_item_id;

  	  	//print_r($PROPERTIES);
  	  	$current_page_id=$page_data['id'];
  	                                     	
  	                                     	$PROPERTIES['current_page_id']=$current_page_id;
$PROPERTIES['current_page_name']=$page_data['title'];
  	                                     	
                                            $page_data["BLOCK".$i]=$this->load->view('/spec/'.$mod_temp,$PROPERTIES,TRUE);

                                            }



                                         }






    //ja speshu poetomu delaju bistrenko tot zhe kod tolko dlja podblokov







    $podbloks=$this->db->get_where('content_fin',array('cpid'=>$blok['id']));


    if($podbloks->num_rows()>0){



     foreach($podbloks->result_array() as $podblok){

if($podblok['spec']!=TRUE){

$page_data["BLOCK".$i].=$podblok['text'];


}
else
{
$specName=$podblok['spec'];
$privat="./webadmin/extensions/$specName.php";

  $fp=fopen($privat,"r");
  $seval=fread($fp,filesize($privat));
  fclose($fp);
  $spos1=strpos($seval,"//HEAD//");
  $spos2=strpos($seval,"//ENDHEAD//");
  $seval=substr($seval,$spos1,$spos2-$spos1);
  //if(isset($MAINFRAME) && $MAINFRAME)  eval($seval);// or write2errorLog ($privat);
  //else
//echo $seval."<hr>";
  eval($seval) ;

                  if(!trim($PROPERTIES["tbname"])) {

                                   $page_data["BLOCK".$i].="";
                                         }
                                      else{


  	                                                 //print_r($PROPERTIES);

  	                                                     if(!$mod_item_id or $mod_item_id==0){
  	                                                      $mod_temp=$PROPERTIES['template_list'];
  	                                                          }
                                                                	else{

  	                                                                   $mod_temp=$PROPERTIES['template_row'];
  	                                                                }

  	                                        $PROPERTIES['page']=$mod_page_id;
  	                                     	$PROPERTIES['my_item_id']=$mod_item_id;
                                             
  	                                     	$current_page_id=$page_data['id'];
  	                                     	
  	                                     	$PROPERTIES['current_page_id']=$current_page_id;
  	 $PROPERTIES['current_page_name']=$page_data['title'];
  	 

                                            $page_data["BLOCK".$i].=$this->load->view('/spec/'.$mod_temp,$PROPERTIES,TRUE);

                                            }



                                         }





    }

    }


















                                             $i++;



//konec cikla
}


//koneca if(there is bloks)
}





                           $file=$page_data['tmpfile'];
//echo $file;
                             $temp=explode('/',$file);
                              $temp_file=$temp[2];
                              //echo $temp_file;

                               //print_r($page_data);
                               //// dirname(__FILE__);


//berem i peredaem sluzhebnie nadpisi
                           $labels=$this->db->get('labels');
                           if($labels->num_rows()>0){
                            foreach($labels->result() as $label){

                            $page_data[$label->keyname]=$label->valtext;
                               }
                              }
                             //$m_data['milenium_link']=$page_data['milenium_link'];
                             // print_r($page_data);
                            //peredaem levoe menu
                             $page_data['cat_menu']=$cat_menu;
                             $page_data["top_menu"]=$this->load->view('main_menu',$page_data,TRUE);
                             $this->load->_ci_view_path ='./templates_rus/';
                             
                            // print_r($page_data);
                             $this->load->view($temp_file,$page_data);





                                }



}



}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
