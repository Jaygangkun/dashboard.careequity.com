<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Dashboards extends CI_Model
{
	public function add($data)
	{
		$query = "INSERT INTO dashboards(`name`, `reports`) VALUES('" . $data['name'] . "', '" . json_encode($data['reports'])."')";
		$this->db->query($query);

		return $this->db->insert_id();
	}

	public function load()
	{
		$query = "SELECT * FROM dashboards";
		$query_result = $this->db->query($query)->result_array();

		return $query_result;
	}

	public function deleteByID($id)
	{
		$query = "DELETE FROM dashboards WHERE id='" . $id . "'";
		return $this->db->query($query);
	}

	public function getByID($id)
	{
		$query = "SELECT * FROM dashboards WHERE id='" . $id . "'";
		$query_result = $this->db->query($query)->result_array();

		return $query_result;
	}

	public function update($data)
	{

		$query = "UPDATE dashboards SET name='" . $data['name'] . "', reports='" . json_encode($data['reports']) . "' WHERE id='" . $data['id'] . "'";

		return $this->db->query($query);
	}

	public function getByName($name)
	{
		$query = "SELECT * FROM dashboards WHERE name='" . $name . "'";
		$query_result = $this->db->query($query)->result_array();

		return $query_result;
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
