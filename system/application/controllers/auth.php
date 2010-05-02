<?php
class Auth extends Controller
{
	// Used for registering and changing password form validation
	var $min_username = 4;
	var $max_username = 20;
	var $min_password = 4;
	var $max_password = 20;

	function Auth()
	{
		parent::Controller();
		
		$this->load->library('Form_validation');
		$this->load->library('DX_Auth');			
		
		$this->load->helper('url');
		$this->load->helper('form');
		
		
		
		
		if($this->session->userdata('DX_logged_in')!=FALSE and $this->uri->segment(2)!='logout' ){
			
			redirect('/content/show_page/cabinet','refresh');
			
		}
	}
	
	function index()
	{
		$this->login();
	}
	
	/* Callback function */
	
	function username_check()
	
	
	{
		$username=$this->input->post('username');
		//$result = $this->dx_auth->is_username_available($username);
		
		
		$users=$this->db->get_where('my_users',array('username'=>$username));
		if ($users->num_rows()>0)
		{
			$result='false';
		}
		else{
			$result='true';
		}
				
		echo $result;
	}

	function email_check()
	{
		$email=$this->input->post('email');
		//$result = $this->dx_auth->is_username_available($username);
		
		
		$users=$this->db->get_where('my_users',array('email'=>$email));
		if ($users->num_rows()>0)
		{
			$result='false';
		}
		else{
			$result='true';
		}
				
		echo $result;
	}

	function captcha_check($code)
	{
		$result = TRUE;
		
		if ($this->dx_auth->is_captcha_expired())
		{
			// Will replace this error msg with $lang
			$this->form_validation->set_message('captcha_check', 'Your confirmation code has expired. Please try again.');			
			$result = FALSE;
		}
		elseif ( ! $this->dx_auth->is_captcha_match($code))
		{
			$this->form_validation->set_message('captcha_check', 'Your confirmation code does not match the one in the image. Try again.');			
			$result = FALSE;
		}

		return $result;
	}
	
	function recaptcha_check()
	{
		$result = $this->dx_auth->is_recaptcha_match();		
		if ( ! $result)
		{
			$this->form_validation->set_message('recaptcha_check', 'Your confirmation code does not match the one in the image. Try again.');
		}
		
		return $result;
	}
	
	/* End of Callback function */
	
	
	function login()
	{
		$this->load->library('session');
		if ( $this->session->userdata('DX_logged_in')!=TRUE)
		{
			$val = $this->form_validation;
			
			// Set form validation rules
			$val->set_rules('username', 'Username', 'trim|required|xss_clean');
			$val->set_rules('password', 'Password', 'trim|required|xss_clean');
		//	$val->set_rules('remember', 'Remember me', 'integer');

			
			
			$check['username']=$this->input->post('username');
			$check['password']=md5($this->input->post('password'));
			$check_q=$this->db->get_where('my_users',$check);
			
			
				
			if ($val->run() AND $check_q->num_rows()>0)
			{
				
				$row=$check_q->row();
				// Set last ip and last login
						$this->dx_auth->_set_last_ip_and_last_login($row->id);
						// Clear login attempts
						$this->dx_auth->_clear_login_attempts();
						
						// Trigger event
						//$this->dx_auth_event->user_logged_in($row->id);
						
						
				$this->session->set_userdata(array('DX_logged_in'=>'1','is_logged_in'=>'1','username'=>$check['username'],'user_id'=>$row->id));
				if($this->session->userdata('redirect_url')!=True){
				// Redirect to homepage
				redirect('/content/show_page/cabinet/', 'location');
				}
				else{
					
				$red=$this->session->userdata('redirect_url');
				$this->session->set_userdata('redirect_url',false);
        		
        		
				redirect($red, 'location');	
					
				}
			}
			else
			{
				// Check if the user is failed logged in because user is banned user or not
				// Default is we don't show captcha until max login attempts eceeded
					$data['show_captcha'] = FALSE;
				
				
					
					// Load login page view
				$data['message']=	$this->load->view($this->dx_auth->login_view, $data,true);
				$data['title']='';
				
				$this->load->view('show_messages',$data);
			
			}
		}
		else
		{
			$data['auth_message'] = 'You are already logged in.';
			$this->load->view($this->dx_auth->logged_in_view, $data);
		}
	}
	
	function logout()
	{
		$this->dx_auth->logout();
		redirect('','refresh');
		$data['auth_message'] = 'You have been logged out.';		
		$this->load->view($this->dx_auth->logout_view, $data);
	}
	
	function register()
	{		
		if ( ! $this->dx_auth->is_logged_in() AND $this->dx_auth->allow_registration)
		{	
			$val = $this->form_validation;
			
			// Set form validation rules			
			$val->set_rules('username', 'Username', 'trim|required|xss_clean|min_length['.$this->min_username.']|max_length['.$this->max_username.']');
			$val->set_rules('password', 'Password', 'trim|required|xss_clean|min_length['.$this->min_password.']|max_length['.$this->max_password.']|matches[confirm_password]');
			$val->set_rules('confirm_password', 'Confirm Password', 'trim|required|xss_clean');
			$val->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
			
			

			// Run form validation and register user if it's pass the validation
			if ($val->run() AND $this->dx_auth->register($val->set_value('username'), $val->set_value('password'), $val->set_value('email')))
			{	
				// Set success message accordingly

				$data['title']='';
					$data['message'] = 'Success. '.anchor(site_url($this->dx_auth->login_uri), 'Einloggen koenen Sie hier');
			
				
				// Load registration success page
				$this->load->view('show_messages', $data);
			}
			else
			{
				// Is registration using captcha
			

				// Load registration page
				$data['message']=$this->load->view($this->dx_auth->register_view,'',true);
				$data['title']='';
				
				$this->load->view('show_messages',$data);
			}
		}
		elseif ( ! $this->dx_auth->allow_registration)
		{
			
			$data['title']='';
			$data['message'] = 'Registration has been disabled.';
			$this->load->view('show_messages', $data);
		}
		else
		{
			$data['title']='';
			$data['message'] = 'You have to logout first, before registering.';
			$this->load->view('show_messages', $data);
		}
	}
	
	function activate()
	{
		// Get username and key
		$username = $this->uri->segment(3);
		$key = $this->uri->segment(4);

		// Activate user
		if ($this->dx_auth->activate($username, $key)) 
		{
			$data['auth_message'] = 'Your account have been successfully activated. '.anchor(site_url($this->dx_auth->login_uri), 'Login');
			$this->load->view($this->dx_auth->activate_success_view, $data);
		}
		else
		{
			$data['auth_message'] = 'The activation code you entered was incorrect. Please check your email again.';
			$this->load->view($this->dx_auth->activate_failed_view, $data);
		}
	}
	
	function forgot_password()
	{
		$val = $this->form_validation;
			
		// Set form validation rules
		$val->set_rules('login', 'Login oder E-mail', 'trim|required|xss_clean');
$user_id=$this->shop_model->reset_password($this->input->post('login'));
				    $data['title']="Kennwort Vergessen";
		// Validate rules and call forgot password function
		if ($val->run() and $user_id>0 )
		{
			$this->shop_model->send_pass_key($user_id);
			
		$data['auth_message'] = ' Soeben wurde Ihnen eine E-Mail mit weiteren Anweisungen zur Erstellung eines neuen Passworts zugeschickt..';
			$data['message']=$this->load->view($this->dx_auth->forgot_password_success_view, $data,TRUE);

			$this->load->view('show_messages',$data);
		
		}
		else
		{
			if($this->input->post('login')){
				
				$data['errors']="<font color='red'> Oops, fehlender Benutzername oder E-mail</font>";
			}
			
			$data['message']=	$this->load->view($this->dx_auth->forgot_password_view,$data,TRUE);
				
				
				$this->load->view('show_messages',$data);
		}
	}
	
	function reset_passwort($user_id,$reset_key)
	{
		// Get username and key
				$val = $this->form_validation;
		
		$uu=$this->db->get_where('my_users',array('id'=>$user_id));
		if($uu->num_rows()<1){
			
			$data['title']="Error";
			$data['message']='Fehlender Benutzer';
			exit($this->load->view('show_messages',$data,true));
		}
		
		$user=$uu->row();
		
		if($user->newpass_key!=$reset_key){
			
						$data['title']="Error";
			$data['message']='Link ist ungueltig. Probieren Sie eine neue zu anfragen '.anchor('/auth/forgot_password','hier').'.';
			exit($this->load->view('show_messages',$data,true));
			
		}
		
		
		if($this->input->post('doit') ){
			
			
			$val->set_rules('password', 'Passwort', 'trim|required|xss_clean');
			$val->set_rules('repassword', 'Re-Passwort', 'trim|required|xss_clean');
			
			
			if ($val->run() )
		{
			
		$up['password']=md5($this->input->post('password'));
        $up['newpass_key']="";
        $up['newpass']="";
        $up['newpass_time']="";
		
		
		$this->db->where('id',$user_id);
	$this->db->update('my_users',$up)	;
	
	redirect('/auth/login/','refresh');
	
		}
			
			
			
			
			
			
		
		}
		else{
			
			$data['title']='';
						
		//$data['auth_message'] = ' Soeben wurde Ihnen eine E-Mail mit weiteren Anweisungen zur Erstellung eines neuen Passworts zugeschickt..';
			$data['message']=$this->load->view('/auth/reset_form', $data,TRUE);

			$this->load->view('show_messages',$data);
			
			
		}
		
		
		
		
		
	}
	
	function change_password()
	{
		// Check if user logged in or not
		if ($this->dx_auth->is_logged_in())
		{			
			$val = $this->form_validation;
			
			// Set form validation
			$val->set_rules('old_password', 'Old Password', 'trim|required|xss_clean|min_length['.$this->min_password.']|max_length['.$this->max_password.']');
			$val->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length['.$this->min_password.']|max_length['.$this->max_password.']|matches[confirm_new_password]');
			$val->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean');
			
			// Validate rules and change password
			if ($val->run() AND $this->dx_auth->change_password($val->set_value('old_password'), $val->set_value('new_password')))
			{
				$data['auth_message'] = 'Your password has successfully been changed.';
				$this->load->view($this->dx_auth->change_password_success_view, $data);
			}
			else
			{
				$this->load->view($this->dx_auth->change_password_view);
			}
		}
		else
		{
			// Redirect to login page
			$this->dx_auth->deny_access('login');
		}
	}	
	
	function cancel_account()
	{
		// Check if user logged in or not
		if ($this->dx_auth->is_logged_in())
		{			
			$val = $this->form_validation;
			
			// Set form validation rules
			$val->set_rules('password', 'Password', "trim|required|xss_clean");
			
			// Validate rules and change password
			if ($val->run() AND $this->dx_auth->cancel_account($val->set_value('password')))
			{
				// Redirect to homepage
				redirect('', 'location');
			}
			else
			{
				$this->load->view($this->dx_auth->cancel_account_view);
			}
		}
		else
		{
			// Redirect to login page
			$this->dx_auth->deny_access('login');
		}
	}

	// Example how to get permissions you set permission in /backend/custom_permissions/
	function custom_permissions()
	{
		if ($this->dx_auth->is_logged_in())
		{
			echo 'My role: '.$this->dx_auth->get_role_name().'<br/>';
			echo 'My permission: <br/>';
			
			if ($this->dx_auth->get_permission_value('edit') != NULL AND $this->dx_auth->get_permission_value('edit'))
			{
				echo 'Edit is allowed';
			}
			else
			{
				echo 'Edit is not allowed';
			}
			
			echo '<br/>';
			
			if ($this->dx_auth->get_permission_value('delete') != NULL AND $this->dx_auth->get_permission_value('delete'))
			{
				echo 'Delete is allowed';
			}
			else
			{
				echo 'Delete is not allowed';
			}
		}
	}
}
?>