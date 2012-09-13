<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth
* 
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
*          
* Added Awesomeness: Phil Sturgeon
* 
* Location: http://github.com/benedmunds/CodeIgniter-Ion-Auth
*          
* Created:  10.01.2009 
* 
* Description:  Modified auth system based on redux_auth with extensive customization.  This is basically what Redux Auth 2 should be.  Original redux license is below.
* Original Author name has been kept but that does not mean that the method has not been modified.
* 
* Modifications made for NADA (@author: Mehmood Asghar)
* - uses MD5 instead of SHA1/SALT to be compatible with nada 2 database
*
*/
 
class Ion_auth
{
	/**
	 * CodeIgniter global 
	 *
	 * @var string
	 **/
	protected $ci;

	/**
	 * account status ('not_activated', etc ...)
	 *
	 * @var string
	 **/
	protected $status;

	/**
	 * message (uses lang file)
	 *
	 * @var string
	 **/
	protected $messages;

	/**
	 * error message (uses lang file)
	 *
	 * @var string
	 **/
	protected $errors = array();

	/**
	 * error start delimiter
	 *
	 * @var string
	 **/
	protected $error_start_delimiter;

	/**
	 * error end delimiter
	 *
	 * @var string
	 **/
	protected $error_end_delimiter;

	/**
	 * extra where
	 *
	 * @var array
	 **/
	public $_extra_where = array();

	/**
	 * extra set
	 *
	 * @var array
	 **/
	public $_extra_set = array();

	/**
	 * __construct
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->config('ion_auth');
		$this->ci->load->library('email');
        $this->ci->load->library('session');
		$this->ci->load->library('language');
		//$this->ci->lang->load('ion_auth');
		$this->ci->load->model('ion_auth_model');
		$this->ci->load->helper('cookie');
		
		$this->message_start_delimiter = $this->ci->config->item('message_start_delimiter');
		$this->message_end_delimiter   = $this->ci->config->item('message_end_delimiter');
		$this->error_start_delimiter   = $this->ci->config->item('error_start_delimiter');
		$this->error_end_delimiter     = $this->ci->config->item('error_end_delimiter');
		
		//auto-login the user if they are remembered
		if (get_cookie('identity') && get_cookie('remember_code'))
		{
			$this->ci->ion_auth_model->login_remembered_user();
		}
	}
	
	/**
	 * Activate user.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function activate($id, $code=false)
	{
		if ($this->ci->ion_auth_model->activate($id, $code))
		{
			$this->set_message('activate_successful');
			return TRUE;
		}
		else 
		{
			$this->set_error('activate_unsuccessful');
			return FALSE;	
		}
	}
	
	/**
	 * Deactivate user.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function deactivate($id)
	{
		if ($this->ci->ion_auth_model->deactivate($id))
		{
			$this->set_message('deactivate_successful');
			return TRUE;
		}
		else 
		{
			$this->set_error('deactivate_unsuccessful');
			return FALSE;	
		}
	}
	
	/**
	 * Change password.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function change_password($identity, $old, $new)
	{
        if ($this->ci->ion_auth_model->change_password($identity, $old, $new))
        {
        	$this->set_message('password_change_successful');
        	return TRUE;
        }
        else
        {
        	$this->set_error('password_change_unsuccessful');
        	return FALSE;
        }
	}

	/**
	 * forgotten password feature
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function forgotten_password($email)
	{
		if ( $this->ci->ion_auth_model->forgotten_password($email) ) 
		{
			// Get user information
			$profile = $this->ci->ion_auth_model->profile($email);

			$data = array('identity'                => $profile->{$this->ci->config->item('identity')},
						  'forgotten_password_code' => $profile->forgotten_password_code
						 );

			$message = $this->ci->load->view($this->ci->config->item('email_templates').$this->ci->config->item('email_forgot_password'), $data, true);
			$this->ci->email->clear();
			$config['mailtype'] = "html";
			$this->ci->email->initialize($config);
			$this->ci->email->set_newline("\r\n");
			$this->ci->email->from($this->ci->config->item('website_webmaster_email'), $this->ci->config->item('website_webmaster_name'));
			$this->ci->email->to($profile->email);
			$this->ci->email->subject(t('forgot_password_verification'));
			$this->ci->email->message($message);
			
			if ($this->ci->email->send())
			{
				$this->set_error('forgot_password_successful');
				return TRUE;
			}
			else
			{
				$this->set_error('forgot_password_unsuccessful');
				return FALSE;
			}
		}
		else 
		{
			$this->set_error('forgot_password_unsuccessful');
			return FALSE;
		}
	}
	
	/**
	 * forgotten_password_complete
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function forgotten_password_complete($code)
	{
	    $identity     = $this->ci->config->item('identity');
	    $profile      = $this->ci->ion_auth_model->profile($code);
		$new_password = $this->ci->ion_auth_model->forgotten_password_complete($code);

		if ($new_password) 
		{
			$data = array(
				'identity'     	=> $profile->{$identity},
				'new_password' 	=> $new_password,
			);
            
			$message = $this->ci->load->view($this->ci->config->item('email_templates').$this->ci->config->item('email_forgot_password_complete'), $data, true);
				
			$this->ci->email->clear();
			//$config['mailtype'] = "html";
			//$this->ci->email->initialize($config);
			$this->ci->email->set_newline("\r\n");
			$this->ci->email->from($this->ci->config->item('website_webmaster_email'), $this->ci->config->item('website_webmaster_name'));
			$this->ci->email->to($profile->email);
			$this->ci->email->subject($this->ci->config->item('website_title') . ' - '.t('new_password'));
			$this->ci->email->message($message);

			if ($this->ci->email->send())
			{
				$this->set_error('password_change_successful');
				return TRUE;
			}
			else
			{
				$this->set_error('password_change_unsuccessful');
				return FALSE;
			}
		}
		else
		{
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}
	}

	/**
	 * register
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function register($username, $password, $email, $additional_data, $group_name = false, $auth_type=NULL) //need to test email activation
	{
	    $email_activation = $this->ci->config->item('email_activation');

		//NO EMAIL verification is required
		if (!$email_activation)
		{
			if ($this->ci->ion_auth_model->register($username, $password, $email, $additional_data, $group_name, $auth_type) )
			{
				$this->set_message('account_creation_successful');
				return TRUE;
			}
			else 
			{
				$this->set_error('account_creation_unsuccessful');
				return FALSE;
			}
		}
		else	//WITH EMAIL VERIFICATION
		{			
			$id = $this->ci->ion_auth_model->register($username, $password, $email, $additional_data, $group_name, $auth_type);
            
			if (!$id) 
			{ 
				$this->set_error('account_creation_unsuccessful');
				return FALSE; 
			}

			$deactivate = $this->ci->ion_auth_model->deactivate($id);

			if (!$deactivate) 
			{ 
				$this->set_error('deactivate_unsuccessful');
				return FALSE; 
			}

			$activation_code = $this->ci->ion_auth_model->activation_code;
			$identity        = $this->ci->config->item('identity');
	    	$user            = $this->ci->ion_auth_model->get_user($id)->row();

			$data = array('identity'   	=> $user->{$identity},
						  'id'         	=> $user->id,
        				  'email'      	=> $email,
        				  'activation' 	=> $activation_code,
						  'password'	=> $password
						 );
            
			$message = $this->ci->load->view($this->ci->config->item('email_templates').$this->ci->config->item('email_activate'), $data, true);
            
			//using email settings from /config/email.php
			$this->ci->email->clear();			
			//$this->ci->email->initialize($config);
			$this->ci->email->set_newline("\r\n");
			$this->ci->email->from($this->ci->config->item('website_webmaster_email'), $this->ci->config->item('website_webmaster_name'));
			$this->ci->email->to($email);
			$this->ci->email->subject($this->ci->config->item('website_title') . ' - '.t('account_activation'));
			$this->ci->email->message($message);
			
			if ($this->ci->email->send() == TRUE) 
			{
				$this->set_message('activation_email_successful');
				return TRUE;
			}
			else 
			{
				$this->set_error('activation_email_unsuccessful');
				return FALSE;
			}
		}
	}
	
	/**
	 * login
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function login($identity, $password, $remember=false)
	{
		if ($this->ci->ion_auth_model->login($identity, $password, $remember))
		{
			$this->set_message('login_successful');
			return TRUE;
		}
		else
		{
			$this->set_error('login_unsuccessful');
			return FALSE;
		}
	}
	
	
	public function openid_login($identity, $remember=false)
	{
		if ($this->ci->ion_auth_model->openid_login($identity, $remember))
		{
			$this->set_message('login_successful');
			return TRUE;
		}
		else
		{
			$this->set_error('login_unsuccessful');
			return FALSE;
		}
	}
	
	/**
	 * logout
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function logout()
	{
	    $identity = $this->ci->config->item('identity');
	    $this->ci->session->unset_userdata($identity);
	    $this->ci->session->unset_userdata('group');
	    $this->ci->session->unset_userdata('id');
	    $this->ci->session->unset_userdata('user_id');
	    
	    //delete the remember me cookies if they exist
	    if (get_cookie('identity')) 
	    {
	    	delete_cookie('identity');	
	    }
		if (get_cookie('remember_code')) 
	    {
	    	delete_cookie('remember_code');	
	    }
	    $this->ci->db_logger->write_log('before-session-destroy',$this->ci->session->userdata("session_id"));
		$this->ci->session->sess_destroy();
		//$this->ci->session->unset_userdata('session_id');
		$this->ci->db_logger->write_log('after-session-destroy',$this->ci->session->userdata("session_id"));
		
		$this->set_message('logout_successful');
		return TRUE;
	}
	
	/**
	 * logged_in
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function logged_in()
	{
	    $identity = $this->ci->config->item('identity');
	    
		return (bool) $this->ci->session->userdata($identity);
	}
	
	/**
	 * is_admin
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function is_admin()
	{	    
		/*
		$admin_group = $this->ci->config->item('admin_group');
	    $user_group  = $this->ci->session->userdata('group');
	    return $user_group == $admin_group;
		*/
		
		
		//check if user is admin by checking group_id==1
		return $this->ci->ion_auth_model->is_admin($this->ci->session->userdata("user_id"));
	}
	
	/**
	 * is_group
	 *
	 * @return bool
	 * @author Phil Sturgeon
	 **/
	public function is_group($check_group)
	{
	    $user_group = $this->ci->session->userdata('group');
	    
	    if(is_array($check_group))
	    {
	    	return in_array($user_group, $check_group);
	    }
	    
	    return $user_group == $check_group;
	}
	
	
	/**
	 * Profile
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function profile()
	{
	    $session  = $this->ci->config->item('identity');
	    $identity = $this->ci->session->userdata($session);
	    
	    return $this->ci->ion_auth_model->profile($identity);
	}
	
	/**
	 * Get Users
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function get_users($group_name = false)
	{
	    return $this->ci->ion_auth_model->get_users($group_name)->result();
	}
	
	/**
	 * Get Users Array
	 *
	 * @return array Users
	 * @author Ben Edmunds
	 **/
	public function get_users_array($group_name = false)
	{
	    return $this->ci->ion_auth_model->get_users($group_name)->result_array();
	}
	
	/**
	 * Get Newest Users
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function get_newest_users($limit = 10)
	{
	    return $this->ci->ion_auth_model->get_newest_users($limit)->result();
	}
	
	/**
	 * Get Newest Users Array
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function get_newest_users_array($limit = 10)
	{
	    return $this->ci->ion_auth_model->get_newest_users($limit)->result_array();
	}
	
	/**
	 * Get Active Users
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function get_active_users($group_name = false)
	{
	    return $this->ci->ion_auth_model->get_active_users($group_name)->result();
	}
	
	/**
	 * Get Active Users Array
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function get_active_users_array($group_name = false)
	{
	    return $this->ci->ion_auth_model->get_active_users($group_name)->result_array();
	}
	
	/**
	 * Get In-Active Users
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function get_inactive_users($group_name = false)
	{
	    return $this->ci->ion_auth_model->get_inactive_users($group_name)->result();
	}
	
	/**
	 * Get In-Active Users Array
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function get_inactive_users_array($group_name = false)
	{
	    return $this->ci->ion_auth_model->get_inactive_users($group_name)->result_array();
	}
	
	/**
	 * Get User
	 *
	 * @return object User
	 * @author Ben Edmunds
	 **/
	public function get_user($id=false)
	{
	    return $this->ci->ion_auth_model->get_user($id)->row();
	}
	
	/**
	 * Get User by Email
	 *
	 * @return object User
	 * @author Ben Edmunds
	 **/
	public function get_user_by_email($email)
	{
	    return $this->ci->ion_auth_model->get_user_by_email($email)->row();
	}
	

	/**
	 * Get User by Username
	 *
	 * @return object User
	 * @author Mehmood Asghar
	 **/
	public function get_user_by_username($username)
	{
	    return $this->ci->ion_auth_model->get_user_by_username($username)->row();
	}
	
	
	/**
	 * Get User as Array
	 *
	 * @return array User
	 * @author Ben Edmunds
	 **/
	public function get_user_array($id=false)
	{
	    return $this->ci->ion_auth_model->get_user($id)->row_array();
	}
	
	/**
	 * Get current logged in user
	 *
	 * @return array User
	 * @author Mehmood Asghar
	 **/
	public function current_user()
		{
			return $this->get_user($this->ci->session->userdata('user_id'));
		}


	
	/**
	 * Get Users Group
	 *
	 * @return object Group
	 * @author Ben Edmunds
	 **/
	public function get_users_group($id=false)
	{
	    return $this->ci->ion_auth_model->get_users_group($id);
	}


	/**
	 * update_user
	 *
	 * @return void
	 * @author Phil Sturgeon
	 **/
	public function update_user($id, $data)
	{
		 if ($this->ci->ion_auth_model->update_user($id, $data))
		 {
		 	$this->set_message('update_successful');
		 	return TRUE;
		 }
		 else
		 {
		 	$this->set_error('update_unsuccessful');
		 	return FALSE;
		 }
	}

	
	/**
	 * update_user
	 *
	 * @return void
	 * @author Phil Sturgeon
	 **/
	public function delete_user($id)
	{
		 if ($this->ci->ion_auth_model->delete_user($id))
		 {
		 	$this->set_message('delete_successful');
		 	return TRUE;
		 }
		 else
		 {
		 	$this->set_error('delete_unsuccessful');
		 	return FALSE;
		 }
	}

	
	/**
	 * set_lang
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_lang($lang = 'en')
	{
		 return $this->ci->ion_auth_model->set_lang($lang);
	}
	
	
	/**
	 * extra_where
	 * 
	 * Crazy function that allows extra where field to be used for user fetching/unique checking etc.
	 * Basically this allows users to be unique based on one other thing than the identifier which is helpful
	 * for sites using multiple domains on a single database.
	 *
	 * @return void
	 * @author Phil Sturgeon
	 **/
	public function extra_where()
	{
		$where =& func_get_args();
		
		$this->_extra_where = count($where) == 1 ? $where[0] : array($where[0] => $where[1]);
	}
	
	/**
	 * extra_set
	 *
	 * Set your extra field for registration
	 *
	 * @return void
	 * @author Phil Sturgeon
	 **/
	public function extra_set()
	{
		$set =& func_get_args();
		
		$this->_extra_set = count($set) == 1 ? $set[0] : array($set[0] => $set[1]);
	}
	
	/**
	 * set_message_delimiters
	 *
	 * Set the message delimiters
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_message_delimiters($start_delimiter, $end_delimiter)
	{
		$this->message_start_delimiter = $start_delimiter;
		$this->message_end_delimiter   = $end_delimiter;
		
		return TRUE;
	}
	
	/**
	 * set_error_delimiters
	 *
	 * Set the error delimiters
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_error_delimiters($start_delimiter, $end_delimiter)
	{
		$this->error_start_delimiter = $start_delimiter;
		$this->error_end_delimiter   = $end_delimiter;
		
		return TRUE;
	}
	
	/**
	 * set_message
	 *
	 * Set a message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_message($message)
	{
		$this->messages[] = $message;
		
		return $message;
	}
	
	/**
	 * messages
	 *
	 * Get the messages
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function messages()
	{
		$_output = '';
		foreach ($this->messages as $message) 
		{
			$_output .= $this->message_start_delimiter . $this->ci->lang->line($message) . $this->message_end_delimiter;
		}
		
		return $_output;
	}
	
	/**
	 * set_error
	 *
	 * Set an error message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_error($error)
	{
		$this->errors[] = $error;
		
		return $error;
	}
	
	/**
	 * errors
	 *
	 * Get the error message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function errors()
	{
		$_output = '';
		foreach ($this->errors as $error) 
		{
			$_output .= $this->error_start_delimiter . t($error) . $this->error_end_delimiter;
		}
		
		return $_output;
	}
	
	/**
	*
	* Return an array of site administrators
	*
	**/
	function get_admin_emails()
	{
		return $this->ci->ion_auth_model->get_admin_emails();
	}

	/**
	*
	* Check if the user has Admin rights for the site or not
	* 
	* Note: Checks user_site_roles table
	*
	* TODO: //disabled for now as there is no UI to create admins for different sites
	*/
	function is_site_admin()
	{
		return TRUE;
		
		
		//current site id
		$site_id=$this->ci->db->database;
		
		//logged in user site roles
		$site_user_roles=$this->ci->session->userdata('site_user_roles');
		
		foreach($site_user_roles as $role)
		{
			$role=(object)$role;
			if ($role->siteid==$site_id && $role->groupid==1)
			{
				return TRUE;
			}
		}
		return FALSE;
	}
}
