<?php

require_once("base.php");
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

	header("Location: ../index.php?signup=success");
} else if (isset($_POST["login"])) {

	if (check_loged_in()) {
		header("Location: ../index.php?error=alreadyloggedin");
		exit();
	}

	$username = $_POST["username"];
	$password = $_POST["password"];

	if (login($username, $password)) {
		header("Location: ../index.php?login=success");
	} else {
		header("Location: ../index.php?error=wrongcredentials");
	}
	exit();
} else if (isset($_POST["logout"])) {
	logout();
	header("Location: ../index.php");
	exit();
} else if (isset($_POST["export-public-key"])) {
	header('Content-Type: application/x-pem-file');
	header("Cache-Control: no-store, no-cache");
	header('Content-Disposition: attachment; filename="key.pem"');
	echo export_public_key();
} else {
	// redirect to login page
	header("Location: index.php");
	exit();
}
