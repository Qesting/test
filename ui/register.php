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
<html lang="pl-PL">
<head>
    <meta charset="UTF-8">
    <title>Zarejestruj</title>
    <link rel="stylesheet" href="../style/main.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Rejestracja</h2>
        <p>Wypełnij formularz aby stworzyć konto.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
                <input type="submit" class="btn btn-primary" value="Zarejestruj">
                <input type="reset" class="btn btn-secondary ml-2" value="Resetuj">
            </div>
            <p>Masz już konto? <a href="login.php">Zaloguj się tutaj</a>.</p>
            <p><a href="../index.php">Powrót do strony głównej.</a></p>
        </form>
    </div>    
</body>
</html>