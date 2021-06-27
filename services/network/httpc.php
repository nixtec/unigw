<?php


/*
 * requirements of a http call
 */

# following parameters will be passed in '$args' 
# $host, $port, $method, $path, [$data, $headers, $cookies, $auth]
# To examine raw request and response you can use: https://dima.nixtecsys.com/dump-request.php?hello=world

# the implementation is not complete, just for demonstration purpose
$fnlist['httpc'] = [

  # common pre-processing of http request before it's invoked
  # passing 'args' by reference, so that we can store the processed data there for subsequent calls
  '__common' => function (&$args=null) use (&$fnlist) {

    $url_info = parse_url($args['url']);
    $host = $url_info['host'];
    $path = $url_info['path'];
    $scheme = strtolower($url_info['scheme'] ?? 'http');
    $port = $url_info['port'] ?? '';
    if ($scheme == 'http') {
      $args['use_ssl'] = false;
      if ($port == '') $port = 80;
    } else if ($scheme == 'https') {
      $args['use_ssl'] = true;
      if ($port == '') $port = 443;
    }
    $query = $url_info['query'] ?? '';

    $args['host'] = $host;
    $args['port'] = $port;
    $args['path'] = $path . ($query != '' ? '?' . $query : '');

    return [ 200, 'OK' ];
  },

  'get' => function (&$args=null) use (&$fnlist) {
    $resp = '';
    $args['method'] = 'GET';

    list ($code, $msg) = $fnlist['httpc']['__common']($args);
    if ($code == 200) {
      list ($code, $resp) = $fnlist['httpc']['__exec']($args);
    }

    return [ 200, $resp ];

  },

  'post' => function (&$args=null) use (&$fnlist) {
    $resp = '';
    $args['method'] = 'POST';

    list ($code, $msg) = $fnlist['httpc']['__common']($args);
    if ($code == 200) {
      list ($code, $resp) = $fnlist['httpc']['__exec']($args);
    }

    return [ 200, $resp ];

  },

  '__exec' => function ($args=null) use (&$fnlist) {

    $cli = new Swoole\Coroutine\HTTP\Client($args['host'], $args['port'], $args['use_ssl']);
    #$cli->set([]); # set configuration
    $common_headers = [
      'Host' => $host,
      'User-Agent' => 'UNIGW/0.1',
    ];
    if (isset($args['headers']) && is_array($args['headers'])) {
      $headers = array_merge($common_headers, $args['headers']);
    } else {
      $headers = $common_headers;
    }
    $cli->setHeaders($headers);

    if ($isset($args['cookies']) && is_array($args['cookies'])) {
      $cli->setCookies($args['cookies']); # associative array of cookies [ 'a' => 'b' ]
    }

    if (isset($args['auth']) && isset($args['auth']['auth_type'])) {
      switch ($args['auth']['auth_type']) {
        case 'http_auth_basic':
          $cli->setBasicAuth($args['auth']['auth_user'], $args['auth']['auth_pass']); # ensure 'auth_user' and 'auth_pass' is set by caller
        break;

        default:
        break;
      }
    }

    $cli->setMethod($args['method']);
    $cli->setDefer(true);
    if (isset($args['data'])) {
      if (!is_scalar($args['data'])) {
	      $args['data'] = http_build_query($args['data']);
      }
      $cli->setData($args['data']);
    }
    $cli->execute($args['path']);
    $statusCode = $cli->getStatusCode();
    $resp = $cli->getBody();
    $cli->close();

    return [ $statusCode, $resp ];
  },


];

?>
