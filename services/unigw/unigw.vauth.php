<?php

# unigw.vauth.php
# manipulate virtual authentications in unigw

$fnlist['unigw.vauth'] = [
  # table(s): ls_vauth

  'add' => function ($args=null) use (&$fnlist) {
    # input: vauth_keyword, vauth_desc
    # output: vauth_id: max(vauth_id)+1
  },

  'get' => function ($args=null) use (&$fnlist) {
    # input: vauth_keyword
    # output: array with keys: [ vauth_id, vauth_keyword, vauth_desc ]
    return [ 200, [ . ] ];
  },

  'update' => function ($args=null) use (&$fnlist) {
    # input: vauth_id, [ one or more of: vauth_keyword, vauth_desc ]
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'delete' => function ($args=null) use (&$fnlist) {
    # input: vauth_id
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'list' => function ($args=null) use (&$fnlist) {
    # input:
    # output: array of [ vauth_id, vauth_keyword, vauth_desc ]
    return [ 200, [ ... ] ];
  },

  # table(s): ls_vauth_ep
  'ep.add' => function ($args=null) use (&$fnlist) {
    # input: vauth_id, ep_id, auth_id, auth_desc
    # output: OK
  },

  'ep.get' => function ($args=null) use (&$fnlist) {
    # input: ep_id, vauth_id
    # output: array with keys [ vauth_id, ep_id, auth_id, auth_desc ]
    return [ 200, [ . ] ];
  },

  'ep.update' => function ($args=null) use (&$fnlist) {
    # input: vauth_id, ep_id, [ one or more of: auth_id, auth_desc ]
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'ep.delete' => function ($args=null) use (&$fnlist) {
    # input: vauth_id, ep_id
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'ep.list' => function ($args=null) use (&$fnlist) {
    # input: vauth_id, ep_id
    # output: array of [ vauth_id, ep_id, auth_id, auth_desc ]
    return [ 200, [ ... ] ];
  },

];

?>
