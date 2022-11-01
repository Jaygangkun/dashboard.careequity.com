<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Users extends CI_Model
{
	public function add($data)
	{
		$query = "INSERT INTO users(`email`, `password`, `dashboard_id`) VALUES('" . $data['email'] . "', PASSWORD('" . $data['password']."'), '".$data['dashboard_id']."')";
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

	public function searchUser($email, $password)
	{
		$query = "SELECT * FROM users WHERE email='".$email."' AND password=PASSWORD('".$password."')";
		$query_result = $this->db->query($query)->result_array();

		return $query_result;
	}
	
}
