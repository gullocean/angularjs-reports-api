<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	Class Users_model extends CI_Model {
		public $email;
		public $password;
		public $username;
		public $cols = array ('email', 'password', 'role', 'username');

		public function __construct() {
			parent::__construct();
		}

		function get($email, $username, $role) {
			$this->db->select("*");
			if (!is_null($email)) $this->db->where('email', $email);
			if (!is_null($username)) $this->db->where('username', $username);
			if (!is_null($role)) $this->db->where('role', $role);
			$query = $this->db->get(USERS_TABLE);
			if ($query->num_rows() > 0) return $query->result();
			else return EXIT_ERROR;
		}

		function is_exist($email) {
			$this->db->where('email', $email);
			$query = $this->db->get(USERS_TABLE);
			if ($query->num_rows() > 0) return EXIT_SUCCESS;
			else return EXIT_ERROR;
		}

		function create($user_data) {
			if (is_null($user_data['email'])) return EXIT_ERROR;
			$data = array();
			foreach ($this->cols as $col) {
				$data[$col] = $user_data[$col];
			}
			if ($this->db->insert(USERS_TABLE, $data)) return EXIT_SUCCESS;
			else return EXIT_ERROR;
		}

		function update($user_data, $oldEmail) {
			if (is_null($user_data['email'])) return EXIT_ERROR;
			$data = array();
			foreach ($this->cols as $col) {
				$data[$col] = $user_data[$col];
			}
			$this->db->where('email', $oldEmail);
			if ($this->db->update(USERS_TABLE, $data)) return EXIT_SUCCESS;
			else return EXIT_ERROR;
		}

		function delete($email) {
			if (is_null($email)) return EXIT_ERROR;
			$this->db->where('email', $email);
			if ($this->db->delete(USERS_TABLE)) return EXIT_SUCCESS;
			else return EXIT_ERROR;
		}
	}
?>