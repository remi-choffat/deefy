<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';
session_start();

use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\repository\DeefyRepository;

DeefyRepository::setConfig('config.db.ini');

// Crée un dispatcheur et exécute l'action
$action = $_GET['action'] ?? 'null';
$dispatcher = new Dispatcher($action);
$dispatcher->run();