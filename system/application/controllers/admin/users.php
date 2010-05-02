<?php
/**
* Removed / Modified deprecated CI methods to work with CI 1.7.x
* Date: 30 December 2009
* Author: Chris Wong (needcod3.com) 
* This modifed version is based on work(s) done by authors below and it 
* still retains its original distributed License (LGPL).
*
*======================================================
 * Auth Controller Class
 *
 * Security controller that provides functionality to handle logins, logout and registration
 * requests.  It also can verify the logged in status of a user and his permissions.
 *
 * The class requires the use of the DB_Session, FAL_validation and FreakAuth libraries.
 *
 * @package     FreakAuth
 * @subpackage  Controllers
 * @category    Authentication
 * @author      Daniel Vecchiato (danfreak) & Christophe Gragnic (grahack)
 * @copyright   Copyright (c) 2007, 4webby.com
 * @license		http://www.gnu.org/licenses/lgpl.html
 * @link 		http://4webby.com/freakauth
 * @version 	1.1
 *
 */

class Users extends Controller
{	
	/**
	 * Initialises the controller
	 *
	 * @return Admin
	 */
    function Users()
    {
        parent::Controller();
        
        ////////////////////////////
		//CHECKING FOR PERMISSIONS
		///////////////////////////
		//-------------------------------------------------
        //only 'admin' and 'superadmin' can manage users
        
        $this->freakauth_light->check('admin');
        
        //-------------------------------------------------
        //END CHECKING FOR PERMISSION
        
        $this->_container = $this->config->item('FAL_template_dir').'template_admin/container';
        
        //loads necessary libraries and models
        $this->lang->load('freakauth');
        $this->load->model('FreakAuth_light/usermodel', 'usermodel');
        if ($this->config->item('FAL_use_country'))
            $this->load->model('country', 'country_model');
        //lets load the form validation class if it hasn't been already loaded
		if (!class_exists('Form_validation'))
		{
		    $this->load->library('form_validation');
		}
		$this->form_validation->set_error_delimiters($this->config->item('FAL_error_delimiter_open'), $this->config->item('FAL_error_delimiter_close'));

    }
	
    	// --------------------------------------------------------------------
	
    /**
     * Displays the users list.
     *
     */
    function index()
    {
		//let's paginate results
		$this->load->library('pagination');
		
		$config['base_url'] = base_url().$this->config->item('index_page').'/'.'admin/users';
		$config['uri_segment'] = 3;
		$config['per_page'] = $this->config->item('FAL_admin_console_records_per_page');
		$config['full_tag_open'] = '<p>';
		$config['full_tag_close'] = '</p>';
		$config['cur_tag_open'] = '<b>';
		$config['cur_tag_close'] = '</b>';
		$config['next_link'] = '&gt';
		$config['prev_link'] = '&lt';
		
		$fields='id';
		$query = $this->usermodel->getUsers($fields);
		
		$config['total_rows'] = $query->num_rows();
		$this->pagination->initialize($config);
		$query->free_result();
			
		$page = $this->uri->segment(3, 0);
    	
    	$fields= 'id, user_name, role';
    	
    	$limit= array('start'=>$config['per_page'],
    				  'end'=>$page
    					);
		
		$query = $this->usermodel->getUsers($fields, $limit);

		
		if ($query->num_rows()>0)
		{ 
			 $i=1;
			 foreach ($query->result() as $row)
			{
                // when do we display links for editing or deleting a user ?
                // we display the edit and delete links if
                // the user in the table is neither a superadmin nor an admin
                $data['user'][$i]['show_edit_link'] =
                    ($row->role != 'admin' AND $row->role != 'superadmin');
                $data['user'][$i]['show_delete_link'] =
                    ($row->role != 'admin' AND $row->role != 'superadmin');
                
                // then we just fill the infos
				$data['user'][$i]['id']= $row->id;
				$data['user'][$i]['user_name']= $row->user_name;
				$data['user'][$i]['role']= $row->role;
				$i++;
			}
			
			$query->free_result();
		}
		else 
		{
			// If we arrive here, it means that we have no users in the db
			// hence no ADMIN. But only admins are allowed to
			// use this controller.
			// The only way to arrive here is to log in as an admin,
			// then delete all users 'by hand'
			// (since FAL do not allow it), and try to display the users list.
			show_error('No user in the database. Please reinstall FreakAuth light.');
		}
			
		//let's display the page
		$data['heading'] = 'VIEW users';
		$data['action'] = 'Manage users';
		$data['pagination_links'] = $this->pagination->create_links();
		$data['controller'] = 'users';
		$data['page'] = $this->config->item('FAL_template_dir').'template_admin/users/list';
						
		$this->load->vars($data);
		
		$this->load->view($this->_container);
		//$this->output->enable_profiler(TRUE);
    }


    // --------------------------------------------------------------------
    
	/**
	 * View record details
	 *
	 * @param record id $id
	 */
    function show()
    {	    			
    	$id = $this->uri->segment(4);
    	$query = $this->usermodel->getUserById($id);
		
		if ($query->num_rows() == 1)
        {
            $row = $query->row();
            
            // initializing two flags, for the edit and delete links
            // we can edit the displayed user if
            //  - we are an admin
            //  - OR the displayed user is not a superadmin
            $data['can_edit_user'] =
                ($row->role != 'superadmin' AND $row->role != 'admin');
            // we cannot delete a superadmin or an admin
            $data['can_delete_user'] =
                ($row->role != 'superadmin' AND $row->role != 'admin');
                
			$data['user']['id']= $row->id;
			$data['user']['user_name']= $row->user_name;
			$data['user']['email']= $row->email;
			$data['user']['role']= $row->role;
			$data['user']['banned']= $row->banned;
			
			//$countries = null;            
		    if ($this->config->item('FAL_use_country') && strlen($row->country_id))
		    {
		    	$query = $this->country_model->getCountryById($row->country_id);
                if (isset($query))
                {
                    $row = $query->row();
                        
                    //SELECT name FROM country WHERE id= $data['user']['country_id']
                    $data['user']['country'] = $row->name;
                }
		    }
		    
		    if (isset($query)) $query->free_result();
		    
		    if ($this->config->item('FAL_create_user_profile')==TRUE)
		    {
		    	$data['user_profile']= $this->freakauth_light->_getUserProfile($id);
		    	$data['f_r'] = $this->freakauth_light->_buildUserProfileFieldsRules();
		    	$data['label'] = $data['f_r'][0]; 
		    }
		    
		    
		    
        }
        else 
        {
        	$data['error_message']='The record you are looking for does not exist';
        }
    	
	    	$data['heading'] = 'Manage users';
	    	$data['action'] = 'View user';
	    	$data['controller'] = 'users';
	    	$data['page'] = $this->config->item('FAL_template_dir').'template_admin/users/detail';

	        $this->load->vars($data);
	        
	    	$this->load->view($this->_container);
	    	
	    	// for debugging
	    	// $this->output->enable_profiler(TRUE);
	    	
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Handles the post from the add admin form.
     *
     */
    
    function add()
    {      
    	//set rules
    	$this->form_validation->set_group_rules('admins/add');
        if ($this->config->item('FAL_use_country'))
        {
            $this->form_validation->set_group_rules('country');
        }
        
        //getting user profile custom data
	    if ($this->config->item('FAL_create_user_profile')==TRUE)
		{	
		    $config = $this->freakauth_light->_buildUserProfileFieldsRules();
			$data['fields'] = $config[0];
		    
			$this->form_validation->set_group_rules($config[1]);
		}
    	
    	
    	//if validation unsuccesfull & data not ok
        if ($this->form_validation->run() == FALSE)
		{
			//$countries = null;            
	        if ($this->config->item('FAL_use_country'))
	        {
	    		//SELECT * FROM country
	            $data['countries'] = $this->country_model->getCountriesForSelect();
	        }

			$data['heading'] = 'Users management admins';
	    	$data['action'] = 'Add User';
	    	$data['controller'] = 'users';
	    	$data ['page'] = $this->config->item('FAL_template_dir').'template_admin/users/add';
            
            $data['role_options'] = $this->_getRoleOptions();
	    	
	        $this->load->vars($data);
	        
	    	$this->load->view($this->_container);
	    	//$this->output->enable_profiler(TRUE);
		}
		//if everything ok
		else
		{
			$values=$this->_get_form_values();
			
        	//insert data in DB
        	$this->usermodel->insertUser($values['user']);
        	
        	
        	//if we want the user profile as well
	        if($this->config->item('FAL_create_user_profile'))
	        {	
	              //let's get the last insert id
	              $values['user_profile']['id']= $this->db->insert_id();
	              $this->load->model('Userprofile');
	              $this->Userprofile->insertUserProfile($values['user_profile']);
	        }
			//set a flash message
			$msg = $this->db->affected_rows().$this->lang->line('FAL_user_added');
			flashMsg($msg);
			
			//redirect to list
			redirect('admin/users', 'location');
		}
        	
    }
    

    // --------------------------------------------------------------------
    
    /**
     * Manages the edit
     * 
     * @access public
     *
     */
    function edit($id = '')
    {
		//get id if the form failed the validation once
		if($id == ''){
			$id = $_POST['id'];
		}
        // security check:
        // admins or superadmins cannot be edited in the users controller
        $edited_role = getUserPropertyFromId($id, 'role');
        $allowed = ($edited_role != 'admin' AND $edited_role != 'superadmin');
        if (!$allowed) $this->freakauth_light->denyAccess(getUserProperty('role'));
        
        //set validation rules
    	$this->form_validation->set_group_rules('admins/add');
		//do we want to set the country?
        //(looks what we set in the freakauth_light.php config)
        if ($this->config->item('FAL_use_country'))
        {
            $this->form_validation->set_group_rules('country');
        }
        
        //getting user profile custom data
	    if ($this->config->item('FAL_create_user_profile')==TRUE)
		{	
		    $config = $this->freakauth_light->_buildUserProfileFieldsRules();
			$data['fields'] = $config[0];
		    
			$this->form_validation->set_group_rules($config[1]);
		}
        
        $data['role_options'] = $this->_getRoleOptions();
        
    	//this avoid 1 extra query if validation doesn't return true
        if ($id != '')
        {	
        	//gets values for the edit form
        	$query = $this->usermodel->getUserById($id);
        
		
	       	foreach ($query->result() as $row)
		        	{
		        		$data['user']['id']= $row->id;
		        		$data['user']['user_name']= $row->user_name;
		        		$data['user']['email']= $row->email;
		        		$data['user']['country_id']= $row->country_id;
		        		$data['user']['role']= $row->role;
		        		$data['user']['banned']= $row->banned;
		        	}
		        	
		    $query->free_result();
		    
		    

		    if ($this->config->item('FAL_create_user_profile')==TRUE)
			{
				$data['user_prof']= $this->freakauth_light->_getUserProfile($id);
		    	$data['f_r'] = $this->freakauth_light->_buildUserProfileFieldsRules();
		    	$data['fields'] = $data['f_r'][0]; 
			}
		    
	    }

	    //$countries = null;            
	    if ($this->config->item('FAL_use_country'))
	    {
	    	//SELECT * FROM country
	        $data['countries'] = $this->country_model->getCountriesForSelect();
	    }
	    	  	
		if ($this->form_validation->run() == FALSE)
        {
               	$data['heading'] = 'Users management';
	        	$data['action'] = 'Edit user';
	        	$data['controller'] = 'users';
	        	$data ['page'] = $this->config->item('FAL_template_dir').'template_admin/users/edit';
				
	        	
	        	$this->load->vars($data);

	        	$this->load->view($this->_container);
	        	
	        	//$this->output->enable_profiler(TRUE);

        }
    	
		//if everything ok
		else
		{			
			//get form values
			$values=$this->_get_form_values();
			
			$id = $values['user']['id'];
			
			//update data in DB
			$where=array('id' => $id);
        	$this->usermodel->updateUser($where, $values['user']);
        	//rows changed in user table
        	$user_tb_affected = $this->db->affected_rows();
        	
        	//if we want the user profile as well
	        if($this->config->item('FAL_create_user_profile'))
	        {	
	              //let's get the last insert id
	              $this->load->model('Userprofile');
	              $this->Userprofile->updateUserProfile($id, $values['user_profile']);
	              //rows changed in user_profile table
	              $userprofile_tb_affected = $this->db->affected_rows();
	        }
        	
        	//lets get the number of rows changed
        	$affected_rows = max($user_tb_affected, $userprofile_tb_affected);
			//set a flash message
			$msg = $affected_rows.$this->lang->line('FAL_user_edited');
        	flashMsg($msg);
			
			//redirect to list
			redirect('admin/users', 'location');
		}
        
    }
    
        // --------------------------------------------------------------------
    
    /**
     * Displays the registration form.
     * 
     * @access public
     *
     */
    function del($id)
    {
        // security check:
        // admins or superadmins cannot be deleted in the users controller
        $edited_role = getUserPropertyFromId($id, 'role');
        $allowed = ($edited_role != 'admin' AND $edited_role != 'superadmin');
        if (!$allowed) $this->freakauth_light->denyAccess(getUserProperty('role'));
        
        $this->usermodel->deleteUser($id);
    	
    	if ($this->config->item('FAL_create_user_profile')==TRUE)
		{
			$this->load->model('Userprofile');
			$this->Userprofile->deleteUserProfile($id);
		}
        //set a flash message
		$msg = $this->db->affected_rows().$this->lang->line('FAL_user_deleted');
        flashMsg($msg);
        
        redirect('admin/users', 'location');
	        
    }
    
    // -------------------------------------------------------------------- 
    
    /**
     * Checks if form $_POST data are set and valid
     * assigns the $_POST data to an array and returns it
     *
     * @return array of form values
     */
    function _get_form_values()
    {
        if (isset($_POST['id'])) 
        {
        	//for edit record
        	$values['user']['id']=$_POST['id']; 
        }

        $values['user']['user_name'] = $this->input->post('user_name', TRUE);
        $values['user']['password'] = $this->input->post('password');
        $values['user']['email'] = $this->input->post('email');
        $values['user']['country_id'] = $this->input->post('country_id');
		$values['user']['banned'] = $this->input->post('banned');
		$values['user']['role'] = $this->input->post('role');
		
		//let's get the custom user profile  values
		if ($this->config->item('FAL_create_user_profile')==TRUE)
		{	
		    $this->load->model('Userprofile', 'userprofile');
		    
		    //array of fields
  			$db_fields=$this->userprofile->getTableFields();

  			//number of DB fields -1
  			//I put a -1 because I must subtract the 'id' field
  			$num_db_fields=count($db_fields) - 1;
  		
  			//I use 'for' instead of 'foreach' because I have to escape the 'id' field that has key=0 in my array
	  		for ($i=1; $i<=$num_db_fields;  $i++)
			{
				$values['user_profile'][$db_fields[$i]]=$this->input->post($db_fields[$i]);
			}
		 }
		
        //let's treat our banned yes/no checkbox
        if (isset($_POST['banned']) AND $_POST['banned'] =='') 
        {
        	//let's assign value zero (not banned)
        	$values['user']['banned']=0; 
        }

        if (($values['user']['user_name'] != false) && ($values['user']['email'] != false))
        {
            //necessary if password is not reset in edit()
        	if ($values['user']['password'] !='')  
            {
	        	$password = $values['user']['password'];
	        	//encrypts the password (md5)
	        	$values['user']['password'] = $this->freakauth_light->_encode($password);
            }
            else 
            {
            	unset($values['user']['password']);
            }

        	return $values;
        }
        
        return false;
    }

    function _getRoleOptions()
    {
        $roles = array_keys($this->config->item('FAL_roles'));
        
        // if the user is a superadmin,
        // let him the ability to give superadmin or admin roles
        if ($this->freakauth_light->isSuperAdmin())
        {
            return $roles;
        }
        //otherwise only the frontend roles
        else
        {
            return array_slice($roles, 2);
        }
    }
}
?>