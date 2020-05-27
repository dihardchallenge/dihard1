<?php 

// file handler
$file = fopen('test.csv', 'w');
// cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://raw.githubusercontent.com/alecristia/dihard_results/master/eval_production.csv");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");


$headers = array();
$headers[] = "Authorization: token e367b49fd05662c97b6b865017e8b331f7408182";
$headers[] = "Accept: application/vnd.github.v3.raw";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// set file handler option
curl_setopt($ch, CURLOPT_FILE, $file);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}

curl_close ($ch);
fclose($file);

$header = '<!DOCTYPE html>

<html>

<head>
    <meta charset="utf-8">
    <title>DIHARD Challenge</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- StyleSheet -->
    <link rel="stylesheet" href="./css/bootstrap.css" media="screen">
    <link rel="stylesheet" href="./css/custom.min.css" media="screen">


    <!-- SCRIPT -->
    <script src="./js/dihard.js"></script>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.0/extensions/filter-control/bootstrap-table-filter-control.min.js">
    <script type="text/javascript" src="./datatable/bootstrap-table.min.js"></script>
    <script type="text/javascript" src="./datatable/Chart.bundle.min.js"></script>
    <script type="text/javascript" src="./datatable/datatable.min.js"></script>
    <link rel="stylesheet" href="./datatable/bootstrap-table.min.css">
    <link rel="stylesheet" href="./datatable/datatable.min.css">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet"/>





</head>';

$body = '<body>


    <div class="container">
        <!-- Navbar -->
        <div class="bs-docs-section clearfix">
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
                <a class="navbar-brand" href="index.html">DIHARD</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                  </button>

                <div class="collapse navbar-collapse" id="navbarColor01">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="overview.html">
                                <font size="4">Overview </font>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="data.html">
                                <font size="4">Data </font>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="instructions.html">
                                <font size="4">Instructions </font>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="submission.html">
                                <font size="4">Submissions </font>
                            </a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="results.php">
                                <font size="4">Results </font><span class="sr-only">(current)</span></a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="contact.html">
                                <font size="4">Contact Us </font>
                            </a>
                        </li>
                    </ul>

                </div>
            </nav>
        </div>
        <!-- Navbar -->


        <!-- Content -->
        <div class="jumbotron">
            <h1>Results</h1>
            <br>
            <hr class="my-4">
            <h2><b>Online leaderboard</b></h2>
            
            <button onClick="location.href=\'#track1\'" class="btn btn-primary btn-lg">Track 1</button>
            <button onClick="location.href=\'#track2\'" class="btn btn-primary btn-lg">Track 2</button>


            

            <hr class="my-4">';

$bodyend = '</div>
        <!-- Content -->
    </div>

</body>

</html>';

$track1 = '<br><h2 id="track1"><b>Track1</b></h2>
<hr class="my-4">
';

$track2 = '<br><br><h2 id="track2"><b>Track2</b></h2>
<hr class="my-4">
';





    function csvtojson($file,$delimiter)
    {
        if (($handle = fopen($file, "r")) === false)
        {
                die("can't open the file.");
        }

        $csv_headers = fgetcsv($handle, 4000, $delimiter);
        $csv_json = array();

        while ($row = fgetcsv($handle, 4000, $delimiter))
        {
                $csv_json[] = array_combine($csv_headers, $row);
        }

        fclose($handle);
        return json_encode($csv_json);
    }


    // index
    $team_system = array();

    $jsonresult = csvtojson("submissions.csv", "\t");
    // echo $jsonresult;
    $data = json_decode($jsonresult, true);
    $firsttable = '';
    if (count($data)) {
        // Open the table
        $firsttable .= '<table class="datatable table table-hover table-condensed" data-bar-hline="true" data-chart-default-mode="bar" data-chart-modes="bar,scatter" data-id-field="ts" data-pagination="true" data-rank-mode="grouped_muted" data-row-highlighting="true" data-show-chart="true" data-show-rank="true" data-sort-name="der" data-sort-order="asc" data-scatter-x="der" data-scatter-y="mi">
        ';

        // Add header
        $firsttable .= '<thead>
        ';
        $firsttable .= '<tr>
';
        $firsttable .= '    <th class="sep-left-cell text-center" data-rank="true">Rank</th>
        ';
        $firsttable .= '    <th class="sep-left-cell text-center" data-field="ts"  data-sortable="true" data-value-type="str">Team (System)</th>
        ';
        $firsttable .= '    <th class="sep-left-cell text-center" data-field="SystemDescription" data-sortable="false" data-value-type="str">Description</th>
        ';
        $firsttable .= '    <th class="sep-left-cell text-center" data-field="Zenodo" data-sortable="false" data-value-type="str">Zenodo</th>
        ';
        $firsttable .= '    <th class="sep-left-cell text-center" data-field="date" data-sortable="false" id="date">Date</th>
        ';
        $firsttable .= '    <th class="sep-left-cell text-center" data-chartable="true" data-field="der" data-sortable="true" data-value-type="float2-percentage">DER</th>
        ';
        $firsttable .= '    <th class="sep-left-cell text-center" data-chartable="true" data-field="mi" data-sortable="true" data-value-type="float4">MI</th>
        ';

        $firsttable .= '</tr>
        ';
        $firsttable .= '</thead>
        ';
        $firsttable .= '<tbody>
';

// sort array by date
$ord = array();
foreach ($data as $key => $value){
    $ord[] = strtotime($value['Date']);
}
array_multisort($ord, SORT_DESC, $data);

        // Cycle through the array
        foreach ($data as $item) {

            $teamname = $item['Team_Name'];
            $systemname = $item['System_Name'];
            $ts = $teamname . " (" . $systemname . ")"; 
            if (in_array($ts, $team_system)) {
            } else {
                
                            // Output a row
                if ($item['Track'] === "Track1") {
                    array_push($team_system, $ts);
                    
                    $rank = $item['Date'];
                    $teamname = $item['Team_Name'];
                    $systemname = $item['System_Name'];
                    $systemdescription = $item['SystemDescription'];
                    $zenodo = $item['URL'];
                    $date = $item['Date'];
                    $der = $item['DER'];
                    $mi = $item['MI'];
                    
                    $ts = $teamname . " (" . $systemname . ")"; 


                    $firsttable .=  "\t<tr>\n";
                    $firsttable .=  "\t\t<td></td>\n";
                    $firsttable .=  "\t\t<td>$ts</td>\n";
                    $firsttable .=  "\t\t<td><a href=\"./docs/system_descriptions/$systemdescription\">$systemdescription</a></td>\n";
                    $firsttable .=  "\t\t<td><a href=\"$zenodo\">Link</a></td>\n";
                    $firsttable .=  "\t\t<td>$date</td>\n";
                    //$track = $item['Track'];
                    //$firsttable .=  "<td>$track</td>\n";
                    $firsttable .=  "\t\t<td>$der</td>\n";
                    $firsttable .=  "\t\t<td>$mi</td>\n";
                    $firsttable .=  "\t</tr>\n";
                }
            }
        }

        // Close the table
        $firsttable .= '</tbody>
        ';
        $firsttable .=  '</table>
        ';
    }


    $team_system2 = array();


    $jsonresult2 = csvtojson("submissions.csv", "\t");
    // echo $jsonresult;
    $data2 = json_decode($jsonresult, true);
    $secondtable = '';
    if (count($data2)) {
        // Open the table
        $secondtable .= '<table class="datatable table table-hover table-condensed" data-bar-hline="true" data-chart-default-mode="bar" data-chart-modes="bar,scatter" data-id-field="ts" data-pagination="true" data-rank-mode="grouped_muted" data-row-highlighting="true" data-show-chart="true" data-show-rank="true" data-sort-name="der" data-sort-order="asc" data-scatter-x="der" data-scatter-y="mi">
        ';

        // Add header
        $secondtable .= '<thead>
        ';
        $secondtable .= '<tr>';
        $secondtable .= '<th class="sep-left-cell text-center" data-rank="true">Rank</th>
        ';
        $secondtable .= '    <th class="sep-left-cell text-center" data-field="ts"  data-sortable="true" data-value-type="str">Team (System)</th>
        ';
        $secondtable .= '    <th class="sep-left-cell text-center" data-field="SystemDescription" data-sortable="false" data-value-type="str">Description</th>
        ';
        $secondtable .= '    <th class="sep-left-cell text-center" data-field="Zenodo" data-sortable="false" data-value-type="str">Zenodo</th>
        ';
        $secondtable .= '<th class="sep-left-cell text-center" data-field="date" data-sortable="false" id="date">Date</th>
        ';
        $secondtable .= '<th class="sep-left-cell text-center" data-chartable="true" data-field="der" data-sortable="true" data-value-type="float2-percentage">DER</th>
        ';
        $secondtable .= '<th class="sep-left-cell text-center" data-chartable="true" data-field="mi" data-sortable="true" data-value-type="float4">MI</th>
        ';

        $secondtable .= '</tr>
        ';
        $secondtable .= '</thead>
        ';
        $secondtable .= '<tbody>
        ';

// sort array by date
$ord2 = array();
foreach ($data2 as $key => $value){
    $ord2[] = strtotime($value['Date']);
}
array_multisort($ord2, SORT_DESC, $data2);

        // Cycle through the array
        foreach ($data2 as $item) {

            $teamname = $item['Team_Name'];
            $systemname = $item['System_Name'];
            $ts = $teamname . " (" . $systemname . ")"; 
            if (in_array($ts, $team_system2)) {
            } else {

            // Output a row
                if ($item['Track'] === "Track2") {
                    array_push($team_system2, $ts);

                    $rank = $item['Date'];
                    $teamname = $item['Team_Name'];
                    $systemname = $item['System_Name'];
                    $systemdescription = $item['SystemDescription'];
                    $zenodo = $item['URL'];
                    $date = $item['Date'];
                    $der = $item['DER'];
                    $mi = $item['MI'];
                    
                    $ts = $teamname . " (" . $systemname . ")"; 


                    $secondtable .=  "\t<tr>\n";
                    $secondtable .=  "\t\t<td></td>\n";
                    $secondtable .=  "\t\t<td>$ts</td>\n";
                    $secondtable .=  "\t\t<td><a href=\"./docs/system_descriptions/$systemdescription\">$systemdescription</a></td>\n";
                    $secondtable .=  "\t\t<td><a href=\"$zenodo\">Link</a></td>\n";
                    $secondtable .=  "\t\t<td>$date</td>\n";
                    //$track = $item['Track'];
                    //$secondtable .=  "<td>$track</td>\n";
                    $secondtable .=  "\t\t<td>$der</td>\n";
                    $secondtable .=  "\t\t<td>$mi</td>\n";
                    $secondtable .=  "\t</tr>\n";
                }
            }
        }

        // Close the table
        $secondtable .= '</tbody>
        ';
        $secondtable .=  '</table><br><hr class="my-4">
        ';
    }




    echo $result = $header . $body . $track1 . $firsttable . $track2 . $secondtable . $bodyend;
?>