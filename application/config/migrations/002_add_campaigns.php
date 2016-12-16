<?php
class Migration_Add_Campaigns extends CI_Migration {
  public function up() {
    $this->dbforge->add_field('id');

    $this->dbforge->add_field(
      array(
        'title'       => array (
          'type'        => 'VARCHAR',
          'constraint'  => 32,
          'null'        => TRUE
        ),
        'url'         => array(
          'type'        => 'VARCHAR',
          'constraint'  => 128
        ),
        'thumbnail'   => array (
          'type'        => 'VARCHAR',
          'constraint'  => 128,
          'default'     => 'assets/images/thumbnails/campaigns/default.png'
        ),
        'view_ID'     => array (
          'type'        => 'INT',
          'constraint'  => 5,
          'unsigned'    => TRUE,
          'null'        => TRUE
        )
      )
    );

    $this->dbforge->create_table('campaigns');
  }

  public function down() {
    $this->dbforge->drop_table('campaigns');
  }
}
?>