<?php

if (isset($_GET['action']) && (trim($_GET['action']) != '' || $_GET['action'] == 'login')) {
	header("Location:login.php");
}

switch($_GET['action']) {
	case 'dashboard':
		$msg = '';
		if(isset($_GET['msg']) && trim($_GET['msg']) != '') {
			$msg = "?msg={$_GET['msg']}";
		}
		header("Location:dashboard.php$msg");
		break;
	case 'logout':
		header("Location:logout.php");
		break;
	case 'list_departments':
		header("Location:list_departments.php");
		break;
	case 'add_department':
		header("Location:add_department.php");
		break;
	case 'list_leavetypes':
		header("Location:list_leavetypes.php");
		break;
	case 'add_leavetype':
		header("Location:add_leavetype.php");
		break;
	case 'delete_leavetype':
		header("Location:delete_leavetype.php?id=".$_GET['id']);
		break;
	case 'list_staff':
		header("Location:list_staff.php");
		break;
	case 'list_department_admins':
		header("Location:list_department_admins.php");
		break;
	case 'add_staff':
		header("Location:add_staff.php");
		break;
	case 'edit_staff':
		header("Location:edit_staff.php?id=".$_GET['id']);
		break;
	case 'check_staff':
		header("Location:check_staff.php?username=".$_POST['username']);
		break;
	default:
		header("Location:logout.php");
}
