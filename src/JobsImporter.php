<?php

class JobsImporter
{
    private $db;

    private $file;

    public function __construct($host, $username, $password, $databaseName, $files)
    {
        $this->files = $files;
        
        /* connect to DB */
        try {
            $this->db = new PDO('mysql:host=' . $host . ';dbname=' . $databaseName, $username, $password);
        } catch (Exception $e) {
            die('DB error: ' . $e->getMessage() . "\n");
        }
    }

    public function importJobs()
    {
        $tab = $this->files;
        foreach ($tab as $file) {
            echo $file.' <br>';
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $valide = array('xml', 'json');
            $objectFile = array();
            if (in_array($extension, $valide))
            { 
                if($extension == 'xml'){
                    /* parse XML file */
                    $objectFile = simplexml_load_file($file);
                }else{
                    $objectFile =  file_get_contents($file);
                    $objectFile = json_decode($objectFile);
                   // echo print_r($objectFile->offers);
                }
            }else{
                return 'Extension non valide';
            }
            /* remove existing items */
            //$this->db->exec('DELETE FROM job');

            /* import each item */
            $count = 0;
            if($extension == 'xml'){
                echo 'je suis xml <br>';
                foreach ($objectFile->item as $item) {
                    $this->db->exec('INSERT INTO job (reference, title, description, url, company_name, publication) VALUES ('
                        . '\'' . addslashes($item->ref) . '\', '
                        . '\'' . addslashes($item->title) . '\', '
                        . '\'' . addslashes($item->description) . '\', '
                        . '\'' . addslashes($item->url) . '\', '
                        . '\'' . addslashes($item->company) . '\', '
                        . '\'' . addslashes($item->pubDate) . '\')'
                    );
                    $count++;
                }
            }else if($extension == 'json'){
                echo 'je suis json <br>';
                foreach ($objectFile->offers as $item) {
                    $this->db->exec('INSERT INTO job (reference, title, description, url, company_name, publication) VALUES ('
                        . '\'' . addslashes($item->reference) . '\', '
                        . '\'' . addslashes($item->title) . '\', '
                        . '\'' . addslashes($item->description) . '\', '
                        . '\'' . addslashes($item->link) . '\', '
                        . '\'' . addslashes($item->companyname) . '\', '
                        . '\'' . addslashes($item->publishedDate) . '\')'
                    );
                    $count++;
                }
                $tt = json_decode($objectFile);
            }
            
            echo $count;
        }
    }
}
