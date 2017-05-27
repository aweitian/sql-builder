<?php

/**
 * 2017/5/16 17:39:34
 * 数据库 - SQL语句构建类 - 抽象类
 * 这里只是提供常用的SQL构建，复杂的SQL直接使用CONNECTION类
 */
namespace Tian\SqlBuild;

abstract class SqlBuild {
	protected $table;
	protected $expr = [ ];
	/**
	 * key => val
	 *
	 * @var array
	 */
	protected $value = [ ];
	/**
	 *
	 * @return sql string
	 */
	abstract public function sql();
	/**
	 *
	 * @param string $table        	
	 */
	public function __construct($table) {
		$this->table = $table;
	}
	
	/**
	 * 绑定表达式 bindExpr("where","concat('%',:key,'%')")
	 *
	 * @param
	 *        	$name
	 * @param
	 *        	$expr
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	protected function bindExpr($name, $expr) {
		if (! isset ( $this->expr [$name] ))
			$this->expr [$name] = [ ];
		$this->expr [$name] [] = $expr;
		return $this;
	}
	/**
	 * 绑定实参 bindValue( "key", "lol")
	 *
	 * @param string $name        	
	 * @param string $key        	
	 * @param object $val        	
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	public function bindValue($key, $val) {
		$this->bind [$key] = $val;
		return $this;
	}
	protected function getBindExprs($name) {
		// support since php 5.3
		return isset ( $this->expr [$name] ) ? $this->expr [$name] : [ ];
	}
	/**
	 * reset bind values
	 *
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	public function reset() {
		$this->bind = [ ];
		$this->expr = [ ];
		return $this;
	}
	protected function parseTable() {
		return $this->table;
	}
	protected function parseField() {
		$expr = $this->getBindExprs ( 'field' );
		return $expr ? implode ( ',', $expr ) : '*';
	}
	protected function parseValues() {
		$expr = $this->getBindExprs ( 'values' );
		return $expr ? implode ( ',', $expr ) : '';
	}
	protected function parseJoin() {
		$expr = $this->getBindExprs ( 'join' );
		return $expr ? ' ' . implode ( ' ', $expr ) : '';
	}
	protected function parseWhere() {
		if ($expr = $this->getBindExprs ( 'where' )) {
			return " WHERE " . implode ( ' AND ', $expr );
		}
		return '';
	}
	protected function parseGroupBy() {
		if ($expr = $this->getBindExprs ( 'groupBy' )) {
			return " GROUP BY " . implode ( ',', $expr );
		}
		return '';
	}
	protected function parseHaving() {
		if ($expr = $this->getBindExprs ( 'having' )) {
			return " HAVING " . current ( $expr );
		}
		return '';
	}
	protected function parseOrderBy() {
		if ($expr = $this->getBindExprs ( 'orderBy' )) {
			return " ORDER BY " . implode ( ',', $expr );
		}
		return '';
	}
	protected function parseLimit() {
		if ($expr = $this->getBindExprs ( 'limit' )) {
			return " LIMIT " . current ( $expr );
		}
		return '';
	}
	protected function parseLock() {
		if ($expr = $this->getBindExprs ( 'lock' )) {
			return ' ' . current ( $expr );
		}
		return '';
	}
	protected function parseSet() {
		$fields = $this->getBindExprs ( 'field' );
		$values = $this->getBindExprs ( 'values' );
		$ret = [ ];
		for($i = 0; $i < count ( $fields ); $i ++) {
			$ret [] = "{$fields[$i]}=$values[$i]";
		}
		if ($ret) {
			return 'SET ' . implode ( ',', $ret );
		}
		return '';
	}
	protected function parseUsing() {
		if ($expr = $this->getBindExprs ( 'using' )) {
			return " USING " . implode ( ',', $expr );
		}
		return '';
	}
}