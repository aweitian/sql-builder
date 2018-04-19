<?php

/**
 * 2017/5/16 17:39:34
 * 数据库 - SQL语句构建类 - 抽象类
 * 这里只是提供常用的SQL构建，复杂的SQL直接使用CONNECTION类
 * WHERE条件是一个树，条件在叶子节点上，非叶子节点为 OR AND
 */

namespace Aw\Build\Mysql;

class Crud
{
    protected $table;
    protected $expr = array();
    /**
     * key => val
     *
     * @var array
     */
    protected $value = array();
    protected $whereCondition = ' AND ';

    protected $use_calc_found_rows = false;

    /**
     *
     * @param string $table
     */
    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * @return $this
     */
    public function useCalcFoundRows()
    {
        $this->use_calc_found_rows = true;
        return $this;
    }

    public function disableUseCalcFoundRows()
    {
        $this->use_calc_found_rows = false;
        return $this;
    }

    /**
     * 在 useCalcFoundRows()  后作用
     * @return string
     */
    public function count()
    {
        return 'SELECT FOUND_ROWS()';
    }

    /**
     *
     * @return string
     */
    public function select()
    {
        return strtr('SELECT' . ($this->use_calc_found_rows ? ' SQL_CALC_FOUND_ROWS' : '') . ' field FROM table join where groupBy having orderBy limit lock', array(
            'field' => $this->parseField(),
            'table' => $this->parseTable(),
            ' join' => $this->parseJoin(),
            ' where' => $this->parseWhere(),
            ' groupBy' => $this->parseGroupBy(),
            ' having' => $this->parseHaving(),
            ' orderBy' => $this->parseOrderBy(),
            ' limit' => $this->parseLimit(),
            ' lock' => $this->parseLock()
        ));
    }

    /**
     *
     * @return $this
     */
    public function andWhere()
    {
        $this->whereCondition = ' AND ';
        return $this;
    }

    /**
     *
     * @return $this
     */
    public function orWhere()
    {
        $this->whereCondition = ' OR ';
        return $this;
    }

    /**
     *
     * @return string
     */
    public function delete()
    {
        // 判断是单表还是多表
        $table = $this->parseTable();
        if (strpos($table, ',') !== false) {
            return strtr("DELETE FROM table using where orderBy limit", array(
                'table' => $table,
                ' using' => $this->parseUsing(),
                ' where' => $this->parseWhere(),
                ' orderBy' => '',
                ' limit' => ''
            ));
        } else {
            return strtr("DELETE FROM table using where orderBy limit", array(
                'table' => $table,
                ' using' => $this->parseUsing(),
                ' where' => $this->parseWhere(),
                ' orderBy' => $this->parseOrderBy(),
                ' limit' => $this->parseLimit()
            ));
        }
    }

    /**
     *
     * @return string
     */
    public function insert()
    {
        return strtr("INSERT INTO table (field) VALUES (values)", array(
            'table' => $this->parseTable(),
            'field' => $this->parseField(),
            'values' => $this->parseValues()
        ));
    }

    /**
     *
     * @return string
     */
    public function update()
    {
        return strtr("UPDATE table set where orderBy limit", array(
            'table' => $this->parseTable(),
            'set' => $this->parseSet(),
            ' where' => $this->parseWhere(),
            ' orderBy' => $this->parseOrderBy(),
            ' limit' => $this->parseLimit()
        ));
    }

    /**
     *
     * @return string
     */
    public function replace()
    {
        return strtr("REPLACE INTO table (field) VALUES (values)", array(
            'table' => $this->parseTable(),
            'field' => $this->parseField(),
            'values' => $this->parseValues()
        ));
    }

    /**
     *
     * @param string $field
     * @param bool|string $value
     * @param bool $values
     * @param string $key
     * @return $this
     * @internal param string $values用于INSERT ,UPDATE,REPLACE语句中的VALUES，:AAA
     */
    public function bindField($field, $value = false, $values = false, $key = null)
    {
        if ($values !== false) {
            $this->bindExpr('values', $values);
        }

        if (is_null($key)) {
            if (preg_match("/^\w+$/", $field)) {
                $key = $field;
            }
        }

        if ($value !== false) {
            return $this->bindExpr('field', $field, $value, $key);
        }
        return $this->bindExpr('field', $field, array(), $key);
    }

    /**
     * 前面必须带:,为了干净的代码
     * bindValues(":field",'test")
     *
     * @param string $field
     * @param bool|string $value
     * @param string $key
     * @return $this
     */
    public function bindValues($field, $value = false, $key = null)
    {
        if ($value !== false) {
            $this->bindValue($field, $value);
        }
        if (is_null($key)) {
            if (preg_match("/^\w+$/", $field)) {
                $key = $field;
            }
        }
        return $this->bindExpr('values', $field, array(), $key);
    }

    /**
     *
     * @param string $expr
     * @param array $bind
     * @param string $key
     * @return $this
     */
    public function bindJoin($expr, $bind = array(), $key = null)
    {
        return $this->bindExpr('join', $expr, $bind, $key);
    }

    /**
     *
     * @param string $expr
     * @param array $bind
     * @param string $key
     * @return $this
     */
    public function bindWhere($expr, $bind = array(), $key = null)
    {
        if (is_null($key)) {
            if (preg_match("/^\w+$/", $expr)) {
                $key = $expr;
                $expr = "$expr = :$expr";
            }
        }
        return $this->bindExpr('where', $expr, $bind, $key);
    }


    /**
     *
     * @param string $expr
     * @param array $bind
     * @param string $key
     * @return $this
     */
    public function bindRawWhere($expr, $bind = array(), $key = null)
    {
        return $this->bindExpr('where', $expr, $bind, $key);
    }

    /**
     *
     * @param string $expr
     * @param array $bind
     * @param string $key
     * @return $this
     */
    public function bindGroupBy($expr, $bind = array(), $key = null)
    {
        return $this->bindExpr('groupBy', $expr, $bind, $key);
    }

    /**
     *
     * @param string $expr
     * @param array $bind
     * @param string $key
     * @return $this
     */
    public function bindHaving($expr, $bind = array(), $key = null)
    {
        return $this->bindExpr('having', $expr, $bind, $key);
    }

    /**
     *
     * @param string $expr
     * @param array $bind
     * @param string $key
     * @return $this
     */
    public function bindOrderBy($expr, $bind = array(), $key = null)
    {
        return $this->bindExpr('orderBy', $expr, $bind, $key);
    }

    /**
     *
     * @param string $expr
     * @param array $bind
     * @param string $key
     * @return $this
     */
    public function bindLimit($expr, $bind = array(), $key = null)
    {
        return $this->bindExpr('limit', $expr, $bind, $key);
    }

    /**
     *
     * @param string $expr
     * @param array $bind
     * @param string $key
     * @return $this
     */
    public function bindLock($expr, $bind = array(), $key = null)
    {
        return $this->bindExpr('lock', $expr, $bind, $key);
    }

    /**
     *
     * @see $this::bindExpr
     *
     * @param string $expr
     * @param array $bind
     * @param string $key
     * @return $this
     */
    public function bindUsing($expr, $bind = array(), $key = null)
    {
        return $this->bindExpr('using', $expr, $bind, $key);
    }


    /**
     * @param $name
     * @param $field
     * @return bool
     */
    protected function unBind($name, $field = null)
    {
        if (is_null($field)) {
            if (isset($this->expr [$name])) {
                unset($this->expr [$name]);
                return true;
            }
            return false;
        } else {
            if (isset($this->expr [$name] [$field])) {
                unset($this->expr [$name] [$field]);
                return true;
            }
            return false;
        }
    }

    /**
     * @param $field
     * @return bool
     */
    public function unBindField($field)
    {
        return $this->unBind("field", $field);
    }

    /**
     * @param $field
     * @return bool
     */
    public function unBindValues($field)
    {
        return $this->unBind("values", $field);
    }

    /**
     * @param $field
     * @return bool
     */
    public function unBindJoin($field)
    {
        return $this->unBind("join", $field);
    }

    /**
     * @param $field
     * @return bool
     */
    public function unBindWhere($field)
    {
        return $this->unBind("where", $field);
    }

    /**
     * @param $field
     * @return bool
     */
    public function unBindGroupBy($field)
    {
        return $this->unBind("groupBy", $field);
    }

    /**
     * @param $field
     * @return bool
     */
    public function unBindHaving($field)
    {
        return $this->unBind("having", $field);
    }

    /**
     * @param $field
     * @return bool
     */
    public function unBindOrderBy($field)
    {
        return $this->unBind("orderBy", $field);
    }

    /**
     * @return bool
     */
    public function unBindLimit()
    {
        return $this->unBind("limit");
    }

    /**
     * @return bool
     */
    public function unBindLock()
    {
        return $this->unBind("lock");
    }

    /**
     * @param $field
     * @return bool
     */
    public function unBindUsing($field)
    {
        return $this->unBind("using", $field);
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
     * @param array $bind
     * @param string $key
     * @return $this
     */
    protected function bindExpr($name, $expr, $bind = array(), $key = null)
    {
        if (!isset ($this->expr [$name]))
            $this->expr [$name] = array();
        if (is_null($key)) {
            $this->expr [$name] [] = $expr;
        } else {
            $this->expr [$name] [$key] = $expr;
        }
        if (is_array($bind))
            foreach ($bind as $bk => $bv)
                $this->bindValue($bk, $bv);
        else if (is_string($bind) || is_numeric($bind))
            $this->bindValue($expr, $bind);
        return $this;
    }

    /**
     * 绑定实参 bindValue( "key", "lol")
     * bindValue([])
     *
     * @param string $key
     * @param object $val
     * @return $this
     * @internal param string $name
     */
    public function bindValue($key, $val = null)
    {
        if (is_array($key))
            $this->value = array_merge($this->value, $key);
        else if (is_string($key) || is_numeric($key))
            $this->value [$key] = $val;
        return $this;
    }

    protected function getBindExprs($name)
    {
        // support since php 5.3
        return isset ($this->expr [$name]) ? $this->expr [$name] : array();
    }

    /**
     * reset bind values
     *
     * @return $this
     */
    public function reset()
    {
        $this->value = array();
        $this->expr = array();
        $this->whereCondition = ' AND ';
        return $this;
    }

    protected function parseTable()
    {
        return $this->table;
    }

    protected function parseField()
    {
        $expr = $this->getBindExprs('field');
        return $expr ? implode(',', $expr) : '*';
    }

    protected function parseValues()
    {
        $ret = $this->getBindValues();
        return $ret ? implode(',', $ret) : '';
    }

    public function getBindValues()
    {
        $values = $this->value;
        $ret = array();
        if (isset($this->expr['field']) && is_array($this->expr['field'])) {
            foreach ($this->expr['field'] as $item) {
                if (!array_key_exists($item, $values)) {
                    $ret[$item] = ":" . $item;
                } else {
                    $ret[$item] = $values[$item];
                }
            }
        }
        return $ret;
    }

    public function getBindFields()
    {
        return array_keys($this->getBindValues());
    }

    protected function parseJoin()
    {
        $expr = $this->getBindExprs('join');
        return $expr ? ' ' . implode(' ', $expr) : '';
    }

    protected function parseWhere()
    {
        if ($expr = $this->getBindExprs('where')) {
            return " WHERE " . implode($this->whereCondition, $expr);
        }
        return '';
    }

    protected function parseGroupBy()
    {
        if ($expr = $this->getBindExprs('groupBy')) {
            return " GROUP BY " . implode(',', $expr);
        }
        return '';
    }

    protected function parseHaving()
    {
        if ($expr = $this->getBindExprs('having')) {
            return " HAVING " . current($expr);
        }
        return '';
    }

    protected function parseOrderBy()
    {
        if ($expr = $this->getBindExprs('orderBy')) {
            return " ORDER BY " . implode(',', $expr);
        }
        return '';
    }

    protected function parseLimit()
    {
        if ($expr = $this->getBindExprs('limit')) {
            if (count($expr) == 1) {
                return " LIMIT " . $expr [0];
            } else if (count($expr) == 2) {
                return " LIMIT " . $expr [0] . "," . $expr [1];
            }
        }
        return '';
    }

    protected function parseLock()
    {
        if ($expr = $this->getBindExprs('lock')) {
            return ' ' . current($expr);
        }
        return '';
    }

    protected function parseSet()
    {
        $fields = array_values($this->getBindExprs('field'));
        $values = array_values($this->getBindValues());
        $ret = array();
        for ($i = 0; $i < count($fields); $i++) {
            $ret [] = "{$fields[$i]}=$values[$i]";
        }
        if ($ret) {
            return 'SET ' . implode(',', $ret);
        }
        return '';
    }

    protected function parseUsing()
    {
        if ($expr = $this->getBindExprs('using')) {
            return " USING " . implode(',', $expr);
        }
        return '';
    }
}
