<?php
require_once 'classes/class.utility.php';
utility::check_login_and_redirect();
// User will be redirected to login page before it reaches this section

require_once 'config.php';
require_once 'classes/class.db.php';
require_once 'classes/class.user.php';
require_once 'classes/class.department.php';

$db = new database;
$dept = new department($db);
$user = new user($db);

$departments = $dept->get_departments();

$_departments = array();
foreach ($departments as $department) {
    $_departments[$department['id']] = $department['name'];
}

$users = array();
foreach ($_departments as $_k => $_department) {
    //$_departments[$department['id']] = $department['name'];
    $user->db->select('users', "department_id = $_k", 1);
    $_rows = $user->db->get_results();
    $users[$_k] = $_rows[0]['count_rows'];
}


//utility::pr($departments);
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

        <title>Dashboard </title>

        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="css/dashboard.css" rel="stylesheet">

    </head>

    <body>

        <?php
//include_once 'nav.php';
        ?>

        <div class="container-fluid">
            <div class="row">
                <?php
                include_once 'sidebar.php';
                ?>
                <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
                    <h2 class="sub-header">Welcome to Staff Management</h2>
                    <?php
                    //utility::pr($users);
                    //utility::pr($_departments);
                    ?>
                    <div id="chart_div"></div>
                </div>
            </div>
        </div>

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/jsapi.js"></script>
        <script type="text/javascript">

            // Load the Visualization API and the piechart package.
            google.load('visualization', '1.0', {'packages': ['corechart']});

            // Set a callback to run when the Google Visualization API is loaded.
            google.setOnLoadCallback(drawChart);

            // Callback that creates and populates a data table,
            // instantiates the pie chart, passes in the data and
            // draws it.
            function drawChart() {

                // Create the data table.
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Department');
                data.addColumn('number', 'Staff');
                data.addRows([
<?php
if (is_array($users) && count($users)) {
    $first = true;
    $_string = '';
    $_comma = '';
    foreach ($users as $k => $v) {
        if($first) {
            $first = false;
            $_string .= "['$_departments[$k]', $v]";
        } else {
            $_string .= ",['$_departments[$k]', $v]";
        }
    }
}
echo $_string;
?>
                ]);

                // Set chart options
                var options = {
                    'title': 'Details of departments',
                    'width': 650,
                    'height': 400,
                    is3D: true
                };

                // Instantiate and draw our chart, passing in some options.
                var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            }
        </script>
    </body>
</html>
