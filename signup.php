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
        <title>Sign Up</title>
        <link href="style.css" rel="stylesheet"/>
    </head>
    <body>        
        <header>
            <h1>My friend System<br>Assignment Sign Up Page</h1>
            <nav>
                <a href="index.php">Home</a>
                <a id="current-page" href="signup.php">Sign Up</a> 
                <a href="login.php">Log In</a>
                <a href="about.php">About</a> 
            </nav>
        </header>
        <br>
        <div class="center">
        <form id="data-input" action="signup.php" method="POST">            
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" value="<?php if (isset($_POST["email"])) { echo $_POST["email"]; }?>"/>
            <br><br>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php if (isset($_POST["name"])) { echo $_POST["name"]; }?>"/>
            <br><br>
            <label for="pswd">Password:</label>
            <input type="text" id="pswd" name="pswd"/>
            <br><br>
            <label for="confirm_pswd">Confirm Password:</label>
            <input type="text" id="confirm_pswd" name="confirm_pswd"/>
            <br><br>
            <input type="submit" name="reset" value="Clear"/>
            <input type="submit" value="Submit"/>
        </form> 
        </div>       
        <?php     
            require_once("functions/mysql.php");     
            require_once("functions/settings.php");       

            // if POST is not empty process data
            if (!empty($_POST)) { 
                $email = "";
                $name = "";
                $pswd = "";
                $errmsg = "";
                
                // reset all post and input boxes by redirect to self
                if (isset($_POST["reset"])) {
                    header("location: signup.php");
                }
                // validate email address
                if (!isset($_POST["email"])) {
                    $errmsg .= "<p>Please enter en email</p>";
                } elseif ($_POST["email"] == "") {
                    $errmsg .= "<p>Please enter an email</p>";
                } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                    $errmsg .= "<p>Please enter a valid email</p>";
                } else { // check if email exists in database
                    $new_email = true;
                    $friends = new Friends($host, $user, $dbpswd, $dbnm);
                    $result = $friends->__get("friend_email");
                    foreach ($result as $row) {
                        if ($row[0] == $_POST["email"]) {
                            $new_email = false; 
                            break;
                        }
                    }
                    $friends->closeConnection();
                    if ($new_email == false) {
                        $errmsg .= "<p>This email already exists with an account</p>";
                    } else {
                        $email = $_POST["email"];
                    }
                }
    
                // validate profile name
                if (!isset($_POST["name"])) {
                    $errmsg .= "<p>Please enter a name</p>";
                } elseif ($_POST["name"] == "") {
                    $errmsg .= "<p>Please enter a name</p>";
                } elseif (!preg_match("/^[A-Za-z]+$/", $_POST["name"])) {
                    $errmsg .= "<p>Name must only contain letters</p>";
                } else {
                    $name = $_POST["name"];
                }
    
                // validate password
                if (!isset($_POST["pswd"])) {
                    $errmsg .= "<p>Please enter a password</p>";
                } elseif ($_POST["pswd"] == "") {
                    $errmsg .= "<p>Please enter a password</p>";
                } elseif (!preg_match("/^[A-Za-z0-9]+$/", $_POST["pswd"])) {
                    $errmsg .= "<p>Password must only contain letters and numbers</p>";
                } elseif (!isset($_POST["confirm_pswd"])) {
                    $errmsg .= "<p>Please confirm password</p>";
                } elseif ($_POST["confirm_pswd"] == "") {
                    $errmsg .= "<p>Please confirm password</p>";
                } elseif ($_POST["pswd"] != $_POST["confirm_pswd"]) {
                    $errmsg .= "<p>Password and confirm password do not match try again</p>";
                } else {    
                    $pswd = $_POST["pswd"];
                }
                
                if ($errmsg != "") { // display errors if there is                    
                    echo "<div class='errors'>".$errmsg."</div>";
                    unset($_POST);
                } else { // set true to success and redirect user
                    // create new record into database
                    $friends = new Friends($host, $user, $dbpswd, $dbnm);
                    $friends->insert_friends($email, $pswd, $name, date("Y-m-d"), 0);
                    
                    // set sessions for redirect
                    $_SESSION["success"] = true;                  
                    $result = $friends->select("friends", "WHERE friend_email = '$email'"); // get details of new user
                    $_SESSION["friend_id"] = $result[0]["friend_id"]; // get id of new user from first result of query
                    $_SESSION["profile_name"] = $result[0]["profile_name"]; // get name of new user form first result of query
                    $friends->closeConnection();
                    header("location: friendadd.php");
                }
            }
        ?> 
    </body>
</html>