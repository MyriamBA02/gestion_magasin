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
<?php
// Include config file
include_once('../Config/DB.php');
// Define variables and initialize with empty values
$First_name = $Last_name = $E_mail=$Mobile_no=$Password=$Retype_password = "";
$referance_err = $location_err = $stock_quantity_err = "";
 // Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate First name 
    $input_referance = trim($_POST["First_name"]);
    if(empty($input_First_name)){
        $First_name_err = "Veuillez saisir First name.";
    }elseif(!ctype_digit($input_referance)){
        $First_name_err = "Veuillez saisir une chaine .";
    }  else{
        $First_name = $First_name;
    }
    