<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  Class Campaigns_model extends CI_Model {

    public $cols = array ('id', 'company', 'url', 'thumbnail', 'view_ID');

    public function __construct() {
      parent::__construct();
      $this->load->database("campaigns");
    }

    function get($id) {

      if (!is_null($id)) $this->db->where('id', $id);

      $query = $this->db->get(CAMPAIGNS_TABLE);
      
      if ($query->num_rows() > 0) {
        if (is_null($id)) {
          return $query->result();
        } else {
          return $query->row();
        }
      } 
      else return null;
    }

    function create($campaign_data) {
      if ($campaign_data === NULL) return EXIT_ERROR;
      $data = array();
      foreach ($this->cols as $col) {
        if (array_key_exists($col, $campaign_data)) {
          $data[$col] = $campaign_data[$col];
        }
      }
      if ($this->db->insert(CAMPAIGNS_TABLE, $data)) return $this->db->insert_id();
      else return -1;
    }

    function update($campaign_data) {
      if ($campaign_data === NULL) return EXIT_ERROR;
      $data = array();
      foreach ($this->cols as $col) {
        if (array_key_exists($col, $campaign_data)) {
          $data[$col] = $campaign_data[$col];
        }
      }
      $this->db->where('id', $data['id']);
      if ($this->db->update(CAMPAIGNS_TABLE, $data)) return EXIT_SUCCESS;
      else return EXIT_ERROR;
    }

    function delete($id) {
      if (is_null($id)) return EXIT_ERROR;
      $this->db->where('id', $id);
      if ($this->db->delete(CAMPAIGNS_TABLE)) return EXIT_SUCCESS;
      else return EXIT_ERROR;
    }
  }
?>