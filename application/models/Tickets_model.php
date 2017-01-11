<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  Class Tickets_model extends CI_Model {

    public function __construct() {
      parent::__construct();
    }

    function get_by_user_id( $user_id = '' ) {
      if ( empty( $user_id ) ) return EXIT_ERROR;

      $this->db->where( 'user_id', $user_id );
      $query = $this->db->get( TICKETS_TABLE );
      
      if ( $query->num_rows() > 0 )
        return $query->row();
      
      return EXIT_ERROR;
    }

    function get_by_token( $token = '' ) {
      if ( empty( $token ) ) return EXIT_ERROR;

      $this->db->where( 'token', $token );
      $query = $this->db->get( TICKETS_TABLE );
      
      if ( $query->num_rows() > 0 )
        return $query->row();
      
      return EXIT_ERROR;
    }

    function insert( $user_id = '', $token = '' ) {
      if ( empty( $user_id ) || empty( $token ) ) return EXIT_ERROR;

      $data = array(
        'user_id' => $user_id,
        'token'   => $token
      );

      if ( $this->db->insert( TICKETS_TABLE, $data ) ) return EXIT_SUCCESS;
      return EXIT_ERROR;
    }

    function delete( $user_id = '' ) {
      if ( empty( $user_id ) ) return EXIT_ERROR;

      $this->db->where( 'user_id', $user_id );

      if ( $this->db->delete( TICKETS_TABLE ) ) return EXIT_SUCCESS;
      return EXIT_ERROR;
    }
  }
?>