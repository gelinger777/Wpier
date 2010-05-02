<div id="sert">


<?php




			$serts=$this->db->get_where('serts');

		if($serts->num_rows()>0){

			echo '<div style="padding-top: 18px; line-height: 18px;">
				<div style="text-align: center">';

			foreach($serts->result() as $sert){


				//list($width, $height, $type, $attr) = getimagesize($_SERVER['DOCUMENT_ROOT'].str_replace('..','',str_replace('/preview','',$sert->img)));





				$atts = array(
              'rel'      => 'example4',
              'title'=>$sert->name
            );





				//echo ''.str_replace('.html','',anchor('/'.str_replace('/preview','',$sert->img),"<img src=\"".str_replace('..','',$sert->img)."\">", $atts));


                echo '<a class="highslide" onclick="return hs.expand(this)" href="'.base_url().''.str_replace('../','',str_replace('/preview','',$sert->img)).'"><img src="'.str_replace('..','',$sert->img).'"></a>';












			}



			echo '</div></div>';




			}




?>


<p class="hr4"></p>
</div>