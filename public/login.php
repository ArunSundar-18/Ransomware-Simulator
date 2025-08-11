<?php
// Step 1: Start session and check if already logged in
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
  header("location: dashboard.php");
  exit;
}

// Step 2: Include database connection
require_once "../includes/db.php";

$username = $password = "";
$err = "";

// Step 3: Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim(filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING));
  $sql = "SELECT user_id, username, password FROM users WHERE username = :username";

  if ($stmt = $pdo->prepare($sql)) {
    $stmt->bindParam(":username", $username, PDO::PARAM_STR);

    if ($stmt->execute()) {
      if ($stmt->rowCount() == 1) {
        if ($row = $stmt->fetch()) {
          $hashed_password = $row["password"];

          if (password_verify(filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING), $hashed_password)) {
            $_SESSION["loggedin"] = true;
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["username"] = $row["username"];
            header("location: dashboard.php");
            exit;
          } else {
            $err = "Invalid password";
          }
        }
      } else {
        $err = "Invalid username";
      }
    } else {
      $err = "Something went wrong. Please try again later!";
    }
    unset($stmt);
  }
  unset($pdo);
}
?>

<!-- Step 4: HTML Sections -->
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Ransomware</title>
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
                <b>Welcome to command and control center</b> — Please authenticate with &nbsp;your administrator
                credentials to access the dashboard
                <br>
                <br>
                <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                  <div class="row">
                    <div class="col col__left label">
                      Username
                    </div>
                    <div class="col col__center">
                      <input type="text" id="login" maxlength="32" name="username" autofocus="true" required>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col col__left label">
                      Password
                    </div>
                    <div class="col col__center">
                      <input type="password" id="password" name="password" required placeholder=""
                        data-error="" maxlength="32" autocomplete="new-password" />
                    </div>
                  </div>
                  <!-- <b class="flash">ACCESS DENIED</b> -->
                  <div class="row">
                    <button type="submit" id="submit" name="submit">[LOGIN]</button>
                  </div>
                  <?php if (!empty($err)) {
                    echo '<div class="error">' . htmlspecialchars($err) . '</div>';
                  } ?>
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