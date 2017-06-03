<?php

/**
 * 2017/5/16 17:42:03
 * 数据库管理组件 - MysqlBuild
 * SQL语句构建类
 */
namespace Tian\SqlBuild;

class MysqlSelectBuild extends SqlBuild {
	public function sql() {
		// 一部分加上空格是为了更严格的SQL
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
	/**
	 *
	 * @param string $field        	
	 * @param string $value        	
	 * @return \Tian\SqlBuild\MysqlSelectBuild
	 */
	public function bindField($field, $value = false) {
		$expr = ':' . $field;
		$this->bindExpr ( 'values', $expr );
		if ($value !== false) {
			$this->bindValue ( $field, $value );
		}
		return $this->bindExpr ( 'field', $field );
	}
	/**
	 *
	 * @param string $field        	
	 * @param string $expr        	
	 * @param string $value        	
	 * @return \Tian\SqlBuild\MysqlSelectBuild
	 */
	public function bindFieldExpr($field, $expr, $value = false) {
		if ($value !== false) {
			$this->bindValue ( $field, $value );
		}
		return $this->bindExpr ( 'field', $expr);
	}
	/**
	 *
	 * @param string $expr
	 * @param array $bind
	 * @return \Tian\SqlBuild\MysqlSelectBuild
	 */
	public function bindJoinExpr($expr, $bind = []) {
		return $this->bindExpr ( 'join', $expr, $bind );
	}
	/**
	 *
	 * @param string $expr
	 * @param array $bind
	 * @return \Tian\SqlBuild\MysqlSelectBuild
	 */
	public function bindWhereExpr($expr, $bind = []) {
		return $this->bindExpr ( 'where', $expr, $bind );
	}
	/**
	 *
	 * @param string $expr
	 * @param array $bind
	 * @return \Tian\SqlBuild\MysqlSelectBuild
	 */
	public function bindGroupByExpr($expr, $bind = []) {
		return $this->bindExpr ( 'groupBy', $expr, $bind );
	}
	/**
	 *
	 * @param string $expr
	 * @param array $bind
	 * @return \Tian\SqlBuild\MysqlSelectBuild
	 */
	public function bindHavingExpr($expr, $bind = []) {
		return $this->bindExpr ( 'having', $expr, $bind );
	}
	/**
	 *
	 * @param string $expr
	 * @param array $bind
	 * @return \Tian\SqlBuild\MysqlSelectBuild
	 */
	public function bindOrderByExpr($expr, $bind = []) {
		return $this->bindExpr ( 'orderBy', $expr, $bind );
	}
	/**
	 *
	 * @param string $expr
	 * @param array $bind
	 * @return \Tian\SqlBuild\MysqlSelectBuild
	 */
	public function bindLimitExpr($expr, $bind = []) {
		return $this->bindExpr ( 'limit', $expr, $bind );
	}
	/**
	 *
	 * @param string $expr
	 * @param array $bind
	 * @return \Tian\SqlBuild\MysqlSelectBuild
	 */
	public function bindLockExpr($expr, $bind = []) {
		return $this->bindExpr ( 'lock', $expr, $bind );
	}
}