<?php
session_start(); // Start or resume a session
if($_SESSION["loggedIn"] == false){
  //not logged in go to log in page.
  header("Location: login.php");
  exit;
}
?>
<html>
<head>
    <title>Trends</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <h2>Trends:</h2>

    <center>
        <?php
            require_once "data-controller.php";
            $residentId = $_SESSION['residentId'];
            $response = get_events($residentId);

            $data = null;
            if ($response !== false) {
                $data = json_decode($response, true); // Convert JSON string to associative array
                //print_r($data); // Output response data
            } else {
                echo 'Error fetching data from API';
            }
            $rows = count($data);

            $TTids = array();
            $TTtimes = array();

            $BTids = array();
            $BTtimes = array();

            for ($i = 0; $i < $rows; $i++) {

                $TTimeval =  array_values($data)[$i]["totalTime"];
                //Cursed one-liner to convert the time into seconds that will be converted back to minutes later haha I hate it in here..
                $trueTime = 24*60*(int)substr($TTimeval, 0, 1) + 60*(int)substr($TTimeval, 3, 4) + (int)substr($TTimeval, 6, 7);
                if($trueTime != 0){
                  $TTtimes[] = $trueTime/60;

                  $TTids[] = (int)array_values($data)[$i]['eventId'];


                }

                $BTimeval = array_values($data)[$i]["timeInBathroom"];
                $trueTime = 24*60*(int)substr($BTimeval, 0, 1) + 60*(int)substr($BTimeval, 3, 4) + (int)substr($BTimeval, 6, 7);
                if($trueTime != 0){
                  $BTtimes[] = $trueTime/60;

                  $BTids[] = (int)array_values($data)[$i]['eventId'];

                }

            }

            $TT_avg = ((array_sum($TTtimes))/count($TTtimes));
            $BT_avg = ((array_sum($BTtimes))/count($BTtimes));

            echo "Average time spent: " . number_format($TT_avg, 1) . " minutes <br>";
            echo "Average time spent in bathroom: " . number_format($BT_avg, 1) . " minutes";

        ?>
        <canvas id="myChart"></canvas>
        <button id="TTButton">Total Time</button>
        <button id="TBButton">Time in Bathroom</button>
        <script>
            // Get the context of the canvas element we want to select
            var ctx = document.getElementById('myChart').getContext('2d');
            var title_chart = "Total time spent";

            ctx.width = 300;
            ctx. height = 150;

            var TTids = <?php echo json_encode($TTids); ?>; // IDs for the x-axis
            var TTtimes = <?php echo json_encode($TTtimes); ?>; // Times in minutes for the y-axis

            var BTids = <?php echo json_encode($BTids); ?>; // IDs for the x-axis
            var BTtimes = <?php echo json_encode($BTtimes); ?>; // Times in minutes for the y-axis

            // Function to create the chart
        function createChart(labels, data) {
            return new Chart(ctx, {
                type: 'line', // You can change this to 'bar' or 'line' depending on your preference
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Time (Minutes)',
                        data: data,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)', // Background color (for bar chart)
                        borderColor: 'rgba(75, 192, 192, 1)', // Border color
                        borderWidth: 1 // Border width
                    }]
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: title_chart,
                            font: {
                              size: 20 // Set the font size here
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'ID'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Time (Minutes)'
                            },
                            beginAtZero: true // Ensure the y-axis starts at zero
                        }
                    }
                }
            });
        }

        // Create the initial chart
        var myChart = createChart(TTids, TTtimes);

        // Add event listener to the button to switch between plots
        document.getElementById('TTButton').addEventListener('click', function() {
            // Destroy the old chart
            myChart.destroy();
            title_chart = "Total time spent";
            myChart = createChart(TTids, TTtimes);
        });
        document.getElementById('TBButton').addEventListener('click', function() {
            // Destroy the old chart
            myChart.destroy();
            title_chart = "Total time spent in bathroom";
            myChart = createChart(BTids, BTtimes);
        });
        </script>
    </center>

</body>
</html>
