<?php
//Query for ratings
//$sqlrating = "select br.band_rating_id, br.band_id, br.user_id, ut.user_name, br.star_rating, br.modified from band_rating br left join user_table ut on ut.user_id = br.user_id";
$sqlRatingsById = [];
$nosqlbandratings = [];
$combinedRatings = [];

//Get all MySQL ratings if connection is selected
if ($sqldbon == 1) {
    $sqlrating = "select band_rating_id,  band_id,  user_id,  star_rating,  modified from band_rating  ";
    $sql_band_ratings1 = $connectiontosqldb->query($sqlrating); 
    if ($sql_band_ratings1) {
        mysqli_data_seek($sql_band_ratings1, 0);
        while ($row = $sql_band_ratings1->fetch_assoc()) {
            $sqlRatingsById[$row['band_rating_id']] = [
                'band_id' => $row['band_id'],
                'user_id' => $row['user_id'],
                'modified' => $row['modified'],
                'star_rating' => $row['star_rating']
            ];
        }
    }
}

//Get all noSQL ratings if connection is selected
if ($nosqldbon == 1) {
        $url = trim($url_api_bandratings) . "/items";
        
        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);

        // Execute the cURL session and get the response
        $nosqlbandratings = curl_exec($ch);

        // Check for errors
        if ($nosqlbandratings === false) {
            echo "cURL Error: " . curl_error($ch);
        } else {
            // Decode the JSON response into a PHP array
            $nosqlbandratings = json_decode($nosqlbandratings, true);    
            if ($nosqlbandratings === null) {
                echo "Error decoding JSON response.";
            } else {
            }
        
        }
        // Close cURL session
        curl_close($ch);
}
        // Merge SQL data first
        foreach ($sqlRatingsById as $ratingId => $sqlBand) {
            $combinedRatings[$ratingId] = $sqlBand; // Add SQL data first
        }
        // Merge data from the API, if available
        foreach ($nosqlbandratings as $apiRating) {
            $ratingId = $apiRating['band_rating_id']; 
            
        // If band_id is in SQL data, merge the API data
        if (isset($combinedRatings[$ratingId])) {
                $combinedRatings[$ratingId] = array_merge($combinedRatings[$ratingId], $apiRating);
            } else {
                // If the band_id wasn't in SQL, just add the API data
                $combinedRatings[$ratingId] = $apiRating;
            }
        } 
?> 
 