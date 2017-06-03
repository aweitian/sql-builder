<?php

/**
 * 2017/5/16 17:42:03
 * 数据库管理组件 - MysqlBuild
 * SQL语句构建类
 */
namespace Tian\SqlBuild;

class MysqlReplaceBuild extends SqlBuild {
	public function sql() {
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
	 * @return \Tian\SqlBuild\MysqlReplaceBuild
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
	 * @return \Tian\SqlBuild\MysqlReplaceBuild
	 */
	public function bindFieldExpr($field, $expr, $value = false) {
		if ($value !== false) {
			$this->bindValue ( $field, $value );
		}
		$this->bindExpr ( 'values', $expr );
		return $this->bindExpr ( 'field', $field );
	}
}