<?php

# unigw.ep.php
# manipulate endpoints in unigw

$fnlist['unigw.ep'] = [

  # table(s): ls_type_ep
  'type.add' => function ($args=null) use (&$fnlist) {
    # input: type_keyword, type_desc, ep_tbl_prefix
    # output: type_id: max(type_id)+1
  },

  'type.get' => function ($args=null) use (&$fnlist) {
    # input: type_id
    # output: array [ type_id, type_keyword, type_desc, ep_tbl_prefix ]
    return [ 200, [ . ] ];
  },

  'type.update' => function ($args=null) use (&$fnlist) {
    # input: type_id, [one or more of: type_keyword, type_desc, ep_tbl_prefix ]
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'type.delete' => function ($args=null) use (&$fnlist) {
    # input: type_id
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'type.list' => function ($args=null) use (&$fnlist) {
    # input: (none)
    # output: array of [ type_id, type_keyword, type_desc, ep_tbl_prefix ]
    return [ 200, [ ... ] ];
  },

  # table(s): ls_ep
  'add' => function ($args=null) use (&$fnlist) {
    # input: ep_keyword, ep_desc, ep_type
    # output: ep_id: max(ep_id)+1
  },

  'get' => function ($args=null) use (&$fnlist) {
    # input: ep_keyword
    # output: array [ type_id, type_keyword, type_desc ]
    return [ 200, [ . ] ];
  },

  'getById' => function ($args=null) use (&$fnlist) {
    # input: ep_id
    # output: array [ type_id, type_keyword, type_desc ]
    return [ 200, [ . ] ];
  },


  'update' => function ($args=null) use (&$fnlist) {
    # input: ep_id, [ one or more of: ep_keyword, ep_desc, ep_type ]
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'delete' => function ($args=null) use (&$fnlist) {
    # input: ep_id
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'list' => function ($args=null) use (&$fnlist) {
    # input: (none)
    # output: array of [ ep_id, ep_keyword, ep_desc, ep_type ]
    return [ 200, [ ... ] ];
  },

  # table(s): ext_ep_config
  'config.add' => function ($args=null) use (&$fnlist) {
    # input: ep_id, ep_baseurl, need_auth, req_proto, req_method, req_datatype, resp_datatype, conn_timeout_msec, resp_timeout_msec, retry_after_conn_timeout, wait_before_retry_msec
    # output: OK
  },

  'config.get' => function ($args=null) use (&$fnlist) {
    # input: ep_id
    # output: array [ ep_id, ep_baseurl, <all-columns> ]
    return [ 200, [ . ] ];
  },

  'config.update' => function ($args=null) use (&$fnlist) {
    # input: ep_id, [one or more of: ep_baseurl, <all-columns> ]
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'config.delete' => function ($args=null) use (&$fnlist) {
    # input: ep_id
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'config.list' => function ($args=null) use (&$fnlist) {
    # input: (none)
    # output: array of [ ep_id, ep_baseurl, <all-columns> ]
    return [ 200, [ ... ] ];
  },

  # table(s): ls_ep_auth
  'auth.add' => function ($args=null) use (&$fnlist) {
    # input: ep_id, auth_keyword, auth_desc
    # output: auth_id: max(auth_id)+1
  },

  'auth.get' => function ($args=null) use (&$fnlist) {
    # input: ep_id, auth_keyword
    # output: array [ ep_id, auth_id, auth_keyword, auth_desc ]
    return [ 200, [ . ] ];
  },

  'auth.update' => function ($args=null) use (&$fnlist) {
    # input: ep_id, auth_id, [one or more of: auth_desc ]
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'auth.delete' => function ($args=null) use (&$fnlist) {
    # input: ep_id, auth_id
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'auth.list' => function ($args=null) use (&$fnlist) {
    # input: ep_id
    # output: array of [ ep_id, auth_id, auth_keyword, auth_desc ]
    return [ 200, [ ... ] ];
  },

  # table(s): ext_ep_func
  'func.add' => function ($args=null) use (&$fnlist) {
    # input: ep_id, func_name_ns, func_name_ep, has_args, has_headers
    # output: func_id: max(func_id)+1
  },

  'func.get' => function ($args=null) use (&$fnlist) {
    # input: ep_id, func_name
    $func_name_ns = $args['func_name'];
    # output: array [ ep_id, func_id, func_name_ns, func_name_ep, has_args, has_headers ]
    return [ 200, [ . ] ];
  },

  'func.getByEp' => function ($args=null) use (&$fnlist) {
    # input: ep_id, func_name
    $func_name_ep = $args['func_name'];
    # output: array [ ep_id, func_id, func_name_ns, func_name_ep, has_args, has_headers ]
    return [ 200, [ . ] ];
  },


  'func.update' => function ($args=null) use (&$fnlist) {
    # input: ep_id, func_id, [one or more of: func_name_ns, func_name_ep, has_args, has_headers ]
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'func.delete' => function ($args=null) use (&$fnlist) {
    # input: ep_id, func_id
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'func.list' => function ($args=null) use (&$fnlist) {
    # input: ep_id
    # output: array of [ ep_id, func_id, func_name_ns, func_name_ep, has_args, has_headers ]
    return [ 200, [ ... ] ];
  },

  # table(s): ext_ep_func_arg
  'func.arg.add' => function ($args=null) use (&$fnlist) {
    # input: func_id, arg_key, arg_value
    # output: OK
  },

  'func.arg.get' => function ($args=null) use (&$fnlist) {
    # input: func_id, arg_key
    # output: array [ func_id, arg_key, arg_value ]
    return [ 200, [ . ] ];
  },

  'func.arg.update' => function ($args=null) use (&$fnlist) {
    # input: func_id, arg_key, [one or more of: arg_value ]
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'func.arg.delete' => function ($args=null) use (&$fnlist) {
    # input: func_id, arg_key
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'func.arg.list' => function ($args=null) use (&$fnlist) {
    # input: func_id
    # output: array of [ func_id, arg_key, arg_value ]
    return [ 200, [ ... ] ];
  },

  # table(s): ext_ep_func_header
  'func.header.add' => function ($args=null) use (&$fnlist) {
    # input: func_id, header_key, header_value
    # output: OK
  },

  'func.header.get' => function ($args=null) use (&$fnlist) {
    # input: func_id, header_key
    # output: array [ func_id, header_key, header_value ]
    return [ 200, [ . ] ];
  },

  'func.header.update' => function ($args=null) use (&$fnlist) {
    # input: func_id, header_key, [ one or more of: header_value ]
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'func.header.delete' => function ($args=null) use (&$fnlist) {
    # input: func_id, header_key
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'func.header.list' => function ($args=null) use (&$fnlist) {
    # input: func_id
    # output: array of [ func_id, header_key, header_value ]
    return [ 200, [ ... ] ];
  },

  # table(s): ls_auth_arg
  'auth.arg.add' => function ($args=null) use (&$fnlist) {
    # input: auth_id, auth_key, auth_value, auth_desc
    # output: OK
  },

  'auth.arg.get' => function ($args=null) use (&$fnlist) {
    # input: auth_id, auth_key
    # output: array [ auth_id, auth_key, auth_value, auth_desc ]
    return [ 200, [ . ] ];
  },

  'auth.arg.update' => function ($args=null) use (&$fnlist) {
    # input: auth_id, auth_key, [one or more of: auth_value, auth_desc ]
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'auth.arg.delete' => function ($args=null) use (&$fnlist) {
    # input: auth_id, auth_key
    # output: 'OK'
    return [ 200, 'OK' ];
  },

  'auth.arg.list' => function ($args=null) use (&$fnlist) {
    # input: auth_id
    # output: array of [ auth_id, auth_key, auth_value, auth_desc ]
    return [ 200, [ ... ] ];
  },

];

?>
