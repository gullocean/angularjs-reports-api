<?php
class Migration_Add_tickets extends CI_Migration {
  public function up() {
    $this->dbforge->add_field('id');

    $this->dbforge->add_field(
      array(
        'token'       => array(
          'type'         => 'VARCHAR',
          'constraint'   => 32,
          'null'         => TRUE,
        ),
        'user_id'    => array (
          'type'         => 'INT',
          'constraint'   => 32,
          'unsigned'     => TRUE
        )
      )
    );

    $this->dbforge->create_table('tickets');
  }

  public function down() {
    $this->dbforge->drop_table('tickets');
  }
}
?>