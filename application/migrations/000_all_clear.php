<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Version=0を指定したときに全てのテーブルをDropするためだけに存在するMigrationクラス。
 */
class Migration_All_clear extends CI_Migration
{
    public function up()
    {
        // 特に何もしない
    }

    public function down()
    {
        // 特に何もしない
    }
}