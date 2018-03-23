## 开始使用

#### 安装组件
使用 composer 命令进行安装或下载源代码使用。
> composer require aweitian/sql-builder
>

#### MysqlSelectBuild :
<pre><code>
$demo = new Crud ( 'tablename' );
$demo->bindField ( "id" );
$demo->bindField ( "concat('%',:lol,'%')" );
$demo->bindField ( "name as n" );
//SELECT id,concat('%',:lol,'%'),name as n FROM tablename

$demo = new Crud ( 'tablename' );
$demo->bindField ( "id" );
$demo->bindField ( "concat('%',:lol,'%')" );
$demo->bindField ( "name as n" );
$demo->bindJoin ( 'left join tba on tba.sid = tablename.hid' );
$demo->bindJoin ( 'left join tbb on tba.sid = tbb.sid' );
$demo->bindWhere ( 'tablename.sid > 100' );
$demo->bindWhere ( 'tablename.name = \'55\'' );
//SELECT id,concat('%',:lol,'%'),name as n FROM tablename left join tba on tba.sid = tablename.hid left join tbb on tba.sid = tbb.sid WHERE tablename.sid > 100 AND tablename.name = '55'

$demo = new Crud ( 'tablename' );
$demo->bindField ( "id" );
$demo->bindField ( "concat('%',:lol,'%')" );
$demo->bindField ( "count(tablename.name) as cc" );
$demo->bindJoin ( 'left join tba on tba.sid = tablename.hid' );
$demo->bindJoin ( 'left join tbb on tba.sid = tbb.sid' );
$demo->bindWhere ( 'tablename.sid > 100' );
$demo->bindWhere ( 'tablename.name = \'55\'' );
$demo->bindGroupBy ( 'tablename.name' );
$demo->bindHaving ( 'cc > 1' );
$demo->bindLimit ( '0,100' );
//SELECT id,concat('%',:lol,'%'),count(tablename.name) as cc 
//FROM tablename left join tba on tba.sid = tablename.hid 
//left join tbb on tba.sid = tbb.sid WHERE tablename.sid > 100 AND tablename.name = '55' 
//GROUP BY tablename.name HAVING cc > 1 LIMIT 0,100
</code></pre>

#### MysqlInsertBuild:
<code><pre>
$demo = new Crud ( 'tablename' );
$demo->bindField ( 'aaa' );
$demo->bindField ( 'bbb', "concat('aa>',:bbb,'<==')" );
//INSERT INTO tablename (aaa,bbb) VALUES (:aaa,concat('aa>',:bbb,'<=='))
</code></pre>

#### MysqlReplaceBuild:
<code><pre>
$demo = new \Tian\SqlBuild\MysqlReplaceBuild ( 'tablename' );
$demo->bindField ( 'aaa' );
$demo->bindField ( 'bbb', "concat('aa>',:bbb,'<==')" );
//REPLACE INTO tablename (aaa,bbb) VALUES (:aaa,concat('aa>',:bbb,'<=='))
</code></pre>

#### MysqlUpdateBuild:
<code><pre>
$demo = new \Tian\SqlBuild\MysqlUpdateBuild ( 'tablename' );
$demo->bindField ( 'aaa' );
$demo->bindField ( 'bbb', "concat('aa>',:bbb,'<==')" );
$demo->bindWhere ( 'sid>100' );
$demo->bindOrderBy ( 'name desc' );
$demo->bindLimit ( '2' );
//UPDATE tablename SET aaa=:aaa,bbb=concat('aa>',:bbb,'<==') WHERE sid>100 ORDER BY name desc LIMIT 2
</code></pre>

#### MysqlDeleteBuild:
<code><pre>
$demo = new \Tian\SqlBuild\MysqlDeleteBuild ( 'tablename' );
$demo->bindWhere ( 'sid>100' );
$demo->bindOrderBy ( 'name desc' );
$demo->bindLimit ( '2' );
//DELETE FROM tablename WHERE sid>100 ORDER BY name desc LIMIT 2

$demo = new \Tian\SqlBuild\MysqlDeleteBuild ( 'tablename,tb2' );
$demo->bindUsing ( 'tb2' );
$demo->bindWhere ( 'tablename.sid>100' );
$demo->bindWhere ( 'tablename.sid = tb2.tb1id' );
$demo->bindOrderBy ( 'name desc' ); // 多表删除中无效
$demo->bindLimit ( '2' ); // 多表删除中无效
//DELETE FROM tablename,tb2 USING tb2 WHERE tablename.sid>100 AND tablename.sid = tb2.tb1id
</code></pre>