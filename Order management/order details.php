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

 
// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Include config file
include_once('../Config/DB.php');
    
    // Prepare a select statement
    $sql = "SELECT * FROM `Order` WHERE id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Set parameters
        $param_id = trim($_GET["id"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
    
            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $id = $row["id"];
                if (!empty($row["client_id"])) {
                    $clientId = $row["client_id"];

                      $sqlClient = "SELECT name FROM Client where id = ?"; 
                       if($stmtClient = mysqli_prepare($conn, $sqlClient)){
                            // Bind variables to the prepared statement as parameters
                            mysqli_stmt_bind_param($stmtClient, "i", $param_id);

                            // Set parameters
                            $param_id = trim($clientId);

                            // Attempt to execute the prepared statement
                            if(mysqli_stmt_execute($stmtClient)){
                                $resultclient = mysqli_stmt_get_result($stmtClient);
                        
                                if(mysqli_num_rows($resultclient) == 1){
                                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                                    $rowClient = mysqli_fetch_array($resultclient, MYSQLI_ASSOC);

                                    $client =$rowClient['name'];

                                }else{
                                echo "can't fetch client";  
                                }
                            }
                       }
                // Close statement
                mysqli_stmt_close($stmtClient);
                }else{
                    $client = "aucun";
                }
                if (!empty($row["supplier_id"])) {
                    $supplierId = $row["supplier_id"];

                      $sqlSupplier = "SELECT name FROM Supplier where id = ?"; 
                       if($stmtSupplier = mysqli_prepare($conn, $sqlSupplier)){
                            // Bind variables to the prepared statement as parameters
                            mysqli_stmt_bind_param($stmtSupplier, "i", $param_id);

                            // Set parameters
                            $param_id = trim($supplierId);

                            // Attempt to execute the prepared statement
                            if(mysqli_stmt_execute($stmtSupplier)){
                                $resultSupplier = mysqli_stmt_get_result($stmtSupplier);
                        
                                if(mysqli_num_rows($resultSupplier) == 1){
                                    /* Fetch result row as an associative array. Since the result set contains only one row, we don't need to use while loop */
                                    $rowSupplier = mysqli_fetch_array($resultSupplier, MYSQLI_ASSOC);

                                    $supplier =$rowSupplier['name'];

                                }else{
                                echo "can't fetch supplier";  
                                }
                            }
                       }
                // Close statement
                mysqli_stmt_close($stmtSupplier);
                }else{
                    $supplier = "aucun";
                }
                if ($row["type"] == 0){
                  $type = "export";
                }elseif ($row["type"] == 1) {
                  $type = "import";
                }else{
                  $type = "aucun type";
                }

                $updatedAt = $row["updated_at"];
                if (empty($row["delivered_at"])) {
                    $deliveredAt = "Pas encore traité";
                }else{
                    $deliveredAt = $row["delivered_at"];
                }

            } else{
                // URL doesn't contain valid id parameter. Redirect to error page
                header("location: ../error.php");
                exit();
            }
            
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    mysqli_stmt_close($stmt);
    

} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: ../error.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Details de la commande</title>

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
                        <h1>Commande <?php echo $row["id"]; ?></h1>
                    </div>
                    <div class="form-group">
                        <label>client</label>
                        <p class="form-control-static"><?php echo $client; ?></p>
                    </div>
                    <div class="form-group">
                        <label>fournisseur</label>
                        <p class="form-control-static"><?php echo $supplier; ?></p>
                    </div>
                    <div class="form-group">
                        <label>type</label>
                        <p class="form-control-static"><?php echo $type; ?></p>
                    </div>
                    <div class="form-group">
                        <label>modifier le</label>
                        <p class="form-control-static"><?php echo $row["updated_at"]; ?></p>
                    </div>
                    <div class="form-group">
                        <label>traité le</label>
                        <p class="form-control-static"><?php echo $deliveredAt; ?></p>
                    </div>
                    <p>
                        <a href="../index.php" class="btn btn-default">Back</a>
                        <?php if (empty($row["delivered_at"])) {
                        echo "<a  href='validate order.php?id=". trim($_GET['id']) ."';  class='btn btn-primary'>Valider commande</a>";                     
                             }

                        ?>
                    </p>
                </div>
            </div>        
        </div>
    </div>
  </section>
  <section class="panel important">
   <div class="page-header clearfix">
                        <h2 class="pull-left">Liste des produits</h2>
                        <?php if (empty($row["delivered_at"])) {
                            echo "<a  href='add product to order.php?id=". trim($_GET['id']) ."';  class='btn btn-warning pull-right'>Ajouter un produit</a>";
                             }
                        ?>
                    </div>

                    <form action="" method="post">  
                        <input type="text" name="term" /><br />  
                        <input class="btn btn-success pull-right" type="submit" value="recherche" />  
                    </form>  
                    <?php


                    if (!empty($_REQUEST['term'])) {

                        $term = mysqli_real_escape_string($conn, $_REQUEST['term']);     

                           // Attempt select query execution
                        $sql = "SELECT * FROM Order_Product WHERE order_id = ".trim($_GET["id"])." AND product_id LIKE '%".$term."%' OR quantity LIKE '%".$term."%'";
                        if($result = mysqli_query($conn, $sql)){
                            if(mysqli_num_rows($result) > 0){
                                echo "<table class='table table-bordered table-striped'>";
                                    echo "<thead>";
                                        echo "<tr>";
                                            echo "<th>referance</th>";
                                            echo "<th>quantite</th>";
                                        echo "</tr>";
                                    echo "</thead>";
                                    echo "<tbody>";
                                    while($row = mysqli_fetch_array($result)){
                                        echo "<tr>";
                                            echo "<td>" . $row['product_id'] . "</td>";
                                            echo "<td>" . $row['quantity'] . "</td>";
                                        echo "</tr>";
                                    }
                                    echo "</tbody>";                            
                                echo "</table>";
                                // Free result set
                                mysqli_free_result($result);
                            } else{
                                echo "<p class='lead'><em>0 produits.</em></p>";
                            }
                        }



                    } else {
                    // Attempt select query execution
                    $sql = "SELECT * FROM Order_Product WHERE order_id = ".trim($_GET["id"])."";
                    if($result = mysqli_query($conn, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>referance</th>";
                                        echo "<th>quantite</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['product_id'] . "</td>";
                                        echo "<td>" . $row['quantity'] . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>0 produits.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
                    } 
                    }
                       
 
                    // Close connection
                    mysqli_close($conn);
                    ?>
      <br><br> <br><br> 
  </section>




</main>
<footer role="contentinfo">welcome <?php echo $_SESSION["username"] ?></footer>
</body>
</html>