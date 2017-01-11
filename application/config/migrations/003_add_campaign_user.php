<?php
class Migration_Add_Campaign_User extends CI_Migration {
  public function up() {
    $this->dbforge->add_field('id');

    $this->dbforge->add_field(
      array(
        'campaign_id' => array (
          'type'      => 'INT',
          'unsigned'  => TRUE,
          'null'      => TRUE
        ),
        'user_id'     => array (
          'type'      => 'INT',
          'unsigned'  => TRUE,
          'null'      => TRUE
        )
      )
    );

    $this->dbforge->create_table('campaign_user');
  }

  public function down() {
    $this->dbforge->drop_table('campaign_user');
  }
}
?>