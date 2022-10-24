<?php

    session_start();
    
    require_once('../config/config.php');
    $link = dbConnect();

    $username = $password = $confirm_password = "";
    $username_err = $password_err = $confirm_password_err = "";
    
    // Processing form data when form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){
    
        // Validate username
        if(empty(trim($_POST["username"]))){
            $username_err = "Proszę wprowadzić nazwę użytkownika.";
        } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
            $username_err = "Nazwa użytkownika moża zawierać tylko litery, cyfry i podkreślenia.";
        } else{
            // Prepare a select statement           
            if($stmt = $link->prepare("SELECT id FROM users WHERE username = ?")){
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("s", $param_username);
                
                // Set parameters
                $param_username = trim($_POST["username"]);
                
                // Attempt to execute the prepared statement
                if($stmt->execute()){
                    /* store result */
                    $stmt->store_result();
                    
                    if($stmt->num_rows !== 0){
                        $username_err = "Ta nazwa jest już zajęta.";
                    } else{
                        $username = trim($_POST["username"]);
                    }
                } else{
                    echo "Coś poszło nie tak, spróbuj ponownie później.";
                }

                // Close statement
                $stmt->close();
            }
        }
        
        // Validate password
        if(empty(trim($_POST["password"]))){
            $password_err = "Proszę wprowadzić hasło.";     
        } elseif(strlen(trim($_POST["password"])) < 6){
            $password_err = "Hasło musi zawierać przynajmniej 6 znaków.";
        } else{
            $password = trim($_POST["password"]);
        }
        
        // Validate confirm password
        if(empty(trim($_POST["confirm_password"]))){
            $confirm_password_err = "Proszę potwierdzić hasło.";     
        } else{
            $confirm_password = trim($_POST["confirm_password"]);
            if(empty($password_err) && ($password != $confirm_password)){
                $confirm_password_err = "Hasła się nie zgadzają.";
            }
        }
        
        // Check input errors before inserting in database
        if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
            
            // Prepare an insert statement            
            if($stmt = $link->prepare("INSERT INTO users (username, password) VALUES (?, ?)")){
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("ss", $param_username, $param_password);
                
                // Set parameters
                $param_username = $username;
                $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
                
                // Attempt to execute the prepared statement
                if($stmt->execute()){
                    // Redirect to login page
                    header("location: login.php");
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
        <title>Rejestracja | testopol</title>
        <link rel="stylesheet" href="../style/main.css">
        <style>
            body{
                font: 14px sans-serif;
                margin-bottom: 120px;
                text-align: center;
            }
            #register {
                margin-left: auto;
                margin-right: auto;
            }
            @media screen and (min-width: 768px) {
                #register {
                    width: 80%;
                }
            }
            @media screen and (min-width: 992px) {
                #register {
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
                    <h2 class='text-uppercase'>Zarejestruj się</h2>
                    <p>Wypełnij formularz aby stworzyć konto</p>
                </div>
            </div>
            <div class='container'>
                <form method="post" id='register'>
                    <div class="form-group">
                        <label>Nazwa użytkownika</label>
                        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                        <span class="invalid-feedback"><?php echo $username_err; ?></span>
                    </div>    
                    <div class="form-group">
                        <label>Hasło</label>
                        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label>Powtórz hasło</label>
                        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                        <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <div class='btn-group'>
                            <button type="submit" class="btn btn-primary"><i class='bi-person-plus-fill'></i> Zarejestruj się</button>
                            <button type="reset" class="btn btn-secondary"><i class='bi-arrow-clockwise'></i> Resetuj</button>
                        </div>
                    </div>
                    <p>Masz już konto? <a href="login.php">Zaloguj się tutaj</a>.</p>
                </form>
            </div>
    </body>
</html>