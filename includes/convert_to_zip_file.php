<?php
require_once '/var/www/html/send_email/vendor/autoload.php';
require_once '/var/www/html/send_email/config.php';

function ConvertToZipFile($excelFile, $zipFile, $randstring)
{
    $zip = new ZipArchive;
    $res = $zip->open($zipFile, ZipArchive::CREATE);
    if ($res === TRUE) {
        $zip->addFile($excelFile);

        // $randstring = generateRandomString();
        $zip->setPassword($randstring);
        $zip->setEncryptionName($excelFile, ZipArchive::EM_AES_128);
        $zip->close();
        return "File $zipFile exported successfully - Password: $randstring";
    } else {
        return 'Failed to create the zip file';
    }
}

function generateRandomString($length = 10)
{
    $characters = '0987654321qwertyuiopasdfghjklzxcvbnm1234567890oiuytrewqlkjhgfdsamnbvcxz123456789qazwsxedcrfvtgbyhnujmik';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
