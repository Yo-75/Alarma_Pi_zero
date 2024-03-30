<?php

include_once("includes/config.php");

    $con = mysqli_connect(DB_HOST, DB_USER, DB_PASS,DB_NAME);
    // Check connection
    if (!$con) {
     die("Connection failed: " . mysqli_connect_error());
    }

    $chartQuery = "SELECT year(data) as year,
		   month(data) as month,
                   day(data) as day,
                   hour(data) as hour,
		   minute(data) as minute,
		   tmp100 as value FROM temperatura WHERE data>'2021-1-21'";
    $chartQueryRecords = mysqli_query($con,$chartQuery);
?>

<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    
  <body style="text-align: center;">
<script src="https://www.gstatic.com/charts/loader.js"></script>
<div id="dashboard">
  <div id="chart_div"></div>
  <div id="control_div"></div>
</div>


   <script type="text/javascript">

google.charts.load('current', {
  callback: function () {
    var data = google.visualization.arrayToDataTable([
             ['Date', 'Temperature'],
            <?php
                 while($row = mysqli_fetch_assoc($chartQueryRecords)){
                    echo "[new Date(".$row['year'].",".($row['month']-1).",".$row['day'].",".$row['hour'].",".$row['minute']."),".$row['value']."],";
                }
            ?>
        ]);


var dash = new google.visualization.Dashboard(document.getElementById('dashboard'));

    var control = new google.visualization.ControlWrapper({
      controlType: 'ChartRangeFilter',
      containerId: 'control_div',
      options: {
        filterColumnIndex: 0,
        ui: {
          chartOptions: {
            height: '100',
            width: '70%',
            chartArea: {
              width: '80%'
            }
          },
          chartView: {
            columns: [0, 1]
          }
        }
      }
    });

    var chart = new google.visualization.ChartWrapper({
      chartType: 'LineChart',
      containerId: 'chart_div'
    });

    google.visualization.events.addListener(control, 'statechange', function () {
      var dateRange = control.getState().range;
      var dayInMS =  1 * 24 * 60 * 60 * 1000;
      var periodShown = dateRange.end.getTime() - dateRange.start.getTime();
      if (periodShown <= dayInMS) {
        chart.setOption('hAxis.format', 'H:mm');
      } else {
        chart.setOption('hAxis.format', 'MM/dd/yyyy');
      }
      chart.draw();
    });

    function setOptions(wrapper) {
      wrapper.setOption('height', '600');
      wrapper.setOption('width', '70%');
      wrapper.setOption('animation.duration', 200);
      wrapper.setOption('hAxis.format', 'MM/dd/yyyy');
    }

    setOptions(chart);

    dash.bind([control], [chart]);
    dash.draw(data);
  },
  packages: ['controls', 'corechart']
});


    </script>
  </head>
  </body>
</html>
