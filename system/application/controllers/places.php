<?php

class Places extends Controller{


              function Places()
    {
        parent::Controller();




        // $this->output->cache(1);
    }



               function index(){

echo "esh";


                 }






               function add_place($place_type=FALSE,$country_id=FALSE){


        $this->output->set_header("Content-Type: text/html; charset=UTF-8");
         $this->load->library('freakauth_light');
          $this->freakauth_light->check();
         $this->load->view('header');
       $this->output->enable_profiler(TRUE);

            $this->load->model('profile_model');
                     $this->load->model('menu_model');

               	
                        if(!$place_type ){
                        	die('Place Type or Country Not defined');
                        }
                     $this->db->where('country_id',$country_id);
                     $strana=$this->db->get('region');



                  $qanak=$strana->num_rows();

                  if($qanak>24){
                  	     $limit=intval($qanak/3);



                  $data["strani"]="";
                  $i=1;
                  $k=1;
                    foreach($strana->result() as $spisok){



                    if(($i%$limit)==0){

                    $k=$k+1;
                    }




                   if($i==1){


                      $data["strani"].='<div style="width: 203px; float: left;"><ul class="reglci">';


                     }
                     elseif((($i%$limit)==0) AND $i!=1 AND $k!=4){


                      $data["strani"].='</ul></div><div style="width: 203px; float: left;"><ul class="reglci">';






                     }


                        if($spisok->region==""){
                        	$name="NO";
                        }
                        else{
                        	$region=$spisok->region;
                        }





                      $data["strani"].="<li><span><a href=\"/places/add_step2/$place_type/$spisok->id\">$region</a></span></li>";








                     $i=$i+1;




                     }


                   $data['strani'].="</ul></div>";

                  }




                 else{



                     if($qanak>0){
                        $data["strani"]="";
                      $data["strani"].='</ul></div><div style="width: 203px; float: left;"><ul class="reglci">';




                     foreach($strana->result() as $spisok){







                                                                          if($spisok->region==""){
                        	$name="NO";
                        }
                        else{
                        	$name=$spisok->region;
                        }





                      $data["strani"].="<li><span><a href=\"/regme/join_step2/$spisok->id\">$name</a></span></li>";













                     }


                     $data['strani'].="</ul></div>";


                    }





                 }


               	


                 $this->load->view('add_place',$data);

                 $this->load->view('footer');

                 }



               function add_step2($place_type=FALSE,$region_id=FALSE){


                        if(!$place_type ){
                        	die('Place Type or Country Not defined');
                        }
                     $this->db->where('region_id',$region_id);
                     $strana=$this->db->get('city');



                  $qanak=$strana->num_rows();

                  if($qanak>24){
                  	     $limit=intval($qanak/3);



                  $data["strani"]="";
                  $i=1;
                  $k=1;
                    foreach($strana->result() as $spisok){



                    if(($i%$limit)==0){

                    $k=$k+1;
                    }




                   if($i==1){


                      $data["strani"].='<p style="width: 203px; float: left;"><ul class="reglci">';


                     }
                     elseif((($i%$limit)==0) AND $i!=1 AND $k!=4){


                      $data["strani"].='</ul></p><p style="width: 203px; float: left;"><ul class="reglci">';






                     }


                        if($spisok->city==""){
                        	$region="NO";
                        }
                        else{
                        	$region=$spisok->city;
                        }





                      $data["strani"].="<li><span><a href=\"/places/add_step2/$place_type/$spisok->id\">$region</a></span></li>";








                     $i=$i+1;




                     }


                   $data['strani'].="</ul></p>";

                  }




                 else{



                     if($qanak>0){
         
                     	
$i=1;$k=1; 
$data["strani"]="";


                     foreach($strana->result() as $spisok){




                                    if($i==1){


                      $data["strani"].='<center><div style="width: 203px; float: left;"><ul class="reglci">';


                     }
                     elseif((($i%3)==0) AND $i!=1 AND $k!=4){


                      $data["strani"].='</ul></div><div style="width: 203px; float: left;"><ul class="reglci">';






                     }


                                                                          if($spisok->city==""){
                        	$name="NO";
                        }
                        else{
                        	$name=$spisok->city;
                        }





                      $data["strani"].="<li><span><a href=\"/regme/join_step2/$spisok->id\">$name</a></span></li>";





                               $i++;







                     }


                     $data['strani'].="</ul></div>";


                    }





                 }


               	


                 $this->load->view('add_place',$data);

                 $this->load->view('footer');

                 }
                 



            function do_edit_photo($photo_id,$way='fotos'){







            $comment=$this->input->post('comment');


                   $up=array('comment'=>$comment);


                   $this->db->where('photo_id',$photo_id);

                  $uu= $this->db->update('photos',$up);

                  if($uu==TRUE){



                  redirect('/account/'.$way,'refresh');


                  }
                  else{

                  echo "Error";                  }


            }


           

           function do_action(){ $this->output->enable_profiler(TRUE);
        $data['user_id']=$user_id=$this->db_session->userdata('id');


              $main_id=$this->input->post('go_photo_id');

           $add=$this->input->post('place_add');
           $type_id=$this->input->post('type_id');
           
           
           
         $user_q=  $this->db->get_where('fa_user_profile',array('id'=>$user_id));
          $user_data=$user_q->row(); 
          $country_id=$user_data->country_id; 
             if($add==TRUE){




             redirect("/places/add_place/$type_id/$country_id",'refresh');




             }




            








            

            $edit=$this->input->post('button_edit');

            if($edit==TRUE){



                redirect("/photos/edit_photo/$main_id","refresh");



            }

            $delete=$this->input->post('button_delete');



            if($delete){

	                $main_id=$this->input->post('go_photo_id');

	                    $data['photo_id']=$main_id;

                  $ff= $this->db->get_where('photos',array('photo_id'=>$main_id));
                  $f_i=$ff->row();


                     $data["image_sm"]=$f_i->image_sm;
                     $data["comment"]=$f_i->comment;

                     $this->load->view('header');
                     $this->load->view('delete_photo',$data);



               }

             $straxovka=$this->input->post('button_insure');
             if($straxovka){

                 if(!$main_id){                     $data['system_title']='Insurance of Photos';
                     $data['system_message']="Oops.You didn't choose the Photo to insure its rate.";                 }

                 else{                   $this->load->helper('form');
                    $data['system_title']='Insurance of Photos';
                     $data['system_message']="Are you shure you whant to make insurance for the foto you have choosen???.
                     You can only make insurance only for one photo. If there is already a foto with insurance, the status of inurance will
                     be transferred to this one.

                     ";
                     $data['system_message'].=form_open("photos/do_insure/$main_id");
                     $data['system_message'].=form_submit('ok','Yes I want');
                     $data['system_message'].=form_close();
                     $data['system_message'].=form_open();
                     $data['system_message'].='<INPUT type=button value="No Thanks" onClick="history.back();">';
                     $data['system_message'].=form_close();




                 }



               $this->load->view('system_messages',$data);             }

           }





   function add_place_json(){



$this->output->set_header("Content-Type: text/html; charset=UTF-8");
         $this->load->library('freakauth_light');
          $this->freakauth_light->check();
         //$this->load->view('header');
       //$this->output->enable_profiler(TRUE);



         //$this->output->cache(1);











           $this->load->view('add_place_json');







   }





function get_regions($country_id=FALSE){
	 header('Content-type: text/html; charset=utf-8');
	if($country_id){
	
	$this->db->where('country_id',$country_id);
$regionik=	$this->db->get('region');
	
	if ($regionik->num_rows()>0) {
     $regions[0]=array('id'=>'0','title'=>'Please Choose Region');
    foreach ($regionik->result() as $r) {
        $regions[] = array('id'=>$r->id, 'title'=>trim($r->region));
        //$i++;
    }
    
    $result = array('type'=>'success', 'regions'=>$regions);
}
else {
    $result = array('type'=>'error');
}
	}
	
	else{
	$result = array('type'=>'error');	
		
	}
/*
 * Упаковываем данные с помощью JSON
 */
print json_encode($result);
	
}





function get_city($region_id=FALSE){
	 header('Content-type: text/html; charset=utf-8');
	if($region_id){
	
	$this->db->where('region_id',$region_id);
$regionik=	$this->db->get('city');
	
	if ($regionik->num_rows()>0) {
    $regions[0]=array('id'=>'0','title'=>'Please Choose City');
    foreach ($regionik->result() as $r) {
        $regions[] = array('id'=>$r->id, 'title'=>trim($r->city));
        //$i++;
    }
    
    $result = array('type'=>'success', 'regions'=>$regions);
}
else {
    $result = array('type'=>'error');
}
	}
	
	else{
	$result = array('type'=>'error');	
		
	}
/*
 * Упаковываем данные с помощью JSON
 */
print json_encode($result);
	
}

function get_place($city_id=FALSE,$type_id=FALSE){
	 header('Content-type: text/html; charset=utf-8');
	if($city_id){
	
	$this->db->where('city_id',$city_id);
	
	
	if($type_id){
		
			$this->db->where('type_id',$type_id);
	
		
		
	}
$regionik=	$this->db->get('places');

	
	if ($regionik->num_rows()>0) {
    $regions[0]=array('id'=>'0','title'=>'Please Choose Place');
    foreach ($regionik->result() as $r) {
        $regions[] = array('id'=>$r->place_id, 'title'=>trim($r->place_name));
        //$i++;
    }
    $regions[]=array('id'=>'other','title'=>'+ click here to add place ');
    $result = array('type'=>'success', 'regions'=>$regions);
}
else {
    $regions[0]=array('id'=>'0','title'=>'----- ');
      $regions[]=array('id'=>'other','title'=>'+ click here to add place ');
    $result = array('type'=>'success', 'regions'=>$regions);
}
	}
	
	else{
	$result = array('type'=>'error');	
		
	}
/*
 * Упаковываем данные с помощью JSON
 */
print json_encode($result);
	
}
function select_ajax($region_id=FALSE){
	 header('Content-type: text/html; charset=utf-8');
	
	 
	 
	 $act=$this->input->post("act");
	 
	 switch($act){
	 	case 'a_get_city_info':
                            echo "i equals city";
                            break;
       	case 'a_get_country_info':
                            echo "i equals country";
                            break;
                                                  
                            
                            
                            
          
	 }
	 
	
/*
 * Упаковываем данные с помощью JSON
 */
print json_encode($result);
	
}



















function join_place(){
	  $this->output->enable_profiler(TRUE);
	
	$ins['place_id']=$this->input->post('place_id');
	
	$ins['user_id']=$this->db_session->userdata('id');
	$ins['date_start']=$this->input->post('start_date');
	$ins['date_end']=$this->input->post('end_date');
	

	
	
$this->load->helper(array('form', 'url'));
		
		$this->load->library('form_validation');
			
		$this->form_validation->set_rules('place_id', 'Place Id', 'required|numeric');
		$this->form_validation->set_rules('start_date','Start Date', 'required');
		$this->form_validation->set_rules('end_date', 'End Date', 'required');
		//$this->form_validation->set_rules('email', 'Email', 'required');
			
		if ($this->form_validation->run() == FALSE)
		{
			echo validation_errors(); 
			
		}
		else
		{
			
			$check=$this->db->get_where('user_place',$ins);
			if($check->num_rows()<1){
			$this->db->insert('user_place',$ins);
			}
			redirect('/account/places/','refresh');
		}
	
	
}


function delete(){
	
	
	
	$user_id=$this->db_session->userdata('id');
	$places=$this->input->post('place_id',TRUE);
	
	//print_r($places);
	
	
	foreach($places as $key=>$place_id){
		
		$this->db->where('user_id',$user_id);
		$this->db->where('place_id',$place_id);
		$this->db->delete('user_place');
		
	}
	
	redirect('/account/places','refresh');
}

function add_new_place(){
	
	$ins['type_id']=$this->input->post('type_id',TRUE);
	$ins['city_id']=$this->input->post('city_id',TRUE);
	$ins['place_name']=$this->input->post('place_name',TRUE);
	$ins['street']=$this->input->post('street',TRUE);
	$token=$this->input->post('token',TRUE);
	
	
	//echo 'OK';
	$session=$this->db_session->userdata('session_id');
	
	//echo substr($session,0,17).':'.$token; 
	
	if(substr($session,0,17)=="$token"){
		
	$this->db->insert('places',$ins);	
		
	$get=$this->db->get_where('places',$ins);
	$place=$get->row();
	$place_id=$place->place_id;	
	
	$say= "$place_id";
		
	}
	else{
		
		$say= "ERROR";
	}
	
	
	echo $say;
	//echo "ERROR";
	
}


function show_place($place_id,$page=0){
	echo $this->load->view('header','',TRUE);

$data['place_id']=$place_id;
	
	$data['limit']=$limit=10;

$user_id=$this->db_session->userdata('id');
 	if(intval($user_id)<1){
 		die('BAD USER ID');
 	}
 	
 	
 	
 	
                        
                  $this->load->model('profile_model');


                        $data['user_id']=$user_id;


          // $data['left_info']=$this->profile_model->user_sample_data($user_id);

            // $data['user_image']=$this->profile_model->user_profile_photo($user_id);

             
                       //$data['friends']=array();


                       

                          $i=0;
                        

                          
                          //$data["friends2"]=TRUE;


//$ff2=$this->profile_model->get_friends2($user_id,$limit,$page);

//$ff2_all=$this->profile_model->get_friends2($user_id,0,FALSE);





                          
$this->load->library('pagination');
 $data['page_id']=$page;

$data['place_id']=$place_id;
        $this->load->view('show_place',$data);
           $this->load->view('footer');
	
	
	
	
	
	
	
	
	
	
	
	
	
}


  }



               ?>
