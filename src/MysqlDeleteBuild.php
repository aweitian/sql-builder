<?php

/**
 * 2017/5/16 17:42:03
 * 数据库管理组件 - MysqlBuild
 * SQL语句构建类
 */
namespace Tian\SqlBuild;

class MysqlDeleteBuild extends SqlBuild {
	//DELETE FROM t1, t2 USING t1, t2, t3 WHERE t1.id=t2.id AND t2.id=t3.id
	public function sql() {
		// 判断是单表还是多表
		$table = $this->parseTable ();
		if (strpos ( $table, ',' ) !== false) {
			return strtr ( "DELETE FROM table using where orderBy limit", [ 
					'table' => $table,
					' using' => $this->parseUsing (),
					' where' => $this->parseWhere () ,
					' orderBy' => '',
					' limit' => '' 
			] );
		} else {
			return strtr ( "DELETE FROM table using where orderBy limit", [ 
					'table' => $table,
					' using' => $this->parseUsing (),
					' where' => $this->parseWhere (),
					' orderBy' => $this->parseOrderBy (),
					' limit' => $this->parseLimit () 
			] );
		}
	}
	public function bindUsingExpr($expr) {
		return $this->bindExpr ( 'using', $expr );
	}
	public function bindWhereExpr($expr) {
		return $this->bindExpr ( 'where', $expr );
	}
	public function bindOrderByExpr($expr) {
		return $this->bindExpr ( 'orderBy', $expr );
	}
	public function bindLimitExpr($expr) {
		return $this->bindExpr ( 'limit', $expr );
	}
}