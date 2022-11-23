<?php
    session_start();
    if (!isset($_SESSION["success"])) {
        $_SESSION["register_success"] = false;
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="desccription" content="Web Application Development" />
        <meta name="Keywords" content="PHP, CSS, HTML" />
        <meta name="author" content="Jared" />
        <title>Login</title>
        <link href="style.css" rel="stylesheet"/>
    </head>
    <body>        
        <header>
            <h1>My friend System<br>Assignment Login Page</h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="signup.php">Sign Up</a> 
                <a id="current-page" href="login.php">Log In</a>
                <a href="about.php">About</a> 
            </nav>
        </header>
        <br>
        <div class="center">
        <form id="data-input" action="login.php" method="POST">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?php if (isset($_POST["email"])) { echo $_POST["email"]; }?>"/>
            <br><br>
            <label for="pswd">Password:</label>
            <input type="text" id="pswd" name="pswd" value=""/>
            <br><br>
            <input type="submit" name="reset" value="Clear"/>
            <input type="submit" value="Login"/>
        </form>    
        </div>
        <?php     
            require_once("functions/mysql.php");     
            require_once("functions/settings.php");  
            
            $valid_email = false;
            $valid_pswd = false;   
            $user_email = "";
            $user_id = "";  
            $user_name = "";       
            $errmsg = "";
            if (!empty($_POST)) {
                // reset all post and input boxes by redirect to self
                if (isset($_POST["reset"])) {
                    header("location: login.php");
                }
                // valiate email address
                if (!isset($_POST["email"])) {
                    $errmsg .= "<p>Please enter en email</p>";
                } elseif ($_POST["email"] == "") {
                    $errmsg .= "<p>Please enter an email</p>";
                } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                    $errmsg .= "<p>Please enter a valid email</p>";
                } else { 
                    // check if email exists in database
                    $friends = new Friends($host, $user, $dbpswd, $dbnm);
                    $result = $friends->__get("friend_email", "friends");
                    foreach ($result as $row) {
                        if ($row[0] == $_POST["email"]) {
                            $valid_email = true; 
                            $user_email = $_POST["email"];
                            break;
                        }
                    }
                    $friends->closeConnection();                                        
                } 
                
                // validate password
                if (!isset($_POST["pswd"])) {
                    $errmsg .= "<p>Please enter a password</p>";
                } elseif ($_POST["pswd"] == "") {
                    $errmsg .= "<p>Please enter a password</p>";
                } elseif (!preg_match("/^[A-Za-z0-9]+$/", $_POST["pswd"])) {
                    $errmsg .= "<p>Password must only contain letters and numbers</p>";
                } elseif ($valid_email == true) {
                    // check if password exists in database with email
                    $friends = new Friends($host, $user, $dbpswd, $dbnm);
                    $result = $friends->select("friends", "WHERE friend_email = '".$user_email."'");
                    foreach ($result as $row) {
                        if ($row["friend_pswd"] == $_POST["pswd"]) {
                            $valid_pswd = true;
                            $user_id = $row["friend_id"];
                            $user_name = $row["profile_name"];
                            break;
                        }
                    }
                    $friends->closeConnection(); 
                }
                // check if both email and password is valid
                if ($valid_email == true && $valid_pswd == true) {
                    $_SESSION["success"] = true;
                    $_SESSION["friend_id"] = $user_id; 
                    $_SESSION["profile_name"] = $user_name;
                    header("location: friendlist.php");
                } else {
                    $errmsg .= "<p>Invalid email or password</p>";
                    echo "<div class='errors'>".$errmsg."</div>";
                }
            }
        ?>  
    </body>
</html>