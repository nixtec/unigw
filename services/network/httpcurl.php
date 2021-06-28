<?php

$fnlist['httpcurl'] = [

  '__common' => function(&$args=null) use (&$fnlist){
    if(!isset($args['url'])){
      return [403, 'Curl URL not found.'];
    }
    $url_info = parse_url($args['url']);

    $common_headers = [
      'Host' => $url_info['host'],
      'User-Agent' => 'UNIGW/0.1'
    ];
    if(isset($args['headers']) && is_array($args['headers'])){
      $header = array_merge($common_headers, $args['headers']);
      unset($args['headers']);
    } else{
      $header = $common_headers;
    }
    foreach($header as $k => $v){
      $args['headers'][] = "{$k}: $v";
    }

    if(isset($args['auth']) && isset($args['auth']['auth_type'])){
      switch ($args['auth']['auth_type']){
        case 'http_auth_basic':
          $auth_user = $args['auth']['auth_user'];
          $auth_pass = $args['auth']['auth_pass'];
          unset($args['auth']);
          $args['auth'] = "{$auth_user}:{$auth_pass}";
        break;

        default:
        break;
      }
    }

    return [200, 'ok'];
  },

  'get' => function(&$args=null) use (&$fnlist){
    $args['method'] = 'GET';

    list($code, $resp) = $fnlist['httpcurl']['__common']($args);
    if($code == 200){
      list ($code, $resp) = $fnlist['httpcurl']['__exec']($args);
    }

    return [$code, $resp];
  },

  'post' => function(&$args=null) use (&$fnlist){
    $args['method'] = 'POST';

    list($code, $resp) = $fnlist['httpcurl']['__common']($args);
    if($code == 200){
      list ($code, $resp) = $fnlist['httpcurl']['__exec']($args);
    }

    return [$code, $resp];
  },


  '__exec' => function ($args=null) use ($fnlist){
    /*
     * args: method, url, data(optional), auth(optional), headers(optional)
    */
    $url = $args['url'];

    $ch = curl_init();
    switch($args['method']){
      case "POST":
        curl_setopt($ch, CURLOPT_POST, 1);
        if(isset($args['data']))
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args['data']));
        break;
      case "GET":
        if(isset($args['data']))
          $url = sprintf("%s?%s", $url, http_build_query($args['data']));
        break;
      case "PUT":
        if(isset($args['data']))
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($args['data']));
        break;
      default:
        return [403, 'Method is not acceptable.'];
        break;
    }
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $args['method']);

    if(isset($args['auth'])){
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, $args['auth']);
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $args['headers']);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $resp = curl_exec($ch);

    if(curl_getinfo($ch, CURLINFO_HTTP_CODE) > 200){
      return [ 500, 'Internal server error.' ];
    }
    if($resp === false){
      return [ 403, curl_error($ch) ];
    }
    curl_close($ch);

    return [ 200, $resp ];
  }

];

?>