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
 

    $input_client = null;
    $input_supplier= null;

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $input_type = trim($_POST["type"]);
    if ($input_type == 0) {
        $input_client = trim($_POST["client"]);
    }elseif ($input_type == 1) {
        $input_supplier= trim($_POST["supplier"]);
    }
        // Prepare an insert statement
        $sql = "INSERT INTO `Order` (type, client_id, supplier_id, updated_at) VALUES (?, ?, ?, NOW())";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sss", $param_type, $param_client, $param_supplier);
            
            // Set parameters
            $param_type = $input_type;
            $param_client = $input_client;
            $param_supplier= $input_supplier;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                //$id = -1;
                //echo "alert($id)";
                // Prepare a select statement
                $sql = "SELECT id FROM `Order` ORDER BY id DESC LIMIT 1";

                // Perform query 
                if ($result = mysqli_query($conn,$sql)) {
                    $row = mysqli_fetch_array($result);
                    //echo "Returned rows are: " .  $row['id'];
                    // Free result set
                    // Records created successfully. Redirect to details page
                    header("location: order details.php?id=". $row['id']);
                    exit();
                    mysqli_free_result($result);

                }

                
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }

    // Close connection
    mysqli_close($conn);  

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une commande</title>

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
                        <h2>Ajouter une commande</h2>
                    </div>
                    <p>Veuillez remplir ce formulaire et soumettre pour ajouter la commande à la base de donnée.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                      <div>
                            <strong>sélectionner le type de commande:</strong>
                              <div>
                                <input type="radio" id="import" name="type" value="1"
                                       checked>
                                <strong for="import">import</strong>
                                <br>
                                <input type="radio" id="export" name="type" value="0">
                                <strong for="export">export</strong>
                              </div>
                      </div>


                    <?php
                    echo "<div class='form-group 0' hidden>";
                    $sql = "SELECT id,name FROM Client"; 
                    if($result = mysqli_query($conn, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<label for='client'>Choisissez un client:</label>";
                            echo "<select id='client' name='client'>";
                            while($row = mysqli_fetch_array($result)){
                              echo "<option value=".$row['id'].">".$row['name']."</option>";

                            } 
                            echo "</select>";
                        }else{
                          echo "<p>0 client</p>";  
                        }
                    }
                    echo "<div class='form-group 0'>";  
                    echo "<br>"; 
                        echo "<a href='add client.php' class='btn btn-success pull-right'>Ajouter un client</a>"; 
                    echo "</div>"; 

                    echo "</div>"; 



                    echo "<div class='form-group 1'>";
                    $sql = "SELECT id,name FROM Supplier"; 
                    if($result = mysqli_query($conn, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<label for='supplier'>Choisissez un fournisseur:</label>";
                            echo "<select id='supplier' name='supplier'>";
                            while($row = mysqli_fetch_array($result)){
                              echo "<option value=".$row['id'].">".$row['name']."</option>";

                            } 
                            echo "</select>";
                        }else{
                          echo "<p>0 fournisseur</p>";  
                        }
                    }
                    echo "<div class='form-group 1'>";  
                    echo "<br>"; 
                        echo "<a href='add supplier.php' class='btn btn-success pull-right'>Ajouter un fournisseur</a>"; 
                    echo "</div>"; 

                    echo "</div>"; 
                    // Close connection
                    mysqli_close($conn);
                    ?>

                        <input type="submit" class="btn btn-success" value="Ajouter">
                        <a href="../index.php" class="btn btn-default">Annuler</a>
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