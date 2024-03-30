<?php
$page_title = 'Presiune';
require_once('includes/load.php');

if (!$session->isUserLoggedIn()) {
    redirect('index.php', false);
    die();
}

include_once('layouts/header.php');
$user_ID=(int) $_SESSION['user_id'];

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
		   value FROM presiune WHERE data>DATE_SUB(NOW(), INTERVAL 10 DAY)";
    $chartQueryRecords = mysqli_query($con,$chartQuery);
?>


<!--suppress ALL -->
<div class="row">
    <div class="col-md-12">
        <?php echo GetSigla() ?>
        <div class="panel panel-default">
            <div class="panel-body">
	<div class="text-center">
        <h2><strong>Valori presiune in ultimele 10 zile</strong></h2>
	</div>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://www.gstatic.com/charts/loader.js"></script>

  <div id="dashboard">
      <div id="chart_div"></div>
      <div id="control_div"></div>
  </div>


   <script type="text/javascript">

google.charts.load('current', {
  callback: function () {
    var data = google.visualization.arrayToDataTable([
             ['Date', 'Pressure'],
            <?php
                 while($row = mysqli_fetch_assoc($chartQueryRecords)){
                    echo "[new 
                    Date(".$row['year'].",".($row['month']-1).",".$row['day'].",".$row['hour'].",".$row['minute']."),".$row['value']."],";
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
      wrapper.setOption('animation.duration', 0);
      wrapper.setOption('hAxis.format', 'MM/dd/yyyy');
    }

    setOptions(chart);

    dash.bind([control], [chart]);
    dash.draw(data);
  },
  packages: ['controls', 'corechart']
});

    </script>

  </div>
        </div>
    </div>
</div>

<?php include_once('layouts/footer.php'); ?>

