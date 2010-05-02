<?php

class Account extends Controller{


              function Account()
    {
        parent::Controller();

        $this->output->set_header("Content-Type: text/html; charset=UTF-8");
         $this->load->library('freakauth_light');
          $this->freakauth_light->check();
         $this->load->view('header');
      // $this->output->enable_profiler(TRUE);

            $this->load->model('profile_model');
                     $this->load->model('menu_model');
   $this->load->model('forum_model');
       // $this->output->cache(1);



   $this->load->library('pagination');



    }



               function index(){



                  $this->freakauth_light->check();







                    $user_id=$this->db_session->userdata('id');




                        $data['left_info']=$this->profile_model->user_sample_data($user_id);

                         $data['user_image']=$this->profile_model->user_profile_photo($user_id);

                         $data['user_id']=$user_id;



                              //vivodim druzej


                        $this->load->model('profile_model');
                         


                        
                       
                       $friends=  $this->profile_model->get_friends($user_id,4);


                       $data['friends']="";
                        $f_qanak=$data['f_qanak']=$this->profile_model->count_my_friends($user_id);

                        $i=0;
                         $nkerner=array();
                         $no_friend="";
                       if($f_qanak>0){
                        foreach ($friends->result() as $buddy){

$friend_id=$buddy->friend_id;

                           $data["friends"].="<td align=\"center\">".$this->profile_model->user_icon($friend_id)."</td>";

                          $nkerner["$i"]=$friend_id;



                          $i=$i+1;                        }

                       }


                       else{
                       $data['friends'].="You have no friends Yet";
                        $data['friends2']="You have no friends of friends.." ;
                       }



                       $data['friends2']="";;

                       if($i>0){
                           $k=1;
                         
                          $ff2=$this->profile_model->get_friends2($user_id,4);






                          foreach($ff2->result() as $fikus){



$f2_id=$fikus->user_id;


                           $data["friends2"].="<td align=\"center\">".$this->profile_model->user_icon($f2_id)."</td>";
                          }
                       }








                      $this->db->select('*');

                      $this->db->join('places','places.place_id=user_place.place_id','left');
                        $this->db->join('place_type','places.type_id=place_type.type_id','left');
                      $this->db->join('city','places.city_id=city.id','left');

                      $qq= $this->db->get_where('user_place',array('user_id'=>$user_id));


                       $data['all_places']=$qq->num_rows();
                      if($qq->num_rows()>0){

                        $data['mesta']="";
                       foreach($qq->result() as $mesta){



                         $kk= $this->db->get_where('user_place',array('place_id'=>$mesta->place_id));

                         $kolichestvo=$kk->num_rows();

                        $data['mesta'].="<tr><td>$mesta->type_name</td><td style=\"width: 70%;\">
                         <a href=\"/places/show_place/$mesta->place_id\">$mesta->place_name /$mesta->city</a></td><td align=\"center\">$kolichestvo</td></tr>";



                       }


                      }
                      else{                      $data["mesta"]="You don't have any added placed";
                      }


                     $data['add_more']="";
                     $mm= $this->db->get('place_type');


                     foreach($mm->result() as $add){


                     $data['add_more'].="<span class=\"feat2\"> | </span><a class=\"feat2\" href=\"/account/places/#tabs-add_place\">$add->type_name</a>";



                     }






                   $this->db->join('photos','photos.photo_id=photo_ratings.photo_id','left');
                   $rr=$this->db->get_where('photo_ratings',array('owner_id'=>$user_id ));

                   if($rr->num_rows()>0){
                       $data['all_ratings']=$rr->num_rows();


                      $rate=$rr->row();



                      $data['rating']="<tr><td align=\"center\">
    ".$this->profile_model->user_icon($rate->rater_id)."</td><td align=\"center\"><div class=\"mark".$rate->rating_value."\"></div>$rate->timestamp</td><td align=\"center\">
     <a href=\"/photos/show_photo/user/".$rate->photo_id." \" onclick=\" openWinCentered(this.href,'1228335994979',700,670,'location=no,status=no,toolbar=no,menubar=no,resizable=no,scrollbars=yes'); return false;\">
     <img class=\"thumbnail\" src=\"../images/uploads/".$rate->image_sm."\" style=\"border-width: 0px;\" alt=\"\"></a>
     <div style=\"color: rgb(119, 119, 119);\">".$rate->dobavleno."<br>
     </div><div style=\"margin: 0pt auto; width: 128px; overflow-y: auto; overflow-x: hidden;\" title=\"".$rate->comment."\">".$rate->comment."</div>
     <div title=\"Всего оценок: 2. Оценок «5+»: 0. Средняя: 5.00.\" class=\"markings\">
     <img class=\"icon\" alt=\"Всего оценок: 2. Оценок «5+»: 0. Средняя: 5.00.\" src=\"../images/labelSumm2.gif\"><span class=\"icon\">
      <a href=\"/phtos/show_photo/user/\">2</a>
      </span>&nbsp;&nbsp;<img class=\"icon\" alt=\"Всего оценок: 2. Оценок «5+»: 0. Средняя: 5.00.\" src=\"../images/label5plus2.gif\">
      <span class=\"icon\"><a href=\"http://odnoklassniki.ru/dk?st.cmd=userPhotoMarks&amp;st.photoId=168880504914&amp;tkn=3494\">0</a></span>
      &nbsp;&nbsp;<img class=\"icon\" alt=\"Всего оценок: 2. Оценок «5+»: 0. Средняя: 5.00.\" src=\"../images/labelAvg.gif\"><span class=\"icon\">
      <a href=\"http://odnoklassniki.ru/dk?st.cmd=userPhotoMarks&amp;st.photoId=168880504914&amp;tkn=2345\">5.00</a></span></div>
      <div><a class=\"norm2\" href=\"http://odnoklassniki.ru/dk;jsessionid=aN1g2Xqm80Y4?st.cmd=userPhotoComments&amp;st.photoId=168
      880504914&amp;tkn=3074\">Комментарии <b>(1)</b></a></div></td></tr>";



                   }
                   else{
                   $data['rating']="You don't have any ratings of your photos yet";
                    $data['all_ratings']="0";
                   }



                    //berem posetitelej

                        $this->db->limit(4);


                    $gg=$this->db->get_where('guests',array('komu'=>$user_id));
                               $g_qanak=$gg->num_rows();

                   if($g_qanak>0){



                   $data['gosti']="";
                    foreach($gg->result() as $spisok){

                        $data['gosti'].='<td align="center">'.$this->profile_model->user_icon($spisok->kto).'</td>';




                    }
                   }

                   else{
                   $data['gosti']="You have no guests .";
                   }



$data['friend_requests']=$this->profile_model->count_friend_requests($user_id);


                  $this->load->view('my_profile',$data);
                  $this->load->view('footer');

                 }










         function invite_friend() {

                    $data['user_id'] =   $user_id=$this->db_session->userdata('id');



         $this->load->view('invite_friend',$data);
          $this->load->view('footer');         }

          function groups($page=0) {

          $this->load->model('groups_model');


           $group_limit=30;

                    $data['user_id'] =   $user_id=$this->db_session->userdata('id');



           $data['left_info']=$this->profile_model->user_sample_data($user_id);

             $data['user_image']=$this->profile_model->user_profile_photo($user_id);
             $data['my_groups']=$this->groups_model->user_groups($data['user_id'],$page,$group_limit);

            //$this->db->join('groups','groups.id=groups_users.group_id','left');
             //$data['gg']=$this->db->get('groups_users');
         $this->load->view('my_groups',$data);
          $this->load->view('footer');
         }

           function places() {


                    $data['user_id'] =   $user_id=$this->db_session->userdata('id');


$data['token']=$this->db_session->userdata('session_id');
           $data['left_info']=$this->profile_model->user_sample_data($user_id);

             $data['user_image']=$this->profile_model->user_profile_photo($user_id);


         $this->load->view('my_places',$data);
          $this->load->view('footer');
         }

  function forum($limit=10,$page=0) {


                    $data['user_id'] =   $user_id=$this->db_session->userdata('id');



           $data['left_info']=$this->profile_model->user_sample_data($user_id);

             $data['user_image']=$this->profile_model->user_profile_photo($user_id);

             
             $data['limit']=$limit;
             $data['page']=$page;
         $this->load->view('my_forum',$data);
          $this->load->view('footer');
         }

          function about(){


                    $data['user_id'] =   $user_id=$this->db_session->userdata('id');



           $data['left_info']=$this->profile_model->user_sample_data($user_id);

             $data['user_image']=$this->profile_model->user_profile_photo($user_id);


         $this->load->view('my_bio',$data);
          $this->load->view('footer');












          }


          function edit_block($block_id){
           $data['user_id']=$user_id=$this->db_session->userdata('id');

           $this->load->model('profile_model');
              $data['system_title']='&nbsp;<font color="green">'.$this->profile_model->about_block_name($block_id).'</font>';


            $data['system_message']="Here You can edit content of block ".br(2);
            $data['system_message'].='<center>'.form_open('/account/do_edit_block/');



            $input = array(
              'name'        => 'text',
              'id'          => 'username',
            'class'=>'jEditor',
              'value'       => $this->profile_model->about_block_content($user_id,$block_id,TRUE),
              'cols'   => '10',
              'rows'        => '10',
              'style'       => 'width:50%',
            );
            $data['system_message'].=form_textarea($input).br();




            $data['system_message'].='<input name="block_id" type="hidden" value="'.$block_id.'">';

            $data['system_message'] .='Show:';

            $data['system_message'] .=form_checkbox('show', '1',$this->profile_model->about_block_content_show($user_id,$block_id) );

            $data['system_message'].=form_submit('mysubmit','Edit');
            $this->load->view('system_messages',$data);

           $this->load->view('footer');







          }

         function do_edit_block(){           $user_id=$this->db_session->userdata('id');
           $block_id=$this->input->post('block_id');
           $text=$this->input->post('text');
           $show=$this->input->post('show');

         $check=$this->db->get_where('user_about', array('block_id'=>$block_id,'user_id'=>$user_id));


          if($check->num_rows()>0){
           $upik=array('text'=>$text,'show'=>$show);

           $this->db->where('block_id',$block_id);

           $this->db->update('user_about',$upik);
            }
           else{
              $this->db->insert('user_about',array('user_id'=>$user_id,'block_id'=>$block_id,'text'=>$text,'show'=>$show));
           }



          redirect('/account/about','refresh');         }




  function edit_info(){


             $user_id=$this->db_session->userdata("id");


                $data['user_id']=$user_id;




                $uu=$this->db->get_where("fa_user_profile",array('id'=>$user_id));

                $user=$uu->row();

                $data["name"]=$user->name;
                $data["family_name"]=$user->family_name;
                $data['city']=$user->city;


                $data['year'] ="";
                for($i=1900;$i<2008;$i++){






                  $data['year'].="<option value='$i'>$i</option>";

                }



                $cc=$this->db->get('country');

                $data['country']="<select name='country'>";

                foreach($cc->result() as $spisok){



                $data['country'].="<option value='$spisok->id'>$spisok->country</option>";
                }
                $data['country'].="</select>";

          $this->load->model('profile_model');

           $data['left_info']=$this->profile_model->user_sample_data($user_id);

             $data['user_image']=$this->profile_model->user_profile_photo($user_id);





        $this->load->view('edit_info',$data);
        $this->load->view('footer');















       }
         


 function ratings(){


             $user_id=$this->db_session->userdata("id");


                $data['user_id']=$user_id;






          $this->load->model('profile_model');

           $data['left_info']=$this->profile_model->user_sample_data($user_id);

             $data['user_image']=$this->profile_model->user_profile_photo($user_id);





        $this->load->view('my_ratings',$data);
        $this->load->view('footer');















       }
         

       


        function friends($page_id=0){

                        $user_id=$this->db_session->userdata('id');



                  $this->load->model('profile_model');


                        $data['user_id']=$user_id;


           $data['left_info']=$this->profile_model->user_sample_data($user_id);

             $data['user_image']=$this->profile_model->user_profile_photo($user_id);


$friends=$this->profile_model->get_friends($user_id,10,$page_id);
$all_friends=$this->profile_model->get_friends($user_id,0);

$data['total_friends']=$all_friends->num_rows();
                       $data['friends']="";
                        $f_qanak=$data['f_qanak']=$friends->num_rows();


                        $this->load->model('profile_model');
                       if($f_qanak>0 and $f_qanak<=4){

                       $i=0;


                        foreach ($friends->result() as $buddy){

                          $i=$i+1;
$friend_id=$buddy->friend_id;





                           $data["friends"].="<td align=\"center\">".$this->profile_model->user_icon($friend_id)."<input value=\"".$friend_id."\" name=\"friend[".$i."]\" type=\"checkbox\"></td>";




                        }

                       }
                          elseif($f_qanak>0 and $f_qanak>4){

                              $i=0;


                              foreach ($friends->result() as $buddy){

                                   $i=$i+1;

                                  $friend_id=$buddy->friend_id;
                               $modul=($i%5);
                              if($modul!=0){

                              $data["friends"].="<td align=\"center\">".$this->profile_model->user_icon($friend_id)."<input value=\"".$friend_id."\" name=\"friend[".$i."]\" type=\"checkbox\"></td>";

                             }
                             else{                                 $data["friends"].="</tr><tr><td align=\"center\">".$this->profile_model->user_icon($friend_id)."<input value=\"".$friend_id."\" name=\"friend[".$i."]\" type=\"checkbox\"></td>";



                             }


                            }



                           }

                       else{

                       $data['friends'].="You have no friends Yet";


                       }

                 

           $this->load->view('my_friends',$data);
           $this->load->view('footer');















        }

function invite_multiple(){  $data['user_id']=$user_id=$this->db_session->userdata('id');



if($this->input->post('posted')==TRUE){}
else{


$this->load->view('invite_multiple',$data);}




















}
        function guests(){


                        $user_id=$this->db_session->userdata('id');



                  $this->load->model('profile_model');


                        $data['user_id']=$user_id;


           $data['left_info']=$this->profile_model->user_sample_data($user_id);

             $data['user_image']=$this->profile_model->user_profile_photo($user_id);



                $this->db->where('komu',$user_id);


                        $this->db->limit(20);
                       $friends=  $this->db->get('guests');


                       $data['friends']="";
                        $f_qanak=$data['f_qanak']=$friends->num_rows();


                        $this->load->model('profile_model');
                       if($f_qanak>0 and $f_qanak<=4){

                       $i=0;


                        foreach ($friends->result() as $buddy){

                          $i=$i+1;


                           $visitor_id=$buddy->kto;







                           $data["friends"].="<td align=\"center\">".$this->profile_model->user_icon($visitor_id)."</td>";




                        }

                       }
                          elseif($f_qanak>0 and $f_qanak>4){

                              $i=0;


                              foreach ($friends->result() as $buddy){

                                   $i=$i+1;

                                      $visitor_id=$buddy->kto;



                               $modul=($i%5);
                              if($modul!=0){

                              $data["friends"].="<td align=\"center\">".$this->profile_model->user_icon($visitor_id)."<br>".$buddy->kogda."</td>";

                             }
                             else{
                                 $data["friends"].="</tr><tr><td align=\"center\">$i-$modul".$this->profile_model->user_icon($visitor_id)."</td>";




                             }


                            }



                           }

                       else{

                       $data['friends'].="You have no friends Yet";


                       }



           $this->load->view('my_guests',$data);
           $this->load->view('footer');
















        }








        function friends2($page=0){
$data['limit']=$limit=10;

$user_id=$this->db_session->userdata('id');
 	if(intval($user_id)<1){
 		die('BAD USER ID');
 	}
 	
 	
 	
 	
                        
                  $this->load->model('profile_model');


                        $data['user_id']=$user_id;


           $data['left_info']=$this->profile_model->user_sample_data($user_id);

             $data['user_image']=$this->profile_model->user_profile_photo($user_id);

             
                       //$data['friends']=array();


                        $this->load->model('profile_model');

                          $i=0;
                        

                          
                          //$data["friends2"]=TRUE;


$ff2=$this->profile_model->get_friends2($user_id,$limit,$page);

$ff2_all=$this->profile_model->get_friends2($user_id,0,FALSE);


                       
                        $data['f_qanak']=$ff2_all->num_rows();


if($data['f_qanak']>0){

                          foreach($ff2->result() as $fikus){






                           $data["friends2"][$i]=$fikus->user_id;
                           $i++;

                          }

} 



                          
//$this->load->helper('pagination');




           $this->load->view('my_friends2',$data);
           $this->load->view('footer');


        

        }



        function fotos(){
                    $user_id=$this->db_session->userdata("id");
                    $this->db->where('album_id','1');

                  $ff= $this->db->get_where('photos',array('user_id'=>$user_id));

                  $qanak=$ff->num_rows();


                $data['user_id']=$user_id;

          $this->load->model('profile_model');

           $data['left_info']=$this->profile_model->user_sample_data($user_id);

             $data['user_image']=$this->profile_model->user_profile_photo($user_id);
                   $data['photo_qanak']=$qanak;

          
                  if($qanak>0){







                        $data['photos']="";
                        $i=0;
                    foreach($ff->result() as $fotos){



                         $modul=$i%4;

                       

                         $straxovka='';

                         if($fotos->insured>0){
                         	$straxovka='<img style="border: 0px none ; margin-top: 2px;" alt="" src="/images/zastrahovano.gif">
                     <br><font color="red">INSURED PHOTO!</font>';
                         }
                         
                         
                        $data['photos'].='
                        <tr id='.$fotos->photo_id.'>
                    <td  ><center>
                   <a href="/photos/show_photo/users/'.$fotos->photo_id.'" rel="example3">
                              <img  src="/images/uploads/'.$fotos->image_sm.'" style="border-width: 0px;" alt=""></a>                
                              
                              '.$straxovka.'
                              <br><br> <button onclick="changeAvatar('.$fotos->photo_id.','.$fotos->user_id.')" class="ui-button ui-state-default ui-corner-all">Set as Avatar</button></center>   </td>
                             <td>'.$modul.$fotos->dobavleno.'</td>
                     <td>'.$fotos->comment.'</td>
                     <td>
                     <img class="icon" alt="Всего оценок: 4. Оценок «5+»: 0. Средняя: 5.00." src="/images/labelSumm2.gif"><span class="icon"><a href="userPhotoMarks&amp;st.photoId=168379917906&amp;tkn=9304">4</a></span>&nbsp;&nbsp;<img class="icon" alt="Всего оценок: 4. Оценок «5+»: 0. Средняя: 5.00." src="/images/label5plus2.gif"><span class="icon"><a href="userPhotoMarks&amp;st.photoId=168379917906&amp;tkn=479">0</a>
                     </span>&nbsp;&nbsp;
                     <img class="icon" alt="Всего оценок: 4. Оценок «5+»: 0. Средняя: 5.00." src="/images/labelAvg.gif"><span class="icon">
                     <a href="/photos/show_photo/users/'.$fotos->photo_id.'">5.00</a></span>
                     <a class="norm2" href="/photos/show/'.$fotos->photo_id.'">Комментарии (0)</a>
                     </td>
                     <td><a href="/photos/edit_photo/'.$fotos->photo_id.'" id="edit_photo" class="ui-button ui-state-default ui-corner-all">Edit</a><br>
    <button id="delete_photo" class="ui-button ui-state-default ui-corner-all" onclick="delPhoto('.$fotos->photo_id.')">Delete</button><br>
        <button id="insure" class="ui-button ui-state-default ui-corner-all" onclick="insurePhoto('.$fotos->photo_id.');">Insure</button></td>
                            </tr>
                                ';

                             
                           


                           $i=$i+1;



                        }

                        $data['photos'].="</tr>";
                  }
                  elseif($qanak==0){


                  $data['photos']="No photos Availble";

                  }


                    $this->load->view('my_photos',$data);
                       $this->load->view('footer');



        }









       function add_photo(){

        $data['user_id']=$user_id=$this->db_session->userdata('id');

              $user_id=$this->db_session->userdata('id');
             $ff= $this->db->get_where('photos',array('user_id'=>$user_id));

                  $qanak=$ff->num_rows();



            $data['photo_qanak']=$qanak;

                $data['error']="";

          $this->load->view('add_photo',$data);
           $this->load->view('footer');







       }





      function do_edit_info(){

        $user_id=$this->db_session->userdata('id');
      $name=$this->input->post('name');
      $family_name=$this->input->post('family_name');
      $gender=$this->input->post('gender');
      $city=$this->input->post('city');
      $b_day=$this->input->post('b_day');
      $b_month=$this->input->post('b_month');
      $b_year=$this->input->post('b_year');
       $country_id=$this->input->post('country');
      $city_name=$this->input->post('city_name');

          $birthday=$b_year.'-'.$b_month.'-'.$b_day;

      $up=array('birthday'=>$birthday,
         'name'=>$name,
         'family_name'=>$family_name,
         'country_id'=>$country_id,
         'gender'=>$gender,
         'city'=>$city
       );




       $this->db->where('id',$user_id);

       $this->db->update('fa_user_profile',$up);

       redirect('/account/','refresh');








      }




   function albums(){              $this->load->model('profile_model');


          $data['user_id']=$user_id=$this->db_session->userdata('id');


           $data['left_info']=$this->profile_model->user_sample_data($user_id,'albums');

             $data['user_image']=$this->profile_model->user_profile_photo($user_id);







   $this->load->view('my_albums',$data);
   }



function cv(){
	$this->load->model('profile_model');


          $data['user_id']=$user_id=$this->db_session->userdata('id');


           $data['left_info']=$this->profile_model->user_sample_data($user_id,'albums');

             $data['user_image']=$this->profile_model->user_profile_photo($user_id);



	
	$this->load->view('my_cv',$data);
	$this->load->view('footer');
	
	
	
	
}

function add_cv(){
	
	
		$this->load->model('profile_model');


          $data['user_id']=$user_id=$this->db_session->userdata('id');


           $data['left_info']=$this->profile_model->user_sample_data($user_id,'albums');

             $data['user_image']=$this->profile_model->user_profile_photo($user_id);


             
             
             
$config['upload_path'] = './uploads/cv/';
		$config['allowed_types'] = 'pdf|doc|docx';
		$config['max_size']	= '1000';
		//$config['max_width']  = '1024';
		//$config['max_height']  = '768';
		
		$this->load->library('upload', $config);
	
		if ( ! $this->upload->do_upload())
		{
			$data['error']='Error'.$this->upload->display_errors();

			
			

			
			
			$this->load->view('my_cv', $data);
		}	
		else
		{
			$upload_data = $this->upload->data();
		$up['cv_file']=$upload_data['file_name'];
		     
$pr=$this->db->get_where('fa_user_profile',array('id'=>$user_id));
		
	$profile=$pr->row();

	
	if(!empty($profile->cv_file)){
		
		
		unlink($_SERVER['DOCUMENT_ROOT'].'/uploads/cv/'.$profile->cv_file);
			
			
			
		
	}
		
			$this->db->where('id',$user_id);
			$this->db->update('fa_user_profile',$up);
             
             
    redirect('/account/cv/','refresh');          
			
			
			
		}        
             
             
             
             
             
             
             
             

	
	//$this->load->view('add_cv',$data);
	$this->load->view('footer');
	
	
	
	
	
}

function videos($page=0){
	
      $data['user_id']=$user_id=$this->db_session->userdata('id');
  	
  
  	
           $data['left_info']=$this->profile_model->user_sample_data($user_id,'albums');

             $data['user_image']=$this->profile_model->user_profile_photo($user_id);


      
  	
  	
  	
  	
  	
  	
	
	$data['page']=$page;
   //$this->output->enable_profiler(TRUE);

            $this->load->model('profile_model');
                     $this->load->model('menu_model');
	$this->load->model('forum_model');




		
$data['user_id']=$this->db_session->userdata('id');
$data['token']=$this->db_session->userdata('session_id');

	$this->load->view('my_videos',$data);
$this->load->view('footer');

}



  }



               ?>
