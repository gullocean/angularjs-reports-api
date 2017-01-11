<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  Class Pairs_model extends CI_Model {

    public $cols = array ( 'id', 'campaign_id', 'user_id' );

    public function __construct() {
      parent::__construct();
      $this->load->database(CAMPAIGN_USER_TABLE);
    }

    function get( $userID, $campaignID = '' ) {
      if ( !is_null( $userID ) ) $this->db->where( 'user_id', $userID );
      if ( !empty( $campaignID ) ) $this->db->where( 'campaign_id', $campaignID );

      $query = $this->db->get( CAMPAIGN_USER_TABLE );

      if ( $query->num_rows() <= 0 ) return '';
      
      if ( is_null( $userID ) ) {
        return $query->result();
      } else {
        return $query->row()->campaign_id;
      }
    }

    function create( $pair_data ) {
      if ( $pair_data === NULL ) return EXIT_ERROR;
      $this->db->where( 'user_id', $pair_data[ 'user_id' ] );
      $this->db->delete( CAMPAIGN_USER_TABLE );
      $this->db->where( 'campaign_id', $pair_data[ 'campaign_id' ] );
      return $this->db->insert( CAMPAIGN_USER_TABLE, $pair_data );
    }

    function update( $pair_data ) {
      if ( $pair_data === NULL ) return EXIT_ERROR;
      $this->db->where( 'user_id', $pair_data[ 'user_id' ] );
      $this->db->delete( CAMPAIGN_USER_TABLE );
      // $query = $this->db->get( CAMPAIGN_USER_TABLE );
      
      // if ( $query->num_rows() <= 0 )
        return $this->db->insert( CAMPAIGN_USER_TABLE, $pair_data );

      // return $this->db->update( CAMPAIGN_USER_TABLE, $pair_data );
    }

    function delete( $id ) {
      if ( is_null( $id ) ) return EXIT_ERROR;
      $this->db->where( 'id', $id );
      if ( $this->db->delete( CAMPAIGN_USER_TABLE ) ) return EXIT_SUCCESS;
      else return EXIT_ERROR;
    }
  }
?>