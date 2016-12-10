<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class Users extends CI_Controller {

		public $response;

		public function __construct () {
			parent::__construct();
		}

		public function index () {
			
		}

		public function auth () {
			$email 		= $this->input->post('email');
			$password = $this->input->post('password');
			$this->load->model('users_model');
			$users = $this->users_model->get($email, null, null);
			if (!is_object($users) && $users == EXIT_ERROR) {
				$this->response['code'] = EXIT_ERROR;
			} else {
				if ($users[0]->password == $password) {
					$this->response['code'] = EXIT_SUCCESS;
					$this->response['data'] = $users[0];
					$this->response['data']->password = null;
				} else {
					$this->response['code'] = EXIT_ERROR;
				}
			}
			echo json_encode($this->response);
		}

		public function get () {
			$email 		= $this->input->post('email');
			$username = $this->input->post('username');
			$role 		= $this->input->post('role');
			$users 		= [];
			$this->load->model('users_model');
			if (is_null($role)) {
				//
			} elseif ($role == ROLE_ADMIN) {
				$users = $this->users_model->get(null, null, null);
			} elseif ($role == ROLE_PM) {
				$users = $this->users_model->get(null, null, ROLE_CLIENT);
			}
			if ($users == EXIT_ERROR || count($users) < 1) {
				$this->response['code'] = EXIT_ERROR;
				$this->response['message'] = 'There is no users!';
			} else {
				$this->response['code'] = EXIT_SUCCESS;
				$this->response['data'] = $users;
			}
			echo json_encode($this->response);
		}

		public function add () {
			$user_data = array();
			$user_data['email'] = $this->input->post('email');
			$user_data['password'] = $this->input->post('password');
			$user_data['username'] = $this->input->post('username');
			$user_data['role'] = $this->input->post('role');
			$this->load->model('users_model');
			if ($this->users_model->is_exist($user_data['email']) == EXIT_SUCCESS) {
				$this->response['code'] = EXIT_ERROR;
				$this->response['message'] = 'User already exist!';
			} else {
				$this->response['code'] = $this->users_model->create($user_data);
				$this->response['message'] = 'Successfully added!';
			}
			echo json_encode($this->response);
		}

		public function update () {
			$user_data = array();
			$oldEmail = $this->input->post('oldEmail');
			$user_data['email'] = $this->input->post('email');
			$user_data['password'] = $this->input->post('password');
			$user_data['username'] = $this->input->post('username');
			$user_data['role'] = $this->input->post('role');
			$this->load->model('users_model');
			if ($this->users_model->is_exist($oldEmail) == EXIT_SUCCESS) {
				$this->response['code'] = $this->users_model->update($user_data, $oldEmail);
				$this->response['message'] = 'Successfully updated!';
			} else {
				$this->response['code'] = EXIT_ERROR;
				$this->response['message'] = 'There is no user!';
			}
			echo json_encode($this->response);
		}

		public function check () {
			$email = $this->input->post('email');
			$this->load->model('users_model');
			$this->response['code'] = $this->users_model->is_exist($email);
			echo json_encode($this->response);
		}

		public function delete () {
			$email = $this->input->post('email');
			$this->load->model('users_model');
			if ($this->users_model->is_exist($email) == EXIT_SUCCESS) {
				$this->response['code'] = $this->users_model->delete($email);
				$this->response['message'] = 'Successfully deleted!';
			} else {
				$this->response['code'] = EXIT_ERROR;
				$this->response['message'] = 'There is no user!';
			}
			echo json_encode($this->response);
		}
	}
?>