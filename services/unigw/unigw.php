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
  
  'core' => function($args = null) use (&$fnlist){
    $rcode = 500;
    $robj = new stdClass();
    $robj->status = 1;
    $robj->msg = 'Something went wrong.';
    $robj->resp = false;

    list($code, $db) = $fnlist['pool']['get']([ 'app' => 'unigw', 'id' => 'mysqlpool' ]);
    if($code != 200){
      $robj->msg = 'DB Connection Failed.';
      goto out;
    }

    $tbl = "ls_ns"; # first look up in namespace
    $sql = "SELECT ns_id FROM `{$tbl}` WHERE `ns_keyword`='{$args['app']}' AND `published`=1 LIMIT 1;";
    try{
      $result = $db->query($sql);
      if($row = $result->fetch_assoc()){
        $ns_id = $row['ns_id'];
      } else{
        throw new Exception('Query Execution Failed [ns].');
      }
    } catch (Exception $e){
      $robj->msg = $e->getMessage();
      goto out;
    }

    # get end-point id by namespace-id
    if($args['ep'] == '-'){
      $sql = "SELECT ep_id, func_name FROM `ls_ns_func` WHERE ns_id={$ns_id} AND published=1";
    } else{
      $tbl = "map_ns_ep";
      $sql = "SELECT GROUP_CONCAT(ep_id) AS ep_id FROM `{$tbl}` WHERE ns_id={$ns_id} AND published=1 GROUP BY ns_id;";
    }
    try{
      $result = $db->query($sql);
      if($row = $result->fetch_assoc()){
        $ep_id   = $row['ep_id'];
        $ep_name = $row['func_name'] ?? $args['ep'];
      } else{
        throw new Exception('Query Execution Failed [ns-ep].');
      }
    } catch (Exception $e){
      $robj->msg = $e->getMessage();
      goto out;
    }

    $tbl = "ls_ep"; # get end-point type-id
    $sql = "SELECT ep_id,ep_type FROM `{$tbl}` WHERE `ep_id` IN({$ep_id}) AND `ep_keyword`='{$ep_name}' AND `published`=1;";
    try{
      $result = $db->query($sql);
      if($row = $result->fetch_assoc()){
        $ep_id = $row['ep_id'];
        $type_id = $row['ep_type'];
      } else{
        throw new Exception('Query Execution Failed [ep].');
      }
    } catch (Exception $e){
      $robj->msg = $e->getMessage();
      goto out;
    }

    $tbl = "ls_type_ep"; # get service type & table prefix
    $sql = "SELECT * FROM `{$tbl}` WHERE `type_id`={$type_id} AND `published`=1 LIMIT 1;";
    try{
      $result = $db->query($sql);
      if($row = $result->fetch_assoc()){
        $service = $row['type_keyword'];
        $tbl_prefix = $row['ep_tbl_prefix'];
      } else{
        throw new Exception('Query Execution Failed [ep].');
      }
    } catch (Exception $e){
      $robj->msg = $e->getMessage();
      goto out;
    }

    list($code, $resp) = $fnlist['unigw'][$service]($args, $db, $ep_id, $tbl_prefix);
    if($code != 200){
      $robj->msg = $resp;
      goto out;

    } else{
      $robj->status = 0;
      $robj->msg = "success";
      $robj->resp = $resp;
    }

    out:
    if(isset($db) && $db != false){
      $fnlist['pool']['put']([ 'app' => 'unigw', 'id' => 'mysqlpool', 'handle' => $db ]);
      $db = false;
    }

    // return [$code, json_encode($robj)];
    return [$code, $robj];
  },


  # following is just demonstration code
  # After writing the code the database schema was changed, so it has to be written from scratch
  'external' => function ($args, $db, $ep_id, $tbl_prefix=null) use (&$fnlist){

    $svc_info = $http_info = [];
    $tbl_prefix = $tbl_prefix ?? "ext_";

    $tbl = "{$tbl_prefix}ep_config"; // get API configuration
    $sql = "SELECT * FROM `{$tbl}` WHERE `ep_id`='{$ep_id}' AND `published`=1 LIMIT 1";
    try{
      $result = $db->query($sql);
      if($row = $result->fetch_assoc()){
        $svc_info['config'] = $row;
      } else{
        throw new Exception('Query Execution Failed [config].');
      }
    } catch (Exception $e){
      return [403, $e->getMessage()];
    }

    $tbl = "{$tbl_prefix}ep_func"; // get end-point function info
    $sql = "SELECT * FROM `{$tbl}` WHERE `ep_id`='{$ep_id}' AND func_name_ns='{$args['wf']}' AND `published`=1";
    try{
      $result = $db->query($sql);
      if($row = $result->fetch_assoc()){
        $svc_info['ep_func'] = $row;
      } else{
        throw new Exception('Query Execution Failed [ep_func].');
      }
    } catch (Exception $e){
      return [403, $e->getMessage()];
    }

    $func_id = $svc_info['ep_func']['func_id'];
    $http_info['url'] = $svc_info['config']['ep_baseurl'] . $svc_info['ep_func']['func_name_ep'];

    // end-point function argument
    if($svc_info['ep_func']['has_args'] == 1){
      $tbl = "{$tbl_prefix}ep_func_arg";
      $sql = "SELECT * FROM `{$tbl}` WHERE `func_id`='{$func_id}' AND `published`=1";
      try{
        if($result = $db->query($sql)){
          $params  = [];
          $reqargs = $args['reqargs'];

          while($row = $result->fetch_assoc()){
            if($row['val_type'] == 1){
              $http_info['data'][$row['arg_key']] = $row['arg_value'];
            } else{
              if(!isset($reqargs[$row['arg_key']])){
                return [403, "The requested argument is not valid."];
              }

              if($row['is_parent'] == 1){
                $http_info['data'][$row['arg_value']][] = $reqargs[$row['arg_key']];
              } else{
                $http_info['data'][$row['arg_value']] = $reqargs[$row['arg_key']];
              }
            }
          }
        } else{
          throw new Exception('Query Execution Failed [func_arg].');
        }
      } catch (Exception $e){
        return [403, $e->getMessage()];
      }
    }

    // end-point function header
    if($svc_info['ep_func']['has_headers'] == 1){
      $tbl = "{$tbl_prefix}ep_func_header";
      $sql = "SELECT * FROM `{$tbl}` WHERE `func_id`='{$func_id}' AND `published`=1";
      try{
        if($result = $db->query($sql)){
          while($row = $result->fetch_assoc()){
            $http_info['headers'][$row['arg_key']] = $row['arg_value'];
          }
        } else{
          throw new Exception('Query Execution Failed [func_header].');
        }
      } catch (Exception $e){
        return [403, $e->getMessage()];
      }
    }

    if($svc_info['config']['need_auth'] == 1){
      $tbl = "{$tbl_prefix}ep_auth"; // get authentication
      $sql = "SELECT * FROM `{$tbl}` WHERE `ep_id`='{$ep_id}' AND `published`=1";
      try{
        if($result = $db->query($sql)){
          while($row = $result->fetch_assoc()){
            $http_info['auth'][$row['auth_key']] = $row['auth_value'];
          }
        } else{
          throw new Exception('Query Execution Failed [auth].');
        }
      } catch (Exception $e){
        return [403, $e->getMessage()];
      }
    }

    list($code, $resp) = $fnlist['httpcurl'][$svc_info['config']['req_method']]($http_info);
    if($code == 200){
      $resp = $fnlist['unigw'][$svc_info['config']['resp_datatype']]($resp);
    }

    return [200, $resp];
  },

  'json' => function ($args=null){
    return json_decode($args);
  },

  'xml' => function ($args=null){
    $data = @new SimpleXMLElement($args);
		$data = json_decode(json_encode($data));
    return $data;
  },

];

?>
