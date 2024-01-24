<?php
require_once('conn.php');



$date = isset($_GET['date']) ? $_GET['date'] : '';

    $selectedDate = new DateTime($date);
     if ($selectedDate->format('m') >= 4) {
        $financialYearStart = date('Y-04-01', strtotime($date));
    } else {
        $financialYearStart = date('Y-04-01', strtotime('-1 year', strtotime($date)));
    }

    $financialYearEnd = date('Y-03-30', strtotime('+1 year', strtotime($financialYearStart)));

    $currentDate = new DateTime($financialYearStart);
    $monthYears = array();

    while ($currentDate->format('Y-m-d') <= $financialYearEnd) {
        $monthYears[] = $currentDate->format('Y-m');
        $currentDate->modify('+1 month');
    }


    $record = $models->execute_kw($db, $uid, $password,
    'simrp.invoice', 'test',
    array(array())
);

$getwostatus = json_decode($record, true);

$monthWiseTotal = array_fill_keys($monthYears, 0);

$invoice_totalSum1 = 0;
foreach ($getwostatus as $invoice) {
    $month = date('Y-m', strtotime($invoice['invdate']));

    if (isset($monthWiseTotal[$month])) {
        $monthWiseTotal[$month] += $invoice['invamt'];

        $invoice_totalSum1 += $invoice['invamt'];
    }
}


$monthWiseTotal = array_combine(
    array_map(function($key) {
        return date('M Y', strtotime($key));
    }, array_keys($monthWiseTotal)),
    $monthWiseTotal
);

### filter data url date wise data 
$date = isset($_GET['date']) ? $_GET['date'] : '';

$dateFormatted = date('M Y', strtotime($date));

$filteredMonthWiseTotal = array_filter($monthWiseTotal, function ($key) use ($dateFormatted) {
    return $key == $dateFormatted;
}, ARRAY_FILTER_USE_KEY);

$invoice_totalSum = reset($filteredMonthWiseTotal);


$filteredTargetArray_invoice = array_filter($monthWiseTotal, function ($key) use ($dateFormatted) {
    return strtotime($key) <= strtotime($dateFormatted);
}, ARRAY_FILTER_USE_KEY);
$sumUpToDate_invoice = array_sum($filteredTargetArray_invoice);














########   credit table ##############

$credit = $models->execute_kw($db, $uid, $password,
'simrp.credit', 'dcredit',
array(array())
);
$creditstatus = json_decode($credit, true);
$monthWiseTotalcredit = array_fill_keys($monthYears, 0);

$credit_totalSum1 = 0;
foreach ($creditstatus as $invoice) {
    $month = date('Y-m', strtotime($invoice['cndate']));

    if (isset($monthWiseTotalcredit[$month])) {
        $monthWiseTotalcredit[$month] += $invoice['basicamount'];

        $credit_totalSum1 += $invoice['basicamount'];
    }
}


$monthWiseTotalcredit = array_combine(
    array_map(function($key) {
        return date('M Y', strtotime($key));
    }, array_keys($monthWiseTotalcredit)),
    $monthWiseTotalcredit
);

### filter data url date wise data 
$date = isset($_GET['date']) ? $_GET['date'] : '';

$dateFormatted = date('M Y', strtotime($date));

$filteredMonthWiseTotal_credit = array_filter($monthWiseTotalcredit, function ($key) use ($dateFormatted) {
    return $key == $dateFormatted;
}, ARRAY_FILTER_USE_KEY);

$credit_totalSum = reset($filteredMonthWiseTotal_credit);

$filteredTargetArray_credit = array_filter($monthWiseTotalcredit, function ($key) use ($dateFormatted) {
    return strtotime($key) <= strtotime($dateFormatted);
}, ARRAY_FILTER_USE_KEY);
$sumUpToDate_credit = array_sum($filteredTargetArray_credit);










########       debit table    #####################

$debit = $models->execute_kw($db, $uid, $password,
'simrp.debit', 'rcdebit',
array(array())
);
$debittstatus = json_decode($debit, true);


$monthWiseTotaldebit = array_fill_keys($monthYears, 0);

$debit_totalSum1 = 0;
// month wise amt add start
foreach ($debittstatus as $invoice) {
    $month = date('Y-m', strtotime($invoice['rdate']));

    // Check if the month is within the financial year range
    if (isset($monthWiseTotaldebit[$month])) {
        $monthWiseTotaldebit[$month] += $invoice['basicamount'];

        $debit_totalSum1 += $invoice['basicamount'];
    }
}

$monthWiseTotaldebit = array_combine(
    array_map(function($key) {
        return date('M Y', strtotime($key));
    }, array_keys($monthWiseTotaldebit)),
    $monthWiseTotaldebit
);

### filter data url date wise data 
$date = isset($_GET['date']) ? $_GET['date'] : '';

$dateFormatted = date('M Y', strtotime($date));

$filteredMonthWiseTotal_debit = array_filter($monthWiseTotaldebit, function ($key) use ($dateFormatted) {
    return $key == $dateFormatted;
}, ARRAY_FILTER_USE_KEY);

$debit_totalSum = reset($filteredMonthWiseTotal_debit);

$filteredTargetArray_debit = array_filter($monthWiseTotaldebit, function ($key) use ($dateFormatted) {
    return strtotime($key) <= strtotime($dateFormatted);
}, ARRAY_FILTER_USE_KEY);

$sumUpToDate_debit = array_sum($filteredTargetArray_debit);

// only month in after url date start
$keys11 = array_keys($filteredTargetArray_debit);
$monthsOnly = array_map(function ($element) {
    return date('M Y', strtotime($element));
}, $keys11);

$resultString = implode('', $monthsOnly);

// only month in after url date start

$combinedArray = array();

foreach ($monthYears as $monthYear) {
    $monthLabel = date('M Y', strtotime($monthYear));

    $debitValue = isset($monthWiseTotaldebit[$monthLabel]) ? $monthWiseTotaldebit[$monthLabel] : 0;
    $creditValue = isset($monthWiseTotalcredit[$monthLabel]) ? $monthWiseTotalcredit[$monthLabel] : 0;
    $invoiceValue = isset($monthWiseTotal[$monthLabel]) ? $monthWiseTotal[$monthLabel] : 0;

    if (in_array($monthLabel, $monthsOnly)) {
        $combinedArray[$monthLabel] = $creditValue + $invoiceValue - $debitValue; 
    } else {
        $combinedArray[$monthLabel] = 0; 
    }
}





$total_achive1 = $sumUpToDate_invoice + $sumUpToDate_credit - $sumUpToDate_debit;
########################################    target array ###################################################################


$target_Array = array(
    'Apr 2021' => 2500000,
    'May 2021' => 2500000,
    'Jun 2021' => 2500000,
    'Jul 2021' => 2000000,
    'Aug 2021' => 2500000,
    'Sep 2021' => 2500000,
    'Oct 2021' => 2500000,
    'Nov 2021' => 2500000,
    'Dec 2021' => 2500000,
    'Jan 2022' => 2500000,
    'Feb 2022' => 2500000,
    'Mar 2022' => 2500000
);

### filter data url date wise data 
$date = isset($_GET['date']) ? $_GET['date'] : '';

$dateFormatted = date('M Y', strtotime($date));

$filteredMonthWiseTotal_target = array_filter($target_Array, function ($key) use ($dateFormatted) {
    return $key == $dateFormatted;
}, ARRAY_FILTER_USE_KEY);

$filteredTargetArray = array_filter($target_Array, function ($key) use ($dateFormatted) {
    return strtotime($key) <= strtotime($dateFormatted);
}, ARRAY_FILTER_USE_KEY);
$sumUpToDate1 = array_sum($filteredTargetArray);

$Target = reset($filteredMonthWiseTotal_target);
$totalTarget1 = array_sum($target_Array);


// $yellow_jia1 = ($total_achive1 / $totalTarget1) * 100 ;
// $green_jia1 = ($total_achive1 / $sumUpToDate1) * 100 ;

$yellow_jia1 = ($total_achive1 != 0 && $totalTarget1 != 0) ? round(($total_achive1 / $totalTarget1) * 100) . '%' : "0%";


$green_jia1 = ($total_achive1 != 0 && $sumUpToDate1 != 0) ? round(($total_achive1 / $sumUpToDate1) * 100) . '%' : "0%";




##  condition company name wise graph array     ###########
$company1 = isset($_GET['company']) ? $_GET['company'] : '';

$overallTarget_jia = (empty($Target)) ? 0 : $Target;

$overallTarget_shaha = 0;
if ($company1 == 'jia') {
   $jsArray = json_encode($combinedArray);
   $overallTarget = $overallTarget_jia;
   $totalTarget = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($totalTarget1));
   $sumUpToDate = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($sumUpToDate1));
   $total_achive = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($total_achive1));
   $yellow = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($yellow_jia1)). '%';
   $green = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($green_jia1)). '%';


} elseif ($company1 == 'shaha') {
    $jsArray = '';
    $overallTarget = $overallTarget_shaha;
    $totalTarget = 0;
    $sumUpToDate = 0;
    $total_achive = 0;
    $yellow = 0;
    $green = 0;

} elseif ($company1 == '') {
    $jsArray = json_encode($combinedArray);
    $overallTarget = $overallTarget_jia + $overallTarget_shaha;
    $totalTarget = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($totalTarget1));
    $sumUpToDate = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($sumUpToDate1));
    $total_achive = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($total_achive1));
    $yellow = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($yellow_jia1)). '%';
    $green = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($green_jia1)). '%';

} 
else{

}
?>




<!doctype html>
<html lang="en">
 
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/libs/css/style.css">



    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
   
    <title>  Dashboard </title>
</head>

<body>

        <div class=""  style="height: 100%; background-color: black;">
            <div class="dashboard-ecommerce" >
                <div class="container-fluid dashboard-content ">
                   
               
                

                    <div class="row">
       
                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12" style ="max-width: 25%;padding-right: 0.83vh;">
                            <div class="card border-3 border-top border-top-primary" style = "background:#90EEEE">
                                <div class="card-body" style ="height: 13.25vh;">
                                    
                                    <div class="metric-value d-inline-block">
                                          <p style="font-size: 5vh;  color: black; font-weight: bold;">Sales Report :</p>
                                    </div>
                                   
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12" style = "max-width: 50%;flex: 0 0 50%;padding-right: 0.83vh;padding-left: 0.83vh;">
                            <div class="card border-3 border-top border-top-primary" style = "background:#ff7400">
                                <div class="card-body" style ="height: 13.25vh;">
                                   
                                <?php
$dateParam1 = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$formattedDate = date('F Y', strtotime($dateParam1));
?>

                                    <div class="metric-value d-inline-block">
                                             <p style="font-size: 5vh;  color: white; font-weight: bold;">  <?php  echo $formattedDate;  ?></p>
                                        
                                    </div>
                                   
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12" style = "max-width: 25%;flex: 0 0 25%;padding-left: 0.83vh;">
                            <div class="card border-3 border-top border-top-primary" style = "background:#A9A9A9" >
                                <div class="card-body" style ="height: 13.25vh;">
                                    


                                    <?php
$company = isset($_GET['company']) ? $_GET['company'] : '';

// Set the display conditions based on the "company" parameter
$displayJia = ($company === 'jia' || $company === '');
$displayShaha = ($company === 'shaha' || $company === '');

$commonStyle = 'border: 0.33vh solid black;
padding: 2.5vh 6.83vh 2.5vh 6.83vh;
border-radius: 1.67vh;
margin-right: 1.67vh;';
$trueStyle = $commonStyle . ' background-color: green;  color: white;';
$falseStyle = $commonStyle . ' background-color: yellow;  color: black;';

// Output the HTML with dynamic styles
echo '<div class="metric-value d-inline-block" style="margin-top: 0.22vh;">';

// Display the "Jia" span
if ($displayJia && !$displayShaha) {
    echo '<span id="jia" style="' . $trueStyle . '">Jia</span>';
    echo '<span id="shaha" style="' . $falseStyle . '">Shaha</span>';
} 
// Display the "Shaha" span
elseif ($displayShaha && !$displayJia) {
    echo '<span id="jia" style="' . $falseStyle . '">Jia</span>';
    echo '<span id="shaha" style="' . $trueStyle . '">Shaha</span>';
    
} 
// Display both "Jia" and "Shaha" spans
elseif ($displayJia && $displayShaha) {
    echo '<span id="jia" style="' . $trueStyle . '">Jia</span>';
    echo '<span id="shaha" style="' . $trueStyle . '">Shaha</span>';
}

echo '</div>'; // Close the div tag
?>




                                   
                                </div>
                            </div>
                        </div>

                    </div>
























                    <style>
    .container {
        font-size: 3.33vh;
    }

    .header-text {
        font-weight: bold;
        margin: 3.33vh 0;
    }

    .box {
        border: 1px solid black;
        padding: 1.67vh;
        width: 75vh;
        margin: 0.33vh auto;
        border-radius: 1.67vh;
        background: linear-gradient(69.9deg, rgb(76, 79, 106) 3.2%, rgb(118, 124, 163) 97.6%);
    }

    .element {
        display: inline-block;
        width: 16.67vh;
        margin-left: 8.33vh;
    }
</style>





                  
                        <div class="row">
                            
                            <div class="col-xl-9 col-lg-12 col-md-6 col-sm-12 col-12" style ="padding-right: 0.83vh;">
                                <div class="card" style="height: 44.25vh;background: #71797E;" >
                                    
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table">
                                                 <!-- ======================start first =============================== -->

                                                 <div class=" " >

                                                    <div class="container">

 <?php
$company = isset($_GET['company']) ? $_GET['company'] : '';

if ($company == 'jia') {
    
    $net_invoice = $invoice_totalSum;
    $net_credit = $credit_totalSum;
    $net_debit = $debit_totalSum;
    $net_total = $net_invoice + $net_credit - $net_debit;
    $rounded_total = round($net_total);

    $target = $Target;
    $net_target =preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($target));
    $net_total =preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($net_invoice));
    $final_total =preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $rounded_total);

    $credit = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($net_credit));
    $debit = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($net_debit));
    $actual =  ($target != 0) ? round(($rounded_total / $target) * 100) . '%' : "100%";


} elseif ($company == 'shaha') {
    $net_invoice1 = 0;
    $net_credit1 = 0;
    $net_debit1 = 0;
    $net_total1 = $net_invoice1 + $net_credit1 - $net_debit1;
    $rounded_total1 = round($net_total1);

    $target1 = 0;
    $net_target =preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($target1));
    $net_total =preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $net_invoice1);
    $final_total =preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($rounded_total1));

    $credit = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($net_credit1));
    $debit = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($net_debit1));
    $actual = ($target1 != 0) ? round(($rounded_total1 / $target1) * 100) . '%' : "100%";

} elseif ($company == '') {
    $net_invoice2 = $invoice_totalSum + 0;
    $net_credit2 = $credit_totalSum + 0;
    $net_debit2 = $debit_totalSum + 0;
    $net_total2 = $net_invoice2 + $net_credit2 - $net_debit2;
    $rounded_total2 = round($net_total2);

    $target2 = $Target + 0 ;
    $net_target =preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($target2));
    $net_total =preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($net_invoice2));
    $final_total =preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($rounded_total2));

    $credit = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($net_credit2));
    $debit = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($net_debit2));
    $actual =  ($target2 != 0) ? round(($rounded_total2 / $target2) * 100) . '%' : "100%";

    
} else {
   
}




?>
                                                        <p class="header-text" style = "text-align: center;font-size: 10.33vh;color: white; "><?php echo $net_total ?></p>

                                                            <div>
                                                                <div class="element" style = "color: white;">
                                                                <p>Net  :</p>
                                                                    </div>
                                                                <div class="element">

                                                                <div class="box" style = "color: white;"> <?php echo $final_total ?> </div>
                                                                    </div>
                                                            </div>

                                                            <div>
                                                                <div class="element" style = "color: white;">
                                                                <p>Target  :</p>
                                                                    </div>
                                                                <div class="element" style = "color: white;">
                                                                <div class="box"> <?php echo $net_target ?></div>
                                                            </div>
                                                        </div>

                                                      </div>
                                                 </div>



                                                  <!-- =========================end first ========================== -->
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <style>
  .container2 {
    font-size: 3vh;
}

.header-text2 {
    font-weight: bold;
    margin: 3.33vh 0;
}

.box2 {
    border: 0.17vh solid black;
    padding: 1.67vh;
    width: 23.33vh;
    margin: 1vh auto;
    border-radius: 1.67vh;
    background: linear-gradient(69.9deg, rgb(76, 79, 106) 3.2%, rgb(118, 124, 163) 97.6%);
    color: white;
}

.element2 {
    color: white;
    display: inline-block;
    width: 13.33vh;
    margin-left: 1.67vh;
}

  </style>
                            <!-- ============================================================== -->
  
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12" style ="padding-right: 0.83vh; padding-left:6px;">
                                <div class="card" style="height: 44.25vh;background: #71797E;">
                                    
                                    <div class="card-body">
                                       
                                    <!-- ========================= second start ========================== -->
                                    <div class=" " >

                                            <div class="container2">
                                                    <div>
                                                        <div class="element2" >
                                                        <p>Debit  :</p>
                                                            </div>
                                                        <div class="element2">
                                                        <div class="box2"><?php echo $debit ?></div>
                                                            </div>
                                                    </div>

                                                    <div>
                                                        <div class="element2">
                                                        <p>Credit  :</p>
                                                            </div>
                                                        <div class="element2">
                                                        <div class="box2"><?php echo $credit ?></div>
                                                    </div>
                                                    </div>

                                                    <div>
                                                        <div class="element2">
                                                        <p>Actual :</p>
                                                            </div>
                                                        <div class="element2">
                                                        <div class="box2"><?php echo $actual ?></div>
                                                    </div>
                                                    </div>

                                            </div>
                                            </div>

                                     <!-- ========================= second start ========================== -->

                                    </div>
                                </div>
                            </div>
                            <!-- ============================================================== -->
                            
                        </div>
                       
                            
                           
                          










                        <div class="row">
                            
                            <div class="col-xl-9 col-lg-12 col-md-6 col-sm-12 col-12" style ="padding-right: 0.83vh;">
                                <div class="card"  style =" margin-bottom: 0px;height: 36.00vh; background:#2B4162;">
                                    
                                            
                                                 <!-- ======================start third  =============================== -->

                                                 <div class=" " style="">

                                                 <div id="chartdiv" style="width: 100%; height: 36.00vh;"></div>

                                                 <script>
    // Add license for the free version
    am4core.addLicense("CH1234567890");

    // Convert PHP array to JavaScript array
    var combinedArray = <?php echo $jsArray;  ?>;
    var overallTarget = <?php echo $overallTarget; ?>;
    // Create chart instance
    var chart = am4core.create("chartdiv", am4charts.XYChart);

    // Add data
    chart.data = Object.keys(combinedArray).map(function(month) {
        return { month: month, sales: combinedArray[month] };
    });

    // Modify chart properties
    chart.paddingRight = 20;
    chart.fontFamily = "Arial, sans-serif";

    // Create axes
    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "month";
    categoryAxis.renderer.grid.template.location = 0;
    categoryAxis.renderer.minGridDistance = 20;
    categoryAxis.renderer.labels.template.fontSize = 10; // Font size
    categoryAxis.renderer.labels.template.fill = am4core.color("white"); // Orange color for x-axis month color
    categoryAxis.renderer.labels.template.fontWeight = "bold"; // Bold text for x-axis

    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.renderer.labels.template.fontSize = 10; // Font size
    valueAxis.renderer.labels.template.fill = am4core.color("white"); // Orange color for y-axis value color
    valueAxis.renderer.labels.template.fontWeight = "bold"; // Bold text for y-axis

    valueAxis.min = 6; // Minimum number of values to display

    categoryAxis.renderer.grid.template.stroke = am4core.color("white"); // Orange color for grid color on x-axis
    valueAxis.renderer.grid.template.stroke = am4core.color("white"); // Orange color for grid color on y-axis

    // Create series for column (sales)
    var series = chart.series.push(new am4charts.ColumnSeries());
    series.dataFields.valueY = "sales";
    series.dataFields.categoryX = "month";
    series.name = "Actual Sales";
    series.tooltipText = "{name}: {valueY.value}";
    series.columns.template.fill = am4core.color("#FF7F50"); // Red color for bar color

    // Display actual sales numbers inside the bars
    var labelBullet = series.bullets.push(new am4charts.LabelBullet());
    labelBullet.label.text = "{valueY.value}";
    labelBullet.label.fill = am4core.color("#90EE90"); // Black color for text
    labelBullet.label.fontSize = 9; // Font size
    labelBullet.locationY = 0.5; // Adjust the position to place it at the bottom of the bar

    // Create a line series for the overall target
    var lineSeries = chart.series.push(new am4charts.LineSeries());
    lineSeries.dataFields.valueY = "target";
    lineSeries.dataFields.categoryX = "month";
    lineSeries.name = "Target Sales";
    lineSeries.strokeWidth = 2;
    lineSeries.stroke = am4core.color("green"); // Blue color for target line
    lineSeries.tooltipText = "{name}: {valueY.value}";

    // Set the target data
    lineSeries.data = Object.keys(combinedArray).map(function(month) {
        return { month: month, target: overallTarget };
    });

    // Add legend
    chart.legend = new am4charts.Legend();
    chart.legend.labels.template.fill = am4core.color("white"); // Orange color for legend text

    // Add cursor
    chart.cursor = new am4charts.XYCursor();
</script>


                                                 </div>



                                                  <!-- =========================end third ========================== -->
                                           
                                        </div>
                                  
                            </div>
                            <!-- ============================================================== -->

                            <style>
    .container4 {
    font-size: 2vh;
}

.box4 {
    border: 0.17vh solid black;
    padding: 1.67vh;
    width: 23.33vh;
    margin: 0.33vh auto;
    border-radius: 1.67vh;
}

.element4 {
    display: inline-block;
    width: 13.33vh;
    margin-left: 1.67vh;
    color: white;
}

.box41 {
    border: 0.17vh solid black;
    padding: 1.90vh;
    width: 13.3vh;
    margin: 0.33vh auto;
    border-radius: 1.67vh;
    color: white;
}

.element41 {
    text-align: center;
    display: inline-block;
    width: 13.3vh;
    margin-left: 1.67vh;
    color: white;
}

  </style>




  
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12" style ="padding-left: 0.83vh;">
                                <div class="card" style="height: 36.00vh; margin-bottom: 0px;background: radial-gradient(666px at 0.4% 48%, rgb(202, 204, 227) 0%, rgb(89, 89, 99) 97.5%);">
                                    <div class="card-body">
                                       
                                    <!-- ========================= Fourth start ========================== -->
                                    <div class=" " >

                                            <div class="container4">
                                                    <div>
                                                        <div class="element4">
                                                        <p style = "color:#228B22; font-weight: bold;">YEW Target  :</p>
                                                            </div>
                                                        <div class="element4">
                                                        <div class="box4"><?php echo $totalTarget ?></div>
                                                            </div>
                                                    </div>

                                                    <div>
                                                        <div class="element4">
                                                        <p style = "color:#228B22; font-weight: bold;">YTD Target  :</p>
                                                            </div>
                                                        <div class="element4">
                                                        <div class="box4"><?php echo $sumUpToDate ?></div>
                                                    </div>
                                                    </div>

                                                    <div>
                                                        <div class="element4">
                                                        <p style = "color:#228B22; font-weight: bold;">Total Achive :</p>
                                                            </div>
                                                        <div class="element4">
                                                        <div class="box4"><?php echo $total_achive ?></div>
                                                    </div>
                                                    </div>


                                                    <div style ="margin-top: 0.83vh;margin-left: 7.5vh;">
                                                        <div class="element41" >
                                                        <div class="box41" style = "background:#FFD400;" ><?php echo $yellow ?></div>
                                                            </div>
                                                        <div class="element41">
                                                        <div class="box41" style = "background:#228B22;" ><?php echo $green ?></div>
                                                    </div>
                                                    </div>

                                            </div>
                                            </div>

                                     <!-- ========================= fourth end ========================== -->

                                    </div>
                                </div>
                            </div>
                            <!-- ============================================================== -->
                            
                        </div>
                       

















                           

                        
                       
           
          
        </div>
        
    </div>

</body>
 
</html>



