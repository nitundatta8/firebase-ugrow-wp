<?php
//require_once __DIR__.'/vendor/autoload.php';
require_once '/home1/ugrow/stagingone.ugrow.tv/wp-content/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

function save_channel_info($channelId, $assetId, $userId){
   $dbname = 'Channels';
  $factory = (new Factory)->withServiceAccount(__DIR__.'/secret/wpugrow-firebase-adminsdk-8rpfs-789fe8b634.json');
  
  $database = $factory->createDatabase();
  $data =[
    $channelId.'/channelId' => $channelId, 
    $channelId.'/assetId' => $assetId,
    $channelId.'/userId' => $userId
  ];
  foreach ($data as $key => $value){
    $database->getReference()->getChild($dbname)->getChild($key)->set($value);
  }
 
}

//save_channel_info(1,'Gym','a1','s1');
?>