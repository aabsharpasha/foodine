<?php
session_start();
if(isset($_SESSION["login_user"])){
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'm74664fU';
$dbname = 'hungermafia';

$conn   =  mysql_connect($dbhost, $dbuser, $dbpass);
$db = mysql_select_db("hungermafia", $conn);

  $sql = mysql_query("select * from hm_contactform",$conn);

		if (mysql_num_rows($sql) > 0) {
		    // output data of each row
		    echo '<table cellpadding="10">
	    <tr>
	        <th>ID</th>
	        <th>Name</th>
	        <th>Email</th>
	        <th>Phone</th>
	        <th>Subject</th>
	        <th>Message</th>
	    </tr>';
		    while($row = mysql_fetch_assoc($sql)) {
		
		        echo '
        <tr>
            <td>'.$row['id'].'</td>
            <td>'.$row['Name'].'</td>
            <td>'.$row['Email'].'</td>
            <td>'.$row['Phone'].'</td>
            <td>'.$row['Subject'].'</td>
            <td>'.$row['Message'].'</td>
        </tr>';
		    }
		    echo '
        </table>';
		} else {
		    echo "0 results";
		}?>
		Click here to <a href="logout.php" tite="Logout">Logout.
<?php
}
else
{
	echo "please login first";
}
?>
