<?php
class MY_Controller extends Controller
{	
	
	var $is_admin=TRUE;
	
	/**
	* Manages both admin/non-admin users
	*
	* @skip			skip authentication  (true=skip authentication)
	* @is_admin		requires the user to have admin rights
	*/
  public function MY_Controller($skip=FALSE,$is_admin=TRUE)
  {		
	parent::Controller();
	
	//test if application is installed
	$this->is_app_installed();

	//switch language
	$this->_switch_language();
	$this->lang->load("general");
		
	$this->load->library(array('site_configurations','ion_auth','session','form_validation'));	
	$this->is_admin=$is_admin;
  		
	if ($skip===FALSE)
	{
	   //apply IP restrictions for site administration
	   $this->apply_ip_restrictions();
	}
  
	//skip authentication
	if ($skip!==TRUE)
	{
		$this->_auth();
	}
  }

	/**
	 * 
	 * Apply IP restrictions for Site Admin
	 * 
	 */
	 public function apply_ip_restrictions()
	 {
		$user_ip=$this->input->ip_address();  		
		$ip_list=$this->config->item("admin_allowed_ip");
		
		if ($ip_list!==FALSE)
		{
		  if (is_array($ip_list) && count($ip_list)>0)
		  {
			  //check ip is in the allowed list  
			  if (!in_array($user_ip, $ip_list))
			  {
				 //log
				 $this->db_logger->write_log('blocked','site access blocked from ip:'.$user_ip,'access-blocked');
				 
				 //show page not found  
				 show_404(); 
			  }  
		  }     
		} 
	 }
    
	/**
	* Switch site language using cookies
	*
	**/
	function _switch_language()
	{
		if($this->session->userdata('language'))
		{	
	        //switch language
			$this->config->set_item('language',$this->session->userdata('language'));
		}
	}
	
	/**
	*
	*
	* check if user is logged in or not
	**/
	function _auth()
	{
		$destination=$this->uri->uri_string();
		
		//check if ajax is set
		if ($this->input->get_post("ajax"))
		{
			$destination.='/?ajax='.$this->input->get_post("ajax");
		}
		//check if print is set
		if ($this->input->get_post("print"))
		{
			$destination.='/?print='.$this->input->get_post("print");
		}
				
		$this->session->set_userdata("destination",$destination);

    	if (!$this->ion_auth->logged_in()) 
		{
	    	//redirect them to the login page
			redirect("auth/login/?destination=$destination", 'refresh');
    	}
    	elseif (!$this->ion_auth->is_admin() && $this->is_admin==TRUE ) 
		{
    		//redirect them to the home page because they must be an administrator to view this
			//redirect($this->config->item('base_url'), 'refresh');
			redirect("auth/login/?destination=$destination", 'refresh');
    	}
	}

	//get public site menu
	function _menu()
	{
		$data['menus']= $this->Menu_model->select_all();		
		$content=$this->load->view('default_menu', $data,true);
		return $content;
	}
	
 	/**
	* Test if app is properly installed and can connect to db
	*/
 	function is_app_installed()
	{
		$this->load->database();
		
		//check if database connection settings are filled in
		if ($this->db->dbdriver=='' || $this->db->username=='' || $this->db->database=='')
		{
			show_error('You have not setup a database');
		}
		
		//test reading from database tables
		$this->db->limit(1);
		$query=$this->db->get('configurations');
		
		if ($query)
		{
			return TRUE;
		}
				
		//test database connection only if everything else above has failed
		switch($this->db->dbdriver)
		{
			case 'mysql':
				$conn_id = @mysql_connect($this->db->hostname, $this->db->username, $this->db->password, TRUE);
				break;
			case 'postgre':
				$conn_id=@pg_connect("host={$this->db->hostname} user={$this->db->username} password={$this->db->password} connect_timeout=5 dbname=postgres");
				break;
			case 'sqlsrv':
				$auth_info = array( "UID"=>$this->db->username,"PWD"=>$this->db->password);
				$conn_id = @sqlsrv_connect($this->db->hostname, $auth_info);
				break;
			default:
				show_error('MY_CONTROLLER::database not supported '.$this->db->dbdriver);
		}

		if (!$conn_id ) 
        {
            //cannot connect to database server
			show_error('Failed to connect to database, check database settings');
        } 
        else //can connect to db server but not to the database
        {
            redirect("install");
        }
	}

	
}	