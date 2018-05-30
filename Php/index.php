<?php include_once '_header.php'; ?>
<section>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li role="presentation" class="active"><a href="#">Overview</a></li>
                <li role="presentation"><a href="history.php">History</a></li>
                <li role="presentation"><a href="schedule.php">Schedule</a></li>
                <li role="presentation"><a href="setup.php">Setup</a></li>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4>Temperature</h4>
                </div>
                <div class="panel-body">
                    <canvas id="temperatureChart"></canvas>
                </div>
            </div>
            <br>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4>Runtime</h4>
                </div>
                <div class="panel-body">
                    <canvas id="runtimeChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4>System Status</h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6 col-xs-4 text-right">Heating Element</div>
                        <div class="col-md-6 col-xs-8">
                            <div class="btn-group" role="group" aria-label="...">
                                <button id="btnElementAuto" onclick="btnElementAutoClick()" type="button" class="btn btn-default">Auto</button>
                                <button id="btnElementOn" onclick="btnElementOnClick()" type="button" class="btn btn-default">On</button>
                                <button id="btnElementOff" onclick="btnElementOffClick()" type="button" class="btn btn-default">Off</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xs-4 text-right">Circulation Pump</div>
                        <div class="col-md-6 col-xs-8">
                            <div class="btn-group" role="group" aria-label="...">
                                <button id="btnPumpAuto" onclick="btnPumpAutoClick()" type="button" class="btn btn-default">Auto</button>
                                <button id="btnPumpOn" onclick="btnPumpOnClick()" type="button" class="btn btn-default">On</button>
                                <button id="btnPumpOff" onclick="btnPumpOffClick()" type="button" class="btn btn-default">Off</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xs-4 text-right">Holiday Mode</div>
                        <div class="col-md-6 col-xs-8">
                            <div class="btn-group" role="group" aria-label="...">
                                <button id="btnHolidayAuto" type="button" class="btn btn-default disabled">Auto</button>
                                <button id="btnHolidayOn" onclick="btnHolidayOnClick()" type="button" class="btn btn-default">On</button>
                                <button id="btnHolidayOff" onclick="btnHolidayOffClick()" type="button" class="btn btn-default">Off</button>
                            </div>
                        </div>
                    </div>
			        <div class="row">
                        <div class="col-md-6 col-xs-4 text-right">Geyser</div>
                        <div class="col-md-6 col-xs-8">
                        	<span id="currentGeyserTemp">Loading...</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xs-4 text-right">Reservoir</div>
                        <div class="col-md-6 col-xs-8">
                            <span id="currentReservoirTemp">Loading...</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xs-4 text-right">Pump Runtime</div>
                        <div class="col-md-6 col-xs-8">
                            <span id="pumpRuntime">Loading...</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-xs-4 text-right">Element Runtime</div>
                        <div class="col-md-6 col-xs-8">
                            <span id="elementRuntime">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
<?php include_once '_footer.php'; ?>
<script>

    function getSystemStatus()
    {
        $.getJSON('/api/system.php?overview', function(data){
            setSystemButtons(data);
        });
    }

    function getTemperatures()
    {
        $.getJSON('/api/temperature.php?latest', function(data){
            $('#currentGeyserTemp').html(Math.round(parseFloat(data.geyser_temp)) + ' &deg; C');
            $('#currentReservoirTemp').html(Math.round(parseFloat(data.reservoir_temp)) + ' &deg; C');
        });
    }

    function initCharts()
    {
        $.getJSON('/api/temperature.php?overview', function(data){
            var geyserData = new Array();
            var reservoirData = new Array();
            var chartLabels = new Array();

            data.forEach(function(row)
            {
                geyserData.push(row.geyser_temp);
                reservoirData.push(row.reservoir_temp);
                chartLabels.push(row.created_at);
            });

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
                }});
        });

        $.getJSON('/api/runtime.php?overview', function(data){
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
                }});
        });
    }

    function setSystemButtons(data)
    {
        if (data.element_auto === '1')
        {
            $('#btnElementAuto').removeClass('btn-default').addClass('btn-warning');
        } else {
            $('#btnElementAuto').removeClass('btn-warning').addClass('btn-default');
        }

        if (data.element_on === '1')
        {
            $('#btnElementOn').removeClass('btn-default').addClass('btn-danger');
            $('#btnElementOff').removeClass('btn-success').addClass('btn-default');
        } else {
            $('#btnElementOn').removeClass('btn-danger').addClass('btn-default');
            $('#btnElementOff').removeClass('btn-default').addClass('btn-success');
        }

        if (data.pump_auto === '1')
        {
            $('#btnPumpAuto').removeClass('btn-default').addClass('btn-warning');
        } else {
            $('#btnPumpAuto').removeClass('btn-warning').addClass('btn-default');
        }

        if (data.pump_on === '1')
        {
            $('#btnPumpOn').removeClass('btn-default').addClass('btn-danger');
            $('#btnPumpOff').removeClass('btn-success').addClass('btn-default');
        } else {
            $('#btnPumpOn').removeClass('btn-danger').addClass('btn-default');
            $('#btnPumpOff').removeClass('btn-default').addClass('btn-success');
        }

        if (data.holiday_on === '1')
        {
            $('#btnHolidayOn').removeClass('btn-default').addClass('btn-danger');
            $('#btnHolidayOff').removeClass('btn-success').addClass('btn-default');
        } else {
            $('#btnHolidayOn').removeClass('btn-danger').addClass('btn-default');
            $('#btnHolidayOff').removeClass('btn-default').addClass('btn-success');
        }

        if (data.pump_runtime)
        {
            $('#pumpRuntime').html(data.pump_runtime.substr(0,5));
        }

        if (data.element_runtime)
        {
            $('#elementRuntime').html(data.element_runtime.substr(0,5));
        }
    }


    function btnElementAutoClick() {
        if( $('#btnElementAuto').hasClass('btn-warning'))
        {
            $.get('/api/element.php?manual');
        } else {
            $.get('/api/element.php?auto');
        }
    }

    function btnElementOnClick() {
        $.get('/api/element.php?on');
    }

    function btnElementOffClick() {
        $.get('/api/element.php?off');
    }

    function btnPumpAutoClick() {
        if( $('#btnPumpAuto').hasClass('btn-warning'))
        {
            $.get('/api/pump.php?manual');
        } else {
            $.get('/api/pump.php?auto');
        }
    }

    function btnPumpOnClick() {
        $.get('/api/pump.php?on');
    }

    function btnPumpOffClick() {
        $.get('/api/pump.php?off');
    }

    function btnHolidayOnClick() {
        $.get('/api/holiday.php?on');
    }

    function btnHolidayOffClick() {
        $.get('/api/holiday.php?off');
    }

    // function btnShutdownClick() {
    //     $.get('/api/system.php?halt');
    // }

    // function btnRestartClick() {
    //     $.get('/api/system.php?restart');
    // }

    initCharts();

    setInterval(getTemperatures, 1000);

    setInterval(getSystemStatus, 1000);

</script>
