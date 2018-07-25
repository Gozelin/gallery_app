<?php

include("../class/Gallery.class.php");

cGallery::$verbose = true;

if (!isset($_POST) || !is_array($_POST)) { 
    exit(0);
}

if (isset($_POST["data"]) && isset($_POST["action"])) {
    $json = NULL;
    if(cJsonHelper::isJson($_POST["data"]))
        $json = $_POST["data"];
    $g = new cGallery($json);
    switch ($_POST["action"]) {
        case "add":
        echo "added";
            $g->insertJson();
            break;
        case "modif":
            echo "modified";
            $g->updateJson();
            break;
        case "suprr":
        echo "suppressed";
            $g->deleteJson($_POST["data"]["id"]);
            break;
        default:
            exit(0);
            break;
    }
}

?>