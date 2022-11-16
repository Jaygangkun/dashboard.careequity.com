<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Dashboards extends CI_Model
{
	public function add($data)
	{
		$query = "INSERT INTO dashboards(`name`, `slug`, `reports`) VALUES('" . $data['name'] . "', '".slugify($data['name'])."', '" . json_encode($data['reports'])."')";
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

		$query = "UPDATE dashboards SET name='" . $data['name'] . "', slug='" . slugify($data['name']) . "', reports='" . json_encode($data['reports']) . "' WHERE id='" . $data['id'] . "'";

		return $this->db->query($query);
	}

	public function getBySlug($slug)
	{
		$query = "SELECT * FROM dashboards WHERE slug='" . $slug . "'";
		$query_result = $this->db->query($query)->result_array();

		return $query_result;
	}

	public function getByName($name)
	{
		$query = "SELECT * FROM dashboards WHERE name='" . $name . "'";
		$query_result = $this->db->query($query)->result_array();

		return $query_result;
	}
	
}
