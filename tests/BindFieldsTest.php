<?php

use Aw\Build\Mysql\Crud;

class BindFieldsTest extends PHPUnit_Framework_TestCase
{
    public function testSelect()
    {
        $demo = new Crud ('tablename');
        $demo->bindField("id");
        $demo->bindField("concat('%',:lol,'%')");
        $demo->bindField("name as n");
        $this->assertEquals("SELECT id,concat('%',:lol,'%'),name as n FROM tablename", $demo->select());
    }

    public function testSelect0()
    {
        $demo = new Crud ('tablename');
        $demo->bindField("id");
        $demo->bindField("concat('%',:lol,'%')");
        $demo->bindField("name as n");
        $demo->bindWhere('qq');
        $demo->bindWhere('xx', 'xx', 'xxv');
        $this->assertEquals("SELECT id,concat('%',:lol,'%'),name as n FROM tablename WHERE qq = :qq AND xx = :xx", $demo->select());

        $data_src = array(
            'lol' => 'lol-value',
            "qq" => "qqv"
        );

        $demo->fillDataSrc($data_src);
        $this->assertEquals(array(
            'xx' => 'xxv',
            'lol' => 'lol-value',
            "qq" => "qqv"
        ), $demo->getBindValue());
    }
}


