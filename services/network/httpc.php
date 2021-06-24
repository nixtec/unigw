<?php

$fnlist['httpc'] = [

  '__common' => function ($args=null) use (&$fnlist) {

    $url_info = parse_url($args['url']);
    $host = $url_info['host'];
    $path = $url_info['path'];
    $scheme = strtolower($url_info['scheme']);
    $port = $url_info['port'];
    if ($scheme == 'http') {
      $use_ssl = false;
      if ($port == '') $port = 80;
    } else if ($scheme == 'https') {
      $use_ssl = true;
      if ($port == '') $port = 443;
    }
    $query = $url_info['query'];


    $cli = new Swoole\Coroutine\HTTP\Client($host, $port, $use_ssl);
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

    if (isset($args['auth'])) {
      switch ($args['auth']['auth_type']) {
      case 'http_auth_basic':
	$cli->setBasicAuth($args['auth']['auth_user'], $args['auth']['auth_pass']);
	break;
      default:
	break;
      }
    }

    #$cli->set([]); # set configuration
    return [ 200, $cli ];
  },

  '__exec' => function ($args=null) use (&$fnlist) {

    $cli = $args['cli'];
    $cli->setDefer(true);
    $cli->execute($args['path']);
    $cli->close();
  },

  'get' => function ($args=null) use (&$fnlist) {

    list ($code, $cli) = $fnlist['httpc'['__common']($args);
    $cli->setMethod('GET');
    $fnlist['httpc']['__exec']($args);
    return [ 200, $resp ];


  },
  'post' => function ($args=null) use (&$fnlist) {
  },
  '__exec' => function ($args=null) use (&$fnlist) {
  },
];

?>
