<?php

/**
 * Plugin Name: n_user_firestore
 * Plugin URI: #
 * Description: Making API calls with cURL
 * Version: 1.0
 * Author: Ugrow
 * Author URI: #
 */
//localy
//require 'vendor/autoload.php';
require_once '/home1/ugrow/stagingone.ugrow.tv/wp-content/vendor/autoload.php';

use Google\Cloud\Firestore\FirestoreClient;

class Usersfirestore {
    protected $firestore;
    protected $dbname = 'users';

    public function __construct(){
        // $acc = ServiceAccount::fromJsonFile(__DIR__ . '/secret/arthursystems-php-tutorials-0066e2bd5954.json');
        // $firebase = (new Factory)->withServiceAccount($acc)->create();
       // $factory = (new Factory)->withServiceAccount(__DIR__.'/secret/wpugrow-firebase-adminsdk-8rpfs-89094ccf38.json');

       
        $this->firestore = new FirestoreClient([

            "keyFilePath"=>__DIR__ . '/secret/wpugrow-firebase-adminsdk-8rpfs-89094ccf38.json'

        ]);


    }

    public function get(int $userID = NULL){
        $collectionReference = $this->firestore->collection('user');
        $documentReference = $collectionReference->document($userID);
        $snapshot = $documentReference->snapshot();
     
        return $snapshot['name'];
    }

}



// var_dump($users->insert([
//    '1' => 'John',
//    '2' => 'Doe',
//    '3' => 'Smith'
// ]));

function add_test_wpfirestore(){
  $users = new Usersfirestore();
  $result = $users->get(1);
 
 echo  "<h1>Result:".$result."=".__DIR__."=</h1>";
}

add_shortcode( 'ugrow_test_wpfirestore', 'add_test_wpfirestore' );

//add_test_wpfirebase();

//var_dump($users->get(1));

//var_dump($users->delete(2));

// var_dump($users->insert([
//     '1' => 'John Doe',
// ]));