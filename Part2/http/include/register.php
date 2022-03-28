<?php


require_once('my_functions.php');

// check if the request came from a valid form submission
if (isset($_POST["signup"])) {
	$username = $_POST["username"];
	$password = $_POST["password"];

	// check if username and password are not empty
	if (empty($username) || empty($password)) {
		header("Location: ../index.php?error=emptyfields");
		exit();
	}

	// // validate credentials
	$username = validate($username);
	$password = validate($password);

	// check if username already exists
	if (check_uniqueness(get_db(), $username) === false) {
		header("Location: ../index.php?error=usertaken");
		exit();
	}

	// // insert new user into database
	add_user(get_db(), $username, $password);
} else if (isset($_POST["login"])) {
} else {
	// redirect to login page
	header("Location: index.php");
	exit();
}
