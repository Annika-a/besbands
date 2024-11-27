<?php
//Create new user  
    // Sanitize and trim input
    $newusername = htmlspecialchars(trim($_POST['newusername']));
    $newuserpassword = htmlspecialchars(trim($_POST['password']));
 
    if ($connectiontosqldbusrs->connect_error) {
        die("Connection failed: " . $connectiontosqldbusrs->connect_error);
    }

    // Test if the username already exists
    $stmttest = $connectiontosqldbusrs->prepare("SELECT count(*) FROM user_table WHERE user_name = ?");
    if ($stmttest === false) {
        echo "Error preparing statement: " . $connectiontosqldbusrs->error;
        exit;
    }
    $stmttest->bind_param("s", $newusername);
    $stmttest->execute();
    
    // Get result
    $stmttest->bind_result($count);
    $stmttest->fetch();
    $stmttest->close();

    if ($count == 0 && strlen($newusername) >=5){ 
        //Hash the password before storing it. comment not with this project!
        //$hashed_password = password_hash($newuserpassword, PASSWORD_DEFAULT); 
        $stmt = $connectiontosqldbusrs->prepare("INSERT INTO user_table (user_name, role, password, created, modified) 
                                VALUES (?, ?, ?, current_timestamp(), ?)");
        if ($stmt === false) {
            echo "Error preparing statement: " . $connectiontosqldbusrs->error;
            exit;
        } 
        // Set values for the placeholders
        $role = 'NORMAL';  
        $modified = '';  

        $stmt->bind_param("ssss", $newusername, $role, $newuserpassword, $modified);

        // Execute the statement
        if ($stmt->execute()) { 
             $_SESSION['systemmessage'] = "New user $newusername created successfully."; 
            header("Location: index.php"); 
            exit; 
        } else {
            echo "Error creating user: " . $stmt->error;
        } 
        $stmt->close();
    } else { 
        $_SESSION['systemmessage'] = "Invalid username";
        echo " Please choose a different username.";
    } 
?>