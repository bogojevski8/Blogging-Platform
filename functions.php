<?php

require "connect.php";

function check_login(){
// if you click on the home page it will redirect you to the login page 
    if(empty($_SESSION['info'])){
        
        header("Location:login.php");
        die;
    }
}