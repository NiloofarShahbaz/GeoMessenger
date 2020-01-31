<?php
session_start();

if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) {
    header('location: ../index.php');
    exit();
}

require_once "../dbConnect.php";
$username = $email = $password = $password2 = "";
$username_err = $email_err = $password_err = $password2_err = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // username validation
    # TODO: username should not have special characters like @
    if (empty(trim($_POST['username']))) {
        $username_err = "Please enter a username.";
    } else {
        $query = "SELECT * FROM users WHERE username=?";
        if ($stmt = mysqli_prepare($con, $query)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST['username']);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST['username']);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        mysqli_stmt_close($stmt);
    }

    //email validation
    $email_regex = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
    var_dump(trim($_POST['email']));
    if (empty(trim($_POST['email']))) {
        $email_err = "Please enter your email.";
    } elseif (!preg_match($email_regex, trim($_POST['email']))) {
        var_dump(trim($_POST['email']));
        $email_err = "Please Enter a valid email.";
    } else {
        $email = trim($_POST['email']);
    }

    //password validation
    if (empty(trim($_POST['password']))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST['password'])) < 6) {
        $password_err = "Your password must be at least 6 characters.";
    } elseif (trim($_POST['password']) == "123456") {
        $password_err = "Your password is too easy.";
    } else {
        $password = trim($_POST["password"]);
    }

    //confirm password validation
    if (empty(trim($_POST['password2']))) {
        $password2_err = "Please confirm your password.";
    } else {
        $password2 = trim($_POST['password2']);
        if (empty($password_err) && ($password != $password2)) {
            $password2_err = "Your password did not match.";
        }
    }


    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($password2_err)) {
        $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        if ($stmt = mysqli_prepare($con, $query)) {
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_email, $param_password);
            $param_username = $username;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);

            if (mysqli_stmt_execute($stmt)) {
                header("location: login.php");
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($con);

}
?>

<!Doctype html>
<html lang="en">
<head>
    <title>Sign up</title>
    <link rel="stylesheet" href="../../static/css/base.css" type="text/css" media="all"/>
    <link rel="stylesheet" href="../../static/bower_components/fontawesome-free-5.12.0-web/css/all.min.css"
          type="text/css" media="all">
</head>
<body style="background-color: #333333;">
<div class="row justify-content-center">
    <div class="col-4 mt-5">
        <div class="card">
            <div class="card-header text-center"><h1>Sign up</h1></div>
            <div class="card-body">
                <form method="post" action="register.php" novalidate>
                    <div class="form-field">
                        <label>Username
                            <div class="<?php if ($username_err) echo 'input-wrapper'; ?>">
                                <input class="form-input" type="text" name="username" value="<?php echo $username; ?>">
                            </div>
                        </label>
                        <span class="form-error"><?php echo $username_err; ?></span>
                    </div>
                    <div class="form-field">
                        <label>Email
                            <div class="<?php if ($email_err) echo 'input-wrapper'; ?>">
                                <input class="form-input" type="text" name="email" value="<?php echo $email; ?>">
                            </div>
                        </label>
                        <span class="form-error"><?php echo $email_err; ?></span>
                    </div>
                    <div class="form-field">
                        <label>Password
                            <div class="<?php if ($password_err) echo 'input-wrapper'; ?>">
                                <input class="form-input" type="password" name="password">
                            </div>
                        </label>
                        <span class="form-error"><?php echo $password_err; ?></span>
                    </div>
                    <div class="form-field">
                        <label>Confirm Password
                            <div class="<?php if ($password2_err) echo 'input-wrapper'; ?>">
                                <input class="form-input" type="password" name="password2">
                            </div>
                        </label>
                        <span class="form-error"><?php echo $password2_err; ?></span>
                    </div>

                    <button class="mt-2 btn-block" type="submit">Sign Up</button>
                </form>
                <div>
                    <p>Already a user? <a href="login.php">Log In</a>.</p>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
