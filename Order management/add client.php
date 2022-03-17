<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
// Check if the user is no stock manger, if he is then redirect him to error page
if(!isset($_SESSION["stock_manager"]) || $_SESSION["stock_manager"] == true){
    header("location: ../error.php");
    exit;
}
?>
 
<?php
// Include config file
include_once('../Config/DB.php');

// Define variables and initialize with empty values
$id = $name = "";
$id_err = $name_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate id
    $input_id = trim($_POST["id"]);
    if(empty($input_id)){
        $id_err = "Veuillez saisir un id.";
    }elseif(!ctype_digit($input_id)){
        $id_err = "Veuillez saisir une valeur positive.";
    }  else{
        $id = $input_id;
    }
    
    // Validate client name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Veuillez saisir un nom.";     
    } else{
        $name = $input_name;
    }
    
  
    // Check input errors before inserting in database
    if(empty($id_err) && empty($name_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO Client (id, name, created_at) VALUES ( ?, ?, NOW())";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_id, $param_name);
            
            // Set parameters
            $param_id = $id;
            $param_name= $name;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records created successfully. Redirect to landing page
                header("location: add order.php");
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
    <title>Ajouter une client</title>

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
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script>
    $(document).ready(function(){
        $('input[type="radio"]').click(function(){
            var inputValue = $(this).attr("value");
            var targetBox = $("." + inputValue);
            $(".form-group").not(targetBox).hide();
            $(targetBox).show();
        });
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
    <?php 
      // check if the user is not stock manager
      if(!isset($_SESSION["stock_manager"]) || $_SESSION["stock_manager"] == false){
        echo '<li class="write"><a href="../index.php">Gestion des commandes</a></li>';
      }
      // check if the user is not order manager
      if(!isset($_SESSION["order_manager"]) || $_SESSION["order_manager"] == false){
        echo '<li class="edit"><a href="../Product management/products.php">Gestion du produit</a></li>';
      }
      // Check if the user is super admin, if not then hide the user management section
    if(!isset($_SESSION["super_admin"]) || $_SESSION["super_admin"] == true){
      echo '<li class="users"><a href="../User management/users.php">Gestion de compte</a></li>';
    }
    ?>
  </ul>
</nav>

<main role="main">




  <section class="panel important">
       <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Ajouter une client</h2>
                    </div>
                    <p>Veuillez remplir ce formulaire et soumettre pour ajouter le client à la base de donnée.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group <?php echo (!empty($id_err)) ? 'has-error' : ''; ?>">
                            <label>identification</label>
                            <input type="number" name="id" class="form-control" value="<?php echo $id; ?>">
                            <span class="help-block"><?php echo $id_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                            <label>nom</label>
                            <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                            <span class="help-block"><?php echo $name_err;?></span>
                        </div>
                        <input type="submit" class="btn btn-success" value="Ajouter">
                        <a href="add order.php" class="btn btn-default">Annuler</a>
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