<?php

@session_start();

/**
 * Description of class
 *
 * @author tcrc
 */
class user {

    public $db;
    public $user = array();
    private $is_logged_in = false;
    private $table = 'users';

    function __construct($db) {
	$this->db = $db;
    }

    function login($username, $password, $remember_me = false) {
	if (trim($username) != '' && trim($password) != '') {
	    $conditions = " "
		    . "username = '$username' "
		    . "&& password = '" . sha1($password) . "'";
	    if ($this->db->select($this->table, $conditions)) {
		$this->user = $this->db->get_results();
		$this->is_logged_in = true;
		$_SESSION['id'] = $this->user[0]['id'];
		if ($remember_me) {
		    $rand_hash = sha1(time() . microtime() . rand() . rand());
		    $data = array('hash' => $rand_hash);
		    $_conditions = "id={$_SESSION['id']}";
		    if ($this->db->update($this->table, $data, $_conditions)) {
			setcookie('remember_me', $rand_hash);
		    }
		}
		return true;
	    } else {
		return false;
	    }
	} else {
	    return false;
	}
    }

    function logout() {
	$this->is_logged_in = false;
	$_hash = $_COOKIE['remember_me'];
	setcookie('remember_me', '', time() - (3600 * 12));
	$this->remove_hash($_hash);
	unset($_SESSION['id']);
	unset($this->user);
	return true;
    }

    function is_logged_in() {
	return $this->is_logged_in;
    }

    function get_name() {
	return $this->user['fname'] . ' ' . $this->user['lname'];
    }

    function get_email() {
	return $this->user['email'];
    }

    function add($post = array()) {
	if (!utility::is_post()) {
	    return false;
	}
	$user = array();
	$user['name'] = $post['name'];
	$user['department_id'] = $post['department_id'];
	$user['gender'] = $post['gender'];
	$user['username'] = $post['username'];
	$user['password'] = sha1($post['password']);
	$user['dob'] = $post['dob'];
	$user['doj'] = $post['doj'];
	$user['description'] = $post['description'];
	$user['type'] = $post['type'];

	return $this->db->insert($this->table, $user);
    }

    function update($post = array(), $condition = '') {
	if (!utility::is_post() || trim($condition) == '') {
	    return false;
	}
	$user = array();
	$user['name'] = $post['name'];
	$user['department_id'] = $post['department_id'];
	$user['gender'] = $post['gender'];
	$user['username'] = $post['username'];
	$user['password'] = sha1($post['password']);
	$user['dob'] = $post['dob'];
	$user['doj'] = $post['doj'];
	$user['description'] = $post['description'];
	$user['type'] = $post['type'];

	return $this->db->update($this->table, $user, $condition);
    }

    function is_valid_cookie($cookie = '') {
	if (empty($cookie)) {
	    return false;
	}
	$conditions = " "
		. "hash = '$cookie' ";
	if ($this->db->select($this->table, $conditions)) {
	    $this->user = $this->db->get_results();
	    if (is_array($this->user[0]) && count($this->user[0])) {
		$_SESSION['id'] = $this->user[0]['id'];
		return true;
	    } else {
		return false;
	    }
	} else {
	    return false;
	}
    }

    function get_staff() {
	return $this->get_users(3);
    }

    function get_department_admins() {
	return $this->get_users(2);
    }

    private function get_users($type = 3, $conditions = '', $count = false) {
	if (trim($conditions) == '') {
	    $conditions = "type=$type";
	}
	if ($this->db->select($this->table, $conditions, $count)) {
	    return $this->db->get_results();
	} else {
	    return false;
	}
    }

    function department_admin_exists($department_id = 0) {
	if (!$department_id) {
	    return false;
	}
	$_data = $this->get_users(2, "department_id = $department_id", 1);
	if (is_array($_data) && count($_data) && isset($_data[0]['count_rows'])) {
	    return $_data[0]['count_rows'];
	} else {
	    return false;
	}
    }

    function get_staff_types() {
	return array(1 => 'Permanent', 2 => 'Contractual');
    }

    function get_staff_status() {
	return array(1 => 'Active', 2 => 'Inactive');
    }

    function remove_existing_department_admin($department_id = 0) {
	if (!$department_id) {
	    return false;
	} else {
	    if ($this->db->update($this->table, array('type' => 3), "`department_id` = $department_id")) {
		return true;
	    } else {
		return false;
	    }
	}
    }

    function remove_hash($_hash = '') {
	$this->db->update($this->table, array('hash' => ''), "`hash` = '$_hash'");
    }
    
    function get_user_details($id = 0) {
	if(!$id) {
	    return false;
	}
	if($this->db->select($this->table, "id=$id")) {
	    return $this->db->get_results();
	}
	return false;
    }

}
