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
        <title>Friend List</title>
        <link href="style.css" rel="stylesheet"/>
    </head>
    <body>          
        <br>     
        <?php                 
            require_once("functions/mysql.php");     
            require_once("functions/settings.php");             

            if ($_SESSION["success"] = false) { // check that session success was true
                echo "<p>We were unable to validate your login details</p>";
            } elseif (!isset($_SESSION["friend_id"])) { // check that session user id was set
                echo "<p>We were unable to retrieve your login details</p>";
            } else {                
                // variables
                $user_id = $_SESSION["friend_id"];
                $user_name = $_SESSION["profile_name"];
                $friends = new Friends($host, $user, $dbpswd, $dbnm);
                // check if unfriend is set when pressed
                // check if unfriend_id is set and has value
                // delete friend from myfriends and reduce number of friends by 1                     
                if (isset($_POST["unfriend"])) {
                    if (!isset($_POST["unfriend_id"])) {
                        echo "<p>Unable to request an unfriend. Please try again</p>";
                    } elseif ($_POST["unfriend_id"] == "") {
                        echo "<p>Unable to request an unfriend. Please try again</p>";
                    } else {                        
                        $unfriend_id = $_POST["unfriend_id"];                       
                        // delete myfriend record
                        $query_extra_user = "WHERE friend_id1 = " . $user_id . " and " . "friend_id2 = " . $unfriend_id;
                        $query_extra_friend = "WHERE friend_id1 = " . $unfriend_id . " and " . "friend_id2 = " . $user_id;
                        $friends->delete("myfriends", $query_extra_user);                                        
                        $friends->delete("myfriends", $query_extra_friend);                                        
                        // update friends record for friend 1 and friend 2
                        $query_extra_user = "SET num_of_friends = num_of_friends - 1 WHERE friend_id = " . $user_id;
                        $query_extra_friend = "SET num_of_friends = num_of_friends - 1 WHERE friend_id = " . $unfriend_id;
                        $friends->update("friends", $query_extra_user);
                        $friends->update("friends", $query_extra_friend);
                        echo "<p>Unfriend success</p>";
                        header("location: friendlist.php");
                    }
                } else {
                    // get number of friends of user
                    $num_of_friends = "";
                    $results = $friends->select("friends", "WHERE friend_id = ".$user_id);
                    $num_of_friends = $results[0]["num_of_friends"];

                    // get friends of user into an array                
                    $query_extra = "JOIN myfriends mf 
                    ON mf.friend_id2 = f.friend_id 
                    WHERE mf.friend_id1 = ". $user_id;
                    $results = $friends->select("friends f", $query_extra);
                    // display page                    
                    echo "<header>";
                    echo "<h1>My friend System";
                    echo "<br>" . $user_name . "'s Friend List Page</h1>";                    
                    echo "<nav>";
                    echo "<a href=\"friendadd.php\">Add Friends</a>";
                    echo "<a href=\"logout.php\">Log Out</a>";
                    echo "</nav>";
                    echo "</header>";
                    // display friends into table
                    // each request for unfriend have form
                    echo "<div class='center'>";
                    echo "<br><h3>Total number of friends is " . $num_of_friends . "</h3>";
                    echo "<table class='display_tb'>";
                    foreach ($results as $row) {
                        echo "<tr><td>".$row["profile_name"]."</td>";
                        echo "<td><form action=\"friendlist.php\" method=\"POST\">";
                        echo "<input type=\"hidden\" name=\"unfriend_id\" value=\"".$row["friend_id"]."\"/>";
                        echo "<input type=\"submit\" name=\"unfriend\" value=\"Unfriend\"/>";
                        echo "</form></td></tr>";
                    }
                    echo "</table></div>";                    
                }
                $friends->closeConnection();
            }   
            ?>  
    </body>
</html>