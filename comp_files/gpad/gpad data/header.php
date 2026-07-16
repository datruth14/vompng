<?php  
require_once __DIR__ . '/Database.php';

require "saveData.php";


//check if user session does not exist then create it
if(!isset($_SESSION['users'])){
   echo quick_alert3("Please Log In","?s=signIn&&screen=gpad");
}

//if user session exist
else{
     //echo "<script>showAlert('".$_SESSION['userId']."');</script>";
     
      //session_destroy();
      //store user data to session
       $userId = $_SESSION['users'];
       //getting user GPC balance from data from database
            $userR = "SELECT * FROM users WHERE user_key='$userId'";
            $userRes = mysqli_query($conn, $userR);
            while($rows=$userRes->fetch_assoc()){
                $userUsername = $rows['full_name'];
                $user_balance = $rows['gpc_balance'];
            }
            
      ?>
      
        <div id="user" class="nav-bar fixed-top">
            <span><i class="fas fa-user"></i> <?php echo ucwords($userUsername); ?> </span>
            <span style="float:right;"> <i class="fa fa-gamepad"></i> <?php echo number_format($user_balance);?></span>
        
        </div>
      
      <?php
}

?>
