<?php
$dbhost = "103.112.209.81";
$dbname = "callcenter";
$dbusername = "hoangdv";
$dbpassword = "hoangdv";

function conn($dbhost, $dbname, $dbusername, $dbpassword)
{
    try {
        $db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
        echo ' kết nối thành công';
        return $db;
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
        return null;
    }
}
function selectCC($conn)
{
    $sql = "SELECT name, currentcall FROM `customers` WHERE status='published' and currentcall > 0;";
    $obj_sql = $conn->prepare($sql);
    $obj_sql->execute();
    $result = $obj_sql->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}
// function controller
?>