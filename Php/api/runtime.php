<?php

require_once '../includes/Database.php';

$db = new Database();

if (isset($_GET['overview']))
{
    $data = $db->query('CALL `geyser_pi`.`getRuntimeOverview`()');
    if ($data->num_rows > 0)
    {
        $json = null;
        while ($row = $data->fetch_assoc())
        {
            $json[] = $row;
        }
        echo json_encode($json);
    }
}

if (isset($_GET['today']))
{
    $data = $db->query('CALL `geyser_pi`.`getRuntimeForToday`()');
    if ($data->num_rows > 0)
    {
        $json = null;
        while ($row = $data->fetch_assoc())
        {
            $json[] = $row;
        }
        echo json_encode($json);
    }
}

if (isset($_GET['yesterday']))
{
    $data = $db->query('CALL `geyser_pi`.`getRuntimeForYesterday`()');
    if ($data->num_rows > 0)
    {
        $json = null;
        while ($row = $data->fetch_assoc())
        {
            $json[] = $row;
        }
        echo json_encode($json);
    }
}

if (isset($_GET['date']))
{
    $data = $db->query('CALL `geyser_pi`.`getRuntimeForDate`("' . $_GET['date'] . '")');
    if ($data->num_rows > 0)
    {
        $json = null;
        while ($row = $data->fetch_assoc())
        {
            $json[] = $row;
        }
        echo json_encode($json);
    }
}