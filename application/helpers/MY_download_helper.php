<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2009, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Download Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/download_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Force Download
 *
 * Generates headers that force a download to happen
 *
 * @access	public
 * @param	string	filename
 * @param	mixed	the data to be downloaded
 * @return	void
 */	
if ( ! function_exists('force_download2'))
{
function force_download2($filename = '', $data = false, $enable_partial = true, $speedlimit = 0)
    {
        if ($filename == '')
        {
            return FALSE;
        }
        
        if($data === false && !file_exists($filename))
            return FALSE;

        // Try to determine if the filename includes a file extension.
        // We need it in order to set the MIME type
        if (FALSE === strpos($filename, '.'))
        {
            return FALSE;
        }
    
        // Grab the file extension
        $x = explode('.', $filename);
        $extension = end($x);

        // Load the mime types
        @include(APPPATH.'config/mimes'.EXT);
    
        // Set a default mime if we can't find it
        if ( ! isset($mimes[$extension]))
        {
            if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
                $UserBrowser = "Opera";
            elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
                $UserBrowser = "IE";
            else
                $UserBrowser = '';
            
            $mime = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
        }
        else
        {
            $mime = (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
        }
        
        $size = $data === false ? filesize($filename) : strlen($data);
        
        if($data === false)
        {
            $info = pathinfo($filename);
            $name = $info['basename'];
        }
        else
        {
            $name = $filename;
        }
        
        // Clean data in cache if exists
        //@ob_end_clean();
        
        // Check for partial download
        if(isset($_SERVER['HTTP_RANGE']) && $enable_partial)
        {
            list($a, $range) = explode("=", $_SERVER['HTTP_RANGE']);
            list($fbyte, $lbyte) = explode("-", $range);
            
            if(!$lbyte)
                $lbyte = $size - 1;
            
            $new_length = $lbyte - $fbyte;
            
            header("HTTP/1.1 206 Partial Content", true);
            header("Content-Length: $new_length", true);
            header("Content-Range: bytes $fbyte-$lbyte/$size", true);
        }
        else
        {
            header("Content-Length: " . $size);
        }
        
        // Common headers
        header('Content-Type: ' . $mime, true);
        header('Content-Disposition: attachment; filename="' . $name . '"', true);
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT", true);
        header('Accept-Ranges: bytes', true);
        header("Cache-control: private", true);
        header('Pragma: private', true);
        
        // Open file
        if($data === false) {
            $file = fopen($filename, 'r');
            
            if(!$file)
                return FALSE;
        }
        
        // Cut data for partial download
        if(isset($_SERVER['HTTP_RANGE']) && $enable_partial)
            if($data === false)
                fseek($file, $range);
            else
                $data = substr($data, $range);
        
        // Disable script time limit
        @set_time_limit(0);
        
        // Check for speed limit or file optimize
        if($speedlimit > 0 || $data === false)
        {
            if($data === false)
            {
                $chunksize = $speedlimit > 0 ? $speedlimit * 1024 : 512 * 1024;
            
                while(!feof($file) and (connection_status() == 0))
                {
                    $buffer = fread($file, $chunksize);
                    echo $buffer;
                    flush();
                    
                    if($speedlimit > 0)
                        sleep(1);
                }
                
                fclose($file);
            }
            else
            {
                $index = 0;
                $speedlimit *= 1024; //convert to kb
                
                while($index < $size and (connection_status() == 0))
                {
                    $left = $size - $index;
                    $buffersize = min($left, $speedlimit);
                    
                    $buffer = substr($data, $index, $buffersize);
                    $index += $buffersize;
                    
                    echo $buffer;
                    flush();
                    sleep(1);
                }
            }
        }
        else
        {
            echo $data;
        }
    } 
}

/**
 * Force Download v3 - TODO:remove
 *
 * Generates headers that force a download to happen
 *
 * @access	public
 * @param	string	filepath
 * @param	string	filename
 * @return	void
 */	
function force_download3($filepath,$filename)
    {

		// required for IE, otherwise Content-disposition is ignored
		if(ini_get('zlib.output_compression'))
		{
		  ini_set('zlib.output_compression', 'Off');
		} 

		if (!file_exists($filepath)){
			  echo "<html><title>".(('Download'))."</title><body>".('ERROR: Specified source does not exists'). "</body></html>";
			  exit;
		}

		$file_extension = strtolower(substr(strrchr($filename,"."),1));


		// Determine mime type
		switch( $file_extension )
		{
		  case "pdf": $ctype="application/pdf"; break;
		  case "exe": $ctype="application/octet-stream"; break;
		  case "zip": $ctype="application/zip"; break;
		  case "doc": $ctype="application/msword"; break;
		  case "xls": $ctype="application/vnd.ms-excel"; break;
		  case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
		  case "gif": $ctype="image/gif"; break;
		  case "png": $ctype="image/png"; break;
		  case "jpeg":
		  case "jpg": $ctype="image/jpg"; break;
		  case "xml": $ctype="text/xml"; break;  
		  case "html": $ctype="text/html"; break;    
		  default: $ctype="application/force-download";
		}


		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers 
		header("Content-Type: $ctype"); 
		header("Content-Disposition: attachment; filename=\"".$filename."\";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($filepath));
		force_download_readfile_chunked("$filepath");
		exit();
    }

		// readfile_chunked function by chrisputnam 
		// from discussion on pHp web site at http://us2.php.net/readfile
	  function force_download_readfile_chunked($filename,$retbytes=true)
	  {	  	
	   $chunksize = 1*(1024*1024); // how many bytes per chunk
	   $buffer = '';
	   $cnt =0;
	   // $handle = fopen($filename, 'rb');
	   $handle = fopen($filename, 'rb');
	   if ($handle === false)
	   {
	     return false;
	   }
	   while (!feof($handle))
	   {
	     $buffer = fread($handle, $chunksize);
	     echo $buffer;
	     flush();
	     if ($retbytes)
	     {
	       $cnt += strlen($buffer);
	     }
		 // change: added call to set_time_limit to avoid 30 seconds timme out (PH)
		 set_time_limit(0);
	   }
	   $status = fclose($handle);
	   if ($retbytes && $status)
	   {
	     return $cnt; // return num. bytes delivered like readfile() does.
	   }
	   return $status;
	  }
/* End of file download_helper.php */
/* Location: ./system/helpers/download_helper.php */