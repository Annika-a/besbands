<?php
$newrating = $_POST[$band["band_id"] . "newrating"];
$ratingband = $band["band_id"];
$banddatabase = !isset($band["database_name"]) ? "nosql" : $band["database_name"]; 
$ratinguser = $_SESSION["username"]; 

//database_name' => "sql"

// Get the user_id could be saved to session
$stmuserid = $connectiontosqldb->prepare( "SELECT user_id FROM user_table WHERE user_name = ?" );
if ($stmuserid === false) {
    echo "Error preparing statement: " . $conn2->error;
    exit();
}

$stmuserid->bind_param("s", $ratinguser);
$stmuserid->execute();
$stmuserid->bind_result($userid);
$stmuserid->fetch();
$stmuserid->close();
 
// Check if user_id was found
if (!$userid) {
    $_SESSION["systemmessage"] = "User not found";
    exit();
}



//Insert or edit  rating to SQL database
if($banddatabase == 'sql'){
// Test if the rating already exists
$stmttest = $connectiontosqldb->prepare("select count(*) from band_rating where user_id = ? and band_id = ?");
if ($stmttest === false) {
    echo "Error preparing statement: " . $connectiontosqldb->error;
    exit();
}
$stmttest->bind_param("ii", $userid, $ratingband);
$stmttest->execute();

// Get the result
$stmttest->bind_result($count);
$stmttest->fetch();
$stmttest->close();

if ($count == 0) {
    // Insert the new rating if it doesn't already exist for this user
    $stmtrating = $connectiontosqldb->prepare("INSERT INTO band_rating (user_id, band_id, star_rating) VALUES (?, ?, ? )");
    if ($stmtrating === false) {
        echo "Error preparing statement: " . $connectiontosqldb->error;
        exit();
    }
    $stmtrating->bind_param("iii", $userid, $ratingband, $newrating);
    $_SESSION["systemmessage"] = "New rating created band: $ratingband rating: $newrating, database: $banddatabase ";
    
    if ($stmtrating->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error creating rating: ". $stmtrating->error;
    }
    $stmtrating->close();
} elseif ($count == 1) {
    // Update rating if it already exists
    $stmteditrating = $connectiontosqldb->prepare("UPDATE band_rating set star_rating = ?, modified = CURRENT_TIMESTAMP() where user_id = ? and band_id = ? ");
    if ($stmteditrating === false) {
        echo "Error preparing statement: " . $stmteditrating->error;
        exit();
    }
    $stmteditrating->bind_param("iii", $newrating, $userid, $ratingband);

    if ($stmteditrating->execute()) {
        $_SESSION["systemmessage"] = "Rating edited. New value $newrating database: $banddatabase ";
        header("Location: index.php");
        exit();
    } else {
        echo "Error creating rating: " . $stmtrating->error;
    }
    exit();
} else {
    echo "error";
}
$stmteditrating->close();
}


//Insert or edit rating to NoSQL database
else if($banddatabase == 'nosql'){ 
// API URL
$url = $url_api_bandratings . "/items";
$band_rating_id = 12;
$band_id = $ratingband;
$user_id = $userid;
$modified = date("Y-m-d h:m:s");
$star_rating = $newrating;

// Data to send in the PUT request
$data = [
   //"band_rating_id" => $band_rating_id, //band_rating_id is defined at lambda function for new 
    'band_id' => $band_id,
    'user_id' => $user_id ,
    'modified' => $modified,
    'star_rating' => $star_rating
];

// Convert the data array to a JSON string
$jsonData = json_encode($data);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");  
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);   
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
]);

// Execute the cURL session and capture the response
$response = curl_exec($ch);

// Check for errors
if ($response === false) {
    echo "cURL Error: " . curl_error($ch);
     $_SESSION["systemmessage"] = "Error inserting rating. database: $banddatabase, cURL Error: " . curl_error($ch);  
} else {
    // Print the response
        echo "Response: " . $response;
        $_SESSION["systemmessage"] = "Rating created,edited band: $ratingband rating: $newrating, database: $banddatabase . NoSQL Response: " . $response;;
        header("Location: index.php");
        exit();
}
curl_close($ch);
}

?>
 


