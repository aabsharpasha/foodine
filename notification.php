<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'm74664fU';
$dbname = 'hungermafia';
$conn   =  mysqli_connect($dbhost, $dbuser, $dbpass,$dbname);
if(! $conn ) {
  die('Could not connect: ' . mysql_error());
}
	$email = $_POST['Email']; 

	$SQL = "INSERT INTO hm_notification (email) VALUES ('$email')";

	if (mysqli_query($conn, $SQL)) {
	   // echo "New record created successfully";
	} else {
	    echo "Error: " . $SQL . "<br>" . mysqli_error($conn);
	}

	mysqli_close($conn);
	

	$to = 'info@hungermafia.com';
	$subject = 'the subject';
	$message = 'Email: '.$email;	
	//	$headers = 'From: ankita.chavda@openxcell.info' . "\r\n";

	$autoTo = $email;
	$autoreply ='Dear:' .$email .'You are receiving this email because you are fast and furious and thus AWESOME.
				 We will get you offers that you can’t refuse. So keep an eye out. '; //change this to your message


	mail($autoTo, "Auto Reply Subject", $autoreply, 'From: info@hungermafia.com');
	 
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) { // this line checks that we have a valid email address
		mail($to,$subject, $message, $headers); //This method sends the mail.
		echo "Your email was sent!"; // success message
	}else{
		echo "Invalid Email!";
	}

?>