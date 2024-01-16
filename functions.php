<?php

function isLoggedIn()
{
    return true;//isset($_SESSION['id']);
}

if (!function_exists('str_starts_with')) {
    function str_starts_with($str, $start) {
      return (@substr_compare($str, $start, 0, strlen($start))==0);
    }
  }

function getRelativePath()
{
    $folder_depth = substr_count(dirname($_SERVER['DOCUMENT_URI']), "/");

    if ($folder_depth == false)
        $folder_depth = 1;

    return str_repeat("../", $folder_depth - 2);
}

function getHomeURL()
{
    $url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $url .= $_SERVER['SERVER_NAME'];
    $url .= $_SERVER['REQUEST_URI'];

    return dirname($url);
}

function getRootDirectory()
{
    $dir = basename(__DIR__);
    $levels = 1;
    if ($dir === 'osd' || $dir === 'trip-management') {
        $levels = 2;
    } else if ($dir === 'backend') {
        $levels = 3;
    }

    return dirname(__FILE__, $levels);
}

function findimages($id) {
    $files = scandir($_SERVER['DOCUMENT_ROOT'] . '/projects/recipe-book/assets/images/recipes/');
    $images = array();
    foreach ($files as $file) {
        if (str_starts_with($file, $id . '-') || strcmp($file, $id . '.png') === 0) {
            array_push($images, $file);
        }
    }
    return $images;
}