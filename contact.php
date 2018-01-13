<?php 

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'm74664fU';
$dbname = 'hungermafia';
$conn   =  mysqli_connect($dbhost, $dbuser, $dbpass,$dbname);
if(! $conn )
{
  die('Could not connect: ' . mysql_error());
}


 	    $name = $_POST['Name'];
		$email = $_POST['Email'];
		$phone = $_POST['Phone'];
		$subject = $_POST['Subject'];
		$message = $_POST['Message'];

		$SQL = "INSERT INTO hm_contactform (name,email, phone,subject,message) VALUES ('$name', '$email', '$phone', '$subject','$message')";

		if (mysqli_query($conn, $SQL)) {
		   // echo "New record created successfully";
		} else {
		    echo "Error: " . $SQL . "<br>" . mysqli_error($conn);
		}

		mysqli_close($conn);
	

        

		$to = ' info@hungermafia.com';
		$subject = 'the subject';
		$message = 'FROM: '.$name.'  Email: '.$email.'  Phone: '.$phone.'  Subject: '.$subject.'  Message: '.$message;
		//	$headers = 'From: ankita.chavda@openxcell.info' . "\r\n";

		$autoTo = $email;
		//$autoreply = 'Dear:' .$email;
	    $autoreply ='Dear:' .$email .'You are receiving this email because you are fast and furious and thus AWESOME.
				  We will get you offers that you can’t refuse. So keep an eye out. '; //change this to your message

		mail($autoTo, "Auto Reply Subject", $autoreply, 'From: info@hungermafia.com');
		 
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) { // this line checks that we have a valid email address
		mail($to, $subject, $message, $headers); //This method sends the mail.
		echo "Your email was sent!"; // success message
		}else{
		echo "Invalid Email!";
		}

?>
