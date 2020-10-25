#!/usr/bin/php
<?php
$fileErrors = 0;

function makephar ($dir, $name, $default)
{
    @unlink($name);
    $phar = new Phar($name);
    if (! $phar)
        exit(
                "[Fatal Error] Error while making phar. Please ensure that phar.readonly is disabled in php.ini.\n");
    $phar->buildFromDirectory($dir);
    $phar->setDefaultStub($default, null);
}

function checkEverything ()
{
    $errCount = 0;
    if (strstr(PHP_OS, "WIN")) {
        echo "[  Fatal ] Operating System is Windows.\n";
        exit(1);
    }
    $phpver = substr(phpversion(), 0, 3);
    if ($phpver < 5.3) {
        echo "[  Fatal ] PHP version {$phpver} < 5.3.\n";
        exit(1);
    }
    if (! extension_loaded("posix")) {
        echo "[  Error ] PHP Module Posix could not found.\n";
        $errCount ++;
    }
    if (! extension_loaded("pcntl")) {
        echo "[  Error ] PHP Module Pcntl could not found.\n";
        $errCount ++;
    }
    if (! extension_loaded("Phar")) {
        echo "[  Error ] PHP Module Phar could not found.\n";
        $errCount ++;
    }
    if (! extension_loaded("sockets")) {
        echo "[  Error ] PHP Module Sockets could not found.\n";
        $errCount ++;
    }
    if (! exec("git")) {
        echo "[  Error ] \"git\" command is not available.\n";
        $errCount ++;
    }
    $file = fopen("./.write", "a");
    if (! fputs($file, ".")) {
        echo "[  Error ] Writing failed.            \n";
        $errCount ++;
    }
    fclose($file);
    if (! file_get_contents("./.write")) {
        echo "[  Error ] Reading failed.            \n";
        $errCount ++;
    }
    unlink("./.write");
    // finish
    if ($errCount == 0) {
        echo "\r[   OK   ] Finished with no errors! Continue.\n";
    } else {
        echo "\r[  Error ] Finished with {$errCount} errors. Please fix them and try again.\n";
        exit(1);
    }
}

function syntaxCheck ($src = ".")
{
    global $fileErrors;
    $dir = opendir($src);
    while (($file = readdir($dir)) !== false) {
        if (($file != '.') && ($file != '..')) {
            $name = explode(".", $file);
            if (is_dir($src . '/' . $file)) {
                syntaxCheck($src . '/' . $file);
            } else {
                if (end($name) !== "php")
                    continue;
                $result = exec("php -l " . $src . '/' . $file);
                if (strchr($result, "Errors parsing")) {
                    $fileErrors ++;
                    echo "[Error] Parsing error.\n";
                }
                echo $result . "\n";
            }
        }
    }
    closedir($dir);
}

function copydir ($src, $dst)
{
    // echo "Copying dir\t{$src}...\n";
    $dir = opendir($src);
    @mkdir($dst);
    while (($file = readdir($dir)) !== false) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copydir($src . '/' . $file, $dst . '/' . $file);
            } else {
                // echo "Copying file\t" . $src . '/' . $file . "\n";
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function copyfile ($src, $dst)
{
    // echo "Copying file\t{$src}\n";
    copy($src, $dst);
    // echo "\n";
}

function deldir ($dir)
{
    // echo "Deleting dir\t{$dir}\n";
    if (! is_dir($dir))
        return;
    if (count(scandir($dir)) == 2) {
        rmdir($dir);
        return;
    }
    $dh = opendir($dir);
    while ($file = readdir($dh)) {
        if ($file != '.' && $file != '..') {
            $fullpath = $dir . "/" . $file;
            if (is_file($fullpath)) {
                // echo "Deleting file\t{$fullpath}\n";
                unlink($fullpath);
            }
            if (is_dir($fullpath)) {
                if (count(scandir($fullpath)) == 2) {
                    rmdir($fullpath);
                } else {
                    deldir($fullpath);
                }
            }
        }
    }
    
    closedir($dh);
    if (rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}
$usage = "Usage: php build.php <command> <args>\nCommands:\nbuild\tBuild this project.\n\tArgs:\n\tnormal\tNormal build. (Delete .cached files and download it.)\n\tcached\tUse .cached files to build.\n";
if ($argv[1] == "build") {
    if (! $argv[2]) {
        echo $usage;
        exit(1);
    }
    echo "Checking everything...\n";
    checkEverything();
    switch ($argv[2]) {
        case "normal":
            @mkdir(".cache");
            echo "Downloading workerman...\n";
            deldir("./.cache");
            system("git clone https://github.com/walkor/Workerman.git .cache");
            echo "Done.\n";
        case "cached":
            echo "Checking if there are some syntax errors.\n";
            syntaxCheck();
            if ($fileErrors > 0)
                exit(2);
            echo "Deleting the last build files...\n";
            @mkdir(".tmp");
            deldir("./.tmp");
            echo "Building...\n";
            echo "Building server side...\n";
            @mkdir(".tmp");
            echo "Copying files...\n";
            copydir("./.cache", "./.tmp");
            copydir("./main/server-side", "./.tmp");
            echo "Making phar file...\n";
            @mkdir("target");
            makephar(__DIR__ . "/.tmp", "./target/GarageProxyServer.phar",
                    "launcher.php");
            echo "Done.\n";
            echo "Building client side...\n";
            deldir("./.tmp");
            @mkdir(".tmp");
            echo "Copying files...\n";
            copydir("./.cache", "./.tmp");
            copydir("./main/client-side", "./tmp");
            echo "Making phar file...\n";
            makephar(__DIR__ . "/.tmp", "./target/GarageProxyClient.phar",
                    "start.php");
            deldir("./.tmp");
            echo "Done.\n";
            @mkdir("test");
            exit(0);
    }
}
echo $usage;
exit(1);
