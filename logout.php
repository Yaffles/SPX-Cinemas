<?php
    session_start();
    require("model/member.php");
    $member = unserialize($_SESSION["member"]);
    $retCode = $member->logout();

    IF ($retCode==0) {
        header("Location:login.php");
        exit();
    }
?>