<?php

/************************************
Entry point of the project.
To be run from the command line.
************************************/

/*define('SQL_HOST', 'mariadb');
define('SQL_USER', 'root');
define('SQL_PWD', 'root');
define('SQL_DB', 'cmc_db');
define('RESSOURCES_DIR', __DIR__ . '/../resources/'); */

define('SQL_HOST', 'localhost');
define('SQL_USER', 'root');
define('SQL_PWD', '');
define('SQL_DB', 'cmc_db');
define('RESSOURCES_DIR', __DIR__ . '/../resources/');


function myAutoload($classname) {
    include_once(__DIR__ . '/' . $classname . '.php');
}
spl_autoload_register('myAutoload');

echo sprintf("Starting...\n");


/* import jobs from regionsjob.xml */
$files = array();
if($dossier = opendir(RESSOURCES_DIR))
    {
        while(($file = readdir($dossier)))
        {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            ($ext != '')?$files[] = RESSOURCES_DIR . $file:'';
        }
     }
     echo print_r($files);
$jobsImporter = new JobsImporter(SQL_HOST, SQL_USER, SQL_PWD, SQL_DB, $files);
$count = $jobsImporter->importJobs();

echo sprintf("> %d jobs imported.\n", $count);


/* list jobs */
$jobsLister = new JobsLister(SQL_HOST, SQL_USER, SQL_PWD, SQL_DB);
$jobs = $jobsLister->listJobs();

echo sprintf("> all jobs (%d):\n", count($jobs));
foreach ($jobs as $job) {
    echo sprintf(" %d: %s - %s - %s\n", $job['id'], $job['reference'], $job['title'], $job['publication']);
}


echo sprintf("Done.\n");
