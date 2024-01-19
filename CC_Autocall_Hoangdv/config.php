<?php
$dbhost = "103.112.209.81";
$dbname = "callcenter";
$dbusername = "hoangdv";
$dbpassword = "hoangdv";

// Establish a database connection
$conn = conn($dbhost, $dbname, $dbusername, $dbpassword);


function selectCC($conn)
{
    $sql = "SELECT code,company, currentcall FROM `customers` WHERE status='published' and currentcall > 10 ORDER BY company ASC;";
    $obj_sql = $conn->prepare($sql);
    $obj_sql->execute();
    $result = $obj_sql->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function totalcallAllCus($conn)
{
    $sql_count = "SELECT 
                SUM(totalcall) AS totalcall, 
                SUM(currentcall) AS totalcurrentcall, 
                SUM(CASE WHEN currentcall < 10 THEN currentcall ELSE 0 END) AS lowCC
                FROM `customers`;
                ";
    $obj_count = $conn->prepare($sql_count);
    $obj_count->execute();
    $totalcall = $obj_count->fetchAll(PDO::FETCH_ASSOC);
    return $totalcall;
}

function conn($dbhost, $dbname, $dbusername, $dbpassword)
{
    try {
        $db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8", $dbusername, $dbpassword);
        // echo ' kết nối thành công';
        return $db;
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
        return null;
    }
}
?>
