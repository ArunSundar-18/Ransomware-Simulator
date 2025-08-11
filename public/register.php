<?php
// Step 1: Start session and include database connection
session_start();
require_once "../includes/db.php";

// Step 2: Check if the user is already logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
  header("location: dashboard.php");
  exit;
}

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Step 3: Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Step 3.1: Validate username
  if (empty(trim($_POST["username"]))) {
    $username_err = "Please enter a username";
  } else {
    $sql = "SELECT user_id FROM users WHERE username = :username";
    if ($stmt = $pdo->prepare($sql)) {
      $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
      $param_username = trim($_POST["username"]);

      if ($stmt->execute()) {
        if ($stmt->rowCount() == 1) {
          $username_err = "This username is already taken";
        } else {
          $username = trim($_POST["username"]);
        }
      } else {
        echo "Something went wrong. Please try again later";
      }
    }
    unset($stmt);
  }

  // Step 3.2: Validate password
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter a password";
  } elseif (strlen(trim($_POST["password"])) < 6) {
    $password_err = "Password must have at least 6 characters";
  } else {
    $password = trim($_POST["password"]);
  }

  // Step 3.3: Validate confirmation password
  if (empty(trim($_POST["confirm_password"]))) {
    $confirm_password_err = "Please confirm password";
  } else {
    $confirm_password = trim($_POST["confirm_password"]);
    if (empty($password_err) && ($password != $confirm_password)) {
      $confirm_password_err = "Password did not match";
    }
  }

  // Step 4: Insert user data into the database
  if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
    $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";
    if ($stmt = $pdo->prepare($sql)) {
      $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
      $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);

      $param_username = $username;
      $param_password = password_hash($password, PASSWORD_DEFAULT);

      if ($stmt->execute()) {
        header("location: login.php");
      } else {
        echo "Something went wrong. Please try again later!";
      }
    }
    unset($stmt);
  }
  unset($pdo);
}
?>

<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Register</title>
  <link rel="stylesheet" href="../assets/css/login-register.css">
</head>

<body class="">
  <div class="container on">
    <div class="screen">
      <h3 class="title flash">
        CONNECTION ESTABLISHED
      </h3>
      <div class="box--outer">
        <div class="box">
          <div class="box--inner">
            <div class="content">
              <div class="holder">
                <p class="register-title">Register Your Account</p>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                  <div class="row">
                    <div class="col col__left label">
                      Username
                    </div>
                    <div class="col col__center">
                      <input type="text" id="login" maxlength="32" name="username" autofocus="true" value="<?php echo $username; ?>">
                      <span><?php echo $username_err; ?></span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col col__left label">
                      Password
                    </div>
                    <div class="col col__center">
                      <input type="password" id="password" name="password"
                        data-error="" maxlength="32" autocomplete="new-password" value="<?php echo $password; ?>" />
                      <span><?php echo $password_err; ?></span>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col col__left label">
                      Re-enter
                    </div>
                    <div class="col col__center">
                      <input type="password" id="password" name="confirm_password"
                        data-error="" maxlength="32" autocomplete="new-password" value="<?php echo $confirm_password; ?>" />
                      <span><?php echo $confirm_password_err; ?></span>
                    </div>
                  </div>
                  <div class="row">
                    <button type="submit" id="submit" name="submit">[REGISTER]</button>
                  </div>
                  <p class="login_here">Already have an account? <a href="login.php">Login here</a></p>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>