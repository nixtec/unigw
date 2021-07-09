<?php


$fnlist['app1'] = [

  'init' => function ($args=null) use ($fnlist) {
    // run initialization routines here
    return [ 200, 'OK' ];
  },

  'hello' => function ($args=null) use ($fnlist) {
    return [ 200, 'Hello World!' ];
  },

  'process' => function($args=null) use ($fnlist){
    $fnlist['unigw']['init']();
    list ($code, $resp) = $fnlist['unigw']['core']($args);
    // print_r($resp);
    
    return [ $code, json_encode($resp) ];
  },

];

?>
