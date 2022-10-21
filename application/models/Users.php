<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Users extends CI_Model
{
	public function add($data)
	{
		$query = "INSERT INTO users(`username`, `password`, `dashboard_id`) VALUES('" . $data['username'] . "', PASSWORD('" . $data['password']."'), '".$data['dashboard_id']."')";
		$this->db->query($query);

		return $this->db->insert_id();
	}

	public function load($dashboard_id)
	{
		//$query = "SELECT * FROM reports WHERE user_id='" . $user_id . "' ORDER BY title ASC";
		$query = "SELECT * FROM users WHERE dashboard_id='".$dashboard_id."'";
		$query_result = $this->db->query($query)->result_array();

		return $query_result;
	}

	public function deleteByDashboardID($dashboard_id)
	{
		$query = "DELETE FROM users WHERE dashboard_id='" . $dashboard_id . "'";
		return $this->db->query($query);
	}



	


	public function getByID($id)
	{
		$query = "SELECT * FROM reports WHERE id='" . $id . "'";
		$query_result = $this->db->query($query)->result_array();

		return $query_result;
	}

	public function update($data)
	{

		$query = "UPDATE reports SET title='" . $data['title'] . "', conditions='" . $data['conditions'] . "', study='" . $data['study'] . "', country='" . $data['country'] . "', terms='" . $data['terms'] . "' WHERE id='" . $data['id'] . "'";

		return $this->db->query($query);
	}

	public function deleteByID($id)
	{
		$query = "DELETE FROM reports WHERE id='" . $id . "'";
		return $this->db->query($query);
	}

	public function duplicateByID($id)
	{

		$query_get = "SELECT * FROM reports WHERE id='" . $id . "'";
		$results_get = $this->db->query($query_get)->result_array();

		if (count($results_get) > 0) {
			$report = $results_get[0];

			// clone
			$query = "INSERT INTO reports(`title`, `conditions`, `study`, `country`, `terms`, `created_at`, `user_id`) VALUES('" . $report['title'] . "', '" . $report['conditions'] . "', '" . $report['study'] . "', '" . $report['country'] . "', '" . $report['terms'] . "', NOW(), '" . $report['user_id'] . "')";
			$this->db->query($query);

			return $this->db->insert_id();
		}

		return null;
	}

	
}
