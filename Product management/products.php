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
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion du produit</title>

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

                    <div class="page-header clearfix">
                        <h2 class="pull-left">Liste des produits</h2>
                        <a href="add product.php" class="btn btn-success pull-right">Ajouter un produit</a>
                    </div>

                    <form action="" method="post">  
                        <input type="text" name="term" /><br />  
                        <input class="btn btn-success pull-right" type="submit" value="recherche" />  
                    </form>  
                    <?php
                    // Include config file
                    include_once('../Config/DB.php');

                    if (!empty($_REQUEST['term'])) {

                        $term = mysqli_real_escape_string($conn, $_REQUEST['term']);     

                        $sql = "SELECT * FROM Product WHERE id LIKE '%".$term."%' OR location LIKE '%".$term."%' ORDER BY `location` ASC"; 
                        if($result = mysqli_query($conn, $sql)){
                            if(mysqli_num_rows($result) > 0){
                                echo "<table class='table table-bordered table-striped'>";
                                    echo "<thead>";
                                        echo "<tr>";
                                            echo "<th>referance</th>";
                                            echo "<th>emplacement</th>";
                                            echo "<th>quantite en stock</th>";
                                            echo "<th>date de modification</th>";
                                            echo "<th>Action</th>";
                                        echo "</tr>";
                                    echo "</thead>";
                                    echo "<tbody>";
                                    while($row = mysqli_fetch_array($result)){
                                        echo "<tr>";
                                            echo "<td>" . $row['id'] . "</td>";
                                            echo "<td>" . $row['location'] . "</td>";
                                            echo "<td>" . $row['stock_quantity'] . "</td>";
                                            echo "<td>" . $row['updated_at'] . "</td>";
                                            echo "<td>";
                                                echo "<a href='edit product.php?id=". $row['id'] ."' title='modifier le produit' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                                echo "<a href='delete product.php?id=". $row['id'] ."' title='supprimer le produit' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                            echo "</td>";
                                            echo "<td><input class='btn btn-success' type='button' value='Allumer led'></td>";
                                            echo "<td><input class='btn btn-danger' type='button' value='Eteindre led'></td>";
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
                    $sql = "SELECT * FROM Product ORDER BY `location` ASC";
                    if($result = mysqli_query($conn, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>referance</th>";
                                        echo "<th>emplacement</th>";
                                        echo "<th>quantite en stock</th>";
                                        echo "<th>date de modification</th>";
                                        echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . $row['location'] . "</td>";
                                        echo "<td>" . $row['stock_quantity'] . "</td>";
                                        echo "<td>" . $row['updated_at'] . "</td>";
                                        echo "<td>";
                                            echo "<a href='edit product.php?id=". $row['id'] ."' title='modifier le produit' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                            echo "<a href='delete product.php?id=". $row['id'] ."' title='supprimer le produit' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                        echo "</td>";
                                        echo "<td><input class='btn btn-success' type='button' value='Allumer led'></td>";
                                        echo "<td><input class='btn btn-danger' type='button' value='Eteindre led'></td>";
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
                    //mysqli_close($conn);
                    ?>
 
  </section>
  <section class="panel">
    <h2>Notes</h2>

    <div class="feedback error">
        <?php 
            // Attempt select query execution
                    $sql = "SELECT id FROM Product WHERE stock_quantity = 0";
                    if($result = mysqli_query($conn, $sql)){
                        echo "produit en rupture de stock:";
                        if(mysqli_num_rows($result) > 0){
                            while($row = mysqli_fetch_array($result)){
                                echo "<ul>";
                                    echo "<li>le produit " . $row['id'] . ".</li>";
                                echo "</ul>";
                            }
                            mysqli_free_result($result);
                        }
                    } 

        ?>

    </div>
    <div class="feedback">
        <?php 
            // Attempt select query execution
                    $sql = "SELECT id FROM Product WHERE stock_quantity < 5";
                    if($result = mysqli_query($conn, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "produit en risque de rupture de stock:";
                            while($row = mysqli_fetch_array($result)){

                                echo "<ul>";
                                    echo "<li>le produit " . $row['id'] . ".</li>";
                                echo "</ul>";
                            }
                            mysqli_free_result($result);
                        }
                    } 
                    // Close connection
                    mysqli_close($conn);

        ?>
    </div>

  </section>
</main>
<footer role="contentinfo">welcome <?php echo $_SESSION["username"] ?></footer>
</body>
</html>