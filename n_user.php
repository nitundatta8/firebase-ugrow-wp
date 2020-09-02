<?php

/**
 * Plugin Name: n_user
 * Plugin URI: #
 * Description: Making API calls with cURL
 * Version: 1.0
 * Author: Ugrow
 * Author URI: #
 */
//localy
require_once __DIR__.'/vendor/autoload.php';
//require_once '/home1/ugrow/stagingone.ugrow.tv/wp-content/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;


class Users {
    protected $database;
    protected $dbname = 'users';

    public function __construct(){
        // $acc = ServiceAccount::fromJsonFile(__DIR__ . '/secret/arthursystems-php-tutorials-0066e2bd5954.json');
        // $firebase = (new Factory)->withServiceAccount($acc)->create();
        $factory = (new Factory)->withServiceAccount(__DIR__.'/secret/wpugrow-firebase-adminsdk-8rpfs-89094ccf38.json');

        $this->database = $factory->createDatabase();
    }

    public function get(int $userID = NULL){
        if (empty($userID) || !isset($userID)) { return FALSE; }

        if ($this->database->getReference($this->dbname)->getSnapshot()->hasChild($userID)){
            return $this->database->getReference($this->dbname)->getChild($userID)->getValue();
        } else {
            return FALSE;
        }
    }

    public function insert(array $data) {
        if (empty($data) || !isset($data)) { return FALSE; }

        foreach ($data as $key => $value){
            $this->database->getReference()->getChild($this->dbname)->getChild($key)->set($value);
        }

        return TRUE;
    }

    public function delete(int $userID) {
        if (empty($userID) || !isset($userID)) { return FALSE; }

        if ($this->database->getReference($this->dbname)->getSnapshot()->hasChild($userID)){
            $this->database->getReference($this->dbname)->getChild($userID)->remove();
            return TRUE;
        } else {
            return FALSE;
        }
    }
}



// var_dump($users->insert([
//    '1' => 'John',
//    '2' => 'Doe',
//    '3' => 'Smith'
// ]));

function add_test_wpfirebase(){
  $users = new Users();
  $result = $users->insert([
    '1' => 'John',
    '2' => 'Doe',
    '3' => 'Smith'
 ]);
 
 echo  "<h1>Result:".$result."=".__DIR__."=</h1>";
}

//add_shortcode( 'ugrow_test_wpfirebase', 'add_test_wpfirebase' );

add_test_wpfirebase();

//var_dump($users->get(1));

//var_dump($users->delete(2));

// var_dump($users->insert([
//     '1' => 'John Doe',
// ]));