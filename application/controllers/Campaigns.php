<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  class Campaigns extends CI_Controller {

    public $response;

    public function __construct () {
      parent::__construct();
    }

    public function index () {
      echo 'campaigns';
    }

    public function get ($id = null) {

      $this->load->model('campaigns_model');

      $campaigns = $this->campaigns_model->get($id);

      if (is_null($campaigns)) {
        $this->response['code']     = EXIT_ERROR;
        $this->response['message']  = 'There is no campaigns!';
      } else {
        $this->response['code'] = EXIT_SUCCESS;
        $this->response['data'] = $campaigns;
      }

      echo json_encode($this->response);
    }

    public function add() {

      $campaign_data = array(
        'company'   => $this->input->post('company'),
        'url'       => $this->input->post('url'),
        'thumbnail' => $this->input->post('thumbnail'),
        'view_ID'   => $this->input->post('view_ID')
      );

      $dir = dirname (BASEPATH);
      $dir = dirname ($dir);
      $dir = $dir . '/assets/images/thumbnails/campaigns/';

      $date = new DateTime ();
      $fileName = $campaign_data['company'] . $date->format ('YmdHis') . '.jpg';

      $filePath = $dir . $fileName;

      $ifp = fopen($filePath, 'wb');

      $data = explode(',', $campaign_data['thumbnail']);

      fwrite($ifp, base64_decode($data[1]));

      fclose($ifp);

      $campaign_data['thumbnail'] = 'assets/images/thumbnails/campaigns/' . $fileName;

      $this->load->model('campaigns_model');

      $this->response['code']     = EXIT_SUCCESS;
      $this->response['data']     = array (
        'id'        => $this->campaigns_model->create($campaign_data),
        'company'   => $campaign_data['company'],
        'url'       => $campaign_data['url'],
        'thumbnail' => $filePath,
        'view_ID'   => $campaign_data['view_ID']
      );
      $this->response['message']  = 'Successfully added!';

      echo json_encode($this->response);
    }

    public function update() {

      $campaign_data = array(
        'id'        => $this->input->post('id'),
        'company'   => $this->input->post('company'),
        'url'       => $this->input->post('url'),
        'thumbnail' => $this->input->post('thumbnail'),
        'view_ID'   => $this->input->post('view_ID')
      );

      $this->load->model('campaigns_model');

      $this->response['code']     = $this->campaigns_model->update($campaign_data);
      $this->response['message']  = 'Successfully updated!';

      echo json_encode($this->response);
    }

    public function delete($id) {

      $this->load->model('campaigns_model');

      $this->response['code'] = $this->campaigns_model->delete($id);
      if ($this->response['code'] == EXIT_SUCCESS) {
        $this->response['message'] = 'Successfully deleted!';
      } else {
        $this->response['message'] = 'Invalide campaign ID!';
      }

      echo json_encode($this->response);
    }
  }
?>