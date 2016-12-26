<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once BASEPATH . '../vendor/autoload.php';

class Analytics extends CI_Controller {
	
	public $response;

	public function index() {

	}

	public function get () {

		$query = array(
      'table-id'   => $this->input->post('table_id'),
      'metrics'    => $this->input->post('metrics'),
      'start-date' => $this->input->post('start_date'),
      'end-date'   => $this->input->post('end_date'),
      'dimensions' => $this->input->post('dimensions')
    );

    $analytics 	= $this->initializeAnalytics();

  	$results 		= $this->queryCoreReportingApi($analytics, $query);

		echo json_encode($results);
	}

	public function checkID ($id) {
		$analytics = $this->initializeAnalytics();
		$accounts  = $this->getAccounts($analytics);
		$key 			 = EXIT_ERROR;
		foreach ($accounts as $account) {
			if ($account->id == $id) {
				$key = EXIT_SUCCESS;
				break;
			}
		}
		$response['code'] 		= $key;
		$response['message'] 	= 'true';
		echo json_encode($response);
	}

	public function getAccountList () {
		$analytics = $this->initializeAnalytics();
		try {
			$accounts  = $this->getAccounts($analytics);

			$response['code'] 		= EXIT_SUCCESS;
			$response['message'] 	= 'Success';
			foreach ($accounts as $account) {
				if ($account->kind == 'analytics#account') {
					// array_splice ($response['data'], $key, 1);
					$response['data'][] = [
						'name' 	=> $account['name'],
						'id' 		=> $this->getFirstProfileId ($analytics, $account->id)
					];
				}
			}
		} catch (Exception $e) {
			$response['code'] 		= EXIT_ERROR;
			$response['message'] 	= $e->getMessage ();
		}

		echo json_encode($response);
	}

	private function queryCoreReportingApi ($service, $query) {
		try {

			$response['data'] 		= $service->data_ga->get($query['table-id'], $query['start-date'], $query['end-date'], $query['metrics'], array ('dimensions' 	=> $query['dimensions'])) -> getRows();
			$response['code'] 		= EXIT_SUCCESS;
			$response['message'] 	= 'Success';

			return $response;
		} catch (apiServiceException $e) {
			print 'There was an Analytics API service error ' . $e->getCode() . ':' . $e->getMessage();
		} catch (apiException $e) {
			print 'There was a general API error ' . $e->getCode() . ':' . $e->getMessage();
		} catch (Exception $e) {
			$response['message'] 	= $e->getMessage ();
			$response['code'] 		= $e->getCode ();

			return $response;
		}
	}
	private function initializeAnalytics() {
		// change the key file location if necessary.
		$KEY_FILE_LOCATION = BASEPATH . KEY_FILE_LOCATION;

		// Create and configure a new client object.
		$client = new Google_Client();
		$client->setApplicationName("Hello Analytics Reporting");
		$client->setAuthConfig($KEY_FILE_LOCATION);
		$client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
		// var_dump($client);
		$analytics = new Google_Service_Analytics($client);

		return $analytics;
	}

	private function getAccounts ($analytics) {
		// Get the list of accounts for the authorized user.
		$accounts = $analytics->management_accounts->listManagementAccounts();

		if (count($accounts->getItems()) > 0) {
			$items = $accounts->getItems();
			return $items;
		} else {
			throw new Exception('No accounts found for this user.');
		}
	}

	private function getFirstProfileId ($analytics, $accountID) {
		// Get the list of properties for the authorized user.
		$properties = $analytics->management_webproperties->listManagementWebproperties($accountID);

		if (count($properties->getItems()) > 0) {
			$items = $properties->getItems();
			$firstPropertyId = $items[0]->getId();

			// Get the list of views (profiles) for the authorized user.
			$profiles = $analytics->management_profiles->listManagementProfiles($accountID, $firstPropertyId);

			if (count($profiles->getItems()) > 0) {
				$items = $profiles->getItems();

				// Return the first view (profile) ID.
				return $items[0]->getId();

			} else {
				throw new Exception('No views (profiles) found for this user.');
			}
		} else {
			throw new Exception('No properties found for this user.');
		}
	}
}
?>