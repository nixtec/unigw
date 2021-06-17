<?php


$fnlist['unigw'] = [
  '__cfg' => [
    'app' => 'unigw',
  ],

  'init' => function ($args=null) use (&$fnlist) {
    // now configure DB Pool
    $dbconfig = [
      'title' => 'MySQL Pool',
      'host' => '127.0.0.1',
      'port' => 3314,
      'sock' => '/var/run/mysqld/mysqld-nixtec.sock',
      'dbname' => 'unigw_dev',
      'charset' => 'utf8mb4',
      'username' => 'unigwdev',
      'password' => 'un1gwd3v#@!'
    ];
    $fnlist['mysqlpool']['init'](['app' => 'unigw', 'config' => $dbconfig ]);
  },

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

    $keyword = $args['keyword'] ?? 'nokeyword';
    $keyfunc = $args['keyfunc'] ?? 'nokeyfunc';

    $need_basic_auth = false;
    $cfg_auth_keys = [ '__cfg_auth_type' ];
    $tbl = "ext_auth";
    $sql = "SELECT * FROM `{$tbl}` WHERE `ext_keyword`='{$keyword}' AND `published`=1";
    try {
      $result = $db->query($sql);
      while ($row = $result->fetch_object()) {
	$auth_key = $row->auth_key;
	$auth_val = $row->auth_value;
	if (in_array($auth_key, $cfg_auth_keys)) {
	  $need_basic_auth = true;
	} else {
	  $auth_kv[$auth_key] = $auth_val;
	}
      }
    } catch (Exception $e) {
      $robj->msg = 'Query Execution Failed [auth].';
      goto out;
    }

    $tbl = "ext_common";
    $sql = "SELECT * FROM `{$tbl}` WHERE `ext_keyword`='{$keyword}' AND `published`=1 LIMIT 1";
    try {
      $result = $db->query($sql);
      $row = $result->fetch_object();
    } catch (Exception $e) {
      $robj->msg = 'Query Execution Failed [common].';
      goto out;
    }

    $ext_id = $row->id;
    $tbl = "ext_func";
    $sql = "SELECT * FROM `{$tbl}` WHERE `ext_id`={$ext_id} AND `func_name`='{$keyfunc}' AND `published`=1 LIMIT 1";
    try {
      $result = $db->query($sql);
      $row = $result->fetch_object();
    } catch (Exception $e) {
      $robj->msg = 'Query Execution Failed [func].';
      goto out;
    }

    $func_id = $row->id;
    if ($row->has_args != 0) {
      $tbl = "ext_func_arg";
      $sql = "SELECT * FROM `{$tbl}` WHERE `func_id`={$func_id} AND `published`=1";
      try {
	$result = $db->query($sql);
	while ($row = $result->fetch_object()) {
	  $func_args[] = $row;
	}
      } catch (Exception $e) {
	$robj->msg = 'Query Execution Failed [func].';
	goto out;
      }
      
    }

    



    out:
    if (isset($db) && $db != false) {
      $fnlist['pool']['put']([ 'app' => 'unigw', 'id' => 'mysqlpool', 'handle' => $db ]);
      $db = false;
    }

    return [ $rcode, json_encode($robj) ];
  },

];

?>
