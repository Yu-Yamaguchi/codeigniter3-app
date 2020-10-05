<?php

class Gmo_api_logs_model extends CI_Model {

    public $id;
    public $api_name;
    public $parameter;
    public $result;

    private $_table = 'gmo_api_logs';

    public function __construct() {
        parent::__construct();
    }
    public function get_last_ten_logs() {
        $query = $this->db->limit(10)
            ->order_by('id', 'desc')
            ->get($this->$_table, 10);
        return $query->result();
    }

    public function insert_log() {
        $this->db->insert($this->$_table, $this);
    }
}