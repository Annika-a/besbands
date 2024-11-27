<?php
ob_start();

//Connection details
include("./php_connection.php");  

error_reporting(E_ALL);
ini_set('display_errors', 1);
 
session_start(); 
$currentuser = "-"; 
$currentsystemmessage = "-"; 
     

$sqldbon = 0;
$nosqldbon = 0;


if (isset($_SESSION['username'])){
    // Save the username in the session 
    $currentuser = $_SESSION['username'];  
}

// Retrieve session values if they exist
if (isset($_SESSION['sqldb_on'])) {
    $sqldbon = $_SESSION['sqldb_on'];
}

if (isset($_SESSION['nosqldb_on'])) {
    $nosqldbon = $_SESSION['nosqldb_on'];
}

// Query for user data
$sqluser = "select * from user_table order by user_id";
$sql_users = $connectiontosqldb->query($sqluser); 


//HTML page
echo "<head> <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />
<link rel=\"stylesheet\" href=\"styles.css\">
</head>
<body>
   <header class=\"site-header\"> </header>
<main>
    <div class=\"container\">  
        <h2>Only good bands</h2><p>Create account or log in to rate the best bands</p></div> 
            <div class=\"container\">
            <div class=\"column\">"; 
    if (isset($_SESSION['username'])) {         
        echo "<h3> Current user: $currentuser</h3> 
                <form method=\"post\"> 
                    <input type=\"submit\" name=\"logout\" value=\"Log out\">
                </form>";
    if (isset($_POST['logout'])) { 
        if (isset($_SESSION['username'])){
            //session_destroy();  
            unset($_SESSION['username']); 
            $_SESSION['systemmessage'] = "$currentuser logged out";  
            header("Location: index.php");
            exit;
        }  
   }      
}

//User login
 if (empty($_SESSION['username'])) {         
    echo "<h3> Login: </h3>
    <form method=\"post\">
        <label for=\"username\">Username:</label><br>
        <input type=\"text\" id=\"loginusername\" name=\"loginusername\" minlength=\"5\" maxlength=\"10\" required><br>
        <label for=\"password\">Password:</label><br>
        <input type=\"password\" id=\"loginpassword\" name=\"loginpassword\" minlength=\"5\" maxlength=\"10\" required><br><br>
        <input type=\"submit\" name=\"userlogin\" value=\"Login\">
    </form>"; 
    if (isset($_POST['userlogin'])) { include("./php_user_login.php");  }
 
//Create new user
echo "<h3> Create new user: </h3> ";
        //Max number of users is 10
         $sql_count_users = $connectiontosqldb->prepare("select count(*) from user_table order by user_id");
        if ($sql_count_users === false) {
            echo "Error preparing query: " . $connectiontosqldb->error;
            exit;
        } 
        // Execute the query
        $sql_count_users->execute();
        $sql_count_users->bind_result($user_count);
        $sql_count_users->fetch();
        $sql_count_users->close();
        
        if ($user_count >= 10) {
        //$_SESSION['systemmessage'] = "Max number of users 10. Delete user before creating new";
        echo "Maximum number of users is 10. Delete old user before creating a new one.";
        }
        else{
        echo"<form method=\"post\">
            <label for=\"username\">Username:</label><br>
            <input type=\"text\" pattern=\"[A-Za-z].{4,9}\" title=\"A-Z a-z, min 5 max 10\" id=\"newusername\" name=\"newusername\" value=\"\" maxlength=\"10\"><br>
            <label for=\"password\">Password:</label><br>
            <input type=\"password\" pattern=\"[A-Za-z].{4,9}\" title=\"A-Z a-z, min 5 max 10\" id=\"password\" name=\"password\" maxlength=\"10\"><br><br>
            <input type=\"submit\" name=\"submitnewuser\" value=\"Create user\">
        </form>";
        }
        if (isset($_POST['submitnewuser'])) {  include("./php_create_new_user.php");  } 
}

//Users table
echo "</div> 
        <div class=\"column\">
        <h3>Users</h3>
         <table > <tr>
            <th>User id</th>
            <th>Username</th>
            <th>Password</th>
            <th>role</th>
            <th>created</th>            
            <th>Delete user and<br> user reviews</th></tr>";
        if ($sql_users) { 
        while($row = $sql_users->fetch_assoc()) {
        echo "<tr><td>". $row["user_id"]. 
            "</td><td>". $row["user_name"]. 
            "</td><td>";
            if($row["role"] == 'NORMAL'){ 
            echo $row["password"]; } else {echo "*********";}
            echo "</td><td>". $row["role"].
            "</td><td>". $row["created"].        
            "<td class=\"centered\">
                <form method=\"post\">
                    <input type=\"hidden\" name=\"deleteuser\" value=\"". $row["user_name"]. "\" /><input type=\"submit\" value=\"Delete\" />
                </form>
            </td></tr>";
            }
        //Delete user and reviews
        if (isset($_POST['deleteuser'])) {include("./php_delete_user.php");  } 
} else {
echo "0 results";
}
echo "</tr></table></div> </div>  ";

//Band section header 
echo "<div class=\"container\" id=\"section-1\">";
echo "<div class=\"column\"><h2>Best Bands</h2>  
        <h3>Select databases for band and rating data:</h3>
        <form method=\"get\">
            <input type=\"checkbox\" name=\"selectdb[]\" value=\"MySQL\">
            <label> MySQL: Finnish bands & ratings </label><br> 
            <input type=\"checkbox\" name=\"selectdb[]\" value=\"NoSQL\">
            <label> NoSQL DynamoDB: Foreign bands & ratings </label><br>
            <input type=\"checkbox\" name=\"selectdb[]\" value=\"noconnection\">
            <label> Disconnect</label><br>
            <input type=\"submit\" value=\"Set connections!\"> <br>
        </form>";

// Display the connection status 
if ($sqldbon == 1) {
    echo " <b>MySQL database Connected</b> <br>"; 
} else {
    echo "MySQL database not connected<br>";
}
if ($nosqldbon == 1) {
    echo "<b>NoSQL database Connected</b><br>";
} else {
    echo "NoSQL database not connected<br>";
}

if (isset($_GET['selectdb'])) { 
    $db_name = $_GET['selectdb'];
    
    // Unset session variables before setting new values
    unset($_SESSION['sqldb_on']);
    unset($_SESSION['nosqldb_on']);
    
    // If no checkboxes are selected, make sure no databases are connected
    if (empty($db_name)) {
        echo "No databases selected.  <br>";
    } else {
        // Loop through each selected database and update session variables accordingly
        foreach ($db_name as $db) {
            if ($db == 'MySQL') {
                $_SESSION['sqldb_on'] = 1;  // MySQL database connected
                $sqldbon = 1;
            }
            if ($db == 'NoSQL') {
                $_SESSION['nosqldb_on'] = 1;  // NoSQL database connected
                $nosqldbon = 1;
            } 
            if ($db == 'noconnection') {
                unset($_SESSION['sqldb_on']);
                unset($_SESSION['nosqldb_on']);
                $nosqldbon = 0;
                $sqldbon = 0;
            } 
        }
    }
    // After processing, redirect back to the page to avoid form resubmission on refresh
    header("Location: index.php");
    exit(); 
} else {
}
echo"</div>"; 

//Delete low ratings section
      echo "<div class=\"column\">
         <h3> Good bands only !  </h3> 
            <form method=\"post\">
                <input type=\"hidden\" name=\"deletebadreviews\" value=\"deletebadreviews\" />
                <input type=\"submit\" name=\"deletelowreviews\" value=\"Delete under 4 start ratings from selected databases\" />
             </form> 
        <br></div> ";  
     if (isset($_POST['deletelowreviews'])) { include("./php_delete_low_ratings.php"); } 

//Get bands
include("./php_getbands.php"); 
include("./php_getratings.php");  

echo "<table><tr><th width=\"20%\">Band Name</th><th width=\"20%\">Country, City, formed year</th><th>Ratings</th><th>Edit/give <br>rating ";
        if (isset($_SESSION['username']))  { 
                    echo "as <br>". $_SESSION['username'];
                    }
                    echo "</th> </tr>";
         
    foreach ($combinedBands as $band) {
    echo "<tr> <td>" . $band['band_name'] . "</td> 
         <td>". $band['country_name']. "<br>" . $band['city_name']. "<br>". $band['formed_year']. "</td><td>";
     // Check if there are ratings for this band
    $hasRatings = false;

    echo"<table class=\"smaller\"><tr><th width=\"50%\">modified</th><th>user id</th><th>star rating</th></tr> ";
    $ratingall = 0;
    $rowcount = 0;       
    foreach ($combinedRatings as $ratingx) {
        // Match the band_id with the ratings
        if ($band['band_id'] == $ratingx['band_id']) {
            // Display each rating in the nested table
            echo "<tr>
                    <td>" . htmlspecialchars($ratingx['modified']) . "</td>
                    <td>" . htmlspecialchars($ratingx['user_id']) . "</td>
                    <td>" . htmlspecialchars($ratingx['star_rating']) . "</td>
                </tr>";
            $ratingall += $ratingx["star_rating"];
            $rowcount += 1;                    
            $hasRatings = true;
        }
    }
   
    //If no ratings were found for this band, show a message
    //Else count average
    if (!$hasRatings) {
        echo "<tr><td colspan='3'>No ratings available</td></tr>";
    }else{
         echo "<tr><td> </td><td><b>Rating all</b>: </td><td>" .  number_format($ratingall / $rowcount, 1, '.'). "/5</td></tr>";
    }
    echo"</table></td><td>";
    //Loggedin user can give ratings and edit ratings
     if (isset($_SESSION['username']))  {
         echo " <form action=\"\" method=\"POST\" id=\"userrating\">
				<input type=\"radio\" name=\"" . $band["band_id"] . "newrating\"  value=\"1\" />
				<label for=\"1\">*</label><br>
				<input type=\"radio\" name=\"" . $band["band_id"] . "newrating\"  value=\"2\" />
				<label for=\"2\">**</label><br>
				<input type=\"radio\" name=\"" . $band["band_id"] . "newrating\" value=\"3\" />
				<label for=\"3\">***</label><br>
				<input type=\"radio\" name=\"" . $band["band_id"] . "newrating\" value=\"4\" />
				<label for=\"4\">****</label><br>
                <input type=\"radio\" name=\"" . $band["band_id"] . "newrating\" value=\"5\" />
				<label for=\"5\">*****</label><br>
                <input type=\"submit\" value=\"Save\">  
            </form>";  
        if (isset($_POST[$band["band_id"]. "newrating"])) { include("./php_give_rating.php"); } 
        }
        else {
        echo "Log in to edit";
        }     
    echo "</td></tr>";
}
echo "</table>";

 
//Footer
echo " <br><br><br><br><br><br>
    <div class=\"footermessages\" >"; 
        if (isset($_SESSION['systemmessage'])){ 
            $currentsystemmessage = $_SESSION['systemmessage']; 
            // Display system message in footer
            echo $currentsystemmessage ;
            echo "<br>session user:  $currentuser<br> ";
        } 
        if ($sqldbon == 1){
              echo "SQL db in use,";
            } else { echo " SQL db not in use,"; }
        if ($nosqldbon == 1){
              echo "  NoSQL db in use";
            } else { echo "  NoSQL db not in use"; }
echo "</div>
</body> ";
ob_end_flush();
?>

 