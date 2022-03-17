<?php

// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to index page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    // Redirect user to home page
    if ($_SESSION["stock_manager"]) {
      header("location: Product management/products.php");
    }else{
      header("location: index.php");
    }
    exit;
}
 
// Include config file
include_once('Config/DB.php');
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
        echo $username_err;
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
        echo $password_err;

    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
      echo 'not empty';
      echo $username;
      echo $password;

        // Prepare a select statement
        $sql = "SELECT id, username, password,super_admin FROM User WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
           echo 'sql prepared';

            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
              echo 'stmt execute';

                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $super_admin);
                    if(mysqli_stmt_fetch($stmt)){
                        if(md5($password) == $hashed_password){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            //check if permision
                            $_SESSION["super_admin"] = false;
                            $_SESSION["stock_manager"] = false;
                            $_SESSION["order_manager"] = false;

                            if ($super_admin == 1) {
                              $_SESSION["super_admin"] = true;
                            }elseif ($super_admin == 2) {
                              $_SESSION["stock_manager"] = true;
                              
                            }
                            elseif ($super_admin == 3) {
                              $_SESSION["order_manager"] = true;
                            }
                            // Redirect user to home page
                            if ($_SESSION["stock_manager"]) {
                              header("location: Product management/products.php");
                            }else{
                              header("location: index.php");
                            }

                        } else{
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                            echo $password_err;
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                    echo $username_err;
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
  <title>Stock managment application</title>
  <!-- imports -->
  <link rel="stylesheet" type="text/css" href="Css/loginstyle.css" media="screen" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

</head>

<body>

   <form action = "" method = "post">

    <div class="login-form">
      <center><img src="Assets/Mini logo.jpg" alt="Mini logo" width="200"></center>
      <br>
      <div class="form-group ">
        <input type="text" class="form-control" placeholder="Username" name = "username" id="UserName">
        <i class="fa fa-user"></i>
      </div>
      <div class="form-group log-status">
        <input type="password" class="form-control" placeholder="Password" name = "password" id="Passwod">
        <i class="fa fa-lock"></i>
      </div>
      <div>
      <span class="help-block"><?php echo $username_err; ?></span>
      </div>
      <div>
      <span class="help-block"><?php echo $password_err; ?></span>
      </div>
      <a class="link" href="#">Lost your password?</a>
      <button type = "submit" value = " Submit " class="log-btn">Log in</button>

    </div>
  </form>



</body>
</html>
