<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Gmo_api_logs extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id'       => [
                'type'           => 'INT',
                'unsigned'       => TRUE,
                'auto_increment' => TRUE
            ],
            'api_name'    => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'parameter' => [
                'type'       => 'JSON'
            ],
            'result' => [
                'type'       => 'JSON'
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('gmo_api_logs');
    }

    public function down()
    {
        $this->dbforge->drop_table('gmo_api_logs');
    }
}