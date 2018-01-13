
<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'm74664fU';
$dbname = 'hungermafia';
session_start();

$error=''; // Variable To Store Error Message 
if (isset($_POST['submit'])) {
	if (empty($_POST['username']) || empty($_POST['password'])) {
			$error = "Username or Password is invalid";
	}
	else
	{
	// Define $username and $password
	$username=$_POST['username'];
	$password=$_POST['password'];
	// Establishing Connection with Server by passing server_name, user_id and password as a parameter
	$conn   =  mysql_connect($dbhost, $dbuser, $dbpass);
	// To protect MySQL injection for Security purpose
	$username = stripslashes($username);
	$password = stripslashes($password);
	$username = mysql_real_escape_string($username);
	$password = mysql_real_escape_string($password);
	// Selecting Database
	$db = mysql_select_db("hungermafia", $conn);
    
	// SQL query to fetch information of registerd users and finds user match.
	$query = mysql_query("select * from hm_login where password='$password' AND username='$username'",$conn);
	$rows = mysql_num_rows($query);
	if ($rows == 1) {
		$_SESSION["login_user"]= $username;
		echo '<pre>';print_r($_SESSION["login_user"]);
		echo "logged in" .$username;
        header('Location:contact_details.php'); 
      
	}
	 else
     {
	 	echo "Enter correct username and password";
	 }
	mysql_close($conn); // Closing Connection
   }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Login Form </title>
</head>
<body>
<h2>Login Form</h2>
<form action="" method="post">
<label>UserName :</label>
<input id="name" name="username" placeholder="username" type="text">
<label>Password :</label>
<input id="password" name="password" placeholder="**********" type="password">
<input name="submit" type="submit" value="Login">
<span><?php echo $error; ?></span>
</form>
</div>
</div>
</body>
</html>