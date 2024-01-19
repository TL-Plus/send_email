<?php 
include 'controllers.php';

$showData = index($conn);

if (!empty($showData)) {
    // Có giá trị
    echo '<script>';
    echo 'var chartData = [';

    foreach ($showData as $resultData) {
        // Kiểm tra xem dữ liệu tồn tại trước khi sử dụng
        if (isset($resultData['code'], $resultData['company'], $resultData['currentcall'])) {
            $label = "{$resultData['code']} - {$resultData['company']}";
            echo "{ y: {$resultData['currentcall']}, label: '{$label}' },";
        } else {
            // Bỏ qua nếu dữ liệu thiếu
            continue;
        }
    }

    // Thêm cột mới cho khách hàng có currentcall < 10
    echo "{ y: {$showData[0]['lowCC']}, label: 'Khách hàng khác' },";

    echo '];';
    echo '</script>';
} else {
    // Không có giá trị
    echo '<script>';
    echo 'var chartData = [];';  // Trường hợp mảng rỗng
    echo '</script>';
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <script type="text/javascript" src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <script type="text/javascript">
        window.onload = function () {
            var chart = new CanvasJS.Chart("chartContainer", {
                theme: "light3",
                animationEnabled: false, // change to true / false
                title: {
                    text: "AUTOCALL DIGINEXT CCU"
                },
                data: [
                    {
                        type: "bar",
                        dataPoints: chartData
                    },
                ]
            });

            chart.render();
        }
    </script>
</head>
<body>
    <center>
        <div id="chartContainer" style="height:400px; width: 1000px;"></div>
    </center>
    
    <div style="width:100%;">
        <p style="font-size: 20px;padding-left: 20px ;">
            Tổng CCU hiện tại: 
            <span style="color: red;"><?php echo $showData[0]['totalcurrentcall']; ?></span>
            <span style="padding-left: 30% ;">
                Tổng CCU khách hàng đăng ký: 
                <span style="color: red ;"><?php echo $showData[0]['totalcall']; ?></span>
            </span>
        </p>
    </div>
</body>
</html>
