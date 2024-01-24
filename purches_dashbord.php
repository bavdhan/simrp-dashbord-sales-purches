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
    'simrp.purchase', 'purchess',
    array(array())
);

$getwostatus = json_decode($record, true);





$monthWiseData = array();
$totalAmount = 0; // New variable to store the total amount for all "Item Types"

if (is_array($getwostatus)) {
    foreach ($getwostatus as $item) {
        // Check if the item is within the selected financial year
        if (isset($item['Item Type'])) {
            $itemType = $item['Item Type'];

            // Skip items without a specified financial year
            if (empty($itemType)) {
                continue;
            }

            $itemData = array('Item Type' => $itemType);

            // Extract data for the specified month
            $formattedKey = date('M y', strtotime($date . '-01'));
            $amountForMonth = isset($item[$formattedKey]) ? $item[$formattedKey] : 0;

            $itemData[$date] = $amountForMonth;
            $totalAmount += $amountForMonth; // Update total amount

            $monthWiseData[] = $itemData;
        }
    }

    // Add the total amount to the result
    // $monthWiseData[] = array('Item Type' => 'Total', $date => $totalAmount);
}

// Print the final data for debugging

$newArray = array();

foreach ($monthWiseData as $item) {
    $newItem = array('Item Type' => $item['Item Type']);

    // Copy other fields excluding the specific month
    foreach ($item as $key => $value) {
        if ($key !== $date) {
            $newItem[$key] = $value;
        }
    }

    // Add the date with the name "Amount"
    $newItem['Amount'] = isset($item[$date]) ? $item[$date] : 0;

    $newArray[] = $newItem;
}


################### purchess month wise total ###############
$date = isset($_GET['date']) ? $_GET['date'] : '';

// Calculate financial year start and end
$selectedDate = new DateTime($date);
if ($selectedDate->format('m') >= 4) {
    $financialYearStart = date('Y-04-01', strtotime($date));
} else {
    $financialYearStart = date('Y-04-01', strtotime('-1 year', strtotime($date)));
}

$financialYearEnd = date('Y-03-31', strtotime('+1 year', strtotime($financialYearStart)));

$monthYears = array();

$currentDate = new DateTime($financialYearStart);

while ($currentDate->format('Y-m-d') <= $financialYearEnd) {
    $monthYears[] = $currentDate->format('M y');
    $currentDate->modify('+1 month');
}

$monthWiseTotal = array();

if (is_array($getwostatus)) {
    foreach ($getwostatus as $item) {
        // Check if the item is within the selected financial year
        if (isset($item['Item Type'])) {
            $itemType = $item['Item Type'];

            // Skip items without a specified financial year
            if (empty($itemType)) {
                continue;
            }

            // Extract data for all months in the financial year
            foreach ($monthYears as $formattedKey) {
                $amountForMonth = isset($item[$formattedKey]) ? $item[$formattedKey] : 0;

                // Update month-wise total
                if (!isset($monthWiseTotal[$formattedKey])) {
                    $monthWiseTotal[$formattedKey] = 0;
                }
                $monthWiseTotal[$formattedKey] += $amountForMonth;
            }
        }
    }
}

// Output the month-wise totals array




########   credit table ##############
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






$credit = $models->execute_kw($db, $uid, $password,
'simrp.credit', 'dcredit_purches',
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
'simrp.debit', 'rcdebit_purchess',
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







#################   expence table ##############################
$expence2 = $models->execute_kw($db, $uid, $password,
'simrp.purchase', 'expence',
array(array())
);

$expence_status = json_decode($expence2, true);

$date = isset($_GET['date']) ? $_GET['date'] : '';

if (strtotime($date) !== false) {
    $formattedDate = date('F j, Y', strtotime($date));

    $monthYear = date('My', strtotime($date));

    if (isset($expence_status[$monthYear])) {
        // print_r($expence_status[$monthYear]);

        $monthlyExpenses = $expence_status[$monthYear];
        // print_r($monthlyExpenses);

        // Initialize a variable to store the sum of amounts
        $totalAmount_expence = 0;

        // Loop through each category and add the amount to the total
        foreach ($monthlyExpenses as $category => $amount) {
            $totalAmount_expence += $amount;
        }

    } else {
        // echo "Data not available for the specified month: $formattedDate";
        $expence_status[$monthYear] = 0;
    }
} else {
    // echo "Invalid date format: $date";
}


if (isset($expence_status[$monthYear]) && is_array($expence_status[$monthYear])) {
    // Assign the array to $monthlyExpenses
    $monthlyExpenses = $expence_status[$monthYear];

    // Initialize an empty array to store data for the Pareto chart
    $paretoChartData = [];

    // Loop through the monthly expenses and add data to the Pareto chart array
    foreach ($monthlyExpenses as $category => $value) {
        try {
            // Check if the category is a string with length greater than 10
            $category1 = strlen($category) > 10 ? substr($category, 0, 10) . '...' : $category;

            // Add data to the Pareto chart array
            $paretoChartData[] = [
                'country' => $category1,
                'visits' => $value
            ];
        } catch (Exception $e) {
            // Handle the exception (log, display a message, etc.)
            // For example, you can log the error message to a file
            error_log('Error processing category: ' . $e->getMessage(), 0);
        }
    }
} else {
    // Handle the case where $expence_status[$monthYear] is not set or not an array
    // For example, you can log an error message or set $paretoChartData to an empty array
    error_log('Invalid or missing data for $expence_status[' . $monthYear . ']', 0);
    $paretoChartData = [];
}
usort($paretoChartData, function ($a, $b) {
  return $b['visits'] - $a['visits'];
});
// Convert the PHP array to JSON to use in JavaScript
$paretoChartJson = json_encode($paretoChartData);


#####  expence monthwise total  ##################################  pending 

$expence3 = $models->execute_kw($db, $uid, $password, 'simrp.purchase', 'expence', array(array()));

#####  adj monthwise total  ##################################  pending 
$adj = $models->execute_kw($db, $uid, $password, 'simrp.purchase', 'adj_val', array(array()));
$adj_status = json_decode($adj, true);

$date = isset($_GET['date']) ? $_GET['date'] : '';
$selectedDate = new DateTime($date);

if ($selectedDate->format('m') >= 4) {
    $financialYearStart = date('Y-04-01', strtotime($date));
} else {
    $financialYearStart = date('Y-04-01', strtotime('-1 year', strtotime($date)));
}

$financialYearEnd = date('Y-03-30', strtotime('+1 year', strtotime($financialYearStart)));


// echo "Financial Year Start: " . $financialYearStart . PHP_EOL;
// echo "Financial Year End: " . $financialYearEnd . PHP_EOL;

$filteredData = array();
$current_month_adj = 0;

foreach ($adj_status as $key => $value) {
    $monthYear = DateTime::createFromFormat('M y', $key);

    if ($monthYear >= new DateTime($financialYearStart) && $monthYear <= new DateTime($financialYearEnd)) {
        $filteredData[$key] = $value;

        if ($monthYear->format('Y-m') === $selectedDate->format('Y-m')) {
            $current_month_adj = $value; // Only add the value, not the key
        }
    }
}

function compareMonths($a, $b) {
    $dateA = DateTime::createFromFormat('M y', $a);
    $dateB = DateTime::createFromFormat('M y', $b);

    return $dateA <=> $dateB;
}

uksort($filteredData, 'compareMonths');



$monthsOnlyArray = array_keys($monthWiseTotaldebit);
// print_r($monthsOnlyArray);


$formattedArray = array();
foreach ($monthWiseTotaldebit as $key => $value) {
    // Convert the existing key to a DateTime object and format it as desired
    $dateObj = DateTime::createFromFormat('M Y', $key);
    $formattedKey = $dateObj->format('M y');
    
    // Create a new array with the formatted key
    $formattedArray[$formattedKey] = $value;
}




$formattedArray2 = array();
foreach ($monthWiseTotalcredit as $key => $value) {
    // Convert the existing key to a DateTime object and format it as desired
    $dateObj = DateTime::createFromFormat('M Y', $key);
    $formattedKey = $dateObj->format('M y');
    
    // Create a new array with the formatted key
    $formattedArray2[$formattedKey] = $value;
}

#########################################    all array addition  ########################

$monthWiseTotalAddition = array();

foreach ($monthWiseTotal as $key => $value) {
    $monthYear = substr($key, 0, 3) . ' ' . substr($key, -2);
    
    $total = $value;

    if (isset($formattedArray[$monthYear])) {
        $total += $formattedArray[$monthYear];
    }

    if (isset($filteredData[$monthYear])) {
        $total += $filteredData[$monthYear];
    }

    if (isset($formattedArray2[$monthYear])) {
        $total += $formattedArray2[$monthYear];
    }

    $monthWiseTotalAddition[$key] = $total;
}

// Output the result  #########################################    ########################

$jsArray = array();
foreach ($monthWiseTotalAddition as $month => $sales) {
    $jsArray[] = array("month" => substr($month, 0, 3), "sales" => $sales);
}











##  condition company name wise graph array     ###########
$company1 = isset($_GET['company']) ? $_GET['company'] : '';
$final = $totalAmount + $credit_totalSum + $current_month_adj + $debit_totalSum;

if (isset($final) && is_numeric($final) && isset($totalAmount_expence) && is_numeric($totalAmount_expence)) {
    $actual = $final + $totalAmount_expence;
} else {
    $actual = 0; 
}

$overallTarget_shaha = 0;
if ($company1 == 'jia') {
   $newArray = $newArray;
   $jsArray = $jsArray;
   $net = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($totalAmount));
   $credit_totalSum = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($credit_totalSum));
   $debit_totalSum = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($debit_totalSum));

   if (isset($totalAmount_expence) && is_numeric($totalAmount_expence)) {
       $totalAmount_expence = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($totalAmount_expence));
    } else {
        $totalAmount_expence = 0; 
    }
   $current_month_adj = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($current_month_adj));
   $final = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($final));
   $actual = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($actual));

} elseif ($company1 == 'shaha') {
    $newArray = '';
    $net = 0;
    $credit_totalSum = 0;
    $debit_totalSum = 0;
    $totalAmount_expence = 0;
    $current_month_adj = 0;
    $jsArray = $jsArray;
    $final = 0;
    $actual = 0;

} elseif ($company1 == '') {
    $newArray = $newArray;
    $jsArray = $jsArray;
    $net = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($totalAmount));
    $credit_totalSum = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($credit_totalSum));
    $debit_totalSum = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($debit_totalSum));

    if (isset($totalAmount_expence) && is_numeric($totalAmount_expence)) {
        $totalAmount_expence = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($totalAmount_expence));
    } else {
        $totalAmount_expence = 0; 
    }

    $current_month_adj = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($current_month_adj));
    $final = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($final));
    $actual = preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", round($actual));

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

    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>


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
                                          <p style="font-size: 5vh;  color: black; font-weight: bold;">Purchase Report :</p>
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
                            
                            <div class="col-xl-9 col-lg-12 col-md-6 col-sm-12 col-12" style ="padding-right: 0.83vh;flex: 0 0 60%;max-width: 60%;">
                                <div class="card" style="height: 44.25vh;background: #71797E;" >
                                    
                                    <div class="card-body p-0">
                              
                                            
                                                 <!-- ======================start first =============================== -->

                                                 <div id="chartdiv1" style="width: 100%; height: 41.00vh;">></div>

                                                 <script>
am5.ready(function() {

var root = am5.Root.new("chartdiv1");


root.setThemes([
  am5themes_Animated.new(root)
]);

var chart = root.container.children.push(am5xy.XYChart.new(root, {
  panX: false,
  panY: false,
  wheelX: "panX",
  wheelY: "zoomX",
  paddingLeft: 0,
  paddingRight: 0,
  layout: root.verticalLayout
}));

var colors = chart.get("colors");

var data = <?php echo $paretoChartJson; ?>;

prepareParetoData();

function prepareParetoData() {
  var total = 0;

  for (var i = 0; i < data.length; i++) {
    var value = data[i].visits;
    total += value;
  }

  var sum = 0;
  for (var i = 0; i < data.length; i++) {
    var value = data[i].visits;
    sum += value;
    data[i].pareto = sum / total * 100;
  }
}

// data.sort(function(a, b) {
//   return b.visits - a.visits;
// });

var xRenderer = am5xy.AxisRendererX.new(root, {
  minGridDistance: 0,
  minorGridEnabled: true,
  
  
});

// xRenderer.labels.template.setAll({
//         fontSize: 4,
//         multiLine: false, // Enable multi-line labels
//         maxLines: 3,     // Set the maximum number of lines per label
//         width: 40,       // Set the maximum width for each line
//     });

xRenderer.labels.template.setAll({
  fontSize: 10, // Set font size for X-axis labels
  rotation: -90 // Rotate labels by -45 degrees
  
});


var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
  categoryField: "country",
  renderer: xRenderer
}));

xRenderer.grid.template.setAll({
  location: 1
})

xRenderer.labels.template.setAll({
  paddingTop: -5
});

xAxis.data.setAll(data);

var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
  renderer: am5xy.AxisRendererY.new(root, {
    strokeOpacity: 0.1
  })
}));

var paretoAxisRenderer = am5xy.AxisRendererY.new(root, { opposite: true });
var paretoAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
  renderer: paretoAxisRenderer,
  min: 0,
  max: 100,
  strictMinMax: true
}));

paretoAxisRenderer.grid.template.set("forceHidden", true);
paretoAxis.set("numberFormat", "#'%");

var series = chart.series.push(am5xy.ColumnSeries.new(root, {
  xAxis: xAxis,
  yAxis: yAxis,
  valueYField: "visits",
  categoryXField: "country"
}));

series.columns.template.setAll({
  tooltipText: "{categoryX}: {valueY}",
  tooltipY: 0,
  strokeOpacity: 0,
  cornerRadiusTL: 6,
  cornerRadiusTR: 6
});

series.columns.template.adapters.add("fill", function (fill, target) {
  return chart.get("colors").getIndex(series.dataItems.indexOf(target.dataItem));
})


// pareto series
var paretoSeries = chart.series.push(am5xy.LineSeries.new(root, {
  xAxis: xAxis,
  yAxis: paretoAxis,
  valueYField: "pareto",
  categoryXField: "country",
  stroke: root.interfaceColors.get("alternativeBackground"),
  maskBullets: false
}));

paretoSeries.bullets.push(function () {
  return am5.Bullet.new(root, {
    locationY: 1,
    sprite: am5.Circle.new(root, {
      radius: 5,
      fill: series.get("fill"),
      stroke: root.interfaceColors.get("alternativeBackground")
    })
  })
})

series.data.setAll(data);
paretoSeries.data.setAll(data);



}); 
</script>

                                                  <!-- =========================end first ========================== -->
                                         
                                       
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
  
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12" style ="padding-right: 0.83vh; padding-left:6px;flex: 0 0 40%;max-width: 40%;">
                                <div class="card" style="height: 44.25vh;background: radial-gradient(666px at 0.4% 48%, rgb(202, 204, 227) 0%, rgb(89, 89, 99) 97.5%);">
                                    
                                   
                                       
                                    <!-- ========================= second start ========================== -->
                                    
                                    <div id="chartdiv3" style="width: 100%; height: 44.00vh;"></div>
                  
                  
                  
                  
 <script>
    // Set theme
    am4core.useTheme(am4themes_animated);

    // Create chart instance
    var chart = am4core.create("chartdiv3", am4charts.PieChart);

    // Add data from PHP array
    chart.data = <?php echo json_encode($newArray); ?>;

    // Add and configure Series
    var pieSeries = chart.series.push(new am4charts.PieSeries());
    pieSeries.dataFields.value = "Amount"; // Assuming "Amount" is the new field name
    pieSeries.dataFields.category = "Item Type"; // Assuming "Item Type" is the field name

    // Let's cut a hole in our Pie chart the size of 40% the radius
    chart.innerRadius = am4core.percent(0);

    // Put a thick white border around each Slice
    pieSeries.slices.template.stroke = am4core.color("#4a2abb");
    pieSeries.slices.template.strokeWidth = 1;
    pieSeries.slices.template.strokeOpacity = 1;

    // Display both Item Type and Amount in labels
    pieSeries.labels.template.fontSize = 10; // Adjust font size for better readability
    pieSeries.labels.template.text = "{category}: [bold green]{value.value} Rs[/]";
    pieSeries.labels.template.wrap = true; // Enable text wrapping
    pieSeries.labels.template.maxWidth = 100; // Set a maximum width for the label
</script>
          
                  
                  
                  
                  
                  
                  
                  
                  
                                    <!-- HTML -->
<!-- HTML -->
<!-- HTML -->
<!-- <script>
    // Set theme
    am4core.useTheme(am4themes_animated);

    // Create chart instance
    var chart = am4core.create("chartdiv3", am4charts.PieChart);

    // Add data with litres and percentage
    chart.data = [{
        "country": "Consumable (stationery / gloves / etc)",
        "litres": 501.9
    }, {
        "country": "Consumable Tools / inserts / Oils",
        "litres": 301.9
    }, {
        "country": "Equipment",
        "litres": 201.1
    }, {
        "country": "Finished Goods (FG)",
        "litres": 165.8
    }, {
        "country": "Input Parts (Boughtout / WIP)",
        "litres": 139.9
    }, {
        "country": "Instrument",
        "litres": 128.3
    }, {
        "country": "Raw Material (RM-Basic)",
        "litres": 99
    }];

    // Add and configure Series
    var pieSeries = chart.series.push(new am4charts.PieSeries());
    pieSeries.dataFields.value = "litres";
    pieSeries.dataFields.category = "country";

    // Let's cut a hole in our Pie chart the size of 40% the radius
    chart.innerRadius = am4core.percent(0);

    // Put a thick white border around each Slice
    pieSeries.slices.template.stroke = am4core.color("#4a2abb");
    pieSeries.slices.template.strokeWidth = 1;
    pieSeries.slices.template.strokeOpacity = 1;

 // Display both country and litres in labels
    pieSeries.labels.template.fontSize = 10; // Adjust font size for better readability
    pieSeries.labels.template.text = "{category}: [bold green]{value.value} Rs[/]";
    pieSeries.labels.template.wrap = true; // Enable text wrapping
    pieSeries.labels.template.maxWidth = 100; // Set a maximum width for the label



</script> -->



                                                 


                                          

                                     <!-- ========================= second start ========================== -->

                                    
                                </div>
                            </div>
                            <!-- ============================================================== -->
                            
                        </div>
                       
                            
                           
                          










                        <div class="row">
                            
                            <div class="col-xl-9 col-lg-12 col-md-6 col-sm-12 col-12" style ="padding-right: 0.83vh;flex: 0 0 60%;max-width: 60%;">
                                <div class="card"  style =" margin-bottom: 0px;height: 36.00vh; background:#2B4162;">
                                    
                                            
                                                 <!-- ======================start third  =============================== -->

                                                 <div class=" " style="">

                                                 <div id="chartdiv" style="width: 100%; height: 36.00vh;"></div>

                                                 <script>
    // Add license for the free version (replace with your valid key)
    am4core.addLicense("CH1234567890");

    // Convert PHP array to JavaScript array
    var combinedArray =  <?php echo json_encode($jsArray); ?>;

    // Overall target value (replace with your actual target)

    // Create chart instance
    var chart = am4core.create("chartdiv", am4charts.XYChart);

    // Add data
    chart.data = combinedArray;

    // Modify chart properties
    chart.paddingRight = 20;
    chart.fontFamily = "Arial, sans-serif";

    // Create axes
    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "month";
    categoryAxis.renderer.grid.template.location = 0;
    categoryAxis.renderer.minGridDistance = 20;
    categoryAxis.renderer.labels.template.fontSize = 10;
    categoryAxis.renderer.labels.template.fill = am4core.color("white");
    categoryAxis.renderer.labels.template.fontWeight = "bold";

    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.renderer.labels.template.fontSize = 10;
    valueAxis.renderer.labels.template.fill = am4core.color("white");
    valueAxis.renderer.labels.template.fontWeight = "bold";
    valueAxis.min = 6;

    categoryAxis.renderer.grid.template.stroke = am4core.color("white");
    valueAxis.renderer.grid.template.stroke = am4core.color("white");

    // Create series for column (sales)
    var series = chart.series.push(new am4charts.ColumnSeries());
    series.dataFields.valueY = "sales";
    series.dataFields.categoryX = "month";
    series.name = " Purchase Amount";
    series.tooltipText = "{name}: {valueY.value}";
    series.columns.template.fill = am4core.color("#FF7F50");

    // Display actual sales numbers inside the bars
    var labelBullet = series.bullets.push(new am4charts.LabelBullet());
    labelBullet.label.text = "{valueY.value}";
    labelBullet.label.fill = am4core.color("#90EE90");
    labelBullet.label.fontSize = 9;
    labelBullet.locationY = 0.5;





    // Add legend
    chart.legend = new am4charts.Legend();
    chart.legend.labels.template.fill = am4core.color("white");

    // Add cursor
    chart.cursor = new am4charts.XYCursor();
  </script>


                                                 </div>



                                                  <!-- =========================end third ========================== -->
                                           
                                        </div>
                                  
                            </div>
                            <!-- ============================================================== -->

                            <style>
   .container112 {
            display: flex; /* Use flexbox to make the child divs inline */
        }

    
        .box112 {
            width: 50%; 
            margin: 10px; 
        }
.header-text22 {
    font-weight: bold;
    margin-top:-20px;
}
.box41112 {
    border: 0.17vh solid black;
    padding: 1.67vh;
    width: 300px;
    padding-right:50px;
    margin: 0.33vh auto;
    border-radius: 1.67vh;
}
  </style>




  
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 col-12" style ="padding-left: 0.83vh;flex: 0 0 40%;max-width: 40%;">
                                <div class="card" style="height: 36.00vh; margin-bottom: 0px;background: #71797E;">
                                    <div class="card-body">
                                       
                                    <!-- ========================= Fourth start ========================== -->
                                    <div class=" " >
                                    <p class="header-text22" style = "text-align: center;font-size: 6.33vh;color: white; "><?php echo $final ?></p>

                                          

                                    <div class="container112">
                                        <div class="box112">
                                            <p style="color:white; font-weight: bold; display: inline; padding-right:30px;">Net     :</p>
                                            <div class="box41112" style="display: inline; color:white; font-weight: bold;"><?php echo $net ?></div>
                                        </div>


                                        <div class="box112">
                                            <p style="color:white; font-weight: bold; display: inline; padding-right:10px;">Debit    :</p>
                                            <div class="box41112" style="display: inline; color:white; font-weight: bold;"><?php echo $debit_totalSum ?></div>
                                        </div>
                                    </div>

                                    
                                    <div class="container112">
                                        <div class="box112">
                                            <p style="color:white; font-weight: bold; display: inline; padding-right:30px;">Adj.    :</p>
                                            <div class="box41112" style="display: inline; color:white; font-weight: bold;"><?php echo $current_month_adj ?></div>
                                        </div>
                                        <div class="box112">
                                            <p style="color:white; font-weight: bold; display: inline; padding-right:5px;">Credit    :</p>
                                            <div class="box41112" style="display: inline; color:white; font-weight: bold;"><?php echo $credit_totalSum ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="container112">
                                        <div class="box112">
                                            <p style="color:white; font-weight: bold; display: inline;">Expence   :</p>
                                            <div class="box41112" style="display: inline; color:white; font-weight: bold;"><?php echo $totalAmount_expence ?></div>
                                        </div>
                                        <div class="box112">
                                            <p style="color:yellow; font-weight: bold; display: inline;">Actual   :</p>
                                            <div class="box41112" style="display: inline; color:white; font-weight: bold;"><?php echo $actual ?></div>
                                        </div>
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



