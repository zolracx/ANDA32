<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_system'][] = array(
                                'class'    => '',
                                'function' => 'pre_system_url_check',
                                'filename' => '',
                                'filepath' => 'hooks',
                                'params'   => array()
                                );

$hook['post_controller_constructor'][] = array(
                                'class'    => '',
                                'function' => 'disable_annonymous_access',
                                'filename' => '',
                                'filepath' => 'hooks',
                                'params'   => array()
                                );

$hook['post_controller_constructor'][] = array(
                                'class'    => '',
                                'function' => 'disable_admin_access',
                                'filename' => '',
                                'filepath' => 'hooks',
                                'params'   => array()
                                );
								
/**
*
* Force SSL for urls under /auth if Server has 
* SSL Support
*
*
**/
function pre_system_url_check()
{
	//load configurations
    include APPPATH.'config/config.php';
	
	if (!$config)
	{
		return FALSE;
	}
	
	$http_port=(int)$config["http_port"];
	$enable_ssl=(bool)$config["enable_ssl"];
		
	if (!$enable_ssl)
	{
		return FALSE;
	}
	
	$path_info='';
	if(isset($_SERVER["PATH_INFO"]))
	{
		$path_info=$_SERVER["PATH_INFO"];
	}
	
	$segments= array_filter(explode("/",$path_info));
	
	$url="https://".$_SERVER['HTTP_HOST']._request_uri();

	//build url for redirect
	$redirect= "https://".$_SERVER['HTTP_HOST']._request_uri();
		
	//remove http port
	if ($http_port>0 && $http_port!=80)
	{
		$redirect=str_replace(':'.$http_port,'',$redirect);
	}

	//check server variable is set
	if(!isset($_SERVER['HTTPS']))
	{
		return FALSE;
	}
				
	//it is a http page
	if($_SERVER['HTTPS']!=="on")
	{	
		//if url first segment has AUTH redirect to HTTPS		
		if (current($segments)=='auth')
		{
			//redirect to SSL page
			header("Location:$redirect");
		}		
	}
	else if($_SERVER['HTTPS']=="on")
	{
		if (current($segments)!='auth')
		{

			if ($http_port!=80)
			{
				$http_port=':'.$http_port;
			}
			else if($http_port==80)
			{
				$http_port='';
			}
			
			//redirect to NON-SSL page			
			$redirect= "http://".$_SERVER['HTTP_HOST'].$http_port._request_uri();
			header("Location:$redirect");
		}
	}
}

/**
*
* Returns the URI for the current page
**/
function _request_uri()
{
	//on IIS 6 request_uri is not available
	if (!empty($_SERVER['REQUEST_URI']))
	{
		return $_SERVER['REQUEST_URI'];
	}
	else
	{
		return $_SERVER['PHP_SELF'];
	}
}

/**
*
* If annonymous access is set to false, ask users to login
*/
function disable_annonymous_access($params)
{
		$CI =& get_instance();
		
		if ($CI->config->item("site_password_protect")!=='yes')
		{
			return;
		}

        $CI->load->helper('url'); // to be on the safe side
			
		//disable rules for the auth/ url, otherwise user will never see the login page
        if($CI->uri->segment(1) !== 'auth')
        {
			//remember the page user was on
			$destination=$CI->uri->uri_string();
			$CI->session->set_userdata("destination",$destination);

			if (!$CI->ion_auth->logged_in()) 
			{
				//redirect to the login page
				redirect("auth/login/?destination=$destination", 'refresh');
			}
        }
}


/**
*
* Disable Admin access
*/
function disable_admin_access($params)
{
		return;
		$CI =& get_instance();		
        $CI->load->helper('url'); // to be on the safe side

		//URL allowed to access admin area
		$allowed_host='http://localhost/';
				
		//segments to disable
		$disallowed_segment='/admin';
		
		//accessing from the allowed host
		if (strpos(current_url(),$allowed_host)!==FALSE)
		{
			return;
		}
		
		//accessing from dis-allowed host
		//check if accessing restricted pages
		if (strpos(current_url(),$disallowed_segment))
		{
			show_404();
		}
}



/* End of file hooks.php */
/* Location: ./system/application/config/hooks.php */