<?php
class Planned extends MY_Controller {

	var $errors='';
	var $search_fields=array('username','email','status');
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->helper(array('form', 'url'));		
		$this->load->library( array('form_validation','pagination') );
       	$this->load->model('Menu_model');		
		$this->lang->load('menu');//menu language file

		//set template to admin
		$this->template->set_template('admin');
	}
	
	function index()
	{			
		//get array of db rows		
		$result['rows']=$this->_search();
		
		$result['form_title']='Planned Studies';
		
		//load the contents of the page into a variable
		$content=$this->load->view('planned/edit', $result,true);
	
		//pass data to the site's template
		$this->template->write('content', $content,true);
		
		//set page title
		$this->template->write('title', t('Planned Studies'),true);

		//render final output
	  	$this->template->render();	
	}
	
	/**
	 * Search - internal method, supports pagination, sorting
	 *
	 * @return string
	 * @author IHSN
	 **/
	function _search()
	{
		//records to show per page
		$per_page = 10;
				
		//current page
		$offset=$this->input->get('offset');//$this->uri->segment(4);

		//sort order
		$sort_order=$this->input->get('sort_order') ? $this->input->get('sort_order') : 'asc';
		$sort_by=$this->input->get('sort_by') ? $this->input->get('sort_by') : 'weight';

		//filter
		$filter=NULL;

		//simple search
		if ($this->input->get_post("keywords") ){
			$filter[0]['field']=$this->input->get_post('field');
			$filter[0]['keywords']=$this->input->get_post('keywords');			
		}		
		
		//records
		$rows=$this->Menu_model->search($per_page, $offset,$filter, $sort_by, $sort_order);

		//total records in the db
		$total = $this->Menu_model->search_count();

		if ($offset>$total)
		{
			$offset=$total-$per_page;
			
			//search again
			$rows=$this->Menu_model->search($per_page, $offset,$filter, $sort_by, $sort_order);
		}
		
		//set pagination options
		$base_url = site_url('admin/menu');
		$config['base_url'] = $base_url;
		$config['total_rows'] = $total;
		$config['per_page'] = $per_page;
		$config['query_string_segment']="offset"; 
		$config['page_query_string'] = TRUE;
		$config['additional_querystring']=get_querystring( array('keywords', 'field'));//pass any additional querystrings
		$config['num_links'] = 1;
		//$config['next_link'] = 'Next';		
		//$config['prev_link'] = 'Prev';
		$config['full_tag_open'] = '<span class="page-nums">' ;
		$config['full_tag_close'] = '</span>';
		
		//intialize pagination
		$this->pagination->initialize($config); 
		return $rows;		
	}
	
	/**
	* Add new menu
	*
	*/
	function add()
	{
			$this->edit();
	}
		
	/**
	* Edit menu
	*
	* handles both add or edit
	*/
	function edit($id=NULL)	
	{
		//load js rich text editor
       	$this->template->add_js('javascript/tiny_mce/tiny_mce.js');

	
		$menu=NULL;
		$content=NULL;
		
		if (!is_numeric($id)  && $id!=NULL)
		{
			show_error('Invalid id provided');exit;		
		}

		//edit page link
		if ($this->input->post("linktype")==1)
		{
			$this->edit_link($id);return;
		}

		//validation rules
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		$this->form_validation->set_rules('url', 'URL', 'trim|required|callback__url_check');		
				
		//process form				
		if ($this->form_validation->run() == TRUE)
		{
			$options=array();
			$post_arr=$_POST;
						
			//read post values to pass to db
			foreach($post_arr as $key=>$value)
			{
				$options[$key]=$this->input->post($key);
			}					
			
			//fix spaces in the url for internal urls
			$options['url']=url_title($options['url'], 'dash', TRUE);
															
			if ($id==NULL)
			{
				$db_result=$this->Menu_model->insert($options);
			}
			else
			{
				//update db
				$db_result=$this->Menu_model->update($id,$options);
			}
						
			if ($db_result===TRUE)
			{
				//update successful
				$this->session->set_flashdata('message', 'Form was updated successfully!');
				
				//redirect back to the list
				redirect("admin/menu","refresh");
			}
			else
			{
				//update failed
				$this->form_validation->set_error('Failed to update database');
			}
		}
		else //loading form the first time
		{
				if ( is_numeric($id) )
				{
					//get menu from db
					$menu=$this->Menu_model->select_single($id);
								
					if ( $menu===FALSE)
					{
						throw new Exception("invalid id");
					}
				
					$menu=(object)$menu;
				
					if ($menu->linktype==1)
					{
						$this->edit_link($id);return;
					}
				}
		}

		//show form
		$content=$this->load->view('menu/edit',$menu,true);									
				
		//pass data to the site's template
		$this->template->write('content', $content,true);
		
		//render final output
	  	$this->template->render();								
	}
	
	
	/**
	* edit page link
	*
	*/
	function edit_link($id)
	{
		$this->add_link($id);
	}
	
	/**
	* handles both add/edit page link
	*
	*/
	function add_link($id=NULL)
	{
	
		if ($id==NULL)
		{
			//form/page title
			$data['form_title']='Add Page Link';
		}
		else
		{
			$data['form_title']='Edit Page Link';
		}
		
       //validate form input
		$this->form_validation->set_rules('title', 'Title', 'xss_clean|trim|required');
		$this->form_validation->set_rules('url', 'URL', 'xss_clean|trim|required|callback__url_check');
    	$this->form_validation->set_rules('published', 'Published', 'xss_clean|numeric');
    	$this->form_validation->set_rules('target', 'Open in', 'xss_clean|numeric');

        if ($this->form_validation->run() == true) 
		{ 
			$options=array();
			$post_arr=$_POST;
						
			//read post values to pass to db
			foreach($post_arr as $key=>$value)
			{
				$options[$key]=$this->input->post($key);
			}					
			
			//replace spaces with dashes
			$options['url']=url_title($options['url'], 'dash', TRUE);

			//change page type to a link
			$options['linktype']=1;
															
			if ($id==NULL)	
			{
				//insert record
				$db_result=$this->Menu_model->insert($options);
			}
			else
			{				
				//update record
				$db_result=$this->Menu_model->update($id,$options);
			}
									
			if ($db_result===TRUE)
			{
				//update successful
				$this->session->set_flashdata('message', 'Form was updated successfully!');
				
				//redirect back to the list
				redirect("admin/menu","refresh");
			}
			else
			{
				//update failed
				$this->form_validation->set_error('Failed to update database');
			}
		} 
		else 
		{ 
			//loading the form for the first time
			
			if ($id!=NULL)	
			{
				//load data from database
				$menu_row=$this->Menu_model->select_single($id);
				$data=array_merge($data,$menu_row);
			}
			
			//flash data message
	        $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			
			//load form			
            $content=$this->load->view('menu/edit_page_link', $data,TRUE);
			
			//render page
			$this->template->write('content', $content,true);
			$this->template->write('title', $data['form_title'],true);
			$this->template->render();		
		}
	}	
	
	
	/**
	* check if URL already exists in the database
	*
	*/
	function _url_check($url)
	{
		$id=$this->uri->segment(4);

		if (!is_numeric($id) )
		{
			$id=NULL;
		}
		
		//replaces spaces with dashes
		$url=url_title($url, 'dash', TRUE);
		
		$exists=$this->Menu_model->url_exists($url,$id);
				
		if ($exists >0)
		{
			$this->form_validation->set_message('_url_check', 'The %s already exists. Choose a different URL.');
			return FALSE;
		}
			return TRUE;
	}
	
	
	/**
	* Delete one or more records
	* note: to use with ajax/json, pass the ajax as querystring
	* 
	* id 	int or comma seperate string
	*/
	function delete($id)
	{			
		//array of id to be deleted
		$delete_arr=array();
	
		//is ajax call
		$ajax=$this->input->get_post('ajax');

		if (!is_numeric($id))
		{
			$tmp_arr=explode(",",$id);
		
			foreach($tmp_arr as $key=>$value)
			{
				if (is_numeric($value))
				{
					$delete_arr[]=$value;
				}
			}
			
			if (count($delete_arr)==0)
			{
				//for ajax return JSON output
				if ($ajax!='')
				{
					echo json_encode(array('error'=>"invalid id was provided") );
					exit;
				}
				
				$this->session->set_flashdata('error', 'Invalid id was provided.');
				redirect('admin/menu',"refresh");
			}	
		}		
		else
		{
			$delete_arr[]=$id;
		}
		
		if ($this->input->post('cancel')!='')
		{
			//redirect page url
			$destination=$this->input->get_post('destination');
			
			if ($destination!="")
			{
				redirect($destination);
			}
			else
			{
				redirect('admin/menu');
			}	
		}
		else if ($this->input->post('submit')!='')
		{
			foreach($delete_arr as $item)
			{
				//confirm delete	
				$this->Menu_model->delete($item);
			}

			//for ajax calls, return output as JSON						
			if ($ajax!='')
			{
				echo json_encode(array('success'=>"true") );
				exit;
			}
						
			//redirect page url
			$destination=$this->input->get_post('destination');
			
			if ($destination!="")
			{
				redirect($destination);
			}
			else
			{
				redirect('admin/menu');
			}	
		}
		else
		{
			//ask for confirmation
			$content=$this->load->view('resources/delete', NULL,true);
			
			$this->template->write('content', $content,true);
	  		$this->template->render();
		}		
	}

	/**
	* Change the order of menu items
	*
	*/
	function menu_sort()
	{		
		$id_list=$this->input->post('id');
		
		if (is_array($id_list))
		{
			//update database
			$result=$this->Menu_model->update_weight($id_list);
			
			if ($result===TRUE)
			{
				redirect('admin/menu','refresh');
			}
		}

		//load javascript/css for the JQuery Sortable
		$this->template->add_css('javascript/jquery/themes/base/ui.all.css');
		$this->template->add_js('javascript/jquery/ui/ui.core.js');
		$this->template->add_js('javascript/jquery/ui/ui.sortable.js');

		//get array of all published menu items
		$data['rows']=$this->Menu_model->select_all('weight','ASC');
		
		//show page sorted by weight
		$content=$this->load->view('menu/menu_sort', $data,TRUE);			
		$this->template->write('content', $content,true);
		$this->template->render();		
	}

}

/* End of file menu.php */
/* Location: ./system/application/controllers/menu.php */