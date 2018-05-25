<?php

use Aw\Build\Mysql\Crud;

class CurdUnbindTest extends PHPUnit_Framework_TestCase
{
    public function testSelect()
    {
        $demo = new Crud ('tablename');
        $demo->bindField("id");
        $demo->bindField("concat('%',:lol,'%')");
        $demo->bindField("name as n", "n");
        $this->assertEquals("SELECT `id`,concat('%',:lol,'%'),name as n FROM `tablename`", $demo->select());

        $demo->unBindField("id");
        $this->assertEquals("SELECT concat('%',:lol,'%'),name as n FROM `tablename`", $demo->select());
        $demo->unBindField("n");
        $this->assertEquals("SELECT concat('%',:lol,'%') FROM `tablename`", $demo->select());

    }

    public function testSelect0()
    {
        $demo = new Crud ('tablename');
        $demo->bindField("id");
        $demo->bindField("concat('%',:lol,'%')");
        $demo->bindField("name as n");
        $demo->bindWhere('qq');
        $this->assertEquals("SELECT `id`,concat('%',:lol,'%'),name as n FROM `tablename` WHERE `qq` = :qq", $demo->select());
        $demo->unBindWhere("qq");
        $this->assertEquals("SELECT `id`,concat('%',:lol,'%'),name as n FROM `tablename`", $demo->select());
    }

    public function testSelect3()
    {
        $demo = new Crud ('tablename');
        $demo->bindField("id");
        $demo->bindField("concat('%',:lol,'%')");
        $demo->bindField("count(`tablename`.name) as cc");
        $demo->bindJoin('left join tba on tba.sid = `tablename`.hid');
        $demo->bindJoin('left join tbb on tba.sid = tbb.sid');
        $demo->bindWhere('`tablename`.sid > 100');
        $demo->bindWhere('`tablename`.name = \'55\'');
        $demo->bindGroupBy('`tablename`.name');
        $demo->bindHaving('cc > 1');
        $demo->bindLimit('0,100');
        $this->assertEquals("SELECT `id`,concat('%',:lol,'%'),count(`tablename`.name) as cc FROM `tablename` left join tba on tba.sid = `tablename`.hid left join tbb on tba.sid = tbb.sid WHERE `tablename`.sid > 100 AND `tablename`.name = '55' GROUP BY `tablename`.name HAVING cc > 1 LIMIT 0,100", $demo->select());
        $demo->unBindLimit();
        $this->assertEquals("SELECT `id`,concat('%',:lol,'%'),count(`tablename`.name) as cc FROM `tablename` left join tba on tba.sid = `tablename`.hid left join tbb on tba.sid = tbb.sid WHERE `tablename`.sid > 100 AND `tablename`.name = '55' GROUP BY `tablename`.name HAVING cc > 1", $demo->select());

    }
}


