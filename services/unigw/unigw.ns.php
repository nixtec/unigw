<?php

# unigw.ns.php
# manipulate namespaces in unigw

$fnlist['unigw.ns'] = [
  # table(s): ls_ns

  'add' => function ($args=null) use (&$fnlist) {
    # input: ns_keyword, ns_desc
    # output: ns_id: max(ns_id)+1
  },

  'get' => function ($args=null) use (&$fnlist) {
    # input: ns_keyword
    # output: array with keys: [ ns_id, ns_keyword, ns_desc ]
    return [ 200, [ . ] ];
  },

  'update' => function ($args=null) use (&$fnlist) {
    # input: ns_id, [ one or more of: ns_keyword, ns_desc ]
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'delete' => function ($args=null) use (&$fnlist) {
    # input: ns_id
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'list' => function ($args=null) use (&$fnlist) {
    # input: (none) [optional limit, or pagination]
    # output: array of [ ns_id, ns_keyword, ns_desc ]
    return [ 200, [ ... ] ];
  },

  # table(s): ls_ns_ep
  'ep.add' => function ($args=null) use (&$fnlist) {
    # input: ns_id, ep_id
    # output: OK
  },

  'ep.exists' => function ($args=null) use (&$fnlist) {
    # input: ns_id, ep_id
    # output: boolean
    return [ 200, [ . ] ];
  },

  'ep.update' => function ($args=null) use (&$fnlist) {
    # input: ns_id, ep_id
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'ep.delete' => function ($args=null) use (&$fnlist) {
    # input: ns_id, ep_id
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'ep.list' => function ($args=null) use (&$fnlist) {
    # input: ns_id
    # output: array of [ ns_id, ep_id ]
    return [ 200, [ ... ] ];
  },

  # table(s): ls_ns_func
  'func.add' => function ($args=null) use (&$fnlist) {
    # input: ns_id, func_name, ep_id
    # output: OK
  },

  'func.get' => function ($args=null) use (&$fnlist) {
    # input: ns_id, func_name
    # output: array [ ns_id, ep_id, func_name ]
    return [ 200, [ . ] ];
  },

  'func.update' => function ($args=null) use (&$fnlist) {
    # input: ns_id, func_name, ep_id
    # action: update 'ep_id' matching 'ns_id' and 'func_name'
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'func.delete' => function ($args=null) use (&$fnlist) {
    # input: ns_id, func_name
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'func.list' => function ($args=null) use (&$fnlist) {
    # input: ns_id
    # output: array of [ ns_id, ep_id, func_name ]
    return [ 200, [ ... ] ];
  },

];

?>
