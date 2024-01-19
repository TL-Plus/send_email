<?php
require_once 'config.php';

function index($conn)
{
    $result = selectCC($conn);

 
    $totalcalls = totalcallAllCus($conn);

    // Mặc định giá trị nếu không có 'totalcurrentcall'
    $currentcall = 0;
    // var_dump($totalcalls);die;
    foreach ($totalcalls as $call) {
        $totalcall = $totalcalls[0]['totalcall'];
        $totalcurrentcall = isset($totalcalls[0]['totalcurrentcall']) ? $totalcalls[0]['totalcurrentcall'] : 0;
        $lowCC = $totalcalls[0]['lowCC'];
    }
    // var_dump($totalcalls);die;
    $dataResult = [];

    foreach ($result as $resultData) {
        $code = $resultData['code'];
        $company = $resultData['company'];
        $currentcall = $resultData['currentcall'];

        $dataResult[] = [
            'code' => $code,
            'company' => $company,
            'currentcall' => $currentcall,
            'totalcurrentcall' => $totalcurrentcall,
            'totalcall' => $totalcall,
            'lowCC' => $lowCC,
        ];
        
    }
    $dataTotal[]=[
        'totalcurrentcall' => $totalcurrentcall,
        'totalcall' => $totalcall,
    ];
    

    

    return $dataResult;

    // return render('index.php', [
    //     'results' => $resultList, // Sử dụng mảng chứa tất cả các giá trị
    //     'currentcall' => $currentcall,
    //     'totalcall' => $totalcall,
    // ]);
}

function dd($data){
    echo '<pre>';
    print_r($data);die;
    echo '</pre>';
}
?>
