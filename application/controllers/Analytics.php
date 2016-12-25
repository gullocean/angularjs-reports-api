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

    $analytics = $this->initializeAnalytics();

    try {

    	$results = $this->queryCoreReportingApi($analytics, $query);

    	$this->response['message']	= 'Success';
			$this->response['code'] 		= EXIT_SUCCESS; 
			$this->response['data'] 		= $results;

    } catch (apiServiceException $e) {
			print 'There was an Analytics API service error ' . $e->getCode() . ':' . $e->getMessage();
			$this->response['message'] = 'There was an Analytics API service error ' . $e->getCode() . ':' . $e->getMessage();
			$this->response['code'] = EXIT_ERROR;

		} catch (apiException $e) {
			print 'There was a general API error ' . $e->getCode() . ':' . $e->getMessage();
			$this->response['message'] = 'There was a general API error ' . $e->getCode() . ':' . $e->getMessage();
			$this->response['code'] = EXIT_ERROR;
		}

		echo json_encode($this->response);
	}

	public function getdata($key=null) {

		if (is_null($key)) return;

		$analytics = $this->initializeAnalytics();

		try {

			switch ($key) {

				case 'Sessions':

					$query = array(
						'table-id' 		=> 'ga:' . TABLE_ID,
						'metrics' 		=> 'ga:sessions, ga:pageviewsPerSession',
						'start-date' 	=> NMonthsAgoFirstDay(2, TODAY),
						'end-date' 		=> NMonthsAgoLastDay(0, TODAY),
						'dimensions' 	=> 'ga:month'
					);

					$cur = $this->queryCoreReportingApi($analytics, $query);

					$query['start-date'] 	= NMonthsAgoFirstDay(2, PreviousYearDay(TODAY));
					$query['end-date'] 		= PreviousYearDay(NMonthsAgoLastDay(0, TODAY));
					$prev = $this->queryCoreReportingApi($analytics, $query);

					$diff = [];

					$cnt_months = count($cur) != count($prev) ?: count($cur);
					$cnt_items 	= $cnt_months ?: count($cur[0]) - 1;
					$keys = ['Sessions', 'Pages / Session'];

					for ($j=0; $j < $cnt_items - 1; $j++) {
						$diff_item = [];
						for ($i=0; $i < $cnt_months; $i++) {
							$diff_item[] = array(
								'month' => $cur[$i][0],
								'value' => number_format(floatval($cur[$i][$j + 1]) - floatval($prev[$i][$j + 1]), 2)
							);
						}
						$diff[] = array('values' => $diff_item, 'key' => $keys[$j]);
					}
					
					$this->response['message'] = 'Success';
					$this->response['code'] = EXIT_SUCCESS;
					$this->response['data'] = $diff;

					break;

				case 'Landing_Pages':
					$query = array(
						'table-id' 		=> 'ga:' . TABLE_ID,
						'metrics' 		=> 'ga:pageviews, ga:entrances, ga:avgTimeOnPage',
						'start-date' 	=> NMonthsAgoFirstDay(3, TODAY),
						'end-date' 		=> TODAY,
						'dimensions' 	=> 'ga:pageTitle, ga:landingPagePath'
					);

					$results = $this->queryCoreReportingApi($analytics, $query);

					$this->response['message'] = 'Success';
					$this->response['code'] = EXIT_SUCCESS; 
					$this->response['data'] = $results;
					break;

				case 'Sessions_Channel':
					$query = array(
						'table-id' 		=> 'ga:' . TABLE_ID,
						'metrics' 		=> 'ga:sessions',
						'start-date' 	=> NMonthsAgoFirstDay(3, TODAY),
						'end-date' 		=> TODAY,
						'dimensions' 	=> 'ga:channelGrouping'
					);

					$results = $this->queryCoreReportingApi($analytics, $query);

					$this->response['message'] = 'Success';
					$this->response['code'] = EXIT_SUCCESS; 
					$this->response['data'] = $results;

					break;

				case 'Sessions_Device':
					$query = array(
						'table-id' 		=> 'ga:' . TABLE_ID,
						'metrics' 		=> 'ga:sessions',
						'start-date' 	=> NMonthsAgoFirstDay(3, TODAY),
						'end-date' 		=> TODAY,
						'dimensions' 	=> 'ga:deviceCategory'
					);

					$results = $this->queryCoreReportingApi($analytics, $query);

					$this->response['message'] = 'Success';
					$this->response['code'] = EXIT_SUCCESS; 
					$this->response['data'] = $results;

					break;

				case 'GBM_Local_Insights':
					# code...
					break;

				case 'Keyword_Ranking':
					# code...
					break;

				case 'PPC_Campaign':
					$query = array(
						'table-id' 		=> 'ga:' . TABLE_ID,
						'metrics' 		=> 'ga:adClicks, ga:adCost, ga:CTR, ga:CPC, ga:transactionRevenue, ga:goalCompletionsAll, ga:goalConversionRateAll',
						'start-date' 	=> MonthFirstDay(TODAY),
						'end-date' 		=> MonthLastDay(TODAY),
						'dimensions' 	=> 'ga:campaign'
					);

					$cur = $this->queryCoreReportingApi($analytics, $query);

					$query['start-date'] = MonthFirstDay(NMonthsAgo(1, TODAY));
					$query['end-date'] = MonthLastDay(NMonthsAgo(1, TODAY));

					$prev = $this->queryCoreReportingApi($analytics, $query);

					$diff = [];

					$cnt_campaign = count($cur) != count($prev) ?: count($cur);
					$cnt_items 	= $cnt_campaign < 0 ?: count($cur[0]) - 1;

					for ($i=0; $i < $cnt_campaign; $i++) {
						$diff_item = [$cur[$i][0]];
						for ($j=0; $j < $cnt_items; $j++) {
							if (floatval($prev[$i][$j + 1]) == 0) {
								if (floatval($cur[$i][$j + 1]) == 0) $diff_item[] = 0;
								else $diff_item[] = 100;
							}
							else $diff_item[] = number_format((floatval($cur[$i][$j + 1]) - floatval($prev[$i][$j + 1])) / floatval($prev[$i][$j + 1]), 2);
						}
						$diff[] = $diff_item;
					}
					
					$this->response['message'] = 'Success';
					$this->response['code'] = EXIT_SUCCESS;
					$this->response['data'] = $diff;

					break;

				case 'PPC_CTR':
					for ($i=0; $i < 3; $i++) { 
						$query = array(
							'table-id' 		=> 'ga:' . TABLE_ID,
							'metrics' 		=> 'ga:CTR',
							'start-date' 	=> MonthFirstDay (NMonthsAgo (2 - $i, TODAY)),
							'end-date' 		=> MonthLastDay (NMonthsAgo (2 - $i, TODAY)),
							'dimensions' 	=> 'ga:date'
						);

						$ctr = $this->queryCoreReportingApi($analytics, $query);

						if (is_null($ctr)) {
							$this->response['message'] = 'There is no data in PPC CTR';
							$this->response['code'] = EXIT_ERROR;
							break;
						}

						$this->response['data'][] = array(array('values' => $ctr, 'key' => GetYearMonth($query['start-date'])));
					}

					$this->response['message'] = 'Success';
					$this->response['code'] = EXIT_SUCCESS;

					break;

				case 'PPC_CPC':
					for ($i=0; $i < 3; $i++) { 
						$query = array(
							'table-id' 		=> 'ga:' . TABLE_ID,
							'metrics' 		=> 'ga:CPC',
							'start-date' 	=> MonthFirstDay (NMonthsAgo (2 - $i, TODAY)),
							'end-date' 		=> MonthLastDay (NMonthsAgo (2 - $i, TODAY)),
							'dimensions' 	=> 'ga:date'
						);

						$ctr = $this->queryCoreReportingApi($analytics, $query);

						if (is_null($ctr)) {
							$this->response['message'] = 'There is no data in PPC CPC';
							$this->response['code'] = EXIT_ERROR;

							break;
						}

						$this->response['data'][] = array(array('values' => $ctr, 'key' => GetYearMonth($query['start-date'])));
					}

					$this->response['message'] = 'Success';
					$this->response['code'] = EXIT_SUCCESS;

					break;

				case 'PPC_Cost':
					// for last 30 days
					$query = array(
						'table-id' 		=> 'ga:' . TABLE_ID,
						'metrics' 		=> 'ga:adCost',
						'start-date' 	=> NDaysAgo(30, TODAY),
						'end-date' 		=> TODAY,
						'dimensions' 	=> ''
					);

					$cost_cur = $this->queryCoreReportingApi($analytics, $query);

					if (is_null($cost_cur)) {
						$this->response['message'] = 'There is no data of PPC Converson Rate in last 30 days!';
						$this->response['code'] = EXIT_ERROR;
						break;
					}

					// for previous period
					$query['start-date'] = NDaysAgo(60, TODAY);
					$query['end-date'] = NDaysAgo(30, TODAY);

					$cost_prev = $this->queryCoreReportingApi($analytics, $query);

					if (is_null($cost_prev)) {
						$this->response['message'] = 'There is no data of PPC Converson Rate in previous period!';
						$this->response['code'] = EXIT_ERROR;

						break;
					}

					$cost_cur = floatval($cost_cur[0][0]);
					$cost_prev = floatval($cost_prev[0][0]);

					if (floatval($cost_prev) == 0) {
						if (floatval($cost_cur) == 0) {
							$this->response['data'] = 0;
						} else {
							$this->response['data'] = 100;
						}
					} else {
						$this->response['data'] = ($cost_cur - $cost_prev) / $cost_prev;
					}

					$this->response['message'] = 'Success';
					$this->response['code'] = EXIT_SUCCESS;

					break;

				case 'PPC_Conversions':
					# code...
					break;

				case 'PPC_CPA_Box':
					# code...
					break;

				case 'PPC_Conversion_Rate':
					// for last 30 days
					$query = array(
						'table-id' 		=> 'ga:' . TABLE_ID,
						'metrics' 		=> 'ga:goalConversionRateAll',
						'start-date' 	=> NDaysAgo(30, TODAY),
						'end-date' 		=> TODAY,
						'dimensions' 	=> ''
					);

					$conv_rate_cur = $this->queryCoreReportingApi($analytics, $query);

					if (is_null($conv_rate_cur)) {
						$this->response['message'] = 'There is no data of PPC Converson Rate in last 30 days!';
						$this->response['code'] = EXIT_ERROR;

						break;
					}

					// for previous period
					$query['start-date'] = NDaysAgo(60, TODAY);
					$query['end-date'] = NDaysAgo(30, TODAY);

					$conv_rate_prev = $this->queryCoreReportingApi($analytics, $query);

					if (is_null($conv_rate_prev)) {
						$this->response['message'] = 'There is no data of PPC Converson Rate in previous period!';
						$this->response['code'] = EXIT_ERROR;

						break;
					}

					$conv_rate_cur = floatval($conv_rate_cur[0][0]);
					$conv_rate_prev = floatval($conv_rate_prev[0][0]);

					if (floatval($conv_rate_prev) == 0) {
						if (floatval($conv_rate_cur) == 0) {
							$this->response['data'] = 0;
						} else {
							$this->response['data'] = 100;
						}
					} else {
						$this->response['data'] = ($conv_rate_cur - $conv_rate_prev) / $conv_rate_prev;
					}

					$this->response['message'] = 'Success';
					$this->response['code'] = EXIT_SUCCESS;

					break;

				case 'PPC_Overview':
					# code...
					break;

				case 'Phone_Calls':
					# code...
					break;

				case 'Goal_Completions':
					$query = array(
						'table-id' 		=> 'ga:' . TABLE_ID,
						'metrics' 		=> 'ga:goalCompletionsAll',
						'start-date' 	=> NDaysAgo(30, TODAY),
						'end-date' 		=> TODAY,
						'dimensions' 	=> ''
					);

					$cur = $this->queryCoreReportingApi($analytics, $query);

					if (is_null($cur)) {
						$this->response['message'] = 'There is no data in last 30 days!';
						$this->response['code'] = EXIT_ERROR;

						break;
					}

					$query['start-date'] = NDaysAgo(60, TODAY);
					$query['end-date'] = NDaysAgo(31, TODAY);
					
					$prev = $this->queryCoreReportingApi($analytics, $query);
					if (is_null($prev)) {
						$this->response['message'] = 'There is no data in previous period!';
						$this->response['code'] = EXIT_ERROR;

						break;
					}

					$diff = 0;

					if (floatval($prev[0][0]) === 0) {
						$diff = 100;
					} else {
						$diff = (floatval($cur) - floatval($prev)) / floatval($prev);
					}
					
					$this->response['message'] = 'Success';
					$this->response['code'] = EXIT_SUCCESS;
					$this->response['data'] = $diff;

					break;

				case 'Conversion_Value':
					$query = array(
						'table-id' 		=> 'ga:' . TABLE_ID,
						'metrics' 		=> 'ga:goalConversionRateAll',
						'start-date' 	=> NDaysAgo(30, TODAY),
						'end-date' 		=> TODAY,
						'dimensions' 	=> ''
					);

					$cur = $this->queryCoreReportingApi($analytics, $query);

					if (is_null($cur)) {
						$this->response['message'] = 'There is no data in last 30 days!';
						$this->response['code'] = EXIT_ERROR;

						break;
					}

					$query['start-date'] = NDaysAgo(60, TODAY);
					$query['end-date'] = NDaysAgo(31, TODAY);

					$prev = $this->queryCoreReportingApi($analytics, $query);

					if (is_null($prev)) {
						$this->response['message'] = 'There is no data in previous period!';
						$this->response['code'] = EXIT_ERROR;

						break;
					}

					$diff = 0;

					if (floatval($prev[0][0]) === 0) {
						$diff = 100;
					} else {
						$diff = (floatval($cur) - floatval($prev)) / floatval($prev);
					}
					
					$this->response['message'] = 'Success';
					$this->response['code'] = EXIT_SUCCESS;
					$this->response['data'] = $diff;

					break;
				
				default:
					$this->response['message'] = 'Invalid Request!';
					$this->response['code'] = EXIT_ERROR;

					break;
			}
		} catch (apiServiceException $e) {
			print 'There was an Analytics API service error ' . $e->getCode() . ':' . $e->getMessage();
			$this->response['message'] = 'There was an Analytics API service error ' . $e->getCode() . ':' . $e->getMessage();
			$this->response['code'] = EXIT_ERROR;
		} catch (apiException $e) {
			print 'There was a general API error ' . $e->getCode() . ':' . $e->getMessage();
			$this->response['message'] = 'There was a general API error ' . $e->getCode() . ':' . $e->getMessage();
			$this->response['code'] = EXIT_ERROR;
		}
		
		echo json_encode($this->response);
	}

	private function queryCoreReportingApi($service, $query) {
		try {
			$response = $service->data_ga->get(
				$query['table-id'],
				$query['start-date'],
				$query['end-date'],
				$query['metrics'],
				array('dimensions' 	=> $query['dimensions'])
			);
			$rows = $response -> getRows();
			return $rows;
		} catch (apiServiceException $e) {
			print 'There was an Analytics API service error ' . $e->getCode() . ':' . $e->getMessage();
		} catch (apiException $e) {
			print 'There was a general API error ' . $e->getCode() . ':' . $e->getMessage();
		} /*catch (Exception $e) {
			print 'There is an exception(' . $e->getCode() . '):' . $e->getMessage();
		}*/
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

	private function getFirstProfileId($analytics) {
		// Get the list of accounts for the authorized user.
		$accounts = $analytics->management_accounts->listManagementAccounts();

		if (count($accounts->getItems()) > 0) {
			$items = $accounts->getItems();
			$firstAccountId = $items[0]->getId();

			// Get the list of properties for the authorized user.
			$properties = $analytics->management_webproperties->listManagementWebproperties($firstAccountId);

			if (count($properties->getItems()) > 0) {
				$items = $properties->getItems();
				$firstPropertyId = $items[0]->getId();

				// Get the list of views (profiles) for the authorized user.
				$profiles = $analytics->management_profiles->listManagementProfiles($firstAccountId, $firstPropertyId);

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
		} else {
			throw new Exception('No accounts found for this user.');
		}
	}
}
?>