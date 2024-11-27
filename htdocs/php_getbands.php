
<?php
$sqlBandsById = [];
$nosqlresponsearray = [];
 //From MySQL database
if ($sqldbon == 1) {
$sqlbband = "select * from band ba left join country co on co.country_code = ba.country_code
left join city ci on ci.city_code = ba.city_code";
$sql_band_info = $connectiontosqldb->query($sqlbband); 

if ($sql_band_info) {
    mysqli_data_seek($sql_band_info, 0);
    while ($row = $sql_band_info->fetch_assoc()) {
        $sqlBandsById[$row['band_id']] = [
            'band_id' => $row['band_id'],           
            'band_name' => $row['band_name'],
            'country_name' => $row['country_name'],
            'city_name' => $row['city_name'],
            'formed_year' => $row['formed_year'],
            'database_name' => "sql"
        ];
    }
}
}
 
if ($nosqldbon == 1) {
//NoSQL database connection to AWS API-> Lambda -> DynamoDB
// API URL 
$url = $url_api_bands . "/items";
// Initialize cURL session
$ch = curl_init(); 
// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);           // Set the URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response instead of outputting it
curl_setopt($ch, CURLOPT_HTTPGET, true);        // Set the request method to GET

// Execute the cURL session and get the response
$response = curl_exec($ch);

// Check for errors
if ($response === false) {
    echo "cURL Error: " . curl_error($ch);
    }else {
    // Decode the JSON response into a PHP array
    $nosqlresponsearray = json_decode($response, true);    
    if ($nosqlresponsearray === null) {
        echo "Error decoding JSON response.";
    }else { 
        //print_r($nosqlresponsearray);  
    }
    }
curl_close($ch);
}

//Combine data from databases in use to combinedBands
//Combine the SQL and API data based on 'band_id'
$combinedBands = [];

        // Merge SQL data first
        foreach ($sqlBandsById as $bandId => $sqlBand) {
            $combinedBands[$bandId] = $sqlBand; // Add SQL data first
        }
        // Merge data from the API, if available
        foreach ($nosqlresponsearray as $apiBand) {
            $bandId = $apiBand['band_id']; // Get the band_id from the API response
            
        // If band_id is in SQL data, merge the API data
        if (isset($combinedBands[$bandId])) {
                $combinedBands[$bandId] = array_merge($combinedBands[$bandId], $apiBand);
            } else {
                // If the band_id wasn't in SQL, just add the API data
            $combinedBands[$bandId] = $apiBand;
            }
        }

        //Bands to alphapetical order
        usort($combinedBands, function($a, $b) {
            return strcmp($a['band_name'], $b['band_name']);
        });
?>