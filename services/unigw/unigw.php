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
  # After writing the code the database schema was changed, so it has to be written from scratch
  'external' => function ($args=null) use (&$fnlist) {
    $rcode = 500;
    $robj = new stdClass();
    $robj->status = 1;
    $robj->msg = 'Something went wrong.';
    $robj->resp = false;

    list ($code, $db) = $fnlist['pool']['get']([ 'app' => 'unigw', 'id' => 'mysqlpool' ]);
    if ($code != 200) {
      $robj->msg = 'DB Connection Failed.';
      goto out;
    }

    $namespace = $args['namespace'] ?? 'default';
    $keyword = $args['keyword'] ?? 'nokeyword';
    $keyfunc = $args['keyfunc'] ?? 'nokeyfunc';

    $svc_info = [];

    $tbl_prefix = "ext_"; # Default, will be overriden by subsequent lines
    $tbl = "ugw_ns"; # first look up in namespace
    $sql = "SELECT * FROM `{$tbl}` WHERE `ns`='{$namespace}' AND `svc_keyword`='{$keyword}' AND `published`=1 LIMIT 1";
    try {
      $result = $db->query($sql);
      $row = $result->fetch_assoc();
      $tbl_prefix = $row['svc_prefix'];
      $svc_id = $row['svc_id'];
      $svc_info['ns'] = $row;
    } catch (Exception $e) {
      $robj->msg = 'Query Execution Failed [ns].';
      goto out;
    }

    $tbl = "{$tbl_prefix}common";
    $sql = "SELECT * FROM `{$tbl}` WHERE `svc_ic`={$svc_id} AND `published`=1 LIMIT 1";
    try {
      $result = $db->query($sql);
      $row = $result->fetch_assoc();
      $need_auth = $row['need_auth'];
      $svc_info['common'] = $row;
    } catch (Exception $e) {
      $robj->msg = 'Query Execution Failed [common].';
      goto out;
    }


    if ($need_auth) {
      $tbl = "{$tbl_prefix}auth";
      $sql = "SELECT * FROM `{$tbl}` WHERE `svc_id`={$svc_id} AND `published`=1";
      try {
        $result = $db->query($sql);
        while ($row = $result->fetch_assoc()) {
          $svc_info['auth'][] = $row;
        }
      } catch (Exception $e) {
        $robj->msg = 'Query Execution Failed [auth].';
        goto out;
      }
    }

    $tbl = "{$tbl_prefix}func";
    $sql = "SELECT * FROM `{$tbl}` WHERE `svc_id`={$svc_id} AND `func_name`='{$keyfunc}' AND `published`=1 LIMIT 1";
    try {
      $result = $db->query($sql);
      $row = $result->fetch_assoc();
      $svc_info['func'] = $row;
      $func_has_args = $row['has_args'];
      $func_has_headers = $row['has_headers'];
      $func_id = $row['id'];
    } catch (Exception $e) {
      $robj->msg = 'Query Execution Failed [func].';
      goto out;
    }

    if ($func_has_args != 0) {
      $tbl = "{$tbl_prefix}func_arg";
      $sql = "SELECT * FROM `{$tbl}` WHERE `func_id`={$func_id} AND `published`=1";
      try {
        $result = $db->query($sql);
        while ($row = $result->fetch_assoc()) {
          $svc_info['func_arg'][] = $row;
        }
      } catch (Exception $e) {
        $robj->msg = 'Query Execution Failed [func_arg].';
        goto out;
      }
    }

    if ($func_has_headers != 0) {
      $tbl = "{$tbl_prefix}func_header";
      $sql = "SELECT * FROM `{$tbl}` WHERE `func_id`={$func_id} AND `published`=1";
      try {
        $result = $db->query($sql);
        while ($row = $result->fetch_assoc()) {
          $svc_info['func_header'][] = $row;
        }
      } catch (Exception $e) {
        $robj->msg = 'Query Execution Failed [func_header].';
        goto out;
      }
    }


    # Now we have all the information to invoke real API call

    



    out:
    if (isset($db) && $db != false) {
      $fnlist['pool']['put']([ 'app' => 'unigw', 'id' => 'mysqlpool', 'handle' => $db ]);
      $db = false;
    }

    return [ $rcode, json_encode($robj) ];
  },

];

?>
