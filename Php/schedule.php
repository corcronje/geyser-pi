<?php include_once '_header.php'; ?>
<style>
.temperature-value {
    cursor: pointer;
}
.temperature-value:hover {
    background-color: #5c6bc0;
    color: #ffffff;
}

.cold {
    background-color: #4caf50;
    color: #ffffff;
}

.cool {
    background-color: #536dfe;
    color: #ffffff;
}

.luke-warm {
    background-color: #ffeb3b;
    color: #000000;
}

.warm {
    background-color: #ff5722;
    color: #ffffff;
}

.hot {
    background-color: #e53935;
    color: #ffffff;
}
</style>
<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li role="presentation"><a href="index.php">Overview</a></li>
                    <li role="presentation"><a href="history.php">History</a></li>
                    <li role="presentation" class="active"><a href="#">Schedule</a></li>
                    <li role="presentation"><a href="setup.php">Setup</a></li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading"><h4>Schedule</h4></div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">H</th>
                                <th class="text-center">Sun</th>
                                <th class="text-center">Mon</th>
                                <th class="text-center">Tue</th>
                                <th class="text-center">Wed</th>
                                <th class="text-center">Thu</th>
                                <th class="text-center">Fri</th>
                                <th class="text-center">Sat</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php for ($hour = 0; $hour <= 23; $hour++) { ?>
                            <tr>
                                <td class="text-center"><?php echo $hour; ?></td>
                                <?php for ($day = 1; $day <= 7; $day++) { ?>
                                    <td id="temp-<?php echo $day . '-'. $hour; ?>" class="text-center temperature-value">&nbsp;</td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Temperature Select Modal -->
<div id="temperature-select-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Temperature</h4>
      </div>
      <div class="modal-body">
        <p>Select a temperature range.</p>
        <p><button id="cold" class="btn btn-block btn-success" onclick="saveChanges(50)">Cold (50)</button></p>
        <p><button id="cool" class="btn btn-block btn-primary" onclick="saveChanges(55)">Cool (55)</button></p>
        <p><button id="luke" class="btn btn-block btn-warning" onclick="saveChanges(60)">Luke (60)</button></p>
        <p><button id="warm" class="btn btn-block btn-warning" onclick="saveChanges(65)">Warm (65)</button></p>
        <p><button id="hot" class="btn btn-block btn-danger" onclick="saveChanges(70)">Hot (70)</button></p>
        <p>
            <div class="input-group">
                <input id="custom-temp" type="number" class="form-control" placeholder="Custom">
                <div class="input-group-btn">
                    <button id="save-custom" class="btn btn-primary">Save</button>
                </div>
            </div>
        </p>
      </div>
    </div>
    <!-- end of Model content -->
  </div>
</div>
<!-- end of Temperature Select Model content -->
<?php include_once '_footer.php'; ?>
<script>

    var selectedCell = null;

    $('.temperature-value').on('click', function () {
        selectedCell = $(this);
        $('#custom-temp').val(selectedCell.html());
        $('#temperature-select-modal').modal();
    });

    $('#save-custom').on('click', function () {
        var customTemp = $('#custom-temp').val();
        if(customTemp != '')
        {
            saveChanges(customTemp);
        }
    });

    function saveChanges(temperature) {
        
        $('#temperature-select-modal').modal('hide');
        
        selectedCell.html(temperature);
        
        var data = {};
        
        $('.temperature-value').each(function (e) {
            data[$(this).attr('id')] = $(this).html();
        });
        
        $.post('/api/schedule.php?update', data, function (e) {
            getScheduleData();
        });        
    }

    function getScheduleData() {
        $.getJSON('/api/schedule.php?overview', function (data) {
            data.forEach(function(row){

                $('#temp-' + row.day + '-' + row.hour)
                    .removeClass('cool')
                    .removeClass('cold')
                    .removeClass('luke-warm')
                    .removeClass('warm')
                    .removeClass('hot');
                
                $('#temp-' + row.day + '-' + row.hour).html(row.temperature);
                
                if(row.temperature <= 54)
                {
                    $('#temp-' + row.day + '-' + row.hour).addClass('cold');
                }
                
                if(row.temperature >= 55 && row.temperature <= 59)
                {
                    $('#temp-' + row.day + '-' + row.hour).addClass('cool');
                }
                
                if(row.temperature >= 60 && row.temperature <= 64)
                {
                    $('#temp-' + row.day + '-' + row.hour).addClass('luke-warm');
                }
                
                if(row.temperature >= 65 && row.temperature <= 69)
                {
                    $('#temp-' + row.day + '-' + row.hour).addClass('warm');
                }
                
                if(row.temperature >= 70)
                {
                    $('#temp-' + row.day + '-' + row.hour).addClass('hot');
                }
            });
        })
    }

    getScheduleData();

</script>