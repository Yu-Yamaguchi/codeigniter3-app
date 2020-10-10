<?php

class Gmo_result_notification_logs_model extends CI_Model {

    public $id;
    public $pay_method;
    public $parameter;
    public $create_date;

    private $_table = 'gmo_result_notification_logs';

    public function __construct() {
        parent::__construct();
    }

    public function insert_log() {
        $this->db->insert($this->_table, $this);
    }

    public function get_last_ten_logs() {
        $query = $this->db->limit(10)
            ->order_by('id', 'desc')
            ->get($this->_table, 10);
        return $query->result();
    }
}