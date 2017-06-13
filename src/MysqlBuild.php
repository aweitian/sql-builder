<?php

/**
 * 2017/5/16 17:39:34
 * 数据库 - SQL语句构建类 - 抽象类
 * 这里只是提供常用的SQL构建，复杂的SQL直接使用CONNECTION类
 */
namespace Tian\SqlBuild;

use \Tian\Base\Arr as Arr;

class MysqlBuild {
	protected $table;
	protected $expr = [ ];
	/**
	 * key => val
	 *
	 * @var array
	 */
	protected $value = [ ];
	/**
	 * 路径 =》 OR | AND
	 *
	 * @var array
	 */
	protected $whereType = [ ];
	/**
	 *
	 * @param string $table        	
	 */
	public function __construct($table) {
		$this->table = $table;
	}
	/**
	 *
	 * @return sql string
	 */
	public function select() {
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
	 * @return sql string
	 */
	public function delete() {
		// 判断是单表还是多表
		$table = $this->parseTable ();
		if (strpos ( $table, ',' ) !== false) {
			return strtr ( "DELETE FROM table using where orderBy limit", [ 
					'table' => $table,
					' using' => $this->parseUsing (),
					' where' => $this->parseWhere (),
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
	/**
	 *
	 * @return sql string
	 */
	public function insert() {
		return strtr ( "INSERT INTO table (field) VALUES (values)", [ 
				'table' => $this->parseTable (),
				'field' => $this->parseField (),
				'values' => $this->parseValues () 
		] );
	}
	/**
	 *
	 * @return sql string
	 */
	public function update() {
		return strtr ( "UPDATE table set where orderBy limit", [ 
				'table' => $this->parseTable (),
				'set' => $this->parseSet (),
				' where' => $this->parseWhere (),
				' orderBy' => $this->parseOrderBy (),
				' limit' => $this->parseLimit () 
		] );
	}
	/**
	 *
	 * @return sql string
	 */
	public function replace() {
		return strtr ( "REPLACE INTO table (field) VALUES (values)", [ 
				'table' => $this->parseTable (),
				'field' => $this->parseField (),
				'values' => $this->parseValues () 
		] );
	}
	/**
	 *
	 * @param string $field        	
	 * @param string $value        	
	 * @param string $values用于INSERT,UPDATE,REPLACE语句中的VALUES，:AAA        	
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	public function bindField($field, $value = false, $values = false) {
		if ($values !== false) {
			$this->bindExpr ( 'values', $values );
		}
		if ($value !== false) {
			return $this->bindExpr ( 'field', $field, $value );
		}
		
		return $this->bindExpr ( 'field', $field );
	}
	/**
	 * 前面必须带:,为了干净的代码
	 * bindValues(":field",'test")
	 *
	 * @param string $field        	
	 * @param string $value        	
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	public function bindValues($field, $value = false) {
		if ($value !== false) {
			$this->bindValue ( $field, $value );
		}
		return $this->bindExpr ( 'values', $field );
	}
	/**
	 *
	 * @param string $expr        	
	 * @param array $bind        	
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	public function bindJoin($expr, $bind = []) {
		return $this->bindExpr ( 'join', $expr, $bind );
	}
	/**
	 *
	 * @param string $expr        	
	 * @param array $bind        	
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	public function bindWhere($expr, $bind = [], $path = null, $type = 'and') {
		$name = 'where';
		if (! isset ( $this->expr [$name] ))
			$this->expr [$name] = [ ];
		$v = & Arr::ref ( $this->expr [$name], $path );
		$t = & Arr::ref ( $this->whereType, $path );
		$t = $type;
		$v = $expr;
		$this->bindValue ( $bind );
		return $this;
	}
	/**
	 *
	 * @param string $expr        	
	 * @param array $bind        	
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	public function bindGroupBy($expr, $bind = []) {
		return $this->bindExpr ( 'groupBy', $expr, $bind );
	}
	/**
	 *
	 * @param string $expr        	
	 * @param array $bind        	
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	public function bindHaving($expr, $bind = []) {
		return $this->bindExpr ( 'having', $expr, $bind );
	}
	/**
	 *
	 * @param string $expr        	
	 * @param array $bind        	
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	public function bindOrderBy($expr, $bind = []) {
		return $this->bindExpr ( 'orderBy', $expr, $bind );
	}
	/**
	 *
	 * @param string $expr        	
	 * @param array $bind        	
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	public function bindLimit($expr, $bind = []) {
		return $this->bindExpr ( 'limit', $expr, $bind );
	}
	/**
	 *
	 * @param string $expr        	
	 * @param array $bind        	
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	public function bindLock($expr, $bind = []) {
		return $this->bindExpr ( 'lock', $expr, $bind );
	}
	
	/**
	 *
	 * @see \Tian\SqlBuild\SqlBuild::bindExpr
	 *
	 * @param string $expr        	
	 * @param array $bind        	
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	public function bindUsing($expr, $bind = []) {
		return $this->bindExpr ( 'using', $expr, $bind );
	}
	
	/**
	 * 第二个参数如果是数组，需要第一个长度为BIND KEY,第二个为BIND VALUE
	 * 如果第二个是数字或者字符串，则BIND KEY为EXPR,参数二为BIND VALUE
	 * 其它只绑定表达表，不绑定值
	 * 绑定表达式 bindExpr("where","concat('%',:key,'%')")
	 * bind参数为key=>value形式，可以多个
	 *
	 *
	 * @param string $name        	
	 * @param string $expr        	
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	protected function bindExpr($name, $expr, $bind = []) {
		if (! isset ( $this->expr [$name] ))
			$this->expr [$name] = [ ];
		$this->expr [$name] [] = $expr;
		if (is_array ( $bind ))
			foreach ( $bind as $bk => $bv )
				$this->bindValue ( $bk, $bv );
		else if (is_string ( $bind ) || is_numeric ( $bind ))
			$this->bindValue ( $expr, $bind );
		return $this;
	}
	public function getBindValue() {
		return $this->value;
	}
	/**
	 * 绑定实参 bindValue( "key", "lol")
	 * bindValue([])
	 * 
	 * @param string $name        	
	 * @param string $key        	
	 * @param object $val        	
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	public function bindValue($key, $val) {
		if (is_array ( $key ))
			$this->value = array_merge ( $this->value, $key );
		else if (is_string ( $key ) || is_numeric ( $key ))
			$this->value [$key] = $val;
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
		$this->whereType = [ ];
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