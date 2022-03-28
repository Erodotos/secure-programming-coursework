<?php


require_once('my_functions.php');

// check if the request came from a valid form submission
if (isset($_POST["login"])) {
	$username = $_POST["username"];
	$password = $_POST["password"];

	validate_csrf_token();

} else {
	// redirect to login page
	header("Location: index.php");
	exit();
}