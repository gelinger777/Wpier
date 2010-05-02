<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Date: 30 December 2009
*
* VALIDATION ARRAY FOR THE modified (to run on CI 1.7.x) FreakAuth_light library
* @author      Chris Wong (needcod3.com) 
* @license     http://www.gnu.org/licenses/lgpl.html
* @version     1.1
*/
$config = array(
	'login' => array(
					array(
					  'field' => 'user_name',
					  'label' => 'lang:FAL_user_name_label',
					  'rules' => 'trim|required|xss_clean'
					),
					array(
					  'field' => 'password',
					  'label' => 'lang:FAL_user_password_label',
					  'rules' => 'trim|required|xss_clean'
					)
				),
	'register' => array(
					array(
					  'field' => 'user_name',
					  'label' => 'lang:FAL_user_name_label',
					  'rules' => 'trim|required|xss_clean|username_check|username_duplicate_check'
					),
					array(
					  'field' => 'password',
					  'label' => 'lang:FAL_user_password_label',
					  'rules' => 'trim|required|xss_clean|password_check'
					),
					array(
					  'field' => 'password_confirm',
					  'label' => 'lang:FAL_user_password_confirm_label',
					  'rules' => 'trim|required|xss_clean|matches[password]'
					),
					array(
					  'field' => 'email',
					  'label' => 'lang:FAL_user_email_label',
					  'rules' => 'trim|required|valid_email|xss_clean|email_duplicate_check'
					)
				),
	'country' => array(
					array(
					  'field' => 'country_id',
					  'label' => 'lang:FAL_user_country_label',
					  'rules' => 'trim|required|numeric|xss_clean|country_check'
					)
				),
	'captcha' => array(
					array(
						  'field' => 'recaptcha_response_field',
						  'label' => 'lang:recaptcha_field_name',
						  'rules' => 'trim|required|captcha_check'
						)
				),
	'forgot_password' => array(
							array(
								  'field' => 'email',
								  'label' => 'lang:FAL_user_email_label',
								  'rules' => 'trim|required|valid_email|xss_clean|email_exists_check'
								)
						),
	'change_password' => array(
					array(
					  'field' => 'user_name',
					  'label' => 'lang:FAL_user_name_label',
					  'rules' => 'trim|required|xss_clean'
					),
					array(
					  'field' => 'old_password',
					  'label' => 'lang:FAL_old_password_label',
					  'rules' => 'trim|required|xss_clean'
					),
					array(
					  'field' => 'password_confirm',
					  'label' => 'lang:FAL_retype_new_password_label',
					  'rules' => 'trim|required|xss_clean|matches[password]'
					),
					array(
					  'field' => 'password',
					  'label' => 'lang:FAL_new_password_label',
					  'rules' => 'trim|required|xss_clean|password_check'
					)
				),
	'admins/add' => array(
						array(
						  'field' => 'user_name',
						  'label' => 'lang:FAL_user_name_label',
						  'rules' => 'trim|required|xss_clean|username_check|username_backend_duplicate_check'
						),
						array(
						  'field' => 'password',
						  'label' => 'lang:FAL_user_password_label',
						  'rules' => 'trim|required|xss_clean|password_backend_check'
						),
						array(
						  'field' => 'password_confirm',
						  'label' => 'lang:FAL_user_password_confirm_label',
						  'rules' => 'trim|required|xss_clean|matches[password]'
						),
						array(
						  'field' => 'email',
						  'label' => 'lang:FAL_user_email_label',
						  'rules' => 'trim|required|valid_email|xss_clean|email_backend_duplicate_check'
						),
						array(
						  'field' => 'role',
						  'label' => 'Role',
						  'rules' => 'required'
						),
						array(
						  'field' => 'banned',
						  'label' => 'Banned',
						  'rules' => 'is_numeric'
						)
					)
);