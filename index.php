<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
// Check if the user is no stock manger, if he is then redirect him to error page
if(!isset($_SESSION["stock_manager"]) || $_SESSION["stock_manager"] == true){
    header("location: error.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion des commandes</title>

    <link rel="stylesheet" type="text/css" href="Css/style.css" media="screen" />
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
  <img class="logo" src="Assets/Logo.jpg" alt="Mini logo" width="200">

  <ul class="utilities">
    <li class="logout warn"><a href="Config/logout.php">Deconexion</a></li>
  </ul>
</header>

<nav role="navigation">
  <ul class="main">
    
    
    <?php 
      // check if the user is not stock manager
      if(!isset($_SESSION["stock_manager"]) || $_SESSION["stock_manager"] == false){
        echo '<li class="write"><a href="index.php">Gestion des commandes</a></li>';
      }
      // check if the user is not order manager
      if(!isset($_SESSION["order_manager"]) || $_SESSION["order_manager"] == false){
        echo '<li class="edit"><a href="Product management/products.php">Gestion du produit</a></li>';
      }
      // Check if the user is super admin, if not then hide the user management section
    if(!isset($_SESSION["super_admin"]) || $_SESSION["super_admin"] == true){
      echo '<li class="users"><a href="User management/users.php">Gestion de compte</a></li>';
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
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Liste des commandes</h2>
                        <a href="Order management/add order.php" class="btn btn-success pull-right">Ajouter une commande</a>
                    </div>

                    <form action="" method="post">  
                        <input type="text" name="term" /><br />  
                        <label for="filter">sélectionner un filtre:</label>
                        <label list="filters" name="filter">
                          <select name="filter" id="filter">
                            <option value="0">aucun</option>
                            <option value="1">export</option>
                            <option value="2">import</option>
                            <option value="3">traité</option>
                            <option value="4">non traité</option>
                          </select>
                        <input class="btn btn-success pull-right" type="submit" value="recherche" />  
                    </form>  
                    <?php
                    // Include config file
                    include_once('Config/DB.php');

                    // if the recherche input or the filter is not empty                      
                    if ((!empty($_REQUEST['term'])) || (!empty($_REQUEST['filter']))) {
                      // if the recherche input is not empty 
                      if ((!empty($_REQUEST['term']))) {
                        $term = mysqli_real_escape_string($conn, $_REQUEST['term']);  
                        // if the recherche input is not empty and the filter is not empty                      
                        if (empty($_REQUEST['filter'])) {
                          $sql = "SELECT * FROM `Order` WHERE id LIKE '%".$term."%'";
                        // if the recherche input and the filter is not empty                      
                        }else{
                          switch ($_POST['filter']) {
                          case 1:
                            $sql = "SELECT * FROM `Order` WHERE id LIKE '%".$term."%' AND type = 0";  
                          break;
                          case 2:
                            $sql = "SELECT * FROM `Order` WHERE id LIKE '%".$term."%' AND type = 1";
                            break;
                          case 3:
                            $sql = "SELECT * FROM `Order` WHERE id LIKE '%".$term."%' AND delivered_at IS NOT NULL";
                            break;
                          case 4:
                            $sql = "SELECT * FROM `Order` WHERE id LIKE '%".$term."%' AND delivered_at IS NULL";
                            break;

                          }        
                        }
                      // if the recherche input is  empty 
                      }else{
                        switch ($_POST['filter']) {
                          case 1:
                            $sql = "SELECT * FROM `Order` WHERE type = 0";  
                          break;
                          case 2:
                            $sql = "SELECT * FROM `Order` WHERE type = 1";
                            break;
                          case 3:
                            $sql = "SELECT * FROM `Order` WHERE delivered_at IS NOT NULL";
                            break;
                          case 4:
                            $sql = "SELECT * FROM `Order` WHERE delivered_at IS NULL";
                            break;

                          }
                      }

                          if($result = mysqli_query($conn, $sql)){
                            if(mysqli_num_rows($result) > 0){
                                echo "<table class='table table-bordered table-striped'>";
                                    echo "<thead>";
                                        echo "<tr>";
                                            echo "<th>id</th>";
                                            echo "<th>id client</th>";
                                            echo "<th>id fournisseur</th>";
                                            echo "<th>date de traitement</th>";
                                            echo "<th>date de modification</th>";
                                            echo "<th>Type</th>";
                                            echo "<th>Action</th>";
                                        echo "</tr>";
                                    echo "</thead>";
                                    echo "<tbody>";
                                    while($row = mysqli_fetch_array($result)){
                                        echo "<tr>";
                                            echo "<td>" . $row['id'] . "</td>";
                                            if (empty($row["client_id"])) {
                                                $client = "aucun";
                                            }else{
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
                                            }
                                            echo "<td>" . $client . "</td>";
                                            if (empty($row["supplier_id"])) {
                                                $supplier = "aucun";
                                            }else{
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
                                            }
                                            echo "<td>" . $supplier . "</td>";
                                            $updatedAt = $row["updated_at"];
                                            if (empty($row["delivered_at"])) {
                                                $deliveredAt = "Pas encore traité";
                                            }else{
                                                $deliveredAt = $row["delivered_at"];
                                            }
                                            echo "<td>" . $deliveredAt . "</td>";
                                            echo "<td>" . $row['updated_at'] . "</td>";
                                            if ($row['type'] == 0){
                                              echo "<td> export </td>";
                                            } else {
                                              echo "<td> import </td>";
                                            } 
                                        echo "<td>";
                                            echo "<a allign='center' href='Order management/order details.php?id=". $row['id'] ."' title='afficher les détails' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                    echo "</tbody>";                            
                                echo "</table>";
                                // Free result set
                                mysqli_free_result($result);
                            } else{
                                echo "<p class='lead'><em>0 commandes.</em></p>";
                            }
                        }
                      
                    } else {
                                            // Attempt select query execution
                    $sql = "SELECT * FROM `Order`";
                    if($result = mysqli_query($conn, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                        echo "<tr>";
                                            echo "<th>référence</th>";
                                            echo "<th>client</th>";
                                            echo "<th>fournisseur</th>";
                                            echo "<th>date de traitement</th>";
                                            echo "<th>date de modification</th>";
                                            echo "<th>Type</th>";
                                            echo "<th>Action</th>";
                                        echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                            echo "<td>" . $row['id'] . "</td>";
                                            if (empty($row["client_id"])) {
                                                $client = "aucun";
                                            }else{
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
                                            }
                                            echo "<td>" . $client . "</td>";
                                            if (empty($row["supplier_id"])) {
                                                $supplier = "aucun";
                                            }else{
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
                                            }
                                            echo "<td>" . $supplier . "</td>";
                                            $updatedAt = $row["updated_at"];
                                            if (empty($row["delivered_at"])) {
                                                $deliveredAt = "Pas encore traité";
                                            }else{
                                                $deliveredAt = $row["delivered_at"];
                                            }
                                            echo "<td>" . $deliveredAt . "</td>";
                                            echo "<td>" . $row['updated_at'] . "</td>";
                                            if ($row['type'] == 0){
                                              echo "<td> export </td>";
                                            } else {
                                              echo "<td> import </td>";
                                            }   
                                        echo "<td>";
                                            echo "<a href='Order management/order details.php?id=". $row['id'] ."' title='afficher les détails' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                        echo "</td>";
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
                </div>
            </div>        
        </div>
    </div>
  </section>



</main>
<footer role="contentinfo">welcome <?php echo $_SESSION["username"] ?></footer>
</body>
</html>