<?php

$txt1 = file_get_contents('assets/functions.php') . "\n";
$txt2 = file_get_contents('assets/classes.php') . "\n";
$txt3 = file_get_contents('assets/api.php') . "\n";
$txt4 = file_get_contents('assets/styles.css') . "\n";
$txt5 = file_get_contents('assets/vendorsc.js') . "\n";

$fp = fopen('installer.php', 'w');
$fs = fopen('index.php', 'r');

if (! $fp || ! $fs) {
    die('Could not create or open file');
} else {
    while (! feof($fs)) {
        $line = fgets($fs);
        preg_match("/(include|require|require_once|include_once)[ ]+'([\w\/\-_]+\.(php|css|js))'/", $line, $matches);
        if (! empty($matches)) {
            $fileName = $matches[2];
            $fileType = $matches[3];

            if ($fileType === 'php') {
                // php include
                fwrite($fp, file_get_contents($fileName) . "\n?>\n");
            } else {
                // js or css include
                fwrite($fp, file_get_contents($fileName) . "\n");
            }
        } else {
            fwrite($fp, $line);
        }
    }
}
