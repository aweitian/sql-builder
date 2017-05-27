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
	 * @param string $expr 默认值为 :$field,如果要传递，FIELD前面需要带:
	 * @return \Tian\SqlBuild\SqlBuild
	 */
	public function bindFieldExpr($field, $expr = '') {
		if (! $expr) {
			$expr = ':' . $field;
		}
		$this->bindExpr ( 'values', $expr );
		return $this->bindExpr ( 'field', $field );
	}
	public function bindWhereExpr($expr) {
		return $this->bindExpr ( 'where', $expr );
	}
	public function bindOrderByExpr($expr) {
		return $this->bindExpr('orderBy', $expr);
	}
	public function bindLimitExpr($expr) {
		return $this->bindExpr ( 'limit', $expr );
	}
}