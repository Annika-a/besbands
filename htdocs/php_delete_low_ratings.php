<?php
$systemmessage = "";
 
// Check if the user is logged in
if (isset($_SESSION['username'])) { 
// Delte from MySQL database all ratings below 4 if connection is selected
if ($sqldbon == 1) {
                $reviewtobedeleted = "DELETE FROM band_rating WHERE star_rating < 4";
                $stmtdeleterating = $connectiontosqldb->prepare($reviewtobedeleted);  
                if ($stmtdeleterating->execute()) {
                    $systemmessage .= "Deleted all reviews with a rating below 4. Good job, " . $_SESSION['username'] . "! database: SQL :: ";
                } else { 
                    $systemmessage .=  "Error deleting reviews: " . $stmtdeleterating->error;
                }
                $stmtdeleterating->close();
}else { 
        $systemmessage .=  "No MySQL connection. Cannot felete from MySQL db. "; 
    }   
 
// NoSQL to delete reviews with rating less than 4 if connection is selected
if ($nosqldbon == 1) {
                //Get all noSQL ratings
                $url = "https://8orwdqal0g.execute-api.eu-north-1.amazonaws.com/items";

                // Initialize cURL session
                $ch = curl_init();

                // Set cURL options
                curl_setopt($ch, CURLOPT_URL, $url);           // Set the URL
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response instead of outputting it
                curl_setopt($ch, CURLOPT_HTTPGET, true);        // Set the request method to GET

                // Execute the cURL session and get the response
                $nosqlbandratings = curl_exec($ch);

                // Check for errors
                if ($nosqlbandratings === false) {
                    echo "cURL Error: " . curl_error($ch);
                } else { 
                    echo "Response: " . $nosqlbandratings;
                }

                //Find deletable from NoSql database
                $nosqlresponsearray = json_decode($nosqlbandratings, true);    
                $tobedeleted = [];
                        foreach ($nosqlresponsearray as $rating ) {
                            $ratingId = $rating['band_rating_id'];
                            if($rating['star_rating'] < 4){
                            array_push ($tobedeleted , $rating['band_rating_id']);
                        }  }
                        print_r($tobedeleted);
                       // Close cURL session
                curl_close($ch);

                 //Delete from NoSql database
                foreach ($tobedeleted as $deletable ) { 
                $band_rating_id = $deletable;                     
                $url = "https://8orwdqal0g.execute-api.eu-north-1.amazonaws.com/items/{$band_rating_id}";
                // Set up cURL to send a DELETE request
                $ch = curl_init($url);
                // Set the HTTP method to DELETE
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                $headers = [
                    "Content-Type: application/json",
                   // "x-api-key: <your-api-key>", 
                ];
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                // Return the response instead of printing it
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // Execute the request
                $response = curl_exec($ch);
                // Check if any error occurred
                if(curl_errno($ch)) {
                    echo "cURL Error: " . curl_error($ch);
                } else {
                    // Successful request, display the response
                    $systemmessage .=  "Response from API: " . $response. "! database: NoSQL" ;
                }
            curl_close($ch);      
            }      
    }else { 
        $systemmessage .=  "No NoSQL connection. Cannot delete from NoSQL db. "; 
    } 
} else { 
        $systemmessage .=  "You need to log in to delete ratings!";
        echo "You need to log in to delete ratings!";
    } 
    $_SESSION['systemmessage'] = $systemmessage ;
    header("Location: index.php"); 
    exit; // Ensure no further code is executed after the redirect     
?>
 
