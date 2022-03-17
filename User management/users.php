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
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestion du utilisateurs</title>

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

  <section class="panel important">

    <div class="page-header clearfix">
        <h2 class="pull-left">Liste des utilisateurs</h2>
        <a href="add user.php" class="btn btn-success pull-right">Ajouter un utilisateur</a>
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

        $sql = "SELECT * FROM User WHERE id LIKE '%".$term."%' or username LIKE '%".$term."%'"; 
        if($result = mysqli_query($conn, $sql)){
            if(mysqli_num_rows($result) > 0){
                echo "<table class='table table-bordered table-striped'>";
                    echo "<thead>";
                        echo "<tr>";
                            echo "<th>id</th>";
                            echo "<th>created_at</th>";
                            echo "<th>username</th>";
                            echo "<th>type</th>";
                            echo "<th>Action</th>";
                        echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                    while($row = mysqli_fetch_array($result)){
                        echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['created_at'] . "</td>";
                            echo "<td>" . $row['username'] . "</td>";
                            switch ($row['super_admin']) {
                              case 0:
                                echo "<td> admin </td>";
                                break;
                              case 1:
                                echo "<td> super admin </td>";
                                break;
                              case 2:
                                echo "<td> Magasinier </td>";
                                break;
                              case 3:
                                echo "<td> Responsable achat et vente </td>";
                                break;
                              default:
                                echo "<td> none </td>";
                                break;
                            }
                            echo "<td>";
                                echo "<a href='edit user.php?id=". $row['id'] ."' title='modifier utilisateur' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                echo "<a href='delete user.php?id=". $row['id'] ."' title='supprimer utilisateur' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                            echo "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";                            
                echo "</table>";
                // Free result set
                mysqli_free_result($result);
            } else{
                echo "<p class='lead'><em>0 utilisateurs.</em></p>";
            }
        }



    } else {
    // Attempt select query execution
    $sql = "SELECT * FROM User";
    if($result = mysqli_query($conn, $sql)){
              if(mysqli_num_rows($result) > 0){
                  echo "<table class='table table-bordered table-striped'>";
                      echo "<thead>";
                          echo "<tr>";
                              echo "<th>id</th>";
                              echo "<th>created_at</th>";
                              echo "<th>username</th>";
                              echo "<th>type</th>";
                              echo "<th>Action</th>";
                          echo "</tr>";
                      echo "</thead>";
                      echo "<tbody>";
                      while($row = mysqli_fetch_array($result)){
                          echo "<tr>";
                              echo "<td>" . $row['id'] . "</td>";
                              echo "<td>" . $row['created_at'] . "</td>";
                              echo "<td>" . $row['username'] . "</td>";
                              switch ($row['super_admin']) {
                              case 0:
                                echo "<td> admin </td>";
                                break;
                              case 1:
                                echo "<td> super admin </td>";
                                break;
                              case 2:
                                echo "<td> Magasinier </td>";
                                break;
                              case 3:
                                echo "<td> Responsable achat et vente </td>";
                                break;
                              default:
                                echo "<td> none </td>";
                                break;
                            }
                            echo "<td>";
                                echo "<a href='edit user.php?id=". $row['id'] ."' title='modifier utilisateur' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                echo "<a href='delete user.php?id=". $row['id'] ."' title='supprimer utilisateur' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                            echo "</td>";
                          echo "</tr>";
                      }
                      echo "</tbody>";                            
                  echo "</table>";
                  // Free result set
                  mysqli_free_result($result);
              } else{
                  echo "<p class='lead'><em>0 utilisateurs.</em></p>";
              }
          } else{
          echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
      }
    }


    // Close connection
    mysqli_close($conn);
    ?>

  </section>

</main>
<footer role="contentinfo">welcome <?php echo $_SESSION["username"] ?></footer>
</body>
</html>