<?php


// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
// Check if the user is no order manger, if he is then redirect him to error page
if(!isset($_SESSION["order_manager"]) || $_SESSION["order_manager"] == true){
    header("location: ../error.php");
    exit;
}

// Include config file
include_once('../Config/DB.php');
 
// Define variables and initialize with empty values
$referance = $location = $stock_quantity = "";
$referance_err = $location_err = $stock_quantity_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
    
    // Validate location location
    $input_location = trim($_POST["location"]);
    if(empty($input_location)){
        $location_err = "Veuillez saisir un emplacement.";     
    } else{
        $location = $input_location;
    }
    
    // Validate stock_quantity
    $input_stock_quantity = trim($_POST["stock_quantity"]);
    if(empty($input_stock_quantity)){
        $stock_quantity_err = "Veuillez saisir le montant de la quantité en stock.";     
    } elseif(!ctype_digit($input_stock_quantity)){
        $stock_quantity_err = "Veuillez saisir une valeur positive.";
    } else{
        $stock_quantity = $input_stock_quantity;
    }
    
    // Check input errors before inserting in database
    if(empty($referance_err) && empty($location_err) && empty($stock_quantity_err)){
        // Prepare an update statement
        $sql = "UPDATE Product SET location=?, stock_quantity=?, updated_at=NOW() WHERE id=?";
         

        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssi", $param_location, $param_stock_quantity, $param_id);
            
            // Set parameters
            $param_referance = $referance;
            $param_location = $location;
            $param_stock_quantity = $stock_quantity;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: products.php");
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
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM Product WHERE id = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $referance = $row["id"];
                    $location = $row["location"];
                    $stock_quantity = $row["stock_quantity"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: ../error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        //mysqli_close($conn);
    } else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: ../error.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modifier un produit</title>

    <link rel="stylesheet" type="text/css" href="../Css/style.css" media="screen" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>

    <style type="text/css">
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>

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

  <section class="panel ">
        <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Modifier Produit</h2>
                    </div>
                    <p>Veuillez modifier les valeurs saisies et soumettre pour mettre à jour le produit.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group <?php echo (!empty($referance_err)) ? 'has-error' : ''; ?>">
                            <label>referance</label>
                            <input disabled name="referance" class="form-control" value="<?php echo $referance; ?>">
                            <span class="help-block"><?php echo $referance_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($location_err)) ? 'has-error' : ''; ?>">
                            <label>location</label>
                            <textarea name="location" class="form-control"><?php echo $location; ?></textarea>
                            <span class="help-block"><?php echo $location_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($stock_quantity_err)) ? 'has-error' : ''; ?>">
                            <label>stock_quantity</label>
                            <input type="text" name="stock_quantity" class="form-control" value="<?php echo $stock_quantity; ?>">
                            <span class="help-block"><?php echo $stock_quantity_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="products.php" class="btn btn-default">Cancel</a>
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