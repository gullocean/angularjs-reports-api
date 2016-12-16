<?php
class Migration_Add_users extends CI_Migration {
	public function up() {
    $this->dbforge->add_field('id');

		$this->dbforge->add_field(
			array(
        'email'       => array(
          'type'         => 'VARCHAR',
          'constraint'   => 100,
          'null'         => TRUE,
        ),
        'password'    => array (
          'type'         => 'VARCHAR',
          'constraint'   => 32,
          'null'         => TRUE
        ),
				'username'    => array(
					'type'         => 'VARCHAR',
					'constraint'   => 32,
          'null'         => TRUE
				),
        'role'        => array (
          'type'         => 'INT',
          'constraint'   => 1,
          'unsigned'     => TRUE,
          'default'      => 2
        ),
        'avatar_url'  => array (
          'type'        => 'VARCHAR',
          'constraint'  => 128,
          'default'     => 'assets/images/avatars/profile.jpg'
        )
			)
		);

		$this->dbforge->create_table('users');
	}

	public function down() {
		$this->dbforge->drop_table('users');
	}
}
?>