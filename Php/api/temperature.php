<?php

require_once '../includes/Database.php';

$db = new Database();

function formatData($data)
{
    return $data;
}

if (isset($_GET['latest']))
{
    $data = $db->query('select geyser_temp, reservoir_temp from temperature_values order by created_at desc limit 1');
    if ($data->num_rows > 0)
    {
        echo json_encode(formatData($data->fetch_assoc()));
    }
}

if (isset($_GET['overview']))
{
    $data = $db->query('CALL `geyser_pi`.`getTemperatureOverview`()');
    if ($data->num_rows > 0)
    {
        $json = null;
        while ($row = $data->fetch_assoc())
        {
            $json[] = $row;
        }
        echo json_encode(formatData($json));
    }
}

if (isset($_GET['today']))
{
    $data = $db->query('CALL `geyser_pi`.`getTemperatureForToday`()');
    if ($data->num_rows > 0)
    {
        $json = null;
        while ($row = $data->fetch_assoc())
        {
            $json[] = $row;
        }
        echo json_encode(formatData($json));
    }
}

if (isset($_GET['yesterday']))
{
    $data = $db->query('CALL `geyser_pi`.`getTemperatureForYesterday`()');
    if ($data->num_rows > 0)
    {
        $json = null;
        while ($row = $data->fetch_assoc())
        {
            $json[] = $row;
        }
        echo json_encode(formatData($json));
    }
}

if (isset($_GET['date']))
{
    $data = $db->query('CALL `geyser_pi`.`getTemperatureForDate`("' . $_GET['date'] . '")');
    if ($data->num_rows > 0)
    {
        $json = null;
        while ($row = $data->fetch_assoc())
        {
            $json[] = $row;
        }
        echo json_encode(formatData($json));
    }
}