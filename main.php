<?php

    include("include/database.class.php");
    $DBClass    =   new database();
    //$DBClass->SyncMembers();
    //$DBClass->Schedule('Email related with features', 'segone', 'active');
    $DBClass->Schedule('Benefits of time tracking software', 'segone', 'inactive');
    //$DBClass->Schedule('Benefits of time tracking software', 'segtwo', 'inactive');

?>