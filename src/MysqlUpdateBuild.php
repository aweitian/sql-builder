<?php

/**
 * 2017/5/16 17:42:03
 * 数据库管理组件 - MysqlBuild
 * SQL语句构建类
 */
namespace Tian\SqlBuild;

class MysqlUpdateBuild extends SqlBuild {
	public function sql() {
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
	 * @param string $field        	
	 * @param string $value        	
	 * @return \Tian\SqlBuild\MysqlUpdateBuild
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
	 * @return \Tian\SqlBuild\MysqlUpdateBuild
	 */
	public function bindFieldExpr($field, $expr, $value = false) {
		if ($value !== false) {
			$this->bindValue ( $field, $value );
		}
		$this->bindExpr ( 'values', $expr );
		return $this->bindExpr ( 'field', $field );
	}
	/**
	 * 
	 * @param string $expr
	 * @param array $bind
	 * @return \Tian\SqlBuild\MysqlUpdateBuild
	 */
	public function bindWhereExpr($expr, $bind = []) {
		return $this->bindExpr ( 'where', $expr, $bind );
	}
	/**
	 *
	 * @param string $expr
	 * @param array $bind
	 * @return \Tian\SqlBuild\MysqlUpdateBuild
	 */
	public function bindOrderByExpr($expr, $bind = []) {
		return $this->bindExpr ( 'orderBy', $expr, $bind );
	}
	/**
	 *
	 * @param string $expr
	 * @param array $bind
	 * @return \Tian\SqlBuild\MysqlUpdateBuild
	 */
	public function bindLimitExpr($expr, $bind = []) {
		return $this->bindExpr ( 'limit', $expr, $bind );
	}
}