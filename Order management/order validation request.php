<?php
// Check if the user is no stock manger, if he is then redirect him to error page
/*if(!isset($_SESSION["stock_manager"]) || $_SESSION["stock_manager"] == true){
    header("location: ../error.php");
    exit;
}*/
    // Include config file
include_once('../Config/DB.php');
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){

    // Prepare an update statement
    $sql = "UPDATE `Order` SET delivered_at=NOW() WHERE id=?";
     

  if($stmt = mysqli_prepare($conn, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Set parameters
        $param_id = $_POST["id"];
        
        // select all order_product of the selected order
        // Prepare a select statement
        $sqlOrder_Product = "SELECT * FROM `Order_Product` WHERE order_id = ".trim($_POST["id"].""); 
       if($result = mysqli_query($conn, $sqlOrder_Product)){
            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_array($result)){
                    echo $row['product_id'];
                    // update the quantity after validating the order
                    // Prepare an update statement
                    $sqlUpdateQuantity = "UPDATE `Product` SET stock_quantity=(stock_quantity-?) WHERE id=?";
                        if($stmtUpdateQuantity = mysqli_prepare($conn, $sqlUpdateQuantity)){
                            // Bind variables to the prepared statement as parameters
                            mysqli_stmt_bind_param($stmtUpdateQuantity, "si", $param_stock_quantity, $param_product_id);
                            
                            // Set parameters
                            $param_stock_quantity= $row['quantity'];
                            $param_product_id= $row['product_id'];
                            // Attempt to execute the prepared statement
                            if(mysqli_stmt_execute($stmtUpdateQuantity)){
                                // Records updated successfully. Redirect to landing page
                                //header("location: ../index.php");
                                //exit();
                            } else{
                                echo "Something went wrong. Please try again later.";
                            }
                        }

                }
                mysqli_free_result($result);

            } else{
                echo "0 produits";
            }
        }

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            // Records updated successfully. Redirect to landing page
            header("location: ../index.php");
            exit();
        } else{
            echo "Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    mysqli_stmt_close($stmt);
}
?>