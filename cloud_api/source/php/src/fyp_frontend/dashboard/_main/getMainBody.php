<?php

function getMainBody($str_page){
    $i = $str_page;
    switch ($i){
        case 0:
            include_once('index.inc.php');
            return getDashboard();
            break;
        case 1:
            include_once('Missions.inc.php');
            return getMissionsPage();
            break;
        case 2:
            include_once('map.inc.php');
            return getMapPage();
            break;
        case 3:
            include_once('view_mission.inc.php');
            return getMission();
            break;
        case 4:
            include_once('profile.inc.php');
            return getProfilePage();
            break;
    }
    
}