<?php


$fnlist['app1'] = [

  'init' => function ($args=null) use ($fnlist) {
    // run initialization routines here
    return [ 200, 'OK' ];
  },

  'hello' => function ($args=null) use ($fnlist) {
    return [ 200, 'Hello World!' ];
  },

];

?>
