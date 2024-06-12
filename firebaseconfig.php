<?php
require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

$factory = (new Factory())
    ->withServiceAccount('warehouse-93629-firebase-adminsdk-nh8xv-2f532020c3.json')
    ->withDatabaseUri('https://warehouse-93629-default-rtdb.firebaseio.com/');
/*  
    withServiceAccount dari config API Firebase
    withDatabaseUri dari utl database Firebase
*/
$database = $factory->createDatabase();
