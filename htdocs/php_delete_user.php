 <?php 
$usernameToDelete = $_POST["deleteuser"];
$_SESSION["systemmessage"] = " ";

$getuserrole = $connectiontosqldbusrs->prepare("select role from user_table WHERE user_name = ?");
$getuserrole->bind_param("s", $usernameToDelete);
$getuserrole->execute();  
$getuserrole->bind_result($userrole);
$getuserrole->fetch(); 
$getuserrole->close(); 

//Only user with role normal can be deleted
if($userrole == 'NORMAL'){

//Ratings are deleted only if connection to SQL databse is open
 if ($sqldbon == 1){
    $deleteratingQuery = "delete from band_rating where user_id = (select user_id from user_table where user_name LIKE ?)";
    $stmtdeleterating = $connectiontosqldb->prepare($deleteratingQuery);
    $stmtdeleterating->bind_param("s", $usernameToDelete);
    }else{
    $_SESSION["systemmessage"] .= " ";
    }

    //Deleting user
    $deleteQuery = "DELETE FROM user_table WHERE user_name = ? and role like 'NORMAL'";
    $stmt = $connectiontosqldbusrs->prepare($deleteQuery);
 
    $stmt->bind_param("s", $usernameToDelete);


    if ($_SESSION["username"] != $usernameToDelete ) {
       
       if($sqldbon == 1){
        if ($stmtdeleterating->execute() ) {
            $_SESSION["systemmessage"] .= "Ratings by user: $usernameToDelete deleted.";
        }
        }else{
            $_SESSION["systemmessage"] .= "SQL database ratings not deleted. Connection not open.";
        } 
        if ($stmt->execute()) {
            $_SESSION["systemmessage"] .= "<br>User $usernameToDelete deleted.";
        }
    } else {
        $_SESSION["systemmessage"] = "You cannot delete the current logged-in user: $usernameToDelete.";
    }
    header("Location: index.php");
    exit();
     if ($sqldbon == 1){ $stmtdeleterating->close();}
    $stmt->close();
    }
    else if($userrole == 'SUPERUSER'){
        $_SESSION["systemmessage"] .= "You cannot delete superuser.";
 }   
?>