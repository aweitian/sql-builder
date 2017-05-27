<?php

/**
 * 2017/5/16 17:42:03
 * 数据库管理组件 - MysqlBuild
 * SQL语句构建类
 */
namespace Tian\SqlBuild;

class MysqlSelectBuild extends SqlBuild{
	public function sql() {
		//一部分加上空格是为了更严格的SQL
		return strtr ( 'SELECT field FROM table join where groupBy having orderBy limit lock', [ 
				'field' => $this->parseField (),
				'table' => $this->parseTable (),
				' join' => $this->parseJoin (),
				' where' => $this->parseWhere (),
				' groupBy' => $this->parseGroupBy (),
				' having' => $this->parseHaving (),
				' orderBy' => $this->parseOrderBy (),
				' limit' => $this->parseLimit (),
				' lock' => $this->parseLock () 
		] );
	}
	public function bindFieldExpr($expr) {
		return $this->bindExpr('field', $expr);
	}
	public function bindJoinExpr($expr) {
		return $this->bindExpr('join', $expr);
	}
	public function bindWhereExpr($expr) {
		return $this->bindExpr('where', $expr);
	}
	public function bindGroupByExpr($expr) {
		return $this->bindExpr('groupBy', $expr);
	}
	public function bindHavingExpr($expr) {
		return $this->bindExpr('having', $expr);
	}
	public function bindOrderByExpr($expr) {
		return $this->bindExpr('orderBy', $expr);
	}
	public function bindLimitExpr($expr) {
		return $this->bindExpr('limit', $expr);
	}
	public function bindLockExpr($expr) {
		return $this->bindExpr('lock', $expr);
	}
}