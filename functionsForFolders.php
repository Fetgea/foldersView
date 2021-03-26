<?php
/**
 * Function returns array with folders ierarchy, folder name = array keys, and if folder have subdirectories 
 * then this will array corresponding to the folders key.
 *
 * @param string $path Path where recursive array building begins.
 * @return mixed array of folders and files in provided paths
 */
function getAllFolders($path = "")
{
    $foldersToTraverse = [];
    $fileDirlist = [];
    $path = trim($path);
    if ($path == "") {
        $currentPath = getcwd();
    } else {
        $currentPath = rtrim($path, "/");
    }
    if (!is_dir($currentPath)) {
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
            if (is_link($currentPath . "/" . $fileDir)) {
                $symlinks[$fileDir] = true;
            } elseif ($fileInfo === "unknown") {
                $symlinks[$fileDir] = true;
                $realPath = realpath($currentPath . "/" . $fileDir);
                //echo $realPath;
                if (!strpos($realPath, $currentPath)) {
                    unset($symlinks[$fileDir]);
                    unset ($foldersToTraverse[$key]);
                    unset ($fileDirlist[$key]);
                }
            }
            
        }
    }
    $fileDirlist = array_flip($fileDirlist);
    $returnResult = $fileDirlist;

    foreach ($foldersToTraverse as $folders) {
        $returnResult[basename($folders)] = getAllFolders($folders);
        if (empty($returnResult[basename($folders)])) {
            $returnResult[basename($folders)] = "folder";
        }
        if (isset($symlinks[basename($folders)])) {
            if (is_array($returnResult[basename($folders)])) {
                $returnResult[basename($folders)]['symlinkFolder'] = true;
                echo $folders;
            } else {
                $returnResult[basename($folders)] = ["symlinkFolder" => true];
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
            if (isset ($arrayValue["symlinkFolder"]) && $arrayValue["symlinkFolder"] === true) {
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