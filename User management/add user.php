<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
// Check if the user is super user, if not then redirect him to error page
if(!isset($_SESSION["super_admin"]) || $_SESSION["super_admin"] !== true){
    header("location: ../error.php");
    exit;
}
?>
 <?php
// Include config file
include_once('../Config/DB.php');
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate username
    $input_username = trim($_POST["username"]);
    if(empty($input_username)){
        $username_err = "Veuillez saisir le nom d'utilisateur.";
    }else{
        $username = $input_username;
    }
    
    // Validate password
    $input_password = trim($_POST["password"]);
    if(empty($input_password)){
        $password_err = "Veuillez saisir un mot de passe.";     
    } else{
        $password = $input_password;
    }
    
    $input_type = trim($_POST["type"]);

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO User (username, password, created_at, super_admin) VALUES ( ?, ?, NOW(), ?)";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $param_type);
            
            // Set parameters
            $param_username = $username;
            $param_password = MD5($password);
            $param_type = $input_type;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: users.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un utilisateur</title>

    <link rel="stylesheet" type="text/css" href="../Css/style.css" media="screen" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        .wrapper{
            width: 650px;
            margin: 0 auto;
        }
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
<header role="banner">
  <img class="logo" src="../Assets/Logo.jpg" alt="Mini logo" width="200">

  <ul class="utilities">
    <li class="logout warn"><a href="../Config/logout.php">Deconexion</a></li>
  </ul>
</header>

<nav role="navigation">
  <ul class="main">
    <li class="write"><a href="../index.php">Gestion des commandes</a></li>
    <li class="edit"><a href="../Product management/products.php">Gestion du produit</a></li>
    <li class="users"><a href="users.php">Gestion de compte</a></li>

  </ul>
</nav>


<main role="main">
 <section class="panel ">
     <div class="wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="page-header">
                            <h2>Ajouter un utilisateur</h2>
                        </div>
                        <p>Veuillez remplir ce formulaire et soumettre pour ajouter l'utilisateur à la base de donnée.</p>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                                <label>Nom d'utilisateur</label>
                                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                                <span class="help-block"><?php echo $username_err;?></span>
                            </div>
                            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                <label>mot de passe</label>
                                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                                <span class="help-block"><?php echo $password_err;?></span>
                            </div>
                            <strong>sélectionner le type d'utilisateur:</strong>
                              <div>
                                <input type="radio" id="type" name="type" value="0"
                                       checked>
                                <strong for="type">Admin</strong>
                                <br>
                                <input type="radio" id="type" name="type" value="2">
                                <strong for="type">Magasinier</strong>
                               <br>
                                <input type="radio" id="type" name="type" value="3">
                                <strong for="type">Responsable achat et vente</strong>
                              </div>
                            <input type="submit" class="btn btn-success" value="Ajouter">
                            <a href="users.php" class="btn btn-default">Annuler</a>
                        </form>
                    </div>
                </div>        
            </div>
        </div>
  </section>
</main>
<footer role="contentinfo">welcome <?php echo $_SESSION["username"] ?></footer>
</body>
</html>