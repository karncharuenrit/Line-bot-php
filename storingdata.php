<?php
include('./index.php');
   if(isset($_POST['push'])){
     $messages = $_POST['messages'];

    $data = [
       'messages' => $messages
    ];
    $ref = "contact from data";
    $pushData = $database->$getReference($ref)->push($data);

   }