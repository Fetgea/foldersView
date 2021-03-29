<?php

/**
 * Function returns array with folders ierarchy, folder name = array keys, and if folder have subdirectories 
 * then this will array corresponding to the folders key.
 *
 * @param string $path Path where recursive array building begins
 * @return array array of folders and files in provided paths 
 */
function getAllFoldersWrapper($path)
{
    $result = getAllFolders($path, $path);
    return $result;
}

/**
 * Function returns array with folders ierarchy, folder name = array keys, and if folder have subdirectories 
 * then this will array corresponding to the folders key.
 * @param string $rootPath root path of search. For symlink folders filtering
 * @param string $path Path where recursive array building begins.
 * @return mixed array of folders and files in provided paths
 */

function getAllFolders($rootPath, $path = "")
{
    $directory = dirname(__FILE__);
    ini_set("open_basedir", $directory);
    $foldersToTraverse = [];
    $fileDirlist = [];
    $path = trim($path);
    if ($path == "") {
        $currentPath = getcwd();
    } else {
        $currentPath = rtrim($path, "/");
    }
    if (!@is_dir($currentPath)) {
        return false;
    }
    $symlinks = [];
    $fileDirlist = @scandir($currentPath, 1);
    if ($fileDirlist == false) {
        return;
    }
    $fileDirlist = array_diff($fileDirlist, [".", ".."]);
    foreach ($fileDirlist as $key => &$fileDir) {
        if (is_dir($currentPath . "/" . $fileDir)) {
            $foldersToTraverse[$key] = $currentPath . "/" . $fileDir;
            $fileInfo = @filetype($currentPath . "/" . $fileDir);
            if (is_link($currentPath . "/" . $fileDir) || $fileInfo === "unknown") {
                $symlinks[$fileDir] = true;
                $realPath = realpath($currentPath . "/" . $fileDir);
                $realPathToLower = strtolower(str_replace('/', DIRECTORY_SEPARATOR, $realPath));
                $rootPathToLower = strtolower($rootPath);
                if (strpos($realPathToLower, $rootPathToLower) === false) {
                    unset($foldersToTraverse[$key]);
                }
            }
        }
    }
    $fileDirlist = array_flip($fileDirlist);
    $returnResult = $fileDirlist;

    foreach ($foldersToTraverse as $key => $folders) {
        $returnResult[basename($folders)] = getAllFolders($rootPath,$folders);
        if (empty($returnResult[basename($folders)])) {
            $returnResult[basename($folders)] = "folder";
        }
        if (!empty($symlinks)) {
            foreach ($symlinks as $key => $symlink) {
                if (isset($returnResult[$key])) {
                    $returnResult[$key] = ["symlinkFolder" => true];
                } 
            }
        }
    }
    return $returnResult;
}
/**
 * Function rearranges array returned by function getAllFolders (add <div> with classes before every element
 * -> item before every element, parent_folder before every folder (empty included), symlink_folder before 
 * every folder with symlink);
 *
 * @param array $array Array returned by getAllFolders function;
 * @return void returns array inserted in HTML markup.
 */
function rearrangeArray($array) 
{
    $returnValue = "<div class='item'> ";
    foreach ($array as $key => $arrayValue) {
        if (is_array($arrayValue)) {
            if (isset($arrayValue["symlinkFolder"]) && $arrayValue["symlinkFolder"] === true) {
                unset($arrayValue["symlinkFolder"]);
                $returnValue .= "<div class='item parent_folder symlink_folder'>" . $key;
            } else {
                $returnValue .= "<div class='item parent_folder'>" . $key;
            }
            $returnValue .= rearrangeArray($arrayValue);
            $returnValue .= "</div>";
        } else {
            if ($arrayValue === "folder") {
                $returnValue .= "<div class='item parent_folder'>" . $key . "</div>";
            } else {
                $returnValue .= "<div class='item file'>" . $key . "</div>";
            }
        }
    }
    $returnValue .= "</div>";
    return $returnValue;
}