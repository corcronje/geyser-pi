<?php include_once '_header.php'; ?>
<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li role="presentation"><a href="index.php">Overview</a></li>
                    <li role="presentation"><a href="history.php">History</a></li>
                    <li role="presentation"><a href="schedule.php">Schedule</a></li>
                    <li role="presentation" class="active"><a href="#">Setup</a></li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <span class="pull-left">
                            <h4>Setup</h4>
                        </span>
                        <span class="pull-right"><button id="btnSaveChanges" class="btn btn-default disabled" onclick="saveChanges()">Save Changes</button></span>
                        <span class="clearfix"></span>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Geyser Element</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Maximum Temperature</label>
                                    <input id="geyser_max_temp" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Minimum Temperature</label>
                                <input id="geyser_min_temp" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h4>Reservoir</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Maximum Temperature</label>
                                    <input id="reservoir_max_temp" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Minimum Temperature</label>
                                <input id="reservoir_min_temp" type="text" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h4>Delta-T</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Switching Temperature</label>
                                    <input id="delta_temp" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Back to Auto (minutes)</label>
                                    <input id="auto_timeout" type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <h4>E-Mail</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>SMTP Host</label>
                                    <input id="smtp_host" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>SMTP Port</label>
                                <input id="smtp_port" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>SMTP Username</label>
                                    <input id="smtp_username" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>SMTP Password</label>
                                <input id="smtp_password" type="password" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>From E-Mail Address</label>
                                    <input id="smtp_from_email" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>From Name</label>
                                <input id="smtp_from_name" type="text" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Recipient E-Mail Address</label>
                                    <input id="smtp_recipient_email" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                &nbsp;
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-danger">Shutdown</button>
                                    <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:btnRestartClick()">Reboot</a></li>
                                        <li><a href="javascript:btnShutdownClick()">Power Off</a></li>
                                    </ul>
                                </div>
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

    $('.form-control').on('change', function () {
        $('#btnSaveChanges').removeClass('disabled').removeClass('btn-default').addClass('btn-danger');
        $(this).addClass('alert-danger');
    });

    function saveChanges() {
        var data = {};
        $('.form-control').each(function (e) {
            data[$(this).attr('id')] = $(this).val();
        })
        $.post('/api/system.php?update', data, function (e) {
            getSystemSetup();
        })
    }

   function getSystemSetup()
    {
        $("#btnSaveChanges").removeClass('btn-danger').addClass('btn-default').addClass('disabled');

        $('.form-control').each(function (e) {
            $(this).removeClass('alert-danger');
        });

        $.getJSON('/api/system.php?overview', function(data){
            $('#geyser_max_temp').val(data.geyser_max_temp);
            $('#geyser_min_temp').val(data.geyser_min_temp);
            $('#reservoir_max_temp').val(data.reservoir_max_temp);
            $('#reservoir_min_temp').val(data.reservoir_min_temp);
            $('#smtp_host').val(data.smtp_host);
            $('#smtp_port').val(data.smtp_port);
            $('#smtp_from_email').val(data.smtp_from_email);
            $('#smtp_from_name').val(data.smtp_from_name);
            $('#smtp_username').val(data.smtp_username);
            $('#smtp_password').val(data.smtp_password);
            $('#smtp_recipient_email').val(data.smtp_recipient_email);
            $('#delta_temp').val(data.delta_temp);
            $('#auto_timeout').val(data.auto_timeout);
        });
    }

    function btnShutdownClick() {
        $.get('/api/system.php?halt');
    }

    function btnRestartClick() {
        $.get('/api/system.php?restart');
    }

    getSystemSetup();

</script>