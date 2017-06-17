<?php
class SqlBuildTest extends PHPUnit_Framework_TestCase {
	public function testSelect() {
		$demo = new \Tian\SqlBuild\MysqlBuild( 'tablename' );
		$demo->bindField ( "id" );
		$demo->bindField ( "concat('%',:lol,'%')", [ 
				"lol" => "lol_value" 
		] );
		$demo->bindField ( "name as n" );
		$this->assertEquals ( "SELECT id,concat('%',:lol,'%'),name as n FROM tablename", $demo->select () );
		// var_dump($demo->getBindValue());
		$this->assertArraySubset ( [ 
				"lol" => "lol_value" 
		], $demo->getBindValue () );
	}
	public function testSelectjoin() {
		$demo = new \Tian\SqlBuild\MysqlBuild ( 'tablename' );
		$demo->bindField ( "id" );
		$demo->bindField ( "concat('%',:lol,'%')", [ 
				"lol" => "lol_value" 
		] );
		$demo->bindField ( "name as n" );
		$demo->bindJoin ( 'left join tba on tba.sid = tablename.hid' );
		$demo->bindJoin ( 'left join tbb on tba.sid = tbb.sid' );
		$demo->bindWhere ( 'tablename.sid > :sid', [ 
				'sid' => '100' 
		] );
		$demo->bindWhere ( 'tablename.name = :name', [ 
				'name' => 'name_search' 
		] );
		$this->assertEquals ( "SELECT id,concat('%',:lol,'%'),name as n FROM tablename left join tba on tba.sid = tablename.hid left join tbb on tba.sid = tbb.sid WHERE tablename.sid > :sid AND tablename.name = :name", $demo->select () );
		$this->assertArraySubset ( [ 
				"lol" => "lol_value",
				"sid" => "100",
				"name" => "name_search" 
		], $demo->getBindValue () );
		// var_dump($demo->getBindValue());
	}
	public function testSelectGrpBy() {
		$demo = new \Tian\SqlBuild\MysqlBuild ( 'tablename' );
		$demo->bindField ( "id" );
		$demo->bindField ( "concat('%',:lol,'%')", [ 
				"lol" => "lol_value" 
		] );
		$demo->bindField ( "count(tablename.name) as cc" );
		$demo->bindJoin ( 'left join tba on tba.sid = tablename.hid' );
		$demo->bindJoin ( 'left join tbb on tba.sid = tbb.sid' );
		$demo->bindWhere ( 'tablename.sid > :sid', [ 
				'sid' => '100' 
		] );
		$demo->bindWhere ( 'tablename.name = :name', [ 
				'name' => 'name_search' 
		] );
		$demo->bindGroupBy ( 'tablename.name' );
		$demo->bindHaving ( 'cc > :cc', [ 
				'cc' => 'cc_value' 
		] );
		$demo->bindLimit ( ':offset,:length', [ 
				"offset" => '100',
				"length" => '10' 
		] );
		$this->assertEquals ( "SELECT id,concat('%',:lol,'%'),count(tablename.name) as cc FROM tablename left join tba on tba.sid = tablename.hid left join tbb on tba.sid = tbb.sid WHERE tablename.sid > :sid AND tablename.name = :name GROUP BY tablename.name HAVING cc > :cc LIMIT :offset,:length", $demo->select () );
		// var_dump($demo->getBindValue());
		$this->assertArraySubset ( [ 
				"lol" => "lol_value",
				"sid" => "100",
				"name" => "name_search",
				"cc" => "cc_value",
				"offset" => "100",
				"length" => "10" 
		], $demo->getBindValue () );
	}
	public function testInsert() {
		$demo = new \Tian\SqlBuild\MysqlBuild ( 'tablename' );
		$demo->bindField ( 'aaa', 'insert_aaa_value', ':aaa' );
		$demo->bindField ( 'bbb', "insert_bbb_value", "concat('aa>',:bbb,'<==')" );
		$this->assertEquals ( "INSERT INTO tablename (aaa,bbb) VALUES (:aaa,concat('aa>',:bbb,'<=='))", $demo->insert () );
		// var_dump($demo->getBindValue());
		$this->assertArraySubset ( [ 
				"aaa" => "insert_aaa_value",
				"bbb" => "insert_bbb_value" 
		], $demo->getBindValue () );
	}
	public function testReplace() {
		$demo = new \Tian\SqlBuild\MysqlBuild ( 'tablename' );
		$demo->bindField ( 'aaa', "insert_aaa_value", ':aaa' );
		$demo->bindField ( 'bbb', "insert_bbb_value", "concat('aa>',:bbb,'<==')" );
		$this->assertEquals ( "REPLACE INTO tablename (aaa,bbb) VALUES (:aaa,concat('aa>',:bbb,'<=='))", $demo->replace () );
		$this->assertArraySubset ( [ 
				"aaa" => "insert_aaa_value",
				"bbb" => "insert_bbb_value" 
		], $demo->getBindValue () );
	}
	public function testUpdate() {
		$demo = new \Tian\SqlBuild\MysqlBuild ( 'tablename' );
		$demo->bindField ( 'aaa', "insert_aaa_value",':aaa' );
		$demo->bindField ( 'bbb', "insert_bbb_value", "concat('aa>',:bbb,'<==')");
		$demo->bindWhere ( 'sid>:sid', [ 
				"sid" => 100 
		] );
		$demo->bindOrderBy ( 'name desc' );
		$demo->bindLimit ( '2' );
		$this->assertEquals ( "UPDATE tablename SET aaa=:aaa,bbb=concat('aa>',:bbb,'<==') WHERE sid>:sid ORDER BY name desc LIMIT 2", $demo->update () );
		// var_dump($demo->getBindValue());
		$this->assertArraySubset ( [ 
				"aaa" => "insert_aaa_value",
				"bbb" => "insert_bbb_value",
				"sid" => "100" 
		], $demo->getBindValue () );
	}
	public function testDelete() {
		// 单表删除
		$demo = new \Tian\SqlBuild\MysqlBuild ( 'tablename' );
		$demo->bindWhere ( 'sid > :sid', [ 
				"sid" => 100 
		] );
		$demo->bindOrderBy ( 'name desc' );
		$demo->bindLimit ( '2' );
		$this->assertEquals ( "DELETE FROM tablename WHERE sid > :sid ORDER BY name desc LIMIT 2", $demo->delete () );
		// var_dump($demo->getBindValue());
		$this->assertArraySubset ( [ 
				"sid" => "100" 
		], $demo->getBindValue () );
	}
	public function testDeleteMuti() {
		// 多表删除,多表删除不能使用ORDER BY 和 LIMIT
		$demo = new \Tian\SqlBuild\MysqlBuild ( 'tablename,tb2' );
		$demo->bindUsing ( 'tb2' );
		$demo->bindWhere ( 'tablename.sid>:sid', [ 
				"sid" => 100 
		] );
		$demo->bindWhere ( 'tablename.sid = tb2.tb1id' );
		$demo->bindOrderBy ( 'name desc' ); // 多表删除中无效
		$demo->bindLimit ( '2' ); // 多表删除中无效
		$this->assertEquals ( "DELETE FROM tablename,tb2 USING tb2 WHERE tablename.sid>:sid AND tablename.sid = tb2.tb1id", $demo->delete () );
		$this->assertArraySubset ( [ 
				"sid" => "100" 
		], $demo->getBindValue () );
	}
}


