<?php
session_start();

$target_dir = "./assets/channelImages/";

// insert ImgId into data base
include_once "./login/connect.php"; 
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD,DB_NAME)
    OR die ('Could not connect to MySQL: '.mysql_error());

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$channelId = mysqli_real_escape_string($conn,$_POST['channel']);
$message = mysqli_real_escape_string($conn,$_POST['message']);
echo $channelId;
$user = $_SESSION['email'];
$sql = "INSERT INTO `channel_messages` VALUES(DEFAULT,'$channelId','$user','$message',DEFAULT,'3',CURRENT_TIMESTAMP)";
        if (mysqli_query($conn, $sql)) {
            
            // header('Location: ../index.php?channel='.$channel_id.'#scrollBottom');
            // exit;
        }else{
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
$lastIdQuery  = "SELECT * FROM `channel_messages` ORDER BY `channel_messages`.`cmessage_id` DESC LIMIT 1";
$lastIdResult = mysqli_query($conn,$lastIdQuery);
$lastId = mysqli_fetch_assoc($lastIdResult);
$lastId =$lastId['cmessage_id'];

$target_file = $target_dir .$lastId.'.png';
echo"file name:". $target_file;
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
        
    }
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;

}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
    $deleteImg = "DELETE FROM channel_messages where cmessage_id='$lastId'";
    mysqli_query($conn,$deleteImg);
  
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        header('location: index.php?channel='.$channelId);
    } else {
        
        echo "Sorry, there was an error uploading your file.";
    }
}
?>