<?php
class SqlBuildTest extends PHPUnit_Framework_TestCase {
	public function testSelect() {
		$demo = new \Tian\SqlBuild\MysqlSelectBuild ( 'tablename' );
		$demo->bindFieldExpr ( "id" );
		$demo->bindFieldExpr ( "concat('%',:lol,'%')" );
		$demo->bindFieldExpr ( "name as n" );
		$this->assertEquals ( "SELECT id,concat('%',:lol,'%'),name as n FROM tablename", $demo->sql () );
	}
	public function testSelectjoin() {
		$demo = new \Tian\SqlBuild\MysqlSelectBuild ( 'tablename' );
		$demo->bindFieldExpr ( "id" );
		$demo->bindFieldExpr ( "concat('%',:lol,'%')" );
		$demo->bindFieldExpr ( "name as n" );
		$demo->bindJoinExpr ( 'left join tba on tba.sid = tablename.hid' );
		$demo->bindJoinExpr ( 'left join tbb on tba.sid = tbb.sid' );
		$demo->bindWhereExpr ( 'tablename.sid > 100' );
		$demo->bindWhereExpr ( 'tablename.name = \'55\'' );
		$this->assertEquals ( "SELECT id,concat('%',:lol,'%'),name as n FROM tablename left join tba on tba.sid = tablename.hid left join tbb on tba.sid = tbb.sid WHERE tablename.sid > 100 AND tablename.name = '55'", $demo->sql () );
	}
	public function testSelectGrpBy() {
		$demo = new \Tian\SqlBuild\MysqlSelectBuild ( 'tablename' );
		$demo->bindFieldExpr ( "id" );
		$demo->bindFieldExpr ( "concat('%',:lol,'%')" );
		$demo->bindFieldExpr ( "count(tablename.name) as cc" );
		$demo->bindJoinExpr ( 'left join tba on tba.sid = tablename.hid' );
		$demo->bindJoinExpr ( 'left join tbb on tba.sid = tbb.sid' );
		$demo->bindWhereExpr ( 'tablename.sid > 100' );
		$demo->bindWhereExpr ( 'tablename.name = \'55\'' );
		$demo->bindGroupByExpr ( 'tablename.name' );
		$demo->bindHavingExpr ( 'cc > 1' );
		$demo->bindLimitExpr ( '0,100' );
		$this->assertEquals ( "SELECT id,concat('%',:lol,'%'),count(tablename.name) as cc FROM tablename left join tba on tba.sid = tablename.hid left join tbb on tba.sid = tbb.sid WHERE tablename.sid > 100 AND tablename.name = '55' GROUP BY tablename.name HAVING cc > 1 LIMIT 0,100", $demo->sql () );
	}
	public function testInsert() {
		$demo = new \Tian\SqlBuild\MysqlInsertBuild ( 'tablename' );
		$demo->bindFieldExpr ( 'aaa' );
		$demo->bindFieldExpr ( 'bbb', "concat('aa>',:bbb,'<==')" );
		$this->assertEquals ( "INSERT INTO tablename (aaa,bbb) VALUES (:aaa,concat('aa>',:bbb,'<=='))", $demo->sql () );
	}
	public function testReplace() {
		$demo = new \Tian\SqlBuild\MysqlReplaceBuild ( 'tablename' );
		$demo->bindFieldExpr ( 'aaa' );
		$demo->bindFieldExpr ( 'bbb', "concat('aa>',:bbb,'<==')" );
		$this->assertEquals ( "REPLACE INTO tablename (aaa,bbb) VALUES (:aaa,concat('aa>',:bbb,'<=='))", $demo->sql () );
	}
	public function testUpdate() {
		$demo = new \Tian\SqlBuild\MysqlUpdateBuild ( 'tablename' );
		$demo->bindFieldExpr ( 'aaa' );
		$demo->bindFieldExpr ( 'bbb', "concat('aa>',:bbb,'<==')" );
		$demo->bindWhereExpr ( 'sid>100' );
		$demo->bindOrderByExpr ( 'name desc' );
		$demo->bindLimitExpr ( '2' );
		$this->assertEquals ( "UPDATE tablename SET aaa=:aaa,bbb=concat('aa>',:bbb,'<==') WHERE sid>100 ORDER BY name desc LIMIT 2", $demo->sql () );
	}
	public function testDelete() {
		// 单表删除
		$demo = new \Tian\SqlBuild\MysqlDeleteBuild ( 'tablename' );
		$demo->bindWhereExpr ( 'sid>100' );
		$demo->bindOrderByExpr ( 'name desc' );
		$demo->bindLimitExpr ( '2' );
		$this->assertEquals ( "DELETE FROM tablename WHERE sid>100 ORDER BY name desc LIMIT 2", $demo->sql () );
	}
	public function testDeleteMuti() {
		// 多表删除,多表删除不能使用ORDER BY 和 LIMIT
		$demo = new \Tian\SqlBuild\MysqlDeleteBuild ( 'tablename,tb2' );
		$demo->bindUsingExpr ( 'tb2' );
		$demo->bindWhereExpr ( 'tablename.sid>100' );
		$demo->bindWhereExpr ( 'tablename.sid = tb2.tb1id' );
		$demo->bindOrderByExpr ( 'name desc' ); // 多表删除中无效
		$demo->bindLimitExpr ( '2' ); // 多表删除中无效
		$this->assertEquals ( "DELETE FROM tablename,tb2 USING tb2 WHERE tablename.sid>100 AND tablename.sid = tb2.tb1id", $demo->sql () );
	}
}

