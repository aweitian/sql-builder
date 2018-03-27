<?php

use Aw\Build\Mysql\Crud;

class CurdTest extends PHPUnit_Framework_TestCase
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
        $this->assertEquals("SELECT id,concat('%',:lol,'%'),name as n FROM tablename WHERE qq = :qq", $demo->select());
    }

    public function testSelect1()
    {
        $demo = new Crud ('tablename');
        $demo->bindField("id");
        $demo->bindField("concat('%',:lol,'%')");
        $demo->bindField("name as n");
        $this->assertEquals("SELECT id,concat('%',:lol,'%'),name as n FROM tablename", $demo->select());
    }


    public function testSelect2()
    {
        $demo = new Crud ('tablename');
        $demo->bindField("id");
        $demo->bindField("concat('%',:lol,'%')");
        $demo->bindField("name as n");
        $demo->bindJoin('left join tba on tba.sid = tablename.hid');
        $demo->bindJoin('left join tbb on tba.sid = tbb.sid');
        $demo->bindWhere('tablename.sid > 100');
        $demo->bindWhere('tablename.name = \'55\'');
        $this->assertEquals("SELECT id,concat('%',:lol,'%'),name as n FROM tablename left join tba on tba.sid = tablename.hid left join tbb on tba.sid = tbb.sid WHERE tablename.sid > 100 AND tablename.name = '55'", $demo->select());
    }

    public function testSelect3()
    {
        $demo = new Crud ('tablename');
        $demo->bindField("id");
        $demo->bindField("concat('%',:lol,'%')");
        $demo->bindField("count(tablename.name) as cc");
        $demo->bindJoin('left join tba on tba.sid = tablename.hid');
        $demo->bindJoin('left join tbb on tba.sid = tbb.sid');
        $demo->bindWhere('tablename.sid > 100');
        $demo->bindWhere('tablename.name = \'55\'');
        $demo->bindGroupBy('tablename.name');
        $demo->bindHaving('cc > 1');
        $demo->bindLimit('0,100');
        $this->assertEquals("SELECT id,concat('%',:lol,'%'),count(tablename.name) as cc FROM tablename left join tba on tba.sid = tablename.hid left join tbb on tba.sid = tbb.sid WHERE tablename.sid > 100 AND tablename.name = '55' GROUP BY tablename.name HAVING cc > 1 LIMIT 0,100", $demo->select());
    }

    public function testInsert1()
    {
        $demo = new Crud ('tablename');
        $demo->bindField('aaa');
        $demo->bindField('bbb', "concat('aa>',:bbb,'<==')");
        $this->assertEquals("INSERT INTO tablename (aaa,bbb) VALUES (:aaa,concat('aa>',:bbb,'<=='))", $demo->insert());
    }

    public function testReplace()
    {
        $demo = new Crud ('tablename');
        $demo->bindField('aaa');
        $demo->bindField('bbb', "concat('aa>',:bbb,'<==')");
        $this->assertEquals("REPLACE INTO tablename (aaa,bbb) VALUES (:aaa,concat('aa>',:bbb,'<=='))", $demo->replace());
    }


    public function testUpdate()
    {
        $demo = new Crud ('tablename');
        $demo->bindField('aaa');
        $demo->bindField('bbb', "concat('aa>',:bbb,'<==')");
        $demo->bindWhere('sid>100');
        $demo->bindOrderBy('name desc');
        $demo->bindLimit('2');
        $this->assertEquals("UPDATE tablename SET aaa=:aaa,bbb=concat('aa>',:bbb,'<==') WHERE sid>100 ORDER BY name desc LIMIT 2", $demo->update());
    }


    public function testDelete()
    {
        $demo = new Crud ('tablename');
        $demo->bindWhere('sid>100');
        $demo->bindOrderBy('name desc');
        $demo->bindLimit('2');
        $this->assertEquals("DELETE FROM tablename WHERE sid>100 ORDER BY name desc LIMIT 2", $demo->delete());
    }


    public function testDelete2()
    {
        $demo = new Crud ('tablename,tb2');
        $demo->bindUsing('tb2');
        $demo->bindWhere('tablename.sid>100');
        $demo->bindWhere('tablename.sid = tb2.tb1id');
        $demo->bindOrderBy('name desc'); // 多表删除中无效
        $demo->bindLimit('2'); // 多表删除中无效
        $this->assertEquals("DELETE FROM tablename,tb2 USING tb2 WHERE tablename.sid>100 AND tablename.sid = tb2.tb1id", $demo->delete());
    }

    public function testSelect11()
    {
        $demo = new Crud ('tablename');
        $demo->useCalcFoundRows();
        $demo->bindField("id");
        $demo->bindField("concat('%',:lol,'%')");
        $demo->bindField("name as n");
        $this->assertEquals("SELECT SQL_CALC_FOUND_ROWS id,concat('%',:lol,'%'),name as n FROM tablename", $demo->select());
    }
}


