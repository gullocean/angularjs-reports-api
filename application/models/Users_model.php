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

		function auth( $email, $password ) {
			if ( empty( $email ) ) return EXIT_ERROR;
			if ( empty( $password ) ) return EXIT_ERROR;

			$this->db->where( 'email', $email );
			$this->db->where( 'password', $password );

			$query = $this->db->get( USERS_TABLE );

			if ( $query->num_rows() === 1 )
				return $query->row();

			return EXIT_ERROR;
		}

		function get( $id = '' ) {
			if ( empty( $id ) ) return '';

			$this->db->where( 'id', $id );
			$query = $this->db->get(USERS_TABLE);
			
			if ($query->num_rows() > 0)
				return $query->row();
			
			return '';
		}

		function get_by_role( $role = '' ) {
			if ( !empty( $role ) ) $this->db->where( 'role', $role );

			$query = $this->db->get(USERS_TABLE);
			
			if ($query->num_rows() > 0)
				return $query->result();
			
			return EXIT_ERROR;
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
			if ($this->db->insert(USERS_TABLE, $data)) return $this->db->insert_id();
			else return -1;
		}

		function update($user_data) {
			if (is_null($user_data['id'])) return EXIT_ERROR;
			$data = array();
			foreach ($this->cols as $col) {
				if ( array_key_exists( $col, $user_data ) && !is_null( $user_data[ $col ] ) ) {
					$data[ $col ] = $user_data[ $col ];
				}
			}
			$this->db->where('id', $user_data['id']);
			if ($this->db->update(USERS_TABLE, $data)) return EXIT_SUCCESS;
			else return EXIT_ERROR;
		}

		function delete($id) {
			if ($id == NULL) return EXIT_ERROR;
			$this->db->where('id', $id);
			if ($this->db->delete(USERS_TABLE)) return EXIT_SUCCESS;
			else return EXIT_ERROR;
		}
	}
?>