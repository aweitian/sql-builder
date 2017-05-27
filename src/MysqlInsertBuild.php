<?php

/**
 * 2017/5/16 17:42:03
 * 数据库管理组件 - MysqlBuild
 * SQL语句构建类
 */
namespace Tian\SqlBuild;

class MysqlInsertBuild extends SqlBuild {
	public function sql() {
		return strtr ( "INSERT INTO table (field) VALUES (values)", [ 
				'table' => $this->parseTable (),
				'field' => $this->parseField (),
				'values' => $this->parseValues () 
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
}