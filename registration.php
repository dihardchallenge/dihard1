<?php

if(empty($_POST['submit']))
{
	echo "Form is not submitted!";
	exit;
}
if(empty($_POST["systemname"]) ||
   empty($_POST["doi"]) ||
   empty($_POST["email"]) ||
   empty($_POST["pin"]))
	{
		echo "Please fill the form";
		exit;
	}

$systemname = $_POST["systemname"];
$systemnameclean = trim(preg_replace('/\s+/', ' ', $systemname));
$doi = $_POST["doi"];
$email = $_POST["email"];
$pin = $_POST["pin"];


$file = 'submission/submission.csv';
$current = date("Y-m-d H:i:s") . "\t" . $systemnameclean . "\t" . $doi . "\t" . $email . "\t" . $pin . "\n";

file_put_contents($file, $current, FILE_APPEND | LOCK_EX);

//mail( 'dihardchallenge@gmail.com' , 'DIHARD New form submission' , 
//"New submission:\n email: $email,\n DOI: $doi"  );
//mail( 'shi@shiyu.fr' , 'DIHARD New form submission' , 
//"New submission:\n email: $email,\n DOI: $doi"  );

header('Location: submitted.html');

?>