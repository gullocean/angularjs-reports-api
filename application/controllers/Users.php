<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	class Users extends CI_Controller {

		public $response;

		public function __construct () {
			parent::__construct();
		}

		public function index() {
			
		}

		public function auth() {
			$email 		= $this->input->post( 'email' );
			$password = $this->input->post( 'password' );

			$this->load->model( 'users_model' );

			$user = $this->users_model->auth( $email, $password );

			if ( !is_object( $user ) && $user == EXIT_ERROR) {
				$this->response = array (
					'code' 		=> EXIT_ERROR,
					'message' => 'Invalide username or password!'
				);
			} else {
				$this->load->model('pairs_model');

				$user->campaignID = $this->pairs_model->get( $user->id );
				$user->password 	= null;

				$this->load->model( 'tickets_model' );
				$token = md5( $user->id . date( 'mdY_His' ) );

				$this->tickets_model->insert(
					$user->id,
					md5( $user->id . date( 'mdY_His' ) )
				);

				$this->response = array (
					'code' 		=> EXIT_SUCCESS,
					'message' => 'Success',
					'data' 		=> $user,
					'token'		=> $token
				);
			}

			echo json_encode( $this->response );
		}

		public function logout() {
			$token = $this->input->get_request_header( 'token', TRUE );

			if ( empty( $token ) ) return;
			
			$this->load->model( 'tickets_model' );
			
			$ticket = $this->tickets_model->get_by_token( $token );

			$this->tickets_model->delete( $ticket->user_id );
		}

		public function get( $id = '' ) {
			$token = $this->input->get_request_header( 'token', TRUE );

			if ( empty( $token ) ) return;
			
			$this->load->model( 'tickets_model' );
			$this->load->model( 'users_model' );
			$this->load->model('pairs_model');
			
			$ticket 			= $this->tickets_model->get_by_token( $token );
			$current_user = $this->users_model->get( $ticket->user_id );

			if ( empty( $id ) ) {
				$users	= [];

				switch ( $current_user->role ) {
					case ROLE_ADMIN:
						$users 	= $this->users_model->get_by_role();
						break;

					case ROLE_PM:
						$users 	= $this->users_model->get_by_role( ROLE_CLIENT );
						break;

					default:
						break;
				}

				if ( $users == EXIT_ERROR || count( $users ) < 1 ) {
					$this->response['code'] 		= EXIT_ERROR;
					$this->response['message'] 	= 'There is no users!';
				} else {
					$this->response['code'] 		= EXIT_SUCCESS;

					foreach ($users as $key => $user) {
						if ( $users[ $key ]->role == ROLE_CLIENT ) {
							$users[ $key ]->campaignID = $this->pairs_model->get( $user->id );
						}

						unset( $users[ $key ]->password );
					}
					$this->response['data'] = $users;
				}
			} else {
				$user = $this->users_model->get( $id );

				if ( empty( $user ) ) {
					$this->response['code'] 		= EXIT_ERROR;
					$this->response['message'] 	= 'There is no user with this id( ' . $id . ')!';
					echo json_encode($this->response);
					return;
				}

				unset( $user->password );

				$this->response['code'] 		= EXIT_SUCCESS;
				$this->response['message'] 	= 'Success!';
				$this->response['data'] 		= $user;

				if ( $user->role == ROLE_CLIENT ) {
					$user->campaignID = $this->pairs_model->get( $id );
					if ( empty( $user->campaignID ) ) {
						$this->response['message'] = 'This user does not have campaign!';
					}
				}
			}
			
			echo json_encode( $this->response );
		}

		public function add() {
			$user_data = array();
			$pair_data = array();

			$user_data['email'] 		= $this->input->post('email');
			$user_data['password'] 	= $this->input->post('password');
			$user_data['username'] 	= $this->input->post('username');
			$user_data['role'] 			= $this->input->post('role');

			$this->load->model('users_model');
			if ($this->users_model->is_exist($user_data['email']) == EXIT_SUCCESS) {
				$this->response['code'] 		= EXIT_ERROR;
				$this->response['message'] 	= 'User already exist!';
			} else {
				$this->response['code'] 		= EXIT_SUCCESS;
				$this->response['message'] 	= 'Successfully added!';
				$this->response['data'] 		= array( 'id' => $this->users_model->create( $user_data ) );
				
				$pair_data['campaign_id'] 	= $this->input->post('campaignID');
				$pair_data['user_id'] 			= $this->response['data']['id'];

				$this->load->model('pairs_model');
				$this->pairs_model->create( $pair_data );
			}

			echo json_encode($this->response);
		}

		public function update() {
			$user_data = array(
				'id' 				=> $this->input->post( 'id' ),
				'email' 		=> $this->input->post( 'email' ),
				'password' 	=> $this->input->post( 'password' ),
				'username' 	=> $this->input->post( 'username' ),
				'role' 			=> $this->input->post( 'role' )
			);

			$pair_data = array(
				'user_id' 		=> $this->input->post( 'id' ),
				'campaign_id' => $this->input->post( 'campaignID' )
			);

			$this->load->model('pairs_model');
			$this->pairs_model->update($pair_data);

			$this->load->model('users_model');
			$this->response['code'] 		= $this->users_model->update($user_data);
			$this->response['message'] 	= 'Successfully updated!';

			echo json_encode($this->response);
		}

		public function check() {
			$email = $this->input->post('email');
			if ($email == NULL) return;
			$this->load->model('users_model');
			$this->response['code'] = $this->users_model->is_exist($email);
			if ($this->response['code'] == EXIT_SUCCESS) {
				$this->response['message'] = 'This email(' . $email . ') already exists!';
			} else {
				$this->response['message'] = 'There is no email same to ' .$email . ' !';
			}
			echo json_encode($this->response);
		}

		public function delete($id) {
			$this->load->model( 'pairs_model' );
			// $this->pairs_model->delete( $)

      $this->load->model('users_model');

      $this->response['code'] = $this->users_model->delete($id);
      if ($this->response['code'] == EXIT_SUCCESS) {
        $this->response['message'] = 'Successfully deleted!';
      } else {
        $this->response['message'] = 'Invalide campaign ID!';
      }

      echo json_encode($this->response);
    }
	}
?>