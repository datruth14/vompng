<?php 



        
//checking if the user data was submited from form
if(isset($_POST['userId'])){
        
        // Get the values from the form
        $fullName = $conn->real_escape_string($_POST['fullName']);
        $userId = $conn->real_escape_string($_POST['userId']);
        $userUsername = $conn->real_escape_string($_POST['userUsername']);
        $level = 1;
        $wallet_address = "GPC".md5(time());
        $date_joined = date("d/m/Y");
    
        //query user data from database
        $sql = "SELECT * FROM users WHERE user_key = '$userId'";
        $result = $conn->query($sql);
        
        //checking if user exist 
        if ($result->num_rows > 0) {
            //store user data to session
            $_SESSION['userId'] = $userId;
            $_SESSION['username'] = $fullName;
            
            echo "<script>window.location.href=''</script>";
        }

    }
    
