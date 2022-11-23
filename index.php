<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="desccription" content="Web Application Development" />
        <meta name="Keywords" content="PHP, CSS, HTML" />
        <meta name="author" content="Jared" />
        <title>Home Page</title>
        <link href="style.css" rel="stylesheet"/>
    </head>
    <body>   
        <header>
            <h1>My friend System<br>Assignment Home Page</h1>
            <nav>
                <a id="current-page" href="index.php">Home</a>
                <a href="signup.php">Sign Up</a> 
                <a href="login.php">Log In</a>
                <a href="about.php">About</a> 
            </nav>
        </header>
        <div class="center">
            <p>
                Name: Jared Teng<br>
                Student ID: 103492121<br>
                Email: <a id="content-link" href="mailto:103492121@student.swin.edu.au">103492121@student.swin.edu.au</a>                        
            </p>
            <p>
                I declare that this assignment is my individual work.
                I have not worked collaboratively nor have I copied from any other
                student's work or from any other source.
            </p>  
            <?php        
                require_once("functions/settings.php");                     
                // connect to server           
                $dbConnect = new mysqli($host, $user, $dbpswd);
                if ($dbConnect->connect_error)
                    die("<p>Unable to connect to the database server.</p>"
                    . "<p>Error code: ".$dbConnect->connect_errno
                    . ":" . $dbConnect->connect_error . "</p>");
                // connect to database
                $dbConnect->select_db($dbnm)
                    or die("<p>Unable to select the database.</p>"
                        . "<p>Error code: ".$dbConnect->connect_errno
                        . ":" . $dbConnect->connection_error . "</p>");
                // Create table friends if not exist            
                $exist_friends = $dbConnect->query("SELECT 1 FROM friends LIMIT 1");
                if ($exist_friends !== FALSE) {
                    // Table exists
                } else {
                    $create_tb_friends = "CREATE TABLE IF NOT EXISTS friends
                    (
                        friend_id INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        friend_email VARCHAR(50) NOT NULL,
                        friend_pswd VARCHAR(20) NOT NULL,
                        profile_name VARCHAR(30) NOT NULL,
                        date_started DATE NOT NULL,
                        num_of_friends INTEGER UNSIGNED
                    )";
                    $result = $dbConnect->query($create_tb_friends)    
                        or die ("<p>Unable to execute the query.</p>"
                        . "<p>Error code 1 " . $dbConnect->errno
                        . ": " . $dbConnect->error . "</p>"); 
                    // Insert records to tb friends
                    $insert_tb_friends = "INSERT INTO friends (friend_id, friend_email, friend_pswd, profile_name, date_started, num_of_friends) VALUES
                    (1, 'lena@gmail.com', 'kcv00sAe3mPthZbi', 'Lena Warren', '2021-9-22', 1),
                    (2, 'marius@gmail.com', 'sYRUFzH866gr2w4l', 'Marius Bravo', '2021-10-12', 3),
                    (3, 'lilia@gmail.com', 'BNKN3itvDNjL3UPz', 'Lilia Walsh', '2021-5-4', 2),
                    (4, 'mayur@gmail.com', 'MAbuLXYOm55GnDwR', 'Mayur Lee', '2020-1-27', 1),
                    (5, 'danny@gmail.com', 'St22ytuahl8UCOEb', 'Danny Nicholson', '2015-10-21', 2),
                    (6, 'brayden@gmail.com', '9qHTaf9Z145lQmRd','Brayden Hume', '2021-2-14', 2),
                    (7, 'elise@gmail.com', '6vNKcQOHbrjNA39G', 'Elise Sanders', '2019-6-17', 3),
                    (8, 'vivaan@gmail.com', 'RxMCFyow516uAAsK', 'Vivaan Beltran', '2019-3-21', 2),
                    (9, 'musa@gmail.com', 'dtrdbeW2s53IfUek', 'Musa Arias', '2019-7-14', 3),
                    (10, 'ivor@gmail.com', 'gifZppNgoyi2Qa7Y', 'Ivor Guy', '2018-12-25', 1)";            
                    $result = $dbConnect->query($insert_tb_friends)
                        or die ("<p>Unable to execute the query.</p>"
                        . "<p>Error code 2 " . $dbConnect->errno
                        . ": " . $dbConnect->error . "</p>");
                }
                // Create table myfriends if not exist
                $exist_myfriends = $dbConnect->query("SELECT 1 FROM myfriends LIMIT 1");
                if ($exist_myfriends !== FALSE) {
                    // Table exist
                } else {
                    // Table doesn't exist
                    $create_tb_myfriends = "CREATE TABLE IF NOT EXISTS myfriends
                    (
                        friend_id1 INTEGER NOT NULL,
                        friend_id2 INTEGER NOT NULL
                    )";          
                    $result = $dbConnect->query($create_tb_myfriends)
                        or die ("<p>Unable to execute the query.</p>"
                        . "<p>Error code 3 " . $dbConnect->errno
                        . ": " . $dbConnect->error . "</p>");
                    // insert records
                    $insert_tb_myfriends = "INSERT INTO myfriends (friend_id1, friend_id2) VALUES
                    (1, 10), (2, 9), (2, 8), (2, 7), (3, 6), (4, 5), (6, 5), (7, 3), (7, 9), (8, 9),
                    (10, 1), (9, 2), (8, 2), (7, 2), (6, 3), (5, 4), (5, 6), (3, 7), (9, 7), (9, 8)";
                    $result = $dbConnect->query($insert_tb_myfriends)    
                        or die ("<p>Unable to execute the query.</p>"
                        . "<p>Error code 4 " . $dbConnect->errno
                        . ": " . $dbConnect->error . "</p>");   
                }

                echo "<p>Tables successfully created and populated</p>";
                // close the connection
                $dbConnect->close();
            ?>
        </div>
    </body>
</html>