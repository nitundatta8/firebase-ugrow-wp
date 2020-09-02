<?php
/**
 * Plugin Name: Add Channel Actions
 * Plugin URI: #
 * Description: Everything that has to happen when a channel is created
 * Version: 1.0
 * Author: Ugrow
 * Author URI: #
 */
 
require( dirname(__FILE__) . '/../../../wp-load.php' );
    







// ==============================================================================================================================================================================================================================================================================================
// ============================================================================================================================================================================================================================================================================================== 
//                  
//                      NEW LIVE STREAM POST CREATION
//                      AUTHORED BY UGrow
//                      VERSION 1.0
//                  
// ==============================================================================================================================================================================================================================================================================================
// ==============================================================================================================================================================================================================================================================================================

/* -------------------------------------------- */
      /* ADD LIVE STREAM FROM CHANNEL */
/* -------------------------------------------- */

add_action('cred_before_save_data', 'add_live_stream_post',10,1);
function add_live_stream_post($form_data) {
    if ( is_user_logged_in() ) {
        if ($form_data['id']=='386') {
            // Get current user
            $current_user = get_current_user_id();

            // Create post 
            $my_post = array(
              'post_title'    => $current_user,
              //'post_content'  => $_POST['post_content'],
              'post_status'   => 'publish',
              'post_author'   => $current_user,
              'post_type' => 'live-stream'
            );
              
            // Insert the post into the database
            wp_insert_post( $my_post );
        }
    }
}











// ==============================================================================================================================================================================================================================================================================================
// ============================================================================================================================================================================================================================================================================================== 
//                  
//                      VIMEO SHOWCASE CREATION
//                      AUTHORED BY UGrow
//                      VERSION 1.0
//                  
// ==============================================================================================================================================================================================================================================================================================
// ==============================================================================================================================================================================================================================================================================================

/* -------------------------------------------- */
      /* ADD VIMEO SHOWCASE FROM CHANNEL */
/* -------------------------------------------- */

function add_vimeo_assets() {
    add_action('cred_before_save_data', 'add_vimeo_showcase_asset',10,1);
        function add_vimeo_showcase_asset($form_data) {
            if ( is_user_logged_in() ) {
                if ($form_data['id']=='386') {
                    
                    $current_user = wp_get_current_user();

                    $data = array(
                        'user_id' => '111828492',
                        'name' => $current_user->ID . ' Showcase',
                        'description' => 'description goes here',
                        'privacy' => 'embed_only'
                    );
                     
                    $payload = json_encode($data);
                     
                    // Prepare new cURL resource
                    $ch = curl_init('https://api.vimeo.com/me/albums');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                     
                    // Set HTTP Header for POST request 
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Authorization: Bearer 2d5b1461e957305ffc81def0383fe3a0'
                        )
                    );
                     
                    // Submit the POST request
                    $result = curl_exec($ch);
                     
                    // Close cURL session handle
                    curl_close($ch);                    
                    
                    
                    
                    
                    
                }
            } else { }
        }
}

add_shortcode( 'ugrow_vimeo_assets', 'add_vimeo_assets' );









// ==============================================================================================================================================================================================================================================================================================
// ============================================================================================================================================================================================================================================================================================== 
//                  
//                      INPLAYER ASSET & PACKAGE CREATION
//                      AUTHORED BY NITUN DATTA & UGrow
//                      VERSION 1.0
//                  
// ==============================================================================================================================================================================================================================================================================================
// ==============================================================================================================================================================================================================================================================================================



/* -------------------------------------------- */
      /* ADD INPLAYER ASSET FROM CHANNEL */
/* -------------------------------------------- */

add_filter( 'wp_insert_post_data' , 'filter_channel_post_data' , '99', 2 );

 function filter_channel_post_data( $data , $postarr ) {

    if ($data['id']=='386'){
     $data['gallery_asset_Id'] = 333;
    }else{
      error_log("gallery_asset_Id not set yet ". $data['gallery_asset_Id']);
    }
    error_log("Complete ". $data['gallery_asset_Id']);
     return $data;
}


function add_inplayer_package(){
    add_action('cred_before_save_data', 'create_ugrow_package',10,1);
    function create_ugrow_package($form_data){
      if ( is_user_logged_in() ) {
        if ($form_data['id']=='386') {
          $logs="";
          $tmp = fopen(dirname(__file__).'/logs.txt', "a+"); 
          
          try {
            
           
              $gallaryAssetId = create_gallary();
             
              $liveStreamAssetId= create_livestream();
               
              $packageId = create_package();
              
              $r1 = add_item_in_package_api($packageId,$gallaryAssetId,$liveStreamAssetId);
              fwrite($tmp,"\r\n\r\n add_item_in_package_api, $packageId, $gallaryAssetId, $liveStreamAssetId  ,r1:".print_r($r1, true));
              $r2 = add_package_price($packageId);
              fwrite($tmp,"\r\n\r\n add_package_price r2: ". print_r($r2, true));
             
            
            } catch (Exception $e) {
              fwrite($tmp,"\r\n\r\n Error".$e->getMessage());
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
            fclose($tmp);
           
    
    
        }
        else{
          echo '<script>alert("Not saved yet")</script>'; 
        }
      }
    
    }
 
/*-------------------------------------------*/
       /*CREATE PACKAGE WITH ASSETS */
/* -------------------------------------------- */



    function create_gallary(){
      return create_item("html_asset", "Gallery" );
      } 
            
            
    function create_livestream(){
      return create_item("html_asset", "Live Stream" );
      }
          
    function create_package(){
      return create_item("package", "Package" );
      }     
        

    function create_item($item_type,$title){
      $current_user = wp_get_current_user(); 
      $new_title = $current_user->ID.' -'.$title;
      $response =  create_item_api($item_type, $new_title );
      return $response['id'];
    }
 






/*-------------------------------------------------------*/
       /*        CREATE ASSET API         */
       /*    1 - Paid ("name")
             2 - Code ("id")
             3 - Auth ("auth") 
             access_control_type_id=3 */
/*-------------------------------------------------------*/
function  create_item_api($item_type,$title){
             
      $curl = curl_init();
            
      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://services.inplayer.com/v2/items",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",             
        CURLOPT_POSTFIELDS => 'item_type='.$item_type.'&title=' .$title.'&access_control_type_id=3&content=<div id="output"></div>
        <script type="text/javascript" src="http://stagingone.ugrow.tv/wp-content/themes/vimeo/gallery/bundle.js"></script>',
        CURLOPT_HTTPHEADER => array(
          "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImp0aSI6ImVkNDFjNjU3LTk5ZjAtNGQ3ZC05N2RhLTgyYzliNTU2NGZhYSJ9.eyJhdWQiOiIzYjM5YjVhYi1iNWZjLTRiYTMtYjc3MC03MzE1NWQyMGU2MWYiLCJqdGkiOiJlZDQxYzY1Ny05OWYwLTRkN2QtOTdkYS04MmM5YjU1NjRmYWEiLCJpYXQiOjE1OTgwMjQ3NjgsIm5iZiI6MTU5ODAyNDc2OCwiZXhwIjoxNjAwNjIwMzY4LCJzdWIiOiJpbmZvQGVhc3lpbnRlcmZhY2UuaW8iLCJzY29wZXMiOltdLCJtaWQiOjEsImFpZCI6MzU3MzQ1OCwibXVpIjoiM2IzOWI1YWItYjVmYy00YmEzLWI3NzAtNzMxNTVkMjBlNjFmIiwiY3R4IjpbIm1lcmNoYW50Il0sInRpZCI6MzU3MzQ1OCwidHV1aWQiOiJlZDQxYzY1Ny05OWYwLTRkN2QtOTdkYS04MmM5YjU1NjRmYWEiLCJvaWQiOjB9.CU0laAJpuKqE0e3-gwraT0sGbCj6P_VAceiSiZ1S034",
          "Content-Type: application/x-www-form-urlencoded"
        ),
      ));
      
      // Submit the POST request
      $response = curl_exec($curl);
      $data = json_decode($response,true);
      return $data;
 }

/*---------------------- -------------------------------*/
      /*          ADD ITEM TO THE PACKAGE       */
/*-----------------------------------------------------*/
function add_item_in_package_api($packageId,$gallaryAssetId,$liveStreamAssetId){
  $curl = curl_init();
            
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://services.inplayer.com/v2/items/packages/".$packageId."/bulk",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "PATCH ",
    CURLOPT_POSTFIELDS => 'add_asset_ids[0]='.$gallaryAssetId.'&add_asset_ids[1]='.$liveStreamAssetId,
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImp0aSI6ImVkNDFjNjU3LTk5ZjAtNGQ3ZC05N2RhLTgyYzliNTU2NGZhYSJ9.eyJhdWQiOiIzYjM5YjVhYi1iNWZjLTRiYTMtYjc3MC03MzE1NWQyMGU2MWYiLCJqdGkiOiJlZDQxYzY1Ny05OWYwLTRkN2QtOTdkYS04MmM5YjU1NjRmYWEiLCJpYXQiOjE1OTgwMjQ3NjgsIm5iZiI6MTU5ODAyNDc2OCwiZXhwIjoxNjAwNjIwMzY4LCJzdWIiOiJpbmZvQGVhc3lpbnRlcmZhY2UuaW8iLCJzY29wZXMiOltdLCJtaWQiOjEsImFpZCI6MzU3MzQ1OCwibXVpIjoiM2IzOWI1YWItYjVmYy00YmEzLWI3NzAtNzMxNTVkMjBlNjFmIiwiY3R4IjpbIm1lcmNoYW50Il0sInRpZCI6MzU3MzQ1OCwidHV1aWQiOiJlZDQxYzY1Ny05OWYwLTRkN2QtOTdkYS04MmM5YjU1NjRmYWEiLCJvaWQiOjB9.CU0laAJpuKqE0e3-gwraT0sGbCj6P_VAceiSiZ1S034",
      "Content-Type: application/x-www-form-urlencoded"
    ),
  ));
  
  $response = curl_exec($curl);
  $data = json_decode($response,true);
  return $data;
  
}

/*--------------------------------------------------------------*/
          /*        PACKAGE WITH PRICE     */
/*--------------------------------------------------------------*/

function add_package_price($packageId){
  $curl = curl_init();
  
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://services.inplayer.com/v2/items/".$packageId."/access-fees",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS =>'access_type_id=3&amount=20&currency=USD&description=Simple Access Fee',
                          
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImp0aSI6ImVkNDFjNjU3LTk5ZjAtNGQ3ZC05N2RhLTgyYzliNTU2NGZhYSJ9.eyJhdWQiOiIzYjM5YjVhYi1iNWZjLTRiYTMtYjc3MC03MzE1NWQyMGU2MWYiLCJqdGkiOiJlZDQxYzY1Ny05OWYwLTRkN2QtOTdkYS04MmM5YjU1NjRmYWEiLCJpYXQiOjE1OTgwMjQ3NjgsIm5iZiI6MTU5ODAyNDc2OCwiZXhwIjoxNjAwNjIwMzY4LCJzdWIiOiJpbmZvQGVhc3lpbnRlcmZhY2UuaW8iLCJzY29wZXMiOltdLCJtaWQiOjEsImFpZCI6MzU3MzQ1OCwibXVpIjoiM2IzOWI1YWItYjVmYy00YmEzLWI3NzAtNzMxNTVkMjBlNjFmIiwiY3R4IjpbIm1lcmNoYW50Il0sInRpZCI6MzU3MzQ1OCwidHV1aWQiOiJlZDQxYzY1Ny05OWYwLTRkN2QtOTdkYS04MmM5YjU1NjRmYWEiLCJvaWQiOjB9.CU0laAJpuKqE0e3-gwraT0sGbCj6P_VAceiSiZ1S034",
      "Content-Type: application/x-www-form-urlencoded"
    ),
  ));
  $response = curl_exec($curl);
  $data = json_decode($response,true);
  return $data;
}

}


add_shortcode( 'ugrow_inplayer_package', 'add_inplayer_package' );










// ==============================================================================================================================================================================================================================================================================================
// ============================================================================================================================================================================================================================================================================================== 
//                  
//                      DESCRIPTION
//                      AUTHORED BY UGrow
//                      VERSION 1.0
//                  
// ==============================================================================================================================================================================================================================================================================================
// ==============================================================================================================================================================================================================================================================================================





?>