<?php
class User_model extends Model {
 
    public function __construct()
    {
        // model constructor
        parent::__construct();
		
		$this->load->config('ion_auth');
		$this->tables  = $this->config->item('tables');		
    }
	
	//search users 
    function search($limit = NULL, $offset = NULL,$filter=NULL,$sort_by=NULL,$sort_order=NULL)
    {
		//$this->output->enable_profiler(TRUE);

		$this->db->start_cache();

		//columns
		$columns=sprintf('%s.id,group_id,username,email,active,created_on,last_login,country,company',$this->tables['users']);
		//select columns for output
		$this->db->select($columns);
		
		//allowed_fields for searching or sorting
		$db_fields=array('username','first_name','last_name','email','country','company');
		
		//set where
		if ($filter)
		{			
			foreach($filter as $f)
			{
				//search only in the allowed fields
				if (in_array($f['field'],$db_fields))
				{
					$this->db->like($f['field'], $f['keywords']); 
				}
				else if ($f['field']=='all')
				{
					foreach($db_fields as $field)
					{
						$this->db->or_like($field, $f['keywords']); 
					}
				}
			}
		}
		
		$this->db->join($this->tables['meta'], sprintf('%s.user_id = %s.id',$this->tables['meta'],$this->tables['users']));
		$this->db->stop_cache();

		//set order by
		if ($sort_by!='' && $sort_order!='')
		{
			if ( in_array($sort_by,$db_fields))
			{
				$this->db->order_by($sort_by, $sort_order); 
			}	
		}
		
		//set Limit clause
	  	$this->db->limit($limit, $offset);
		$this->db->from($this->tables['users']);
		

        $result= $this->db->get()->result_array();
		return $result;
    }
  	
    function search_count($filter=NULL)
    {
          return $this->db->count_all_results($this->tables['users']);
    }
	
	function getSingle($userid)
	{
		$this->db->where('id', $userid); 
		return $this->db->get($this->tables['users']);
	}
	
	function delete($id)
	{
		$this->db->where('id', $id); 
		$deleted=$this->db->delete($this->tables['users']);
		
		if ($deleted)
		{
			//remove meta
			$this->db->where('user_id', $id); 
			$this->db->delete($this->tables['meta']);
		}
		
		return $deleted;
	}
	
	/**
	* Returns a list of all countries in the database
	*
	*/
	function get_all_countries()
	{
		$this->db->select('countryid,name');
		$query=$this->db->get('countries');
		
		$output=array('-'=>'-');
		
		if ($query)
		{
			$rows=$query->result_array();
			
			foreach($rows as $row)
			{
				$output[$row['countryid']]=$row['name'];
			}				
		}
		
		return $output;
	}
}
?>