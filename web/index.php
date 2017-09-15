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

// Our web handlers

$app->get('/', function(Request $request) use($app) {
  $app['monolog']->addDebug('logging output.');
  $parametri = $request->query->all();
  if(!is_null(@$parametri['mac'])){
    // CHECK THE MAC Address
    if(!is_null(@$parametri['ip']) ) {
      // CHECK THE IP

    }
  }
  file_put_contents("data.json", json_encode(array('mac' => 'ab:ab:ga:bc:de:da:ba', 'ip' => '192.168.1.1', 'network_id' => 'homeinternet') ) );

  $response = new Response(
    json_encode(array("status" => "ok") ),
    Response::HTTP_OK,
    array('content-type' => 'application/json')
  );

  return $response;
});
// 501 Failed mac address check
// 502 Missing parameters in request

// 201 Needs approval of ip change
$app->post('/', function(Request $request) use($app) {
  $app['monolog']->addDebug('logging output.');
  $data = $request->request->all();
  if(!is_null(@$data['mac'])){
    if($data['mac'] == "b8:27:eb:cc:02:a4") {
      // Prošla provjera mac adrese
      if(!is_null(@$data['ip']) ) {
        if(!is_null(@$data["ssid"])){
            // Prošlo sve provjere zapiši u temp file i status treba biti da se treba odobriti promjena ip adrese mail ili interface?
            file_put_contents("data.json", json_encode( $data ) );
            file_put_contents("status.json", json_encode( array("status" => 201) ) );
        } else $data = array('status' => 'failed', 'code' => 502);
      } else $data = array('status' => 'failed', 'code' => 502);
    } else $data = array('status' => 'failed', 'code' => 501);
  } else $data = array('status' => 'failed', 'code' => 502);
  

  $response = new Response(
    json_encode(array("status" => "ok") ),
    Response::HTTP_OK,
    array('content-type' => 'application/json')
  );

  return $response;

});

$app->get('/discover', function(Request $request) use($app) {
  $app['monolog']->addDebug('logging output.');

  $response_data = array();
  $status_json = file_get_contents("status.json");
  $status = json_decode($status_json);
  switch($status["status"]){
    case 201:
      $response_data = $status;
    break;
    case 200:
      $response_data = $status;
      $response_data["data"] = json_encode("data.json");
  }

  $response = new Response(
    json_encode( $response_data ),
    Response::HTTP_OK,
    array('content-type' => 'application/json')
  );

  return $response;
});

$app->run();
