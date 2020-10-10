<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Gmo_result_notification_logs extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id'       => [
                'type'           => 'int',
                'unsigned'       => TRUE,
                'auto_increment' => TRUE
            ],
            'pay_method'    => [
                'type'       => 'varchar',
                'constraint' => '10',
            ],
            'parameter' => [
                'type'       => 'json'
            ],
            'create_date' => [
                'type'       => 'datetime'
            ]
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('gmo_result_notification_logs');
    }

    public function down()
    {
        $this->dbforge->drop_table('gmo_result_notification_logs');
    }
}