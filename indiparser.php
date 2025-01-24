<?php


header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');

ini_set('display_errors', 1);

require_once __DIR__.'/core.php';

$queries = require_once __DIR__. '/create_tables.php';


//Создаем таблицы базы данных
if(isset($_GET['migrate']) || @$argv[1] == 'migrate'){
    $db->query($query_create);
    exit('done!');
}

$token = 'i';

if (!isset($_GET['token'])) {
    http_response_code(400);
    exit('Error: No token passed.');
}


$incoming_token = $_GET['token'];

if ($incoming_token !== $token) {
    http_response_code(403);
    exit('Wrong token: ' . htmlspecialchars($incoming_token));
}




$json = file_get_contents('php://input');
//if(empty($json) && !$_POST) exit();
$arr = json_decode($json, 1);
$text = @trim($arr['text']);


$user_id =    (int)@$_REQUEST['user_id'];
//$text    = (string)@$_REQUEST['text'];

if(isset($_GET['html'])){
    echo '<form>';
    echo '<input type="hidden" name="html">';
    echo '<textarea autofocus rows="22" cols="55" name="text">'.$text.'</textarea>';
    echo '<input type="text" name="user_id" value="'.$user_id.'">';
    echo '<input type="submit">';
    echo '</form>';
}
else {
    $responseText = "Данные успешно сохранены на сервере";
    if(empty($user_id)) exit('error user_id');
    if(empty($text)) {
	$responseText = "Данные получены с сервера";
	$text = $db->getOne("SELECT `text_in` FROM `strat` WHERE `user_id` = ?i LIMIT 1", $user_id);
    }

    
    $tmp = '{
              "title":"Настройки индикаторов",
              "label":"Настройки индикаторов",
              "submit":{"label":"Отправить","variant":"elevated","disabled":false},
              "components":[
                  {
                      "label":"Настройки индикаторов",
                      "type":"textarea",
                      "value":"",
                      "rows":"4",
                      "name":"text",
                      "required":true,
                      "error":"",
                      "cols":{"cols":12,"md":6}
                  },
		
		  {
		      "type": "post",
        	      "text": ""
        		
    		  }
              ]
          }';
    $a = json_decode($tmp, 1);
    $a['components'][0]['value'] = $text;
    $a['components'][1]['text'] = $responseText;
    echo json_encode($a, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    
    
    $json = file_get_contents('php://input');
    if(empty($json) && !$_POST) exit();
}



$db->query("INSERT INTO `strat_history` SELECT * FROM `strat` WHERE `user_id` = ?i LIMIT 1", $user_id);


$d = [];
$d['saved']   = 0;
$d['user_id'] = $user_id;
$d['text_in'] = $text;
//save_to_tbl1($d);


$r = parse_text($text);  // парсим синтаксис сигналов и в случае ошибок добавляем знаки вопроса в начало соответствующих строк
//$d = [];
//$d['user_id'] = $user_id;
$d['text_out'] = $r['text'];
$d['json'] = json_encode(array_values($r['signals']));  // JSON_PRETTY_PRINT
//save_to_tbl1($d);
db_insert_update('strat', $d);




$indiset_combo_ids = [];
foreach($r['signals'] as $v){
    $indiset_ids = [];
  //$indiset_uuids = [];
    foreach($v['indiset'] as $vv){
      //save_to_tbl3($vv);  // indiset
        db_insert_update('indiset', [  // заменил ignore на update, чтобы фиксить indiset_parser() можно было
      //db_insert_ignore('indiset', [
                                        'str'  =>                $vv,
                                        'json' => indiset_parser($vv),
                                    ]);

        $q = $db->getRow("SELECT * FROM `indiset` WHERE `str` = ?s LIMIT 1", $vv);
      //echo '<br>'.$q['id'];

        $indiset_ids[$vv]   = (int)$q['id'];
      //$indiset_uuids[$vv] =      $q['uuid'];
    }

    $takestop_ids = [];
  //$takestop_uuids = [];
    foreach($v['takestop_decimal'] as $vv){
        $ins = [];
        $ins['take'] = $vv[0];
        $ins['stop'] = $vv[1];
        db_insert_ignore('takestop', $ins);

        $q = $db->getRow("SELECT * FROM ?n WHERE `take` = ?s AND `stop` = ?s LIMIT 1", 'takestop', $ins['take'], $ins['stop']);
        $takestop_ids  [implode('_', $ins)] = (int)$q['id'];
      //$takestop_uuids[implode('_', $ins)] =      $q['uuid'];

    }

    if(!empty($v['is_valid'])){
        $a = [];
      //$b = [];
        foreach($v['indiset'] as $vv){
            $a[] = $indiset_ids[$vv];
          //$b[] = $indiset_uuids[$vv];
        }
        sort($a);  // sort($b);  // по возрастанию значения, без сохранения ключей
        $ins = [];
      //$ins['indiset_ids']   = implode(',',$a);
      //$ins['indiset_uuids'] = implode(',',$b);
        $ins['indiset_ids']   = json_encode($a);  // JSON_PRETTY_PRINT
      //$ins['indiset_uuids'] = json_encode($b);  // JSON_PRETTY_PRINT
        
      //save_to_tbl4($ins);  // indiset_combo
        db_insert_ignore('indiset_combo', $ins);

        $q = $db->getRow("SELECT * FROM `indiset_combo` WHERE `indiset_ids` = ?s LIMIT 1", $ins['indiset_ids']);

        $ins = [];
        $ins['user_id'] = $user_id;
        $ins['indiset_combo_id']   = $indiset_combo_ids[] = $q['id'];
      //$ins['indiset_combo_uuid']                        = $q['uuid'];
        
        $ins['pair'] = $v['pair'];
      //$ins['takestop_combo_id'] = 0;
      //$ins['takestop_combo_uuid'] = json_encode($v['takestop']);  /////////////////////// наверное это надо прогнать через таблицы и положить сюда combo_id
      //$ins['takestop_combo_uuid'] = json_encode($v['takestop_decimal']);
        $a = [];
      //$b = [];
        foreach($v['takestop_decimal'] as $vv){
            $a[] = $takestop_ids  [$vv[0].'_'.$vv[1]];
          //$b[] = $takestop_uuids[$vv[0].'_'.$vv[1]];
        }
        sort($a);  // sort($b);  // по возрастанию значения, без сохранения ключей
        $ins['side'] = $v['side'];
        $ins['takestop_ids']   = json_encode($a);  // JSON_PRETTY_PRINT
      //$ins['takestop_uuids'] = json_encode($b);  // JSON_PRETTY_PRINT

      
      

        $ins['enable'] = 1;

      //save_to_tbl5($ins);
        db_insert_update('indiset_combo_by_users', $ins);
    }
}

$db->query("UPDATE `indiset_combo_by_users` SET `enable` = 0 WHERE `user_id` = ?i AND `indiset_combo_id` NOT IN(?a)", $user_id, $indiset_combo_ids);
//$db->query("UPDATE `indiset_combo_by_users` SET `enable` = 1 WHERE `user_id` = ?i AND `indiset_combo_id`     IN(?a)", $user_id, $indiset_combo_ids);
//$q = $db->getAll("SELECT * FROM `indiset_combo_by_users` WHERE `user_id` = ?i", $user_id);


$q0 = $db->getCol("SELECT `indiset_combo_id` FROM `indiset_combo_by_users` WHERE `enable` = 0");
$q1 = $db->getCol("SELECT `indiset_combo_id` FROM `indiset_combo_by_users` WHERE `enable` = 1");
if(!empty($q0)) $db->query("UPDATE `indiset_combo` SET `enable` = 0 WHERE `id` IN(?a)", array_diff($q0, $q1));
if(!empty($q1)) $db->query("UPDATE `indiset_combo` SET `enable` = 1 WHERE `id` IN(?a)", $q1);




$result = $r;




$qq = $db->getAll("SELECT * FROM `indiset_combo` WHERE 1");
foreach($qq as $vv){
    
    $tmp = json_decode($vv['indiset_ids'], 1);
    if(empty($tmp)) continue;  // ERROR
  //$q = $db->getCol("SELECT `str` FROM `indiset` WHERE `id` IN(?a)", $tmp);
    ////////////////////////////////////////////////////////////////////////////////// здесь нужна сортировка по timeframe desc
  //$indicator = implode("\n", $q);
    $q = $db->getCol("SELECT `json` FROM `indiset` WHERE `id` IN(?a)", $tmp);
    $r = [];
    foreach($q as $v){
	$r[] = json_decode($v, 1);
    }
    $indicator = json_encode($r);
    
    $takestop = '';
    $q = $db->getAll("SELECT * FROM `indiset_combo_by_users` WHERE `enable` = 1 AND `indiset_combo_id` = ?i", $vv['id']);

    implode(',' array_column($q, 'side'));
    
	
    $r = [];
    foreach($q as $v){
        $tmp = json_decode($v['takestop_ids'], 1);
        if(!empty($tmp)) $r = array_merge($r, $tmp);
    }
    $r = array_unique($r);
    if(!empty($r)){
        $q = $db->getAll("SELECT * FROM `takestop` WHERE `id` IN(?a)", $r);
        $res = [];
        foreach($q as $v){
            $res[] = [$v['take'], $v['stop']];
        }
        $takestop = json_encode($res);
    }

    
    $ins = [];
    $ins['id'] = $vv['id'];
  //$ins['parent_id'] = 0;
    $ins['pair'] = '*';
    $ins['timeframe'] = 'undefined';
  //$ins['price'] = '';
    $ins['indicator'] = $indicator;
  //$ins['condition'] = '';
    $ins['enable'] = $vv['enable'];
    $ins['dt_ins'] = $vv['dt_ins'];
    $ins['positionSide'] = empty($takestop) ? 'undefined' : $takestop;
    $db->query("INSERT INTO `z_0_signal` SET ?u ON DUPLICATE KEY UPDATE ?u", $ins, $ins);

    $wdb->query("INSERT INTO `z_0_signal` SET ?u ON DUPLICATE KEY UPDATE ?u", $ins, $ins);
}




$db->query("UPDATE `strat` SET `saved` = 1 WHERE `user_id` = ?i LIMIT 1", $user_id);


//echo '<pre>'; print_r($result); echo '</pre>';













function takestop_parser($a = [], $side = 'long'){  // $a = json_decode('[[1.02,1.01],[1.03,1.04]]', 1);
    $r = [];
    foreach($a as $k=>$v){
        foreach($v as $kk=>$vv){
            if(substr($vv, -1) == '%'){
                $vv = substr($vv, 0, -1) * 0.01;

                if($kk == 0){  // take
                    if(    $side == 'long')  $vv = 1+$vv;
                    elseif($side == 'short') $vv = 1-$vv;
                }
                if($kk == 1){  // stop
                    if(    $side == 'long')  $vv = 1-$vv;
                    elseif($side == 'short') $vv = 1+$vv;
                }
            }

            $r[$k][$kk] = $vv;
        }
    }

    return $r;
}


function is_takestop($str){  // валидатор строки такого вида: [[1.02,1.01],[1.03,1.04]]
    $a = json_decode($str, 1);
    if(!empty($a)) return true;
    return false;
}


function is_indiset($str){
    if(strpos($str, '<') !== false || strpos($str, '>') !== false) return true;
    return false;
}


function is_pair($str){  // is_pairs
    if($str == '*') return true;  //// реализовать валидацию: *, через запятую, *usdt

    $r = true;
    $a = explode(',', $str);
    foreach($a as $v){
        if(substr($v, -4) != 'usdt') $r = false;
    }

    return $r;
}


function parse_text($text){
    $r = [];
    $r['signals'] = [];

    $a = $orig = explode("\n", $text);
    $a = array_map('trim', $a);
    $a = array_map('strtolower', $a);
    $a = str_replace(' ', '', $a);
    $i = 0;  // номер абзаца
    $n = 0;  // номер строки в абзаце
    foreach($a as $k=>$v){
        if(empty($v)){
          //if(!empty($a[$k+1])) $i++;
            $n = 0;
            continue;
        }

        if(substr($v, 0, 1) == '#')  continue;
        if(substr($v, 0, 2) == '//') continue;

        $n++;
        if($n == 1) $i++;


        if(empty($r['signals'][$i])){  // зададим порядок элементов в массиве
            $r['signals'][$i] = [
                                    'is_valid'          => true,
                                    'pair'              => '',
                                    'indiset'           => [],
                                    'takestop_original' => [],
                                    'takestop_decimal'  => [],
                                    'side'              => 'long',
                                    'stock'             => 'sandbox',  // sandbox_binance_spot
                                ];
        }


        $is_p  = is_pair($v);
        $is_i  = is_indiset($v);
        $is_ts = is_takestop($v);

        
        if(    in_array($v, ['short','long']))           $r['signals'][$i]['side']  = $v;
        elseif(in_array($v, ['sandbox','binance_spot'])) $r['signals'][$i]['stock'] = $v;
        elseif( $is_p && !$is_i && !$is_ts)              $r['signals'][$i]['pair']  = str_replace(['_','-','/'], '', $v);  // if($n == 1)  //////////////////////////// здесь нужна либо регулярка только цифры и англ.буквы или список монет в функцию
        elseif(!$is_p && !$is_i &&  $is_ts)              $r['signals'][$i]['takestop_original'] = json_decode($v, 1);      // if(empty($a[$k+1])
        elseif(!$is_p &&  $is_i && !$is_ts)              $r['signals'][$i]['indiset'][] = $v;
        else {
            $orig[$k] = '?????????? '.$orig[$k];
            $r['signals'][$i]['is_valid'] = false;
        }
    

        $takestop_original = $r['signals'][$i]['takestop_original'];
        $side              = $r['signals'][$i]['side'];
        if(!empty($takestop_original)) $r['signals'][$i]['takestop_decimal'] = takestop_parser($takestop_original, $side);

    }

    $r['text'] = implode("\n", $orig);

    
    foreach($r['signals'] as $i=>$v){
        if(empty($r['signals'][$i]['indiset'])) $r['signals'][$i]['is_valid'] = false;
    }


    return $r;
}




/*
function save_to_tbl1($d){  // strat
    global $tbl1;
    global $db;

    $ts = time();

    $ins = $upd = $d;

    $ins['ts_ins'] = $ts;
    $ins['dt_ins'] = date('Y-m-d H:i:s', $ts);

    $upd['ts_upd'] = $ts;
    $upd['dt_upd'] = date('Y-m-d H:i:s', $ts);

    $db->query("INSERT INTO ?n SET ?u ON DUPLICATE KEY UPDATE ?u", $tbl1, $ins, $upd);  //////////////////////// uniq(user_id)

    //echo '<br>';
    //echo '<br>ins='.$db->insertId();
    //echo '<br>upd='.$db->affectedRows();
    //echo '<br>';
}
*/

/*
function save_to_tbl3($d){  // indiset
    global $tbl3;
    global $db;

    $ts = time();

    $ins = [];
    
    $ins['uuid'] = guidv4();
    $ins['str'] = $d;
    
    $ins['ts_ins'] = $ts;
    $ins['dt_ins'] = date('Y-m-d H:i:s', $ts);

    $db->query("INSERT IGNORE INTO ?n SET ?u", $tbl3, $ins);

    //echo '<br>';
    //echo '<br>ins='.$db->insertId();
    //echo '<br>upd='.$db->affectedRows();
    //echo '<br>';
}
*/

/*
function save_to_tbl4($ins){  // indiset_combo
    global $tbl4;
    global $db;

    $ts = time();

  //$ins = [];
    
    $ins['uuid'] = guidv4();
    
    $ins['ts_ins'] = $ts;
    $ins['dt_ins'] = date('Y-m-d H:i:s', $ts);

    $db->query("INSERT IGNORE INTO ?n SET ?u", $tbl4, $ins);
}
*/

/*
function save_to_tbl5($ins){  // indiset_combo_by_users
    global $tbl5;
    global $db;

    $ts = time();

    $upd = $ins;
    
  //$ins['uuid'] = guidv4();
    
    $ins['ts_ins'] = $ts;
    $ins['dt_ins'] = date('Y-m-d H:i:s', $ts);

    $upd['ts_upd'] = $ts;
    $upd['dt_upd'] = date('Y-m-d H:i:s', $ts);

  //$db->query("INSERT IGNORE INTO ?n SET ?u", $tbl5, $ins);
    $db->query("INSERT INTO ?n SET ?u ON DUPLICATE KEY UPDATE ?u", $tbl5, $ins, $upd);
}
*/

function db_insert_ignore($tbl, $ins = []){
    global $db;

  //if(in_array($tbl, ['indiset','indiset_combo','takestop'])) $ins['uuid'] = guidv4();

    $ts = time();
    $ins['ts_ins'] = $ts;
    $ins['dt_ins'] = date('Y-m-d H:i:s', $ts);

    db_autoincrement($tbl);
    $db->query("INSERT IGNORE INTO ?n SET ?u", $tbl, $ins);
}

function db_insert_update($tbl, $ins = []){
    global $db;

    $upd = $ins;
    
  //if(in_array($tbl, ['indiset','indiset_combo','takestop'])) $ins['uuid'] = guidv4();

    $ts = time();
    $ins['ts_ins'] = $ts;
    $ins['dt_ins'] = date('Y-m-d H:i:s', $ts);
    $upd['ts_upd'] = $ts;
    $upd['dt_upd'] = date('Y-m-d H:i:s', $ts);

    db_autoincrement($tbl);
    $db->query("INSERT INTO ?n SET ?u ON DUPLICATE KEY UPDATE ?u", $tbl, $ins, $upd);
}

function db_autoincrement($tbl){
    global $db;
    try {
        $max_id = $db->getOne("SELECT MAX(`id`) FROM ?n", $tbl);
        if(!empty($max_id)) $db->query("ALTER TABLE ?n AUTO_INCREMENT = ?i", $tbl, ($max_id+1));
    }
    catch (Exception $e){

    }
}

function db_select($tbl, $data = []){
    
}




function indiset_parser($str){  // $str = 'bbands(5m,15)[1] > close';
  //$str = str_replace(' ', '', $str);  // закоментировал, потому что в функцию уже без пробелов строку передаем

    
    $regex = '/(\w+)\((.+)\)(\[(\d+)\])?([<>]=?)(.+)/';

    if(!preg_match($regex, $str, $match)) return '{"ok":false}';

    /*
    $regex = '/(\w+)\((.+)\)\[(\d+)\]([<>]=?)(.+)/';
    $result = [
        'function'  => $match[1],
        'params'    => explode(',', $match[2]),
        'num'       => $match[3],
        'sign'      => $match[4],
        'condition' => $match[5]
    ];
    */
    
    $result = [
        'function'  => $match[1],
        'params'    => explode(',', $match[2]),
        'num'       => empty($match[4]) ? '-1' : $match[4] ,
        'sign'      => $match[5],
        'condition' => $match[6]
    ];

    return json_encode($result);
}
