<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
 
// Include config file
require_once('../config/config.php');
$link = dbConnect();
 
// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Proszę wpisać nowe hasło.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Hasło musi zawierać przynajmniej 6 znaków.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Proszę powtórzyć hasło.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Hasła się nie zgadzają.";
        }
    }
        
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement       
        if($stmt = $link->prepare("UPDATE users SET password = ? WHERE id = ?")){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("si", $param_password, $param_id);
            
            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                header("location: login.php");
                exit();
            } else{
                echo "Coś poszło nie tak, spróbuj ponownie później.";
            }

            // Close statement
            $stmt->close();
        }
    }
    
    // Close connection
    $link->close();
}
?>
<!DOCTYPE html>
<html lang='pl-Pl'>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Logowanie | testopol</title>
        <link rel="stylesheet" href="../style/main.css">
        <style>
            body{
                font: 14px sans-serif;
                margin-bottom: 120px;
                text-align: center;
            }
            #reset {
                margin-left: auto;
                margin-right: auto;
            }
            @media screen and (min-width: 768px) {
                #reset {
                    width: 80%;
                }
            }
            @media screen and (min-width: 992px) {
                #reset {
                    width: 60%;
                }
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand navbar-dark bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand"><b>T</b>ESTOPOL</a>
                <div class="collapse navbar-collapse">
                    <div class="navbar-nav ms-auto">
                        <a class='nav-item nav-link-active' href='../'><i class='bi-house-fill'></i> Strona główna</a>
                    </div>
                </div>
            </div>
        </nav>
        <div class="wrapper">
            <div class='container my-3'>
                <div class='text-center'>
                    <h2 class='my-5'>Zresetuj hasło</h2>
                </div>
            </div>
            <div class='container'>
                <form method="post" id='reset'>
                    <div class="form-group">
                        <label>Nowe hasło</label>
                        <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
                        <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Powtórz hasło</label>
                        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <div class='btn-group'>
                            <button type="submit" class="btn btn-primary" ><i class='bi-check'></i> Zresetuj hasło</button>
                            <a class='btn btn-danger' href='userpage.php'><i class='bi-x'></i> Anuluj</a>
                        </div>
                    </div>
                </form>
            </div>
    </body>
</html>