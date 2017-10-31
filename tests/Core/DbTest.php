<?php

use Daxia\Core\DB;
use Daxia\Core\Config;

class DBTest extends PHPUnit_Framework_TestCase
{
    public function testgetInstance()
    {
        $db = DB::getInstance();
        $this->assertInternalType('object', $db);
        $this->assertThat($db, $this->isInstanceof(DB::class));
    }

    public function testQuery()
    {
        $db = DB::getInstance();
        $sql = "select * from rent_goods_info_test limit 1";
        $result = $db->query($sql)[0];
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('rent_gid', $result);
        $this->assertArrayHasKey('seller_uid', $result);
    }

    public function testGet()
    {
        $db = DB::getInstance();

        $result = $db->select('*')
            ->from('rent_goods_info_test')
            ->where(['seller_uid' => 34350275])
            ->limit(1, 0)
            ->get();

        $this->assertObjectHasAttribute('sql', $result);
        $this->assertInternalType('string', $result->sql);
        $sql = "SELECT *\n"
            . "FROM (rent_goods_info_test)\n"
            . "WHERE seller_uid = 34350275\n"
            . "LIMIT 1";

        $this->assertEquals($sql, $result->sql);

        $this->assertObjectHasAttribute('result_array', $result);
        $result_array = $result->result_array[0];
        $this->assertInternalType('array', $result_array);
        $this->assertCount(3, $result_array);
        $this->assertArrayHasKey('id', $result_array);
        $this->assertArrayHasKey('rent_gid', $result_array);
        $this->assertArrayHasKey('seller_uid', $result_array);
    }

    public function testSelect()
    {
        $db = DB::getInstance();

        $result = $db->select('rent_gid, seller_uid');
        $this->assertInternalType('object', $result);
        $this->assertObjectHasAttribute('ar_select', $result);

        $ar_select = $result->ar_select;
        $this->assertInternalType('array', $ar_select);
        $this->assertCount(2, $ar_select);
        $this->assertContains('rent_gid', $ar_select);
        $this->assertContains('seller_uid', $ar_select);
    }

    public function testFrom()
    {
        $db = DB::getInstance();

        $result = $db->from('rent_goods_info_test');
        $this->assertObjectHasAttribute('ar_from', $result);

        $ar_from = $result->ar_from;
        $this->assertInternalType('array', $ar_from);
        $this->assertCount(1, $ar_from);
        $this->assertContains('rent_goods_info_test', $ar_from);
    }

    public function testWhere()
    {
        $db = DB::getInstance();

        $where = [
            'rent_gid'   => '10000699700002',
            'seller_uid' => 34350275
        ];
        $result = $db->where($where);
        $this->assertObjectHasAttribute('ar_where', $result);

        $ar_where = $result->ar_where;
        $this->assertInternalType('array', $ar_where);
        $this->assertCount(2, $ar_where);
        $this->assertContains('rent_gid = 10000699700002', $ar_where);
        $this->assertContains('AND seller_uid = 34350275', $ar_where);
    }

    public function testLimit()
    {
        $db = DB::getInstance();

        $result = $db->limit(2, 0);

        $this->assertObjectHasAttribute('ar_limit', $result);
        $this->assertInternalType('int', $result->ar_limit);
        $this->assertEquals(2, $result->ar_limit);

        $this->assertObjectHasAttribute('ar_offset', $result);
        $this->assertInternalType('int', $result->ar_offset);
        $this->assertEquals(0, $result->ar_offset);
    }

    public function testGroupBy()
    {
        $db = DB::getInstance();

        $result = $db->group_by('seller_uid');
        $this->assertObjectHasAttribute('ar_groupby', $result);

        $ar_groupby = $result->ar_groupby;
        $this->assertInternalType('array', $ar_groupby);
        $this->assertCount(1, $ar_groupby);
        $this->assertContains('seller_uid', $ar_groupby);
    }

    public function testOrderBy()
    {
        $db = DB::getInstance();

        $result = $db->order_by('id', 'DESC');
        $this->assertObjectHasAttribute('ar_orderby', $result);

        $ar_orderby = $result->ar_orderby;
        $this->assertInternalType('array', $ar_orderby);
        $this->assertCount(1, $ar_orderby);
        $this->assertContains('id DESC', $ar_orderby);
    }
}
