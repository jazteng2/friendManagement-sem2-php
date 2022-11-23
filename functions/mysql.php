<?php
    class Friends {     
        private $dbconnect;

        public function __construct($host, $user, $pswd, $dbnm) {
            $dbconnect = new mysqli($host, $user, $pswd, $dbnm);
            // Error handling db connection
            if ($dbconnect->connect_error) 
            {
                die ("<p>Unable to connect to the database server.</p>"
                    ."<p>Error code ".$dbconnect->connect_errno.": "
                    .$dbconnect->connect_error."</p>"); // debug
            }
            else 
            {
                $this->dbconnect = $dbconnect;                
            }
        }
        // Properties
        public function __get($name) {
            // return an array of all elements from a column from table friends
            $query = "SELECT $name FROM friends";
            $result = $this->dbconnect->query($query)
                or die("<p>Unable to execute the query of getting $name in friends</p>"
                . "<p>Error code " . $this->dbconnect->connect_errno
                . ": " . $this->dbconnect->connect_error . "</p>");    
                
            // display result
            $row = $result->fetch_all();
            return $row;
        }
        
        // Friend Methods
        public function get_nonfriends($user_id) {
            // get non friends by comparing a list of users to user's friends
            // create view of user's friends to work against table friends
            $query_compare = "CREATE TABLE friendlist AS 
            SELECT friend_id FROM friends f
            JOIN myfriends mf
            ON mf.friend_id2 = f.friend_id
            WHERE mf.friend_id1 = ".$user_id;

            $this->dbconnect->query($query_compare)
            or die ("<p>Unable to execute create view friendlist query.</p>"
            . "<p>Error code " . $this->dbconnect->errno
            . ": " . $this->dbconnect->error . "</p>");

            // find non friends using created view friendlist as comparison
            $query_non_friends = "SELECT * FROM friends f
            WHERE NOT EXISTS ( 
                SELECT fl.friend_id
                FROM friendlist fl
                WHERE fl.friend_id = f.friend_id
            ) AND f.friend_id != $user_id";

            $result = $this->dbconnect->query($query_non_friends)
            or die ("<p>Unable to execute get non friends query.</p>"
            . "<p>Error code " . $this->dbconnect->errno
            . ": " . $this->dbconnect->error . "</p>");

            // delete crated table
            $this->dbconnect->query("DROP TABLE friendlist") 
            or die ("<p>Unable to execute drop table friendlist query.</p>"
            . "<p>Error code " . $this->dbconnect->errno
            . ": " . $this->dbconnect->error . "</p>");

            // return associate array to user
            // return null when array is empty (no results from query)
            while ($row = $result->fetch_assoc()) {
                $array[] = $row;
            }
            if (empty($array)) {
                return null;
            }
            return $array;
        }
        public function insert_friends($email, $pswd, $profile_name, $dateStart, $num_friends) {
            // insert records to table friends
            $query = "INSERT INTO friends (friend_email, friend_pswd, profile_name, date_started, num_of_friends) VALUES ('$email', '$pswd', '$profile_name', '$dateStart', '$num_friends')";
            $this->dbconnect->query($query)
            or die ("<p>Unable to execute insert friends query.</p>"
            . "<p>Error code " . $this->dbconnect->errno
            . ": " . $this->dbconnect->error . "</p>");
        }    
        public function insert_myfriends($friend_id1, $friend_id2) {
            // insert records to table myfriends
            $query = "INSERT INTO myfriends (friend_id1, friend_id2) VALUES ('$friend_id1', '$friend_id2')";
            $this->dbconnect->query($query)
            or die ("<p>Unable to execute insert myfriends query.</p>"
            . "<p>Error code " . $this->dbconnect->errno
            . ": " . $this->dbconnect->error . "</p>");
        }

        // GENERAL FUNCTIONS
        public function select($tbname, $query_extra) { 
            // general select mysql php function
            $query = "SELECT * FROM " . $tbname . " " . $query_extra;            
            $array = array();
            $result = $this->dbconnect->query($query)
            or die("<p>Unable to execute select query.</p>"
            . "<p>Error code " . $this->dbconnect->errno
            . ": " . $this->dbconnect->error . "<p>");
            while ($row = $result->fetch_assoc()) {
                $array[] = $row;
            }
            return $array;
        }   
        public function delete($tbname, $query_extra) { 
            // general delete mysql php function
            $query = "DELETE FROM " . $tbname . " " . $query_extra;
            $this->dbconnect->query($query)
            or die ("<p>Unable to execute delete query.</p>"
            . "<p>Error code " . $this->dbconnect->errno
            . ": " . $this->dbconnect->error . "</p>");
        } 
        public function update($tbname, $query_extra) { 
            // general udpate mysql php function
            $query = "UPDATE " . $tbname . " " . $query_extra;
            $this->dbconnect->query($query)
            or die ("<p>Unable to execute update query.</p>"
            . "<p>Error code " . $this->dbconnect->errno
            . ": " . $this->dbconnect->error . "</p>");
        }
        public function closeConnection() { $this->dbconnect->close(); }
    }
?>