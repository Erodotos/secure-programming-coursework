<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Super Secure Digital Signature Service</title>

  <!-- Bootstrap -->
  <link href="css/bootstrap.min.css" rel="stylesheet">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>
  <?php

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  // include 'include/functions.php';

  require_once("include/base.php");
  ?>

  <div class="jumbotron">
    <h1>Super Secure Digital Signature Service</h1>
  </div>

  <div class="container">
    <?php
    if (check_loged_in()) {
      print('<p>Loged in user:' . $_SESSION['username']  . '</p>');
      print ' <form name="logout" class="form-horizontal" action="include/actions.php" method="post"><button style="width:100%" name="logout" type="submit">Logout</button>';
      print ' <input name="csrf_token" type="hidden" value="' . $_SESSION['csrf_token'] . '">';
      print ' </form>';
      print ' <br>';

      print ' <form name="export-public-key" class="form-horizontal" action="include/actions.php" method="post"><button style="width:100%" name="export-public-key" type="submit">Export Public Key</button>';
      print ' <input name="csrf_token" type="hidden" value="' . $_SESSION['csrf_token'] . '">';
      print ' </form>';
      print ' <br>';

      print ' <form name="sign-text" class="form-horizontal" action="include/actions.php" method="post">';
      print ' <textarea style="width:100%" name="input-text" id="input-text" maxlength="10000"></textarea>';
      print ' <div>';
      print '   <button style="margin-top: 10px; width:100%" name="sign-text" type="submit">Sign Text</button>';
      print ' </div>';
      print ' <input name="csrf_token" type="hidden" value="' . $_SESSION['csrf_token'] . '">';
      print ' </form>';
      if (isset($_GET['error'])) {
        if ($_GET['error'] == 'texttoolong') {
          print '<p>Text should be maximum 10000 characters</p>';
        }
      }
      print ' <br>';

      print ' <form name="verify-signature" class="form-horizontal" action="include/actions.php" method="post" enctype="multipart/form-data">';
      print '   <textarea placeholder="Input message here" style="width:100%" name="input-text" id="input-text" maxlength="10000"></textarea>';
      print '   <input placeholder="Input signature here" style="width:70%; margin-top: 10px;" name="signature" id="signature">';
      print '   <div style="margin-top: 10px;">';
      print '     <label>Upload Public Key File</label>';
      print '     <input type="file" name="public-key" id="public-key">';
      print '   </div>';
      print '   <div>';
      print '     <button style="margin-top: 10px; width:100%" name="verify-signature" type="submit">Verify Signature</button>';
      print '   </div>';
      print '   <input name="csrf_token" type="hidden" value="' . $_SESSION['csrf_token'] . '">';
      print ' </form>';
      if (isset($_GET['error'])) {
        if ($_GET['error'] == 'texttoolong') {
          print '<p>Text should be maximum 10000 characters</p>';
        }
      }
      if (isset($_GET['verify-signature'])) {
        if ($_GET['verify-signature'] == 'success') {
          print '<script>alert("Signature verification: Success");</script>';
        }
        if ($_GET['verify-signature'] == 'fail') {
          print '<script>alert("Signature verification: Fail");</script>';
        }
      }
      print ' <br>';
    } else {
      create_csrf_token();
      print '<form name="login" class="form-horizontal" action="include/actions.php" method="post">';
      print '  <fieldset>';
      print '    <legend>Login or sign up</legend>';
      print '    <div class="control-group">';
      print '      <label class="control-label" for="username">User name</label>';
      print '      <div class="controls">';
      print '        <input id="username" name="username" type="text" placeholder="username" class="input-medium" required="">';
      print '      </div>';
      print '    </div>';
      print '    <div class="control-group">';
      print '      <label class="control-label" for="password">Password</label>';
      print '      <div class="controls">';
      print '        <input id="password" name="password" type="password" placeholder="password" class="input-medium" required="">';
      print '      </div>';
      print '    </div>';
      print '    <div class="control-group">';
      print '      <label class="control-label" for="createaccount"></label>';
      print '      <div class="controls">';
      print '        <button id="signup" name="signup" type="submit" value="signup" class="btn btn-default">Sign up</button>';
      print '        <button id="login" name="login" type="submit" value="login" class="btn btn-default">Log in</button>';
      print '      </div>';
      print '    </div>';
      print '  </fieldset> <input name="csrf_token" type="hidden" value="' . $_SESSION['csrf_token'] . '">';
      print '</form>';
      if (isset($_GET['error'])) {
        if ($_GET['error'] == 'emptyfields') {
          print '<p class="error">Fill in all fields!</p>';
        } else if ($_GET['error'] == 'usertaken') {
          print '<p class="error">User name is already taken!</p>';
        } else if ($_GET['error'] == 'wrongcredentials') {
          print '<p class="error">Wrong credentials!</p>';
        }
      }
    }
    ?>
  </div>

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="js/bootstrap.min.js"></script>
</body>

</html>