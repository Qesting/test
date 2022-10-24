<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: userpage.php");
    exit;
}
 
// Include config file
require_once('../config/config.php');

$link = dbConnect();
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
$priv = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Proszę wprowadzić nazwę użytkownika.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Proszę wprowadzić hasło.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement        
        if($stmt = $link->prepare("SELECT id, username, password, priv FROM users WHERE username = ?")){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Store result
                $stmt->store_result();
                
                // Check if username exists, if yes then verify password
                if($stmt->num_rows == 1){                    
                    // Bind result variables
                    $stmt->bind_result($id, $username, $hashed_password, $priv);
                    if($stmt->fetch()){
                        if(password_verify($password, $hashed_password)){
                            if($priv != 0) {// Password is correct, so start a new session
                                session_start();
                                
                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                $_SESSION["priv"] = $priv;                            
                                
                                // Redirect user to welcome page
                                header("location: userpage.php");
                            } else {
                                $login_err = "Nie masz jeszcze pozwolenia na zalogowanie, skontaktuj się z administratorem.";
                            }
                            
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Niepoprawna nazwa użytkownika lub hasło.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Niepoprawna nazwa użytkownika lub hasło.";
                }
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
            #login {
                margin-left: auto;
                margin-right: auto;
            }
            @media screen and (min-width: 768px) {
                #login {
                    width: 80%;
                }
            }
            @media screen and (min-width: 992px) {
                #login {
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
                    <h2 class='text-uppercase'>Zaloguj</h2>
                </div>
            </div>
            <div class='container'>
                <?php 
                    if(!empty($login_err)){
                        echo '<div class="alert alert-danger">' . $login_err . '</div>';
                    }        
                ?>

                <form method="post" id='login'>
                    <div class="form-group">
                        <label>Nazwa użytkownika</label>
                        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>    
                    <div class="form-group">
                        <label>Hasło</label>
                        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" ><i class='bi-person-check-fill'></i> Zaloguj</button>
                    </div>
                    <p>Nie masz jeszcze konta? <a href="register.php">Zarejestruj się</a>.</p>
                </form>
            </div>
    </body>
</html>