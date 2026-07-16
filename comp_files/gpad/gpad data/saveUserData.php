<?php
// Check if the form is submitted
if (isset($_POST['saveData'])) {
// Get the values from the form
    $userId = $_SESSION['users'];
    $scoreValueData = (int)$_POST['scoreValueData']; // Score as integer
    
    //checking if user has reach the required target
    if($scoreValueData >= 1000){
            // update score where userid = userid
            $sql = "SELECT * FROM users WHERE user_key = '$userId'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $existingScore = $row['gpc_balance'];
            $newScore = $existingScore + $scoreValueData;  // Add the new score
            
            // Update the score in the database
            $updateSql = "UPDATE users SET gpc_balance = '$newScore' WHERE user_key = '$userId'";
            $res = mysqli_query($conn, $updateSql);
            
            if ($res) {
                  echo quick_alert3('Added '.$scoreValueData.' GPC To Your Earnings.','?s=gpad&&game=colorswipe');
            } else {
                    echo "Error updating score: " . $conn->error;
            }
            
    }else{
        echo quick_alert3('Sorry, the minimum GPC to claim is from 1000 and above','?s=gpad&&game=colorswipe');
    }       
}
    
?>
