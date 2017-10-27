<?php

use Daxia\Core\DB;

class DBTest extends PHPUnit_Framework_TestCase
{
    public function testConnect()
    {
        $db = new DB();
        $this->assertInternalType('object', $db);
        // $this->assertObjectHasAttribute('mysqli', $db);
        // $this->assertInternalType('object', $db->mysqli);
    }

    public function testQuery()
    {
        $db = new DB();
        $sql = "select * from rent_goods_info_test limit 1";
        $result = $db->query($sql);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('rent_gid', $result);
        $this->assertArrayHasKey('seller_uid', $result);
    }
}
