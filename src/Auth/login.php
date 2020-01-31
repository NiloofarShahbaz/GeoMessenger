<?php

session_start();

if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) {
    header('location: ../index.php');
    exit();
}

require_once "../dbConnect.php";
$username = $email = $password = "";
$username_err = $password_err = $form_error = "";

if($_SERVER["REQUEST_METHOD"] == 'POST') {
    // username or email validation
    if(empty(trim($_POST['username']))){
        $username_err = "Please enter your username or email";
    } elseif (strpos(trim($_POST['username']), '@')){
        $email = trim($_POST['username']);
    } else {
        $username = trim($_POST['username']);
    }

    // password validation
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }


    if(empty($username_err) && empty($password_err)){
        if(!empty($username)){
            $query = 'SELECT id, username, password FROM users WHERE username=?';
            $username_param = $username;
        } else { //email is set instead of username
            $query = 'SELECT id, username, password FROM users WHERE email=?';
            $username_param = $email;
        }
        if($stmt = mysqli_prepare($con, $query)){
            mysqli_stmt_bind_param($stmt, "s", $username_param);

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    // username or email exist, check password
                    mysqli_stmt_bind_result($stmt, $id,$username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            session_start();
                            $_SESSION['loggedIn'] = true;
                            $_SESSION['username'] = $username;
                            $_SESSION['id'] = $id;

                            header('location: ../index.php');
                        } else {
                            $form_error = "Either your username/email or password is incorrect. Please try again.";
                        }
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                } else {
                    $form_error = "Either your username/email or password is incorrect. Please try again.";
                }
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
    <title>Log in</title>
    <link rel="stylesheet" href="../../static/css/base.css" type="text/css" media="all"/>
    <link rel="stylesheet" href="../../static/bower_components/fontawesome-free-5.12.0-web/css/all.min.css"
          type="text/css" media="all">
    <script src="../../static/js/base.js"></script>
</head>
<body style="background-color: #333333;">
<div class="row justify-content-center">
    <div class="col-4 mt-5">
        <div class="card">
            <div class="card-header text-center"><h1>Log in</h1></div>
            <div class="card-body">
                <p class="error"><?php echo $form_error ?></p>
                <form method="post" action="login.php" novalidate>
                    <div class="form-field">
                        <label>Username or Email
                            <div class="<?php if ($username_err) echo 'input-wrapper'; ?>">
                                <input class="form-input" type="text" name="username" value="<?php echo $username; ?>">
                            </div>
                        </label>
                        <span class="form-error"><?php echo $username_err; ?></span>
                    </div>
                    <div class="form-field">
                        <label>Password
                            <div class="<?php if ($password_err) echo 'input-wrapper'; ?>">
                                <input class="form-input" type="password" name="password" id="loginPass">
                                <i class="fas fa-eye-slash login-eye-icon" onclick="hideOrRevealPassword()" id="passIcon"></i>
                            </div>
                        </label>
                        <span class="form-error"><?php echo $password_err; ?></span>
                    </div>

                    <button class="mt-2 btn-block" type="submit">Log In</button>
                </form>
                <div>
                    <p>Don't have an account? <a href="register.php">Create One</a>.</p>
                </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
