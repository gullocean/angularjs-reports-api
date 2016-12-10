<?php
class Migration_Add_users extends CI_Migration {
	public function up() {
		$this->dbforge->add_field(
			array(
        'email' => array(
          'type' => 'VARCHAR',
          'constraint' => '100',
          'null' => true,
        ),
        'password' => array (
          'type' => 'VARCHAR',
          'constraint' => '32',
          'null' => true
        ),
				'username' => array(
					'type' => 'VARCHAR',
					'constraint' => '32',
				),
        'role' => array (
          'type' => 'INT',
          'unsigned' => true
        )
			)
		);

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('users');
	}

	public function down() {
		$this->dbforge->drop_table('users');
	}
}
?>