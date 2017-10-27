<?php

use Daxia\Core\Config;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $config = new Config();
        $database = $config->get('database');
        $this->assertArrayHasKey('db_host', $database);
        $this->assertArrayHasKey('db_user', $database);
        $this->assertArrayHasKey('db_pwd', $database);
        $this->assertArrayHasKey('db_name', $database);
    }
}
