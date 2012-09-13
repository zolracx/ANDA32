<?php
class Configurations_model extends Model {
 
    public function __construct()
    {
        parent::__construct();
		//$this->output->enable_profiler(TRUE);
    }
	
	/**
	* load nada configurations
	*
	*/
    function load()
    {
		$this->db->select('name,value');
		$result= $this->db->get('configurations');

		if ($result)
		{
			return $result->result_array();
		}		

		return FALSE;	
    }
  	
	/**
	* returns all settings
	*
	*/
	function select_all()
    {
		$this->db->select('*');		
		$this->db->from('configurations');
        return $this->db->get()->result_array();
    }	

	/**
	* returns an array of site configurations
	*
	*/	
	function get_config_array()
    {
		$this->db->select('name,value');		
		$this->db->from('configurations');
        $rows=$this->db->get()->result_array();
		
		$result=array();
		foreach($rows as $row)
		{
			$result[$row['name']]=$row['value'];
		}
		
		return $result;
    }	


	/**
	* update configurations
	*
	*/
	function update($options)
	{		
		foreach($options as $key=>$value)
		{
			$data=array('value'=>$value);
			$this->db->where('name', $key);
			$result=$this->db->update('configurations', $data);
			
			if(!$result)
			{
				return FALSE;
			}
		}		
		return TRUE;
	}
	
	/**
	* add new configuration
	*
	*/
	function add($name, $value,$label=NULL, $helptext=NULL)
	{	
		if (trim($name)=='')
		{
			return FALSE;
		}	
			
		$data=array('name'=>$name,'value'=>$value,'label'=>$label,'helptext'=>$helptext);
		$result=$this->db->insert('configurations', $data);
		
		if(!$result)
		{
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	*
	* Return an array of vocabularies
	*
	*/
	function get_vocabularies_array()
    {
		$this->db->select('vid,title');		
		$this->db->from('vocabularies');
        $query=$this->db->get();
		
		if($query)
		{
			$rows=$query->result_array();
			
			$result=array('-'=>'---');
			foreach($rows as $row)
			{
				$result[$row['vid']]=$row['title'];
			}
			return $result;
		}
		
		return FALSE;
    }	
	
	
}
?>