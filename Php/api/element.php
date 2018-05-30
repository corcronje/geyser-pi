<?php

require_once '../includes/Database.php';

$db = new Database();

if (isset($_GET['on']))
{
    $db->query('update system_config set element_on = 1');
}

if (isset($_GET['off']))
{
    $db->query('update system_config set element_on = 0');
}

if (isset($_GET['auto']))
{
    $db->query('update system_config set element_auto = 1');
}

if (isset($_GET['manual']))
{
    $db->query('update system_config set element_auto = 0');
}