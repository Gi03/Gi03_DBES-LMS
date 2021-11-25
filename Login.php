<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
  
    header("location: welcome.php");
    exit;
}
 
// Include config file
require_once "Config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT  Account_type, username, password FROM user_account WHERE username =:username";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Check if username exists, if yes then verify password
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        //$id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        $Account_type = $row["Account_type"];
                 
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            //$_SESSION["id"] = $id;
                            $_SESSION["Account_type"]= $Account_type;
                            $_SESSION["username"] = $username;
                            
                            //$_SESSION["school"] = $school; 
                            //Check user type
                            if($_SESSION["Account_type"] == '1'){
                            // Redirect user to welcome page
                            header("location: welcome.php"); 
                                                  
                            }
                            else{
                                //Redirec user to other page
                                header("location: welcome2.php");
                                
                            }
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid password.";
                            echo $Account_type," ", $password," ", $username," ", $hashed_password;
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            unset($stmt);
        }
    }
    // Close connection
    unset($pdo);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ 
              font-family: 14px monospace; 
              alignment-adjust: central; 
              background-image: url('img//SMS.jpg');
              background-repeat: no-repeat;
              background-attachment: fixed;
              background-size: cover;          
        }
        .wrapper{ 
            width: 360px; 
            position: absolute;
            left: 50%;
            top: 70%;
            transform: translate(-50%, -50%);
            padding: 10px;
          
        }   
        #corner
        {
            border-radius: 20px;
            box-shadow: inset 0px 4px 4px rgba(0, 0, 0, 0.25);
        }
        #btnlogin
        {
          
            width: 68px;
            height: 36px;
            left: 260px;
            top: 225px;
            background: rgba(41, 217, 255, 0.8);
            box-shadow: inset 0px 4px 4px rgba(0, 0, 0, 0.25);
            border-radius: 10px;
            background: rgba(41, 217, 255, 0.8);
            box-shadow: inset 0px 4px 4px rgba(0, 0, 0, 0.25);
            border-radius: 10px;
        }  
        label 
            {
            display: inline-block;
            width: 140px;
            text-align: left;
            }

</style>
</head>
<body>
  
   <div ><img src="logos\SMSlogo.png" alt="Smiley face" width="100" height="100" style="position: absolute; left: 240px; top: 250px"> 
        <h2 style="position: absolute; left: 350px; top: 50px">Saint Mark's School of Quirino</h2>
    </div>
    <div class="wrapper">
        <p>Please fill in your credentials to login.</p>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input id="corner" type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input id="corner" type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input id="btnlogin" type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div>
</body>
</html>
