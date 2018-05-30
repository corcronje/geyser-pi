<?php include_once '_header.php'; ?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li role="presentation"><a href="index.php">Overview</a></li>
                    <li role="presentation" class="active"><a href="#">History</a></li>
                    <li role="presentation"><a href="schedule.php">Schedule</a></li>
                    <li role="presentation"><a href="setup.php">Setup</a></li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <span class="pull-left">
                            <h4>Temperature</h4>
                        </span>
                        <span class="pull-right">
                            <div class="btn-group" role="group">
                                <button id="btnCustom" class="btn btn-default" data-date-format="yyyy-mm-dd">Custom</button>
                                <button id="btnYesterday" class="btn btn-default" onclick="getChartDataForYesterday()">Yesterday</button>
                                <button id="btnToday" class="btn btn-default" onclick="getChartDataForToday()">Today</button>
                            </div>
                        </span>
                        <span class="clearfix"></span>
                    </div>
                    <div class="panel-body">
                        <canvas id="temperatureChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                            <h4>Runtime</h4>
                    </div>
                    <div class="panel-body">
                        <canvas id="runtimeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include_once '_footer.php'; ?>
<script>

    var temperatureChart = null;

    $('#btnCustom').datepicker().on('changeDate', function (ev) {
        getTemperatureChartData('date=' + ev.date.toISOString().substring(0,10));
        getRuntimeChartData('date=' + ev.date.toISOString().substring(0,10));
        $('#btnCustom').datepicker('hide');
    });

    function setPeriodButtons(period) {

        $('.btn-success').removeClass('btn-success').addClass('btn-default');

        if (period === 'today')
        {
            $('#btnToday').removeClass('btn-default').addClass('btn-success');
        }

        if (period === 'yesterday')
        {
            $('#btnYesterday').removeClass('btn-default').addClass('btn-success');
        }

        if (period.substr(0,4) === 'date')
        {
            $('#btnCustom').removeClass('btn-default').addClass('btn-success');
        }
    }

    function getRuntimeChartData(period) {
        $.getJSON('/api/runtime.php?' + period, function(data){
            var elementData = new Array();
            var pumpData = new Array();
            var chartLabels = new Array();
            var elementRuntime = 0;
            var pumpRuntime = 0;

            data.forEach(function(row)
            {
                elementData.push(row.element_runtime);
                pumpData.push(row.pump_runtime);
                chartLabels.push(row.created_at);
            });
            initRuntimeChart(chartLabels, elementData, pumpData);
        });
    }

    function getTemperatureChartData(period) {
        $.getJSON('/api/temperature.php?' + period, function(data){
            var geyserData = new Array();
            var reservoirData = new Array();
            var chartLabels = new Array();
            data.forEach(function(row)
            {
                geyserData.push(row.geyser_temp);
                reservoirData.push(row.reservoir_temp);
                chartLabels.push(row.created_at);
            });
            setPeriodButtons(period);
            initTemperatureChart(chartLabels, geyserData, reservoirData);
        });
    }



    function initTemperatureChart(chartLabels, geyserData, reservoirData)
    {
        var ctxTemperatureChart = $('#temperatureChart');
        temperatureChart = new Chart(ctxTemperatureChart, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: "Geyser",
                    borderColor: 'rgb(255, 99, 132)',
                    fill: false,
                    data: geyserData
                }, {
                    label: "Reservoir",
                    borderColor: 'rgb(99, 132, 255)',
                    fill: false,
                    data: reservoirData
                }]
            }
        });
    }

    function initRuntimeChart(chartLabels, elementData, pumpData)
    {
        var ctxRuntimeChart = $('#runtimeChart');
        runtimeChart = new Chart(ctxRuntimeChart, {
            type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: "Element",
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        fill: false,
                        data: elementData
                    }, {
                        label: "Pump",
                        borderColor: 'rgb(99, 132, 255)',
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        fill: false,
                        data: pumpData
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
    }

    function getChartDataForYesterday()
    {
        getTemperatureChartData('yesterday');
        getRuntimeChartData('yesterday');
    }

    function getChartDataForToday()
    {
        getTemperatureChartData('today');
        getRuntimeChartData('today');
    }

    getChartDataForToday();

</script>