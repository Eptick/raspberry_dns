<?php

require('../vendor/autoload.php');
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));
// 501 Failed mac address check
// 502 Missing parameters in request
// 503 No Status file
// 504 No Data file

// 200 Ok
// 201 Needs approval of ip change

// 303 Logic Error

// 404 Pi not logged in

/* 
 * Functions 
 */
function Respond( $data = null , $code = 303 ) {
  if(is_null($data)) $data = array("status" => $code);
  $response = new Response(
    json_encode( $data ),
    Response::HTTP_OK,
    array('content-type' => 'application/json')
  );
  return $response;
}
function Status() {
  try{
    $status_json = @file_get_contents("status.json");
    $status = json_decode($status_json, true);
  } catch(Exception $e) {
    $status = false;
  }
  if(!$status)
    file_put_contents("status.json", json_encode( array("status" => 504) ) );
  return $status;
}
function Data(){
  try{
    $data_json = @file_get_contents("data.json");
    $data = json_decode($data_json, true);
  } catch(Exception $e) {
    $data = false;
  }
  if(!$data){
    file_put_contents("status.json", json_encode( array("status" => 504) ) );
  }
  return $data;
}

/* 
 * End Functions 
 */


// Our web handlers

// TODO Ovdje ide view za obavještavanje i to sve živo
$app->get('/', function(Request $request) use($app) {
  $app['monolog']->addDebug('logging output.');
  $status = Status();
  if(!$status) return Respond(null, 503);

  switch($status["status"]){
    case 201:
      $data = Data();
      if(!$data) return Respond( array('status'=>404) );
      $response_data = $status;
      $response_data["data"] = $data;

      return $app->redirect("http://".$data["ip"]);
    break;
    case 504:
      // No data file view
      return Respond($status);
    default:
      return Respond($status);
    break;
  }

  return $app->redirect("http://www.google.com");
});

$app->post('/', function(Request $request) use($app) {
  $app['monolog']->addDebug('logging output.');
  $data = $request->request->all();
  if(!is_null(@$data['mac'])){
    if($data['mac'] == "") {
      // Prošla provjera mac adrese
      if(!is_null(@$data['ip']) ) {
        if(!is_null(@$data["ssid"])){
            // Prošlo sve provjere zapiši u temp file i status treba biti da se treba odobriti promjena ip adrese mail ili interface?
            unset($data["mac"]);
            file_put_contents("data.json", json_encode( $data ) );
            file_put_contents("status.json", json_encode( array("status" => 201) ) );
            $data = array( 'status' => 200 );
        } else $data = array('status' => 502);
      } else $data = array('status' => 502);
    } else $data = array('status' => 501);
  } else $data = array('status' => 502);

  return Respond( $data );

});

$app->get('/discover', function(Request $request) use($app) {
  $app['monolog']->addDebug('logging output.');

  $response_data = array();
  $status = Status();
  if(!$status) return Respond(null, 503);

  switch($status["status"]){
    case 201:
      return Respond( $status );
    break;
    case 200:
      $data = Data();
      if(!$data) return Respond( array('status'=>404) );
      $response_data = $status;
      $response_data["data"] = $data;

      return Respond( $response_data );
    default:
      return Respond($status);
  }
});
$app->get("/test", function() use($app){
  return var_dump( getenv("RPI_MAC", true) );
});

$app->run();
