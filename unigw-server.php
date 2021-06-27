<?php

# SWOOLE_BASE: reactor based mode, the business logic is running in the reactor
# SWOOLE_PROCESS: multiple process mode, the business logic is running in child processes, the default running mode of server
# SWOOLE_BASE can be used for asynchronous operations (need to test properly)
# There must be no Locking
# Benchmark shows SWOOLE_BASE has better performance in Hello World benchmark (where there is No Blocking)

# Protocol specs: unix, http, https, tcp, udp, ws (websocket), mqtt

$svc_map = [
  'http' => 'http',
  'https' => 'http',
  'tcp' => 'tcp',
  'tcps' => 'tcp',
  'udp' => 'udp',
  'udps' => 'udps',
  'mqtt' => 'mqtt',
  'ws' => 'ws',
];
$svc_opts = [
  'http' => [
    'http_parse_post' => true,
    #'worker_num' => 4*16,
    #'worker_num' => 16,
    #'worker_num' => 1,
    #'reactor_num' => 16,
    'open_cpu_affinity' => 1,
    'enable_reuse_port' => true,
    'open_http_protocol' => true,
    'open_http2_protocol' => true,
    'open_websocket_protocol' => true,
    #'ssl_key_file' => 'privatekey.pem',
    #'ssl_cert_file' => 'publickey.pem',
  ],
  'tcp' => [],
  'udp' => [],
  'mqtt' => ['open_mqtt_protocol' => true],
];

$svc_listen = [
  'apigw_http' => [ 'http://unix:/tmp/apigw-http.sock', 'http://0.0.0.0:7014', 'http://[::]:7014', ],
];

$svc_mode = SWOOLE_BASE;
#$svc_mode = SWOOLE_PROCESS;

date_default_timezone_set('Asia/Dhaka');

$server = false;

$srv = false;
foreach ($svc_listen as $k => $v) {

  if ($srv != false) break; # only one instance of server can run

  foreach ($v as $lspec) {
    $parsed = parse_url($lspec);
    $scheme = $parsed['scheme'];
    $host = $parsed['host'];
    $port = $parsed['port'] ?? 0;
    $path = $parsed['path'] ?? '';

    switch ($scheme) {
      case 'http':
      case 'https':
        $type = SWOOLE_SOCK_TCP;
        if ($path != '') {
          $host = $path;
          $port = 0;
          $type = SWOOLE_UNIX_STREAM;
          @unlink($path);
        } else if (substr($host, 0, 1) == '[') { # ipv6 address
          $host = trim($host, '[]'); # remove the []
          $type = SWOOLE_SOCK_TCP6;
        }

        if ($scheme == 'https') $type |= SWOOLE_SSL;

        if ($srv == false) {
          $srv = new Swoole\WebSocket\Server($host, $port, $svc_mode, $type);
        } else {
          $srv->addListener($host, $port, $type);
        }
      break;

      case 'tcp':
        $type = SWOOLE_SOCK_TCP;
        if ($path != '') {
          $host = $path;
          $port = 0;
          $type = SWOOLE_UNIX_STREAM;
          @unlink($path);
        } else if (substr($host, 0, 1) == '[') { # ipv6 address
          $host = trim($host, '[]'); # remove the []
          $type = SWOOLE_SOCK_TCP6;
        }

        if ($scheme == 'tcps') $type |= SWOOLE_SSL;

        if ($srv == false) {
          $srv = new Swoole\Server($host, $port, $svc_mode, $type);
        } else {
          $srv->addListener($host, $port, $type);
        }
      break;

      case 'udp':
        $type = SWOOLE_SOCK_UDP;
        if ($path != '') {
          $host = $path;
          $port = 0;
          $type = SWOOLE_UNIX_DGRAM;
          @unlink($path);
        } else if (substr($host, 0, 1) == '[') { # ipv6 address
          $host = trim($host, '[]'); # remove the []
          $type = SWOOLE_SOCK_UDP6;
        }

        if ($scheme == 'udps') $type |= SWOOLE_SSL;

        if ($srv == false) {
          $srv = new Swoole\Server($host, $port, $svc_mode, $type);
        } else {
          $srv->addListener($host, $port, $type);
        }
      break;
      
      default:
      break;
    }
    echo $lspec . "\n";
  }
  $server = $srv;
  $svc_type = $svc_map[$scheme];
  $server->set($svc_opts[$svc_type]);
}

if ($server == false) die("No Server!\n");


$server->on('workerstart', function ($server, $id) use (&$fnlist) {
  echo "Worker started: id=${id}.\n";
});
$server->on('workerstop', function ($server, $id) {
  echo "Worker stopped: id=${id}.\n";
});

$fnlist = [];
$fnlist['__ds'] = []; # data structures

# load core services
require('services/core/core.php');
require('services/core/cache.php');
require('services/core/session.php');
require('services/core/pool.php');
require('services/core/mysqlpool.php');
#require('services/core/vars.php');
require('services/unigw/unigw.php');
require('services/network/httpc.php');

# load application services
require('services/app1/app.php');
# call initialization of applications
$fnlist['app1']['init'](); # Initialize application app1
# app 'app1' routine ends



// http && http2
$server->on('request', function (Swoole\Http\Request $request, Swoole\Http\Response $response) use (&$fnlist) {
  
  $__uri = $request->server['request_uri'];
  if(strpos($__uri,'favicon.ico') === false){
    $uparts = preg_split('@/@', $__uri, -1, PREG_SPLIT_NO_EMPTY);
    
    $ns       = $uparts[0] ?? '-';
    $ep       = $uparts[1] ?? '-';
    $fn       = $uparts[2] ?? '-';
    $getargs  = $request->get ?? [];
    $postargs = $request->post ?? [];
    $reqargs  = array_merge($getargs, $postargs);

    $env = [];
    #$env['server']['HRTIME'] = hrtime(true);
    #$env['server']['REMOTE_ADDR'] = $request->header['x-real-ip'] ?? null;
    #$env['server']['HTTP_HOST'] = $request->header['x-forwarded-host'] ?? null;
    #$env['server']['HTTPS'] = ($request->header['x-forwarded-proto'] ?? 'http') == 'https'? 'on' : 'off';
    #$env['server']['TIME'] = $request->server['request_time'] ?? time();

    $args = [ 'app' => $ns, 'ep' => $ep, 'fn' => $fn, 'request' => $request, 'response' => $response, 'getargs' => $getargs, 'postargs' => $postargs, 'reqargs' => $reqargs, 'env' => $env ];
    list($code, $data) = $fnlist['app1']['process']($args);

    /* not meant to serve static contents */
    $response->header("Cache-Control", "no-store");
    $response->header("Expires", "Thu, 19 Nov 1981 08:52:00 GMT"); # some really old day
    $response->header("Pragma", "no-cache");
    #$response->status($code);
    $response->status(200); # We are sending 200 always, so that the client doesn't consider the error otherwise
    $response->end($data);
  }
});


// websocket
$server->on('open', function (Swoole\WebSocket\Server $server, Swoole\Http\Request $request) use (&$fnlist) {
  echo "websocket connection open: {$request->fd}\n";
  /*
  $server->tick(1000, function() use ($server, $request) {
    $server->push($request->fd, json_encode(['hello', time()]));
  });
  */
});

// websocket
$server->on('message', function (Swoole\WebSocket\Server $server, Swoole\WebSocket\Frame $frame) use (&$fnlist) {
  echo "message on websocket: {$frame->fd}\n";
  $server->push($frame->fd, 'Hello ' . $frame->data);
});

echo "Starting Server...\n";
$server->start();

die();

?>
