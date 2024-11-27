<?php
    // Sanitize input
    $loginpassword = htmlspecialchars(trim($_POST['loginpassword']));
    $loginusername = htmlspecialchars(trim($_POST['loginusername']));
 
    // Prepare and execute query using prepared statements to avoid SQL injection
    $selectuser = $connectiontosqldb->prepare("SELECT count(*) FROM user_table WHERE user_name = ? AND password = ?");
    $selectuser->bind_param("ss", $loginusername, $loginpassword);
    $selectuser->execute(); 
    // Get result
    $selectuser->bind_result($count);
    $selectuser->fetch(); 
    if ($count == 1) {
        // Store username in session
        $_SESSION['username'] = $loginusername; 
        $_SESSION['systemmessage'] = "Login succesfull. User: $loginusername  ";    
        $currentuser = $_SESSION['username'];
        header("Location: index.php");
        exit;
    } else { 
        $_SESSION['systemmessage'] = "Invalid username or password.";
        echo "Invalid username or password.";
    } 
    // Clean up
    $selectuser->close(); 
?>