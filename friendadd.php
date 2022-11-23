<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="desccription" content="Web Application Development" />
        <meta name="Keywords" content="PHP, CSS, HTML" />
        <meta name="author" content="Jared" />
        <title>Add Friend</title>
        <link href="style.css" rel="stylesheet"/>
    </head>
    <body>          
        <br>     
        <?php                 
            require_once("functions/mysql.php");     
            require_once("functions/settings.php");             

            if ($_SESSION["register_success"] = false) { // check that session success was true
                echo "<p>We were unable to validate your login details</p>";
                echo "<a href=\"login.php\">Login</a>";
            } elseif (!isset($_SESSION["friend_id"])) { // check that session user id was set
                echo "<p>We were unable to retrieve your login details</p>";
                echo "<a href=\"login.php\">Login</a>";
            } else {                
                // variables
                $user_id = $_SESSION["friend_id"];
                $user_name = $_SESSION["profile_name"];
                $friends = new Friends($host, $user, $dbpswd, $dbnm);

                // check if unfriend is set when pressed
                // check if unfriend_id is set and has value
                // delete friend from myfriends and reduce number of friends by 1
                // redirect user back to same page                    
                if (isset($_POST["addfriend"])) {
                    if (!isset($_POST["friend_id"])) {
                        echo "<p>Unable to request an unfriend. Please try again</p>";
                    } elseif ($_POST["friend_id"] == "") {
                        echo "<p>Unable to request an unfriend. Please try again</p>";
                    } else {                        
                        $friend_id = $_POST["friend_id"];       

                        // add myfriend record
                        $friends->insert_myfriends($user_id, $friend_id);
                        $friends->insert_myfriends($friend_id, $user_id);
                        
                        // update friends record for friend 1 and friend 2
                        $query_extra_user = "SET num_of_friends = num_of_friends + 1 WHERE friend_id = " . $user_id;
                        $query_extra_friend = "SET num_of_friends = num_of_friends + 1 WHERE friend_id = " . $friend_id;
                        $friends->update("friends", $query_extra_user);
                        $friends->update("friends", $query_extra_friend);
                        header("location: friendadd.php");
                    }
                } else {
                    // get number of friends of user
                    $num_of_friends = "";
                    $results = $friends->select("friends", "WHERE friend_id = ".$user_id);
                    $num_of_friends = $results[0]["num_of_friends"];

                    // get details of non friends
                    $non_friends = $friends->get_nonfriends($user_id);

                    // display page
                    echo "<header>";
                    echo "<h1>My friend System";
                    echo "<br>" . $user_name . "'s Add Friend Page</h1>";
                    echo "<nav>";
                    echo "<a href=\"friendlist.php\">Friend List</a>";
                    echo "<a href=\"logout.php\">Log Out</a>";
                    echo "</nav>";
                    echo "</header>";

                    // display friends into table
                    // each request for unfriend have form
                    if ($non_friends == null) {
                        echo "<p>You have no more friends to add</p>";
                    } else {
                        // create sessions for number of friends that needs to be displayed   
                        echo "<div class='center'>";
                        echo "<br><h3>Total number of friends is " . $num_of_friends . "</h3>";                     
                        echo "<table class='display_tb'>";
                        $index = 0;                        
                        foreach ($non_friends as $row) {
                            echo "<tr><td>".$row["profile_name"]."</td>";
                            echo "<td><form action=\"friendadd.php\" method=\"POST\">";
                            echo "<input type=\"hidden\" name=\"friend_id\" value=\"".$row["friend_id"]."\"/>";
                            echo "<input type=\"submit\" name=\"addfriend\" value=\"Add Friend\"/>";
                            echo "</form></td></tr>";
                            $index = $index + 1;
                        }
                        echo "</table></div>";
                    }                    
                }
                $friends->closeConnection();
            }   
            ?>              
    </body>
</html>