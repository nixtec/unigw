<?php


# Core must never modify passed arguments

$fnlist['core'] = [

  'hello' => function ($args=null) use ($fnlist) {
    return [ 200, 'Hello World!' ];
  },


  'dot' => function ($args=null) use ($fnlist) {
    return [ 200, '.' ];
  },

];

?>
