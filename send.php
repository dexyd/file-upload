<?php 
$uploadDir = 'uploads/'; 
$response = array( 
    'status' => 0, 
    'message' => 'Form submission failed, please try again.' 
);

    // Get submitted form data on submission
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $emailSanitize = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $email = filter_var($emailSanitize, FILTER_VALIDATE_EMAIL);
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
    $gender = filter_var($_POST['gender'], FILTER_SANITIZE_STRING);
    $education = filter_var($_POST['education'], FILTER_SANITIZE_STRING);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
     
    // Check if submitted data is not empty 
    if(!(empty($name) && empty($email) && empty($phone) && empty($gender) && empty($education) && empty($message))){ 
        // Validate email 
        if($email === false){
            $response['message'] = 'Please enter a valid email.'; 
        }else{ 
            $uploadStatus = 1; 
             
            // Upload file 
            $uploadedFile = ''; 
            if(!empty($_FILES["file"]["name"])){ 
                 
                // File path config 
                $fileName = basename($_FILES["file"]["name"]); 
                $targetFilePath = $uploadDir . $fileName; 
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
                 
                // Allow certain file formats 
                $allowTypes = array('pdf', 'doc', 'docx', 'jpg', 'png', 'jpeg'); 
                if(in_array($fileType, $allowTypes)){ 
                    // Upload file to the server 
                    if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)){ 
                        $uploadedFile = $fileName; 
                    }else{ 
                        $uploadStatus = 0;
                        $response['message'] = 'Sorry, there was an error uploading your file.'; 
                    } 
                }else{ 
                    $uploadStatus = 0;
                    $response['message'] = 'Sorry, only PDF, DOC, JPG, JPEG, & PNG files are allowed for upload.'; 
                } 
            } 
             
            if($uploadStatus == 1){ 
                
            // Database configuration 
            $dbHost     = "localhost"; 
            $dbUsername = "root"; 
            $dbPassword = ""; 
            $dbName     = "database_name";
            
            // Create database connection 
            $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName); 
            
            // Check connection 
            if ($conn->connect_error) { 
                die("Connection failed: " . $conn->connect_error); 
            } 
                 
                // Insert form data into database 
                $insert = $conn->query("INSERT INTO table_name (names, emails, phones, genders, education, files, messages) VALUES ('$name','$email','$phone','$gender','$education','$uploadedFile','$message')");
                 
                if($insert){ 
                    $response['status'] = 1; 
                    $response['message'] = 'Form data submitted successfully!'; 
                } 
            } 
        } 
    }else{ 
         $response['message'] = 'Please fill all the mandatory fields (name and email).'; 
    }
 
// Return response 
echo json_encode($response);
