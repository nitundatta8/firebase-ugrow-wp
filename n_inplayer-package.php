<?php
/**
 * Plugin Name: n_inplayer-package
 * Plugin URI: #
 * Description: Making API calls with cURL
 * Version: 1.0
 * Author: Ugrow
 * Author URI: #
 */
require( dirname(__FILE__) . '/../../../wp-load.php' );
require('n_wpfirebase.php');  

/* -------------------------------------------- */
      /* ADD INPLAYER ASSET FROM CHANNEL */
/* -------------------------------------------- */

// add_filter( 'wp_insert_post_data' , 'filter_channel_post_data' , '99', 2 );

//  function filter_channel_post_data( $data , $postarr ) {

//     if ($data['id']=='386'){
//      $data['gallery_asset_Id'] = 333;
//     }else{
//       error_log("gallery_asset_Id not set yet ". $data['gallery_asset_Id']);
//     }
//     error_log("Complete ". $data['gallery_asset_Id']);
//      return $data;
// }

// update_post_meta( $post_id, 'wpcf-overdue', {ID goes here} );


function add_inplayer_package(){
    add_action('cred_save_data', 'create_ugrow_package',10,2);
    function create_ugrow_package($post_id,$form_data){
      if ( is_user_logged_in() ) {
        if ($form_data['id']=='386') {
         
          $tmp = fopen(dirname(__file__).'/logs.txt', "a+");
          try {
            
           
              $gallaryAssetId = create_gallary();

              
             
              $liveStreamAssetId= create_livestream();
               
              $packageId = create_package();
              
              $r1 = add_item_in_package_api($packageId,$gallaryAssetId,$liveStreamAssetId);
              $r2 = add_package_price($packageId);
              error_log( 'Adding asset id into channel assetid: '. $gallaryAssetId." postid:".$post_id);
              update_post_meta( $post_id, 'wpcf-embed-channel', $gallaryAssetId);
              $current_user = wp_get_current_user(); 
              save_channel_info($post_id,  $gallaryAssetId, $current_user->ID);
              fwrite($tmp,"\r\n\r\n add_package_price r2: ". print_r($r2, true));
            
            } catch (Exception $e) {
              
             // error_log( 'Caught exception: ',  $e->getMessage(), "\n");
              fwrite($tmp,"\r\n\r\n Error".$e->getMessage());
               
            }
            
           
            
    
        }
        
      }
    
    }
 
/*-------------------------------------------*/
       /*CREATE PACKAGE WITH ASSETES */
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


?>