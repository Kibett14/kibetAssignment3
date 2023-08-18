<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location:admin.html");
    exit;
}

// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: admin.html");
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Login | EPL BLOG</title>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="stylesheet" href="http://localhost/EPL/css/style.css">
</head>
<body>

    
 <!-----------------------header section------------------------------------>
 <header>
    <nav class="nav-bar">

      <div class="nav-logo">
        <a href="#" class="logo-pic"><img src="http://localhost/EPL/images/pl log.png" alt=""></a>
        <a href="index.html" class="logo-brand">EPL BLOG</a>
      </div>

      <div class="nav-menu">
        <ul class="nav-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="table.html">Table</a></li>
            <li><a href="survey.html">Survey</a></li>
        </ul>
      </div>

    </nav>
</header>

<!----------------------------login form-------------------------->
   <section id="login-form">
    <div class="form-container">
       <!--- <i class="uil uil-times form-close"></i>--->
    <div class="login-form">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h2>Login</h2>

            <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

            <div class="input-box">
                <input type="text" name="username" placeholder="Enter your Username" required <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?> value="<?php echo $username; ?>"/>
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
                <i class="uil uil-envelope-alt email"></i>

            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="Enter your password" required <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?> />
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
                <i class="uil uil-lock-alt password"></i>
                <i class="uil uil-eye-slash pw-hide"></i>
            </div>

            <div class="option-field">
                <span class="checkbox">
                    <input type="checkbox" id="check">
                    <label for="check">Remember me.</label>
                </span>
                <a href="reset.php" class="forgot-pw">Forgot password?</a>
            </div>

            <button class="button">Login</button>

            <div class="signup">
                Don't have an account?
                <a href="signup.php" id="signup">Signup</a>
            </div>
        </form>
    </div>
    </div>
   </section>

   <footer class="footer">
    <div class="footer-content">
        EPL BLOG &copy; 2023,   All Rights Reserved.
    </div>
   </footer>
   
</body>
</html>