<?php

require_once '../includes/Database.php';

$db = new Database();

function formatData($data)
{
    return $data;
}

if (isset($_GET['overview']))
{
    $data = $db->query('select * from temperature_schedule');

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

if (isset($_GET['update']))
{
    foreach ($_POST as $key => $value)
    {
        $day = substr($key, 5,1);
        $hour = substr($key, 7, 2);
        $db->query('update temperature_schedule set temperature = ' . $value . ' where day = ' . $day . ' and hour = ' . $hour);
    }
}