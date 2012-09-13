<?php
class Access_licensed extends MY_Controller {
 
 	var $form_model='licensed';
	
    public function __construct()
    {
		//requires authentication, and user can be non-admin
		parent::__construct($SKIP=TRUE,$is_admin=FALSE);
			
		$this->load->model('Licensed_model');
		$this->load->model('Datafiles_model');
		$this->load->model('Catalog_model');
		$this->template->set_template('default');
		$this->load->helper('admin_notifications');
		
		$this->lang->load('general');
		$this->lang->load('licensed_request');
		$this->lang->load('licensed_access_form');
    		
    	//$this->load->library(array('site_configurations'));
    	//$this->output->enable_profiler(TRUE);
    }
 
    /**
     * Show the request form
     * 
     * @param $survey_id
     */
	function index($survey_id=NULL)
	{	
		//change template for ajax
		$ajax_params='';
		if ($this->input->get_post("ajax"))
		{
			$this->ajax=1;
			$this->template->set_template('blank');
			$ajax_params='/?ajax=1';
		}
				
		if ( !is_numeric($survey_id))
		{
			show_404();return;
		}
			
		//check if user is logged in
		if (!$this->ion_auth->logged_in()) 
		{
		    $this->session->set_flashdata('reason', t('reason_login_licensed_access'));
			$destination=$this->uri->uri_string();
			$this->session->set_userdata("destination",$destination);

			//redirect them to the login page
			redirect("auth/login/?destination=$destination", 'refresh');
    	}

		$this->load->library( array('form_validation') );
			
		//get user info
		$user=$this->ion_auth->current_user();
		
		//get survey row
		$survey=$this->Catalog_model->select_single($survey_id);
		
		if ($survey==FALSE)
		{
			show_404();return;
		}

		//check if the survey has the correct form type
		if ($this->Catalog_model->get_survey_form_model($survey_id)!=$this->form_model)
		{
			show_404();
		}

		//check if user has already submitted this form. if the request is PENDING or was APPROVED
		// 	and the approved expiry period is still valid then redirect them to the previous request 
		//	information page
		$this->requests=$this->Licensed_model->get_survey_requests_by_user($user->id,$survey_id);

		if ($this->requests)
		{
			foreach($this->requests as $request)
			{
				if ($request['status']=='PENDING')
				{
					//redirect to PENDING request page
					//redirect('access_licensed/track/'.$request['id'].$ajax_params);
					///$this->track($request['id']);
		   			//return;
				}	 
    			elseif ($request['status']=='APPROVED')
				{
					if ($request['expiry']>=date("U") || (int)$request['expiry']==0)
					{
						//redirect to APPROVED request page
						//redirect('access_licensed/track/'.$request['id'].$ajax_params);
						//$this->track($request['id']);
						//return;
        			}
    			}
			}
		}		

		$content=NULL;
			
		//set data to be passed to the view
		$data->user_id=$user->id;
		$data->username=$user->username;
		$data->fname=$user->first_name;
		$data->lname=$user->last_name;
		$data->organization=$user->company;
		$data->email=$user->email;
		$data->survey_title=$survey["titl"];
		$data->survey_id=$survey["surveyid"];
		$data->survey_uid=$survey_id;
		$data->proddate=$survey["proddate"];
		$data->abstract=$this->input->post("abstract");

		//validation rules
		$this->form_validation->set_rules('org_rec', t('receiving_organization_name'), 'trim|required|xss_clean|max_length[255]');
		$this->form_validation->set_rules('address', t('postal_address'), 'trim|required|xss_clean|max_length[255]');
		$this->form_validation->set_rules('tel', t('telephone'), 'trim|required|xss_clean|max_length[14]');
		$this->form_validation->set_rules('datause', t('intended_use_of_data'), 'trim|required|xss_clean|max_length[1000]');
		$this->form_validation->set_rules('dataset_access', t('dataset_access'), 'trim|required|xss_clean|max_length[15]');
		
		//optional fields
		$this->form_validation->set_rules('org_type', t('org_type'), 'trim|xss_clean|max_length[45]');
		$this->form_validation->set_rules('compdate', t('expected_completion'), 'trim|xss_clean|max_length[45]');
		$this->form_validation->set_rules('datamatching', t('data_matching'), 'trim|xss_clean|max_length[1]');
		$this->form_validation->set_rules('fax', t('fax'), 'trim|xss_clean|max_lengh[14]');		
	
		//process form				
		if ($this->form_validation->run() == TRUE)
		{			
			$post=$_POST;
			$options=array();
			
			foreach($post as $key=>$value)
			{
				$options[$key]=$this->input->xss_clean($value);
			}
		
			//insert
			$new_requestid=$this->Licensed_model->insert_request($data->survey_uid,$data->user_id,$options);
			
			if ($new_requestid!==FALSE)
			{
				//update successful
				$this->session->set_flashdata('message', t('form_update_success'));
				
				//send confirmation email to the user and the site admin
				$this->_confirmation_email($new_requestid);
								
				//redirect to the confirmation page
				redirect('access_licensed/confirm/'.$new_requestid.$ajax_params,"refresh");
			}
			else
			{
				//update failed
				$this->form_validation->set_error(t('form_update_fail'));
			}
		}
			
		//load the contents of the page into a variable
		$content.=$this->load->view('access_licensed/request_form', $data,true);			
			
		//set page title
		$this->template->write('title', t('application_access_licensed_dataset'),true);
		$this->template->write('content', $content,true);
	  	$this->template->render();
	}
	
	//does nothing, except to show a confirmation message
	function confirm()
	{
		//change template for ajax
		if ($this->input->get_post("ajax"))
		{
			$this->template->set_template('blank');
		}
		
		$content=$this->load->view('access_licensed/request_confirmation',NULL,TRUE);
		$this->template->write('content', $content,true);
	  	$this->template->render();
	}
	
	function _confirmation_email($requestid)
	{			
		//get user info
		$user=$this->ion_auth->current_user();

		//get request data
		$data=$this->Licensed_model->get_request_by_id($requestid);
		$data=(object)$data;	
		//set data to be passed to the view
		$data->user_id=$user->id;
		$data->username=$user->username;
		$data->fname=$user->first_name;
		$data->lname=$user->last_name;
		$data->organization=$user->company;
		$data->email=$user->email;
		$data->survey_title=$data->nation. ' - '.$data->titl;
		$data->survey_id=$data->surveyid;
		
		$subject=t('confirmation_application_for_licensed_dataset').' - '.$data->survey_title;			
		$message=$this->load->view('access_licensed/request_form_view', $data,true);

		$this->load->library('email');
		$this->email->clear();		
		$this->email->initialize();//intialize using the settings in mail
		$this->email->set_newline("\r\n");
		$this->email->from($this->config->item('website_webmaster_email'), $this->config->item('website_title'));
		$this->email->to($data->email);
		$this->email->subject($subject );
		$this->email->message($message);
		
		if ($this->email->send())
		{
			//notification for the site admins
			$subject=t('notification_licensed_survey_request_received');
			$message=$this->load->view('access_licensed/admin_notification_email', $data,true);
			
			//notify the site admin
			notify_admin($subject,$message,$notify_all_admins=false);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	
	/*
	* Track licensed request by request id
	*
	* @request_id	integer
	*/
	function track($request_id)
	{
		if (!is_numeric($request_id))
		{
			show_404();
		}
		
		//check if user is logged in
		if (!$this->ion_auth->logged_in()) 
		{
			$destination=$this->uri->uri_string();
			$this->session->set_userdata("destination",$destination);

			//redirect to the login page
			redirect("auth/login/?destination=$destination", 'refresh');
    	}

		//get user info
		$user=$this->ion_auth->current_user();
		
		//get request from db
		$data=$this->Licensed_model->get_request_by_id($request_id);
		
		if ($data===FALSE)
		{
			echo show_message(t('invalid_id'));return;
			show_404();
		}
		
		//user can only view his requests
		if ($data['userid']!=$user->id)
		{
			show_404();
		}
		
		$contents=$this->load->view('access_licensed/request_status', $data,true);				
		
		if ($data['status']=='APPROVED')
		{		
			$contents.=$this->_get_licensed_files($request_id);
		}
		
		if ($this->input->get("print")=='yes' || $this->input->get("ajax"))
		{
			$this->template->set_template('blank');	
		}
		else
		{		
			$this->template->set_template('default');
		}

		$this->template->add_css('css/forms.css');	
		$this->template->write('content', $contents,true);
	  	$this->template->render();
	}
	
	//returns a list of downloadable files for the request
	function _get_licensed_files($request_id)
	{				
		$this->load->helper('file');
				
		//get the surveyid by requestid
		$survey_id=$this->Licensed_model->get_surveyid_by_request($request_id);

		//check if the survey form is set to LICENSED
		if ($this->Catalog_model->get_survey_form_model($survey_id)!='licensed')
		{
			show_error('form_not_available');
			return;
		}
		
		//get files by request id
		$files['rows']=$this->Licensed_model->get_request_downloads($request_id);		
		
		//get survey folder path, this is needed for the view(downloads_licensed)
		$this->survey_folder=$this->_get_survey_folder($survey_id);
		
		//show files
		$content=$this->load->view('managefiles/downloads_licensed', $files,true);

		return $content;
	}
	
	/**
	* Download licensed data files
	*		
	* Checks before a file can be downloaded
	*	- status=APPROVED
	*	- IP_LIMIT on request
	*	- Expiry date on file
	*	- Download Limits
	*
	* NOTE: Downloads are logged and stats are updated
	*/
	function download($request_id=NULL,$file_id=NULL)
	{
		$this->load->model('managefiles_model');
		
		if (!is_numeric($request_id) )
		{			
			show_404();return;
		}

		if ($file_id=='')
		{			
			show_404();return;	
		}
		
		//get currently logged-in user info
		$user=$this->ion_auth->current_user();

		//get file information
		$fileinfo=$this->managefiles_model->get_resource_by_id($file_id);

		//get the surveyid by requestid
		$survey_id=$this->Licensed_model->get_surveyid_by_request($request_id);

		//check if the survey form is set to LICENSED
		if ($this->Catalog_model->get_survey_form_model($survey_id)!='licensed')
		{
			show_404();
			return;
		}
				
		//get request info from db
		$request=$this->Licensed_model->get_request_by_id($request_id);
			
		//download stats data
		$download_stats=$this->Licensed_model->get_download_stats($request_id, $file_id);		
		
		if (!$download_stats)
		{
			show_error( 'File is no longer available for download');
			exit();			
		}
		
		//no. of times the file can be downloaded
		$download_limit=$download_stats['download_limit'];
		
		//how many times the files has been downloaded
		//download will stop once the limit is reached
		$download_times=$download_stats['downloads'];
				
		if ($download_times>=$download_limit)
		{
			$data['limit']=$download_limit;
			$data['email']=$this->config->item("website_webmaster_email");
			$contents=$this->load->view('access_licensed/download_limit_reached',$data,TRUE);
			//echo 'file download limit was reached. cannto be downloaded any more.';
			//exit();
		
			$this->template->set_template('blank');	
			$this->template->write('content', $contents,true);
		  	$this->template->render();
			return;
		}				
		
		//increment the download tick
		$this->Licensed_model->update_download_stats($file_id,$request_id,$user->email);
		
		//survey folder path
		$survey_folder=$this->_get_survey_folder($survey_id);
		
		//build licensed file path
		$file_path=$survey_folder.'/'.$fileinfo['filename'];
		
		if (!file_exists($file_path))
		{
			show_error('The file was not found.');
			return;
		}
		
		//download file
		$this->load->helper('download');
		
		//log
		log_message('info','Downloading file <em>'.$file_path.'</em>');
		$this->db_logger->write_log('survey',$file_id,'licensed-download',$survey_id);
		
		force_download2($file_path);
	}


	/**
	* Returns the survey folder path
	*
	*/
	function _get_survey_folder($surveyid)
	{
	
		//build fixed survey folder path
		$catalog_root=$this->config->item("catalog_root");
		$survey_folder=$this->Catalog_model->get_survey_path($surveyid);

		if($survey_folder===FALSE)
		{
			show_404();
		}
					
		//survey folder path
		$survey_folder=unix_path($catalog_root.'/'.$survey_folder);
		
		return $survey_folder;
	}	
}
/* End of file access_licensed.php */
/* Location: ./controllers/access_licensed.php */