<?php

include 'src/upload.php';


$upload = new Upload;

$setup = array(
    "file" => ['foto_1', 'foto_2', 'foto_3', 'foto_4'],
    "path" => "filemanager",
    "filename" => true
);

$upload->init($setup)
        ->start();
