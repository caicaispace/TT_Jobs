<?php
/**
 * Created by PhpStorm.
 * User: yangcai
 * Date: 2018/6/28
 * Time: 14:09
 */

header("Content-type: text/html; charset=utf-8");

// $prefix = '';
// 替换所有表的表前缀

// if(@$_GET['prefix']){
// 	$prefix = 'rm_';
// 	foreach($tables as $key => $val){
// 		$tableName = $val['TABLE_NAME'];
// 		$string = explode('_',$tableName);
// 		if($string[0] != $prefix){
// 			$string[0] = $prefix;
// 			$newTableName = implode('_', $string);
// 			mysql_query('rename table '.$tableName.' TO '.$newTableName);
// 		}
// 	}
// 	echo "替换成功！";exit();
// }

/**
 * 生成mysql数据字典
 */
class showTables
{
	private $_mysql_conn;

	private $_db_host = "rdskk56yz58kwr82t3im.mysql.rds.aliyuncs.com";
	private $_db_username = "S945_699pic";
	private $_db_password = "YC_Q7bhEm2Qn";
	private $_db_name = "db_699pic_tongji";

	private $_not_show_table = [];    //不需要显示的表
	private $_not_show_field = [];   //不需要显示的字段

	private $_tables = [];
	private $_html = '';

	function __construct()
	{
		$this->_mysqlConn();

		$this->getTables();
		$this->getTablesDetail();

		$this->_mysqlClose();

		$this->_render();
	}

	function getHtml()
	{
		return $this->_html;
	}

	function getTables()
	{
		$table_result = mysqli_query($this->_mysql_conn, 'show tables');

		//取得所有的表名
		while($row = mysqli_fetch_array($table_result)){
			if(!in_array($row[0],$this->_not_show_table)){
				$this->_tables[]['TABLE_NAME'] = $row[0];
			}
		}
	}

	function getTablesDetail()
	{
		if (empty($this->_tables)) {
			return false;
		}

		//循环取得所有表的备注及表中列消息
		foreach ($this->_tables as $k=>$v) {
		    $sql  = 'SELECT * FROM ';
		    $sql .= 'INFORMATION_SCHEMA.TABLES ';
		    $sql .= 'WHERE ';
		    $sql .= "table_name = '{$v['TABLE_NAME']}'  AND table_schema = '{$this->_db_name}'";
		    $table_result = mysqli_query($this->_mysql_conn, $sql);
		    while ($t = mysqli_fetch_array($table_result) ) {
		        $this->_tables[$k]['TABLE_COMMENT'] = $t['TABLE_COMMENT'];
		    }

		    $sql  = 'SELECT * FROM ';
		    $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
		    $sql .= 'WHERE ';
		    $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$this->_db_name}'";

		    $fields = [];
		    $field_result = mysqli_query($this->_mysql_conn, $sql);
		    while ($t = mysqli_fetch_array($field_result) ) {
		        $fields[] = $t;
		    }
		    $this->_tables[$k]['COLUMN'] = $fields;
		}
	}

	private function _mysqlConn()
	{
		$this->_mysql_conn = @mysqli_connect(
			$this->_db_host,
			$this->_db_username,
			$this->_db_password,
			$this->_db_name
		) or die("Mysql connect is error.");
		mysqli_query($this->_mysql_conn, 'SET NAMES utf8');
	}

	private function _mysqlClose()
	{
		mysqli_close($this->_mysql_conn);
	}

	private function _render()
	{
		$this->_html = '';
		//循环所有表
		foreach ($this->_tables as $k=>$v) {
		    $this->_html .= '	<h3>' . ($k + 1) . '、' . $v['TABLE_COMMENT'] .'  （'. $v['TABLE_NAME']. '）</h3>'."\n";
		    // $this->_html .= '	<h3>' . $v['TABLE_NAME']. ' -- ' . $v['TABLE_COMMENT'] . '</h3>'."\n";

		    $this->_html .= '	<table border="1" cellspacing="0" cellpadding="0" width="100%">'."\n";
		    $this->_html .= '		<tbody>'."\n";
			$this->_html .= '			<tr>'."\n";
			$this->_html .= '				<th>字段名</th>'."\n";
			$this->_html .= '				<th>数据类型</th>'."\n";
			$this->_html .= '				<th>默认值</th>'."\n";
			$this->_html .= '				<th>允许非空</th>'."\n";
			$this->_html .= '				<th>自动递增</th>'."\n";
			$this->_html .= '				<th>备注</th>'."\n";
			$this->_html .= '			</tr>'."\n";

		    foreach ($v['COLUMN'] as $f) {
				if(!is_array(@$this->_not_show_field[$v['TABLE_NAME']])){
					$this->_not_show_field[$v['TABLE_NAME']] = [];
				}
				if(!in_array($f['COLUMN_NAME'],$this->_not_show_field[$v['TABLE_NAME']])){
					$this->_html .= '			<tr>'."\n";
					$this->_html .= '				<td class="c1">' . $f['COLUMN_NAME'] . '</td>'."\n";
					$this->_html .= '				<td class="c2">' . $f['COLUMN_TYPE'] . '</td>'."\n";
					$this->_html .= '				<td class="c3">' . $f['COLUMN_DEFAULT'] . '</td>'."\n";
					$this->_html .= '				<td class="c4">' . $f['IS_NULLABLE'] . '</td>'."\n";
					$this->_html .= '				<td class="c5">' . ($f['EXTRA']=='auto_increment'?'是':'&nbsp;') . '</td>'."\n";
					$this->_html .= '				<td class="c6">' . $f['COLUMN_COMMENT'] . '</td>'."\n";
					$this->_html .= '			</tr>'."\n";
				}
		    }
		    $this->_html .= '		</tbody>'."\n";
			$this->_html .= '	</table>'."\n";
		}
	}
}
?>


<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>MySql 数据字典</title>

<style>
body, td, th { font-family: "微软雅黑"; font-size: 14px; }
.warp{margin:auto; width:900px;}
.warp h3{margin:0px; padding:0px; line-height:30px; margin-top:10px;}
table { border-collapse: collapse; border: 1px solid #CCC; background: #efefef; }
table th { text-align: left; font-weight: bold; height: 26px; line-height: 26px; font-size: 14px; text-align:center; border: 1px solid #CCC; padding:5px;}
table td { height: 20px; font-size: 14px; border: 1px solid #CCC; background-color: #fff; padding:5px;}
.c1 { width: 120px; }
.c2 { width: 120px; }
.c3 { width: 150px; }
.c4 { width: 80px; text-align:center;}
.c5 { width: 80px; text-align:center;}
.c6 { width: 270px; }
</style>
</head>
<body>
<div class="warp">
	<h1 style="text-align:center;">MySql 数据字典</h1>
	<?php echo (new showTables)->getHtml() ; ?>
</div>
</body>
</html>

