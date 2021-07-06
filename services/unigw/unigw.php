<?php


$fnlist['unigw'] = [
  '__cfg' => [
    'app' => 'unigw',
  ],

  'init' => function ($args=null) use (&$fnlist) {
    // now configure DB Pool
    $dbconfig = [
      'title'    => 'MySQL Pool',
      'host'     => '127.0.0.1',
      'port'     => 3314,
      'sock'     => '/var/run/mysqld/mysqld-nixtec.sock',
      'dbname'   => 'unigw_dev',
      'charset'  => 'utf8mb4',
      'username' => 'unigwdev',
      'password' => 'un1gwd3v#@!'
    ];
    $fnlist['mysqlpool']['init'](['app' => 'unigw', 'config' => $dbconfig ]);
  },

  # following is just demonstration code
  'exec' => function ($args=null) use (&$fnlist) {
    $rcode = 500;
    $robj = new stdClass();
    $robj->status = 1;
    $robj->msg = 'Something went wrong.';
    $robj->resp = false;

    # get DB Handle from Pool

    /*
    list ($code, $db) = $fnlist['pool']['get']([ 'app' => 'unigw', 'id' => 'mysqlpool' ]);
    if ($code != 200) {
      $robj->msg = 'DB Connection Failed.';
      goto out;
    }
     */


    $ns = $args['ns'];
    $ep = $args['ep'];
    $func = $args['func'];

    $xargs = [ 'ns_keyword' => $ns, ... ]
    list ($code, $nsinfo) = $fnlist['unigw.ns']['get']($xargs);
    #
    # if got record, then continue down, otherwise out
    #
    if ($ep == "_") {
      # special handling of endpoint
      $func_ns = $func;
      $xargs = [ 'ns_id' => $nsid, 'func_name' => $func_ns, ... ];
      list ($code, $epinfo1) = $fnlist['unigw.ns']['func.get']($xargs);
      $xargs = [ 'ep_id' => $epinfo1['ep_id'], ... ];
      list ($code, $epinfo) = $fnlist['unigw.ep']['getById']($xargs);
      $xargs = [ 'ep_id' => $epinfo['ep_id'], 'func_name_ns' => $func_ns, ... ];
      list ($code, $funcinfo) = $fnlist['unigw.ep']['func.getByNs']($xargs);
    } else {
      $func_ep = $func;
      $xargs = [ 'ep_keyword' => $ep, ... ];
      list ($code, $epinfo) = $fnlist['unigw.ep']['get']($xargs);
      $xargs = [ 'func_name_ep' => $ep, ... ];
      list ($code, $funcinfo) = $fnlist['unigw.ep']['func.get']($xargs);
      $func_id = $funcinfo['func_id'];
    }

    # if got function record, then continue down, otherwise out
    $xargs = [ 'ep_id' => $epinfo['ep_id'], ... ];
    list ($code, $epcfginfo) = $fnlist['unigw.ep']['config.get']($xargs);

    # if need_auth is true (1)
    # need to resolve 'auth_id'
    $auth_keyword = $args['auth_keyword'];
    if ($ep == '_') {
      $xargs = [ 'vauth_keyword' => $auth_keyword, ... ];
      list ($code, $vauthinfo) = $fnlist['unigw.vauth']['get']($xargs);
      $xargs = [ 'ep_id' => $epinfo['ep_id'], 'vauth_id' => $auth_keyword, ... ];
      list ($code, $vauthepinfo) = $fnlist['unigw.vauth']['ep.get']($xargs);
      # we got 'auth_id' here
      $auth_id = $vauthepinfo['auth_id'];
    } else {
      $xargs = [ 'ep_id' => $epinfo['ep_id'], 'auth_keyword' => $auth_keyword, ... ];
      list ($code, $authinfo) = $fnlist['unigw.vauth']['get']($xargs);
      # we got 'auth_id' here
      $auth_id = $authinfo['auth_id'];
    }

    # we got 'auth_id' here
    $xargs = [ 'auth_id' => $auth_id, ... ];
    list ($code, $autharginfo) = $fnlist['unigw.ep']['auth.arg.list']($xargs);

    # if $funcinfo['has_args'] is true
    $xargs = [ 'func_id' => $funcinfo['func_id'], ... ];
    list ($code, $funcarginfo) = $fnlist['unigw.ep']['func.arg.list']($xargs);

    # if $funcinfo['has_headers'] is true
    $xargs = [ 'func_id' => $funcinfo['func_id'], ... ];
    list ($code, $funcheaderinfo) = $fnlist['unigw.ep']['func.header.list']($xargs);

    # we got all necessary information to prepare real HTTP CLIENT call
    # we prepare function argument array accordingly




    # cleanup block to be executed
    /*
    out:
    if (isset($db) && $db != false) {
      $fnlist['pool']['put']([ 'app' => 'unigw', 'id' => 'mysqlpool', 'handle' => $db ]);
      $db = false;
    }
     */

    return [ $rcode, json_encode($robj) ];
  },

];

?>
