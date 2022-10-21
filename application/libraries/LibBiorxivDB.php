<?php

class LibBiorxivDB 
{

	function __construct()
	{
		$this->CI =& get_instance();

	}

    static function reports_load_by_ids($ids)
	{
		$CI =& get_instance();
		$db = $CI->load->database('biorxiv', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

		$where = '';
		foreach($ids as $id) {
			if($where == '') {
				$where = 'WHERE id="'.$id.'"';
			}
			else {
				$where .= ' OR id="'.$id.'"';
			}
		}
		
		$result = $db->query('SELECT * FROM reports '.$where.' ORDER BY title ASC')->result_array();
		
		$db->close();

		return $result;
	}

	static function reports_get_by_id($id)
	{
		$CI =& get_instance();
		$db = $CI->load->database('biorxiv', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

		$result = $db->query("SELECT * FROM reports WHERE id='".$id."'")->result_array();
		
		$db->close();

		return $result;
	}

	public function reports_search($keyword, $sort, $ids){
		
		$CI =& get_instance();
		$db = $CI->load->database('biorxiv', TRUE); // the TRUE paramater tells CI that you'd like to return the database object.

		$query = '';

		if($sort == 'az'){
			$orderby = 'ORDER BY title ASC';
		}
		else if($sort == 'newold'){
			$orderby = 'ORDER BY status ASC';
		}
		else if($sort == 'oldnew'){
			$orderby = 'ORDER BY status DESC';
		}

		$where = '';
		foreach($ids as $id) {
			if($where == '') {
				$where = 'WHERE id="'.$id.'"';
			}
			else {
				$where .= ' OR id="'.$id.'"';
			}
		}

		if($keyword == ''){
			$query = "SELECT * FROM reports ".$where." ".$orderby;
		}
		else{
			$query = "SELECT * FROM reports ".$where." AND (title LIKE '%".$keyword."%' OR conditions LIKE '%".$keyword."%' OR study LIKE '%".$keyword."%' OR country LIKE '%".$keyword."%' OR terms LIKE '%".$keyword."%') ".$orderby;
		}

		$query_result = $db->query($query)->result_array();

		$db->close();

		return $query_result;
	}
}
?>