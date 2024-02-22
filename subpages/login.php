<?php
require 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = $_POST['member_email'];
  $result = $conn->query("SELECT * FROM members WHERE member_email = '$email'");
  $row = $result->fetch_assoc();
  if($result -> num_rows == 1){
    if(password_verify($_POST['member_password'], $row['member_password'])){
      header("Location: Dashboard.php");
    }else{
      echo "wrong";
    }

  }else{
    echo "email wrong";
  }
  $conn->close();
}
