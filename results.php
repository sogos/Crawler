<?php

require_once  __DIR__ . '/vendor/autoload.php';

use Predis\Client as RedisClient;

$redis = new Predis\Client();

$errors = $redis->smembers('s2');
$crawled = $redis->smembers('s0');


echo "<html>";
echo "<head>";
echo '	<link href="//maxcdn.bootstrapcdn.com/bootswatch/3.2.0/flatly/bootstrap.min.css" rel="stylesheet">';
echo '	<link href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css" rel="stylesheet">';
echo '	<link href="https://datatables.net/release-datatables/extensions/TableTools/css/dataTables.tableTools.css" rel="stylesheet">';
echo '  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>';
echo '  <script src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>';
echo '  <script src="http://datatables.net/release-datatables/extensions/TableTools/js/dataTables.tableTools.js"></script>';
echo '
	<script src="http://code.highcharts.com/highcharts.js"></script>
	<script src="http://code.highcharts.com/modules/exporting.js"></script>
';
echo "</head>";
echo "<body>";
echo '<div id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>';
echo "<div class='container'>";
echo "<table class='table table-striped table-bordered' id='results'>";
echo "<thead>";
	echo "<tr>";
		echo "<th>";
			echo "Code";
		echo "</th>";
		echo "<th>";
			echo "Url";
		echo "</th>";
		echo "<th>";
			echo "Parent";
		echo "</th>";
	
	echo "</tr>";
echo "</thead>";
echo "<tbody>";
$errors_code = array();
foreach ($errors as $error) {
			$decoded = json_decode($error, true);
	echo "<tr>";
		echo "<td>";
			print_r($decoded['status_code']);
			if(!isset($errors_code[$decoded['status_code']])) {
				$errors_code[$decoded['status_code']] = 1;
			} else {
				$errors_code[$decoded['status_code']]++;
			}
		echo "</td>";
		echo "<td>";
			print_r($decoded['url']);
		echo "</td>";
		echo "<td>";
			print_r($decoded['parent']);
		echo "</td>";


	echo "</tr>";

}
echo "</tbody>";
echo "</table>";
echo "</div>";
echo "<script>";
echo "	$(document).ready(function(){
    $('#results').dataTable();
});";
echo "
$(function () {
    $('#container').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 1,//null,
            plotShadow: false
        },
        title: {
            text: 'Repartition du crawler'
        },
        tooltip: {
    	    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} % ({point.y:.1f})',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            type: 'pie',
            name: 'Errors',
            data: [
                ['200', " . (sizeof($crawled)-sizeof($errors)) .  "],
";
		foreach($errors_code as $code => $value) {
		  echo "['" . $code ."', " . $value .  "],";
		}
echo "
           ]
        }]
    });
});
    

";
echo "</script>";
echo "</body>";
echo "</html>";
