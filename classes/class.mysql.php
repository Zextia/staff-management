<?php

class database {

    private $resource;
    private $num_rows = null;
    private $valid_resource = false;

    function __construct() {
	mysql_connect(HOSTNAME, USERNAME, PASSWORD);
	mysql_select_db(DATABASE);
	return $this;
    }

    function select(
	    $table = '', 
	    $conditions = '', 
	    $count = false, 
	    $group_by = '', 
	    $agg = '', 
	    $agg_col = '',
	    $agg_other_cols = ''
	    ) {
	$return = array();
	if (trim($table) != '') {
	    if (trim($conditions) == '') {
		$conditions = ' 1 = 1';
	    }
	    if ($count) {
		$sql = "SELECT count(*) as count_rows ";
	    } else {
		$sql = "SELECT * ";
	    }
	    
	    if(trim($agg) != '' && trim($agg_col) != '') {
		$sql = "SELECT $agg($agg_col) as $agg_col, $agg_other_cols ";
	    }
	    
	    $sql .= "FROM $table WHERE $conditions";
	    if(trim($group_by) != '') {
		$sql .= " GROUP BY $group_by;";
	    } else {
		$sql .= ";";
	    }
	    
	    //echo $sql."<br />";
	    $this->query($sql);
	    if ($this->valid_resource && ($this->num_rows > 0)) {
		return true;
	    } else {
		return false;
	    }
	} else {
	    return false;
	}
	return $return;
    }

    private function query($sql = '', $type = 'select') {
	if (trim($sql) != '') {
	    $this->resource = mysql_query($sql);
	    if (!$this->resource) {
		return false;
	    }
	    switch($type) {
		case 'insert':
		    //TODO return the affected number of rows
		    break;
		case 'select':
		    $this->set_number_of_results();
		    break;
		case 'update':
		    break;
		default:
		    //
	    }
	    $this->valid_resource = true;
	    //return $this->valid_resource;
	} else {
	    return false;
	}
    }

    function update($table = '', $data = array(), $conditions = '') {
	if (
		trim($table) == '' || trim($conditions) == '' || !is_array($data) || count($data) == 0
	) {
	    return false;
	}

	$sql = $this->create_update_sql($table, $data, $conditions);

	$this->query($sql, 'update');

	return $this->valid_resource;
    }

    function delete($table = '', $condition = '') {
	if (
		trim($table) == ''
		|| trim($condition) == '' 
		|| trim($condition) == '1 = 1'
		|| trim($condition) == '1=1'
		|| trim($condition) == '1 =1' 
		|| trim($condition) == '1= 1'
	) {
	    return false;
	}
	$sql = "DELETE FROM $table WHERE $condition;";

	$this->query($sql);

	return $this->valid_resource;
    }

    function insert($table = '', $data = array()) {
	if (trim($table) == '' || !is_array($data) || count($data) == 0) {
	    return false;
	}

	$sql = $this->create_insert_sql($table, $data);

	$this->query($sql);

	return $this->valid_resource;
    }

    function get_results() {
	$result = array();
	while ($row = mysql_fetch_array($this->resource)) {
	    $result[] = $row;
	}
	return $result;
    }

    function get_one_result() {
	return mysql_fetch_array($this->resource);
    }

    function get_number_of_results() {
	return $this->num_rows;
    }

    function get_db_instance() {
	return $this;
    }

    function set_number_of_results() {
	$this->num_rows = mysql_num_rows($this->resource);
    }

    private function create_insert_sql($table = '', $data = array()) {
	$sql = "INSERT INTO $table ";
	$columns = array();
	$values = array();
	$_columns = '';
	$_values = '';
	$i = 0;
	foreach ($data as $k => $v) {
	    $columns[$i] = $k;
	    $values[$i] = $v;
	    $i++;
	}

	$first = true;
	foreach ($columns as $k => $column) {
	    if ($first) {
		$_columns .= "`{$column}`";
		$_values .= "'{$values[$k]}'";
		$first = false;
	    } else {
		$_columns .= ", `{$column}`";
		$_values .= ", '{$values[$k]}'";
	    }
	}
	$sql .= " ($_columns) VALUES ($_values) ;";
	return $sql;
    }

    private function create_update_sql($table = '', $data = array(), $conditions = '') {
	$sql = "UPDATE `$table` SET ";
	$columns = array();
	$values = array();
	$_columns = '';
	$_values = '';
	$i = 0;
	foreach ($data as $k => $v) {
	    $columns[$i] = $k;
	    $values[$i] = $v;
	    $i++;
	}

	$first = true;
	foreach ($columns as $k => $column) {
	    if ($first) {
		$first = false;
	    } else {
		$sql .= ', ';
	    }
	    if(is_numeric($values[$k])) {
		$sql .= "`{$column}` = {$values[$k]}";
	    } else {
		$sql .= "`{$column}` = '{$values[$k]}'";
	    }
	}
	$sql .= " WHERE $conditions";
	return $sql;
    }

}
