<?php

require_once '../includes/Database.php';

$db = new Database();

function formatData($data)
{
    return $data;
}

if (isset($_GET['overview']))
{
    $data = $db->query('select * from system_config');

    if ($data->num_rows > 0)
    {
        echo json_encode(formatData($data->fetch_assoc()));
    }
}

if (isset($_GET['update']))
{
    foreach ($_POST as $key => $value)
    {
        $db->query('update system_config set ' . $key . ' = "' . $value . '"');
    }
}

if (isset($_GET['restart']))
{
    exec('sudo -u root -S shutdown -r now < pi');
}

if (isset($_GET['halt']))
{
    exec('sudo -u root -S shutdown -h now < pi');
}