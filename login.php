<?php
session_start();
// Check if data is POST

require_once 'config.php';
require_once 'classes/class.db.php';
require_once 'classes/class.user.php';
require_once 'classes/class.utility.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$db = new database();
	$user = new user($db);
	if(isset($_POST['remember_me']) && trim($_POST['remember_me']) && $_POST['remember_me'] == 1) {
		if ($user->login($_POST['username'], $_POST['password'], 1)) {
			header("Location:index.php?action=dashboard");
		} else {
			header("Location:login.php?error=2");
		}
	} else {
		if ($user->login($_POST['username'], $_POST['password'])) {
			header("Location:index.php?action=dashboard&type=1&msg=Welcome");
		} else {
			header("Location:login.php?error=1");
		}
	}
} else {
	// Check if a user is already logged in
	
	// Find a "remember_me" cookie, if found, check with valid data
	// If valid data found, then login automatically
	
	if(isset($_COOKIE['remember_me']) && trim($_COOKIE['remember_me']) != '') {
		$db = new database();
		$user = new user($db);
		if($user->is_valid_cookie($_COOKIE['remember_me'])) {
			header("Location:index.php?action=dashboard&type=3");
		}
	}
	
	// If no cookie or invalid cookie found, then check if session exists
	// If no session found, then send user to Login screen, 
	//	to dashboard otherwise
	
	
	
	require_once 'classes/class.utility.php';
	$data = utility::check_login();

	if (utility::check_login()) {
		header("Location:index.php?action=dashboard&type=2");
	}
}

if (isset($_GET['error']) && trim($_GET['error']) != '') {
	$message = '';
	switch ($_GET['error']) {
		case 1:
			$message = 'Username or Password do not match';
			break;
		default:
			$message = 'Please fill the login form';
	}
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Signin Template for Bootstrap</title>

        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <!--<link href="signin.css" rel="stylesheet">-->

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>

        <div class="container">

            <form class="form-signin" id="form_login" action="login.php" method="POST">
				<?php
				if (isset($message) && trim($message) != '') {
					?>
					<h2 class="form-signin-heading"><?= $message ?></h2>
					<?php
				}
				?>
                <h2 class="form-signin-heading">Please sign in</h2>
                <label for="inputEmail" class="sr-only">Email address</label>
                <input name="username" type="text" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
                <label for="inputPassword" class="sr-only">Password</label>
                <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="1" name="remember_me"> Remember me
                    </label>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            </form>
            <a href="fb.php" class="btn btn-info">Login using Facebook</a>

        </div> <!-- /container -->


        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    </body>
</html>
