<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DATABASE FUNCTIONS

// Get the databaase PDO sqlite try catch
function get_db()
{
    try {
        $db = new PDO('sqlite:../db/ds_service.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        print("<p>Error connecting to database: {$e->getMessage()}</p>");
        exit();
    }
}
function check_uniqueness($db, $username)
{
    $check = $db->prepare("SELECT * FROM users_new WHERE username=:name");
    $check->bindParam(':name', $username);
    $result = $check->execute();

    if ($result) {
        while ($row = $check->fetch()) {
            return False;
        }
        return True;
    }

    return False;
}

function add_user($db, $username, $password)
{
    // hash the password
    $password = password_hash($password, PASSWORD_DEFAULT);
    $rsa_keys = generate_rsa_key_pair();
    $insert = $db->prepare("INSERT INTO users_new VALUES(:name, :pass, :public_key, :private_key)");
    $private_key = encrypt_private_key($rsa_keys[1], $username);
    $insert->bindParam(':name', $username);
    $insert->bindParam(':pass', $password);
    $insert->bindParam(':public_key', $rsa_keys[0]);
    $insert->bindParam(':private_key', $private_key);
    $insert->execute();
}

// Destroy the session token
function logout()
{
    unset($_SESSION['csrf_token']);
    unset($_SESSION['csrf_token_time']);
    unset($_SESSION['username']);
    print_r($_SESSION);
}

function login($username, $password)
{
    logout();

    try {
        $db = get_db();


        $check = $db->prepare("SELECT * FROM users_new WHERE username=:name");
        $check->bindParam(':name', $username);
        $result = $check->execute();

        while ($row = $check->fetch()) {
            if (!password_verify($password, $row['password'])) {
                return False;
            }
            create_csrf_token();
            $_SESSION["username"] = $row['username'];
            return True;
        }

        return False;
    } catch (PDOException $e) {
        print($e->getMessage());
    }
}

// CSRF FUNCTIONS
function check_loged_in()
{
    // echo $_SESSION['csrf_token'];
    if (isset($_SESSION['username'])) {
        return True;
    }
    return False;
}

// create secure csrf token with time
function create_csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $token = bin2hex(openssl_random_pseudo_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
    }
}

// validate csrf token
function validate_csrf_token()
{

    if (isset($_SESSION['csrf_token']) && isset($_POST['csrf_token'])) {
        if ($_SESSION['csrf_token'] === $_POST['csrf_token']) {
            if ($_SESSION['csrf_token_time'] < time() - 3600) {
                logout();
                exit("<p>Invalid CSRF token.</p>");
            }
            return true;
        }
    }
    logout();
    exit("<p>Invalid CSRF token.</p>");
}

// UTILITY FUNCTIONS
function validate($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generate_rsa_key_pair()
{
    $config = array(
        "digest_alg" => "sha256",
        "private_key_bits" => 2048,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    );

    $res = openssl_pkey_new($config);

    openssl_pkey_export($res, $privKey);
    $pubKey = openssl_pkey_get_details($res);
    $pubKey = $pubKey["key"];

    return array($pubKey, $privKey);
}

// encrypt data using aes
function encrypt_private_key($data, $username)
{
    $key = $username;
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

// decrypt data using aes
function decrypt_private_key($data, $username)
{
    $key = $username;
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, 'AES-256-CBC', $key, 0, $iv);
}


// export public key in pem format
function export_public_key()
{
    try {
        $db = get_db();

        // select the public key
        $check = $db->prepare("SELECT public_key FROM users_new WHERE username=:name");
        $check->bindParam(':name', $_SESSION['username']);
        $result = $check->execute();

        while ($row = $check->fetch()) {
            return $row['public_key'];
        }

        return "ERROR EXPORTING PUBLIC KEY";
    } catch (PDOException $e) {
        print($e->getMessage());
    }
}

// generate digital signature
function sign_text($data)
{
    // get private_key from database
    $db = get_db();

    // select the private key
    $check = $db->prepare("SELECT private_key FROM users_new WHERE username=:name");
    $check->bindParam(':name', $_SESSION['username']);
    $result = $check->execute();

    $private_key = "";
    while ($row = $check->fetch()) {
        $private_key = $row['private_key'];
    }

    $private_key = decrypt_private_key($private_key, $_SESSION['username']);
    openssl_sign($data, $signature, $private_key);
    return base64_encode($signature);
}

// verify digital signature
function verify_signature($data, $signature, $public_key)
{
    $result = openssl_verify($data, base64_decode($signature), $public_key);
    return $result;
}
