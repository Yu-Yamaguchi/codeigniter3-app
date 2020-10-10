<?php

/**
 * GMOから実行される結果通知プログラムのログ情報モデルクラス
 */
class Gmo_result_notification_logs_model extends CI_Model {

    /** ID（自動採番） */
    public $id;
    /** 決済方法（credit:クレジット，cvs:コンビニ ...etc） */
    public $pay_method;
    /** 結果通知プログラムに送られてきたパラメータをJSON変換したもの */
    public $parameter;
    /** 登録日時 */
    public $create_date;

    /** モデルで操作するテーブル名 */
    private $_table = 'gmo_result_notification_logs';

    public function __construct() {
        parent::__construct();
    }

    /**
     * ログを登録する
     */
    public function insert_log() {
        $this->db->insert($this->_table, $this);
    }

    /**
     * ログを削除する
     */
    public function delete_log() {
        $this->db->delete($this->_table, array('id' => $this->id));
    }

    /**
     * 最新１０件のログを取得する
     */
    public function get_last_ten_logs() {
        $query = $this->db->limit(10)
            ->order_by('id', 'desc')
            ->get($this->_table, 10);
        return $query->result();
    }
}