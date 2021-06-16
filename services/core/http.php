<?php

Swoole\Runtime::enableCoroutine(SWOOLE_HOOK_CURL);
$fnlist['http'] = [
  'get' => function ($args=null) use ($fnlist) {
  },
  'post' => function ($args=null) use ($fnlist) {
  },
  'exec' => function ($args=null) use ($fnlist) {
  },
];

?>
