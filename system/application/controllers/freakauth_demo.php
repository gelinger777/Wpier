<?php
class Freakauth_demo extends Controller {
	
	function Freakauth_demo ()
	{
		parent::Controller();
		
		$this->_container = $this->config->item('FAL_template_dir').'template/container';
		
	}
	
	function index()
	{		
		$data['heading'] = 'FreakAuth_recoded';
        
        $data['message']='<h1>Welcome to FreakAuth_light&copy;_recoded</h1>';
		$data['message'] .= '<p>This work is based on the original FreakAuth which can be found here <a href="http://www.4webby.com/freakauth" target="_blank">4webby.com/freakauth</a></p>';
		$data['message'] .= '<p>We have modified some files to make this great piece of code work better with the current CodeIgniter 1.7.x. This version also includes some minor bug fixes found in the original work.</p>';
		$data['message'] .= '<p>The changes made are mainly removing deprecated CI functions and also improvements of certain features, i.e. incorporating a new captcha system from <a href="http://www.recaptcha.net" target="_blank">reCaptcha.net</a></p>';
		$data['message'] .='<p><b>ORIGINAL WORK: Characteristics</b> and <b>Documentation</b>: visit <a href="http://www.4webby.com/freakauth" target="_blank">4webby.com/freakauth'."\n";
		$data['message'].='</a></p>'."\n";
        $data['message'].="<p>To check that everything is ok for <b>FreakAuth_light&copy;</b> to work properly and to create the first system superadmin go to the ".anchor('installer').". </p>";
        $data['message'].="<p class=\"important\">IMPORTANT:<br /> after creating the superadmin #1 REMOVE THE ".anchor('installer', 'INSTALLER').": (system/application/controllers/installer.php)</p>";
		$data['message'].="<br /><span class=\"important\">For frontend login you must register as user ".anchor($this->config->item('FAL_register_uri'), 'here')."</span>";
        
        $this->load->vars($data);

		$this->load->view($this->_container);
		
		//$this->output->enable_profiler(TRUE);
	}
}
?>