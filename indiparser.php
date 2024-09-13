<?php
ini_set('display_errors', 1);




$query_create = "
CREATE TABLE `example` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,
    `text` longtext NOT NULL DEFAULT '',
    `pair` varchar(250) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    UNIQUE (`pair`),
    INDEX (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

$query_create = "
CREATE TABLE `strat` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,
    `dt_upd` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_upd` int UNSIGNED NOT NULL DEFAULT 0,

    `user_id` int UNSIGNED NOT NULL DEFAULT 0,
    `text_in` longtext NOT NULL DEFAULT '',
    `text_out` longtext NOT NULL DEFAULT '',
    `json` longtext NOT NULL DEFAULT '',

    PRIMARY KEY (`id`),
    UNIQUE (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `strat_history` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,
    `dt_upd` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_upd` int UNSIGNED NOT NULL DEFAULT 0,

    `user_id` int UNSIGNED NOT NULL DEFAULT 0,
    `text_in` longtext NOT NULL DEFAULT '',
    `text_out` longtext NOT NULL DEFAULT '',
    `json` longtext NOT NULL DEFAULT '',

    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `indiset` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` char(36) NOT NULL DEFAULT '',
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,

    `str` varchar(250) NOT NULL DEFAULT '',

    PRIMARY KEY (`id`),
    UNIQUE (`str`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `indiset_combo` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` char(36) NOT NULL DEFAULT '',
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,

   #`indiset_id` int UNSIGNED NOT NULL DEFAULT 0,
   #`indiset_uuid` char(36) NOT NULL DEFAULT '',

    `indiset_ids` longtext NOT NULL DEFAULT '',
    `indiset_uuids` longtext NOT NULL DEFAULT '',
    `enable` int UNSIGNED NOT NULL DEFAULT 0,

    PRIMARY KEY (`id`),
    UNIQUE(`indiset_ids`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `indiset_combo_by_users` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  # `uuid` char(36) NOT NULL DEFAULT '',
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,

    `dt_upd` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_upd` int UNSIGNED NOT NULL DEFAULT 0,

    `user_id` int UNSIGNED NOT NULL DEFAULT 0,
    `indiset_combo_id` int UNSIGNED NOT NULL DEFAULT 0,
    `indiset_combo_uuid` char(36) NOT NULL DEFAULT '',
    `enable` int UNSIGNED NOT NULL DEFAULT 0,
    `pairs` longtext NOT NULL DEFAULT '',
    `takestop_combo_id` int UNSIGNED NOT NULL DEFAULT 0,
    `takestop_combo_uuid` char(36) NOT NULL DEFAULT '',

    PRIMARY KEY (`id`),
    UNIQUE(`user_id`,`indiset_combo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

#CREATE TABLE `indiset_combo_history` ######################################################

CREATE TABLE `takestop` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` char(36) NOT NULL DEFAULT '',
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,

    `take` varchar(250) NOT NULL DEFAULT '',
    `stop` varchar(250) NOT NULL DEFAULT '',

    PRIMARY KEY (`id`),
    UNIQUE (`take`,`stop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `takestop_combo` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid` char(36) NOT NULL DEFAULT '',
    `dt_ins` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
    `ts_ins` int UNSIGNED NOT NULL DEFAULT 0,

    `takestop_id` int UNSIGNED NOT NULL DEFAULT 0,
    `takestop_uuid` char(36) NOT NULL DEFAULT '',

    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";



$user_id =    (int)@$_REQUEST['user_id'];
$text    = (string)@$_REQUEST['text'];


echo '<form>';
echo '<textarea autofocus rows="22" cols="55" name="text">'.$text.'</textarea>';
echo '<input type="text" name="user_id" value="'.$user_id.'">';
echo '<input type="submit">';
echo '</form>';

/*
ТАБЛИЦА В БД:
id
dt_ins
ts_ins
dt_upd
ts_upd
str1
str2
str3
str4
*/



require_once __DIR__.'/core.php';
$tbl1 = 'strat';  // uniq(user_id) + textarea
$tbl2 = '';  // textarea change log
$tbl3 = 'indiset';  // indiset     // INDIcators SETtings
$tbl4 = 'indiset_combo';  // setindiset  // SET of INDIcators SETtings  // indiset_combo   // combo_indiset
$tbl5 = 'indiset_combo_by_users';  // takestop                                   // takestop_combo  // combo_takestop
$tbl6 = '';




/*
$q = select from tbl1
if($q){
    insert into tbl2 set $q (log)
}
*/




$d = [];
$d['user_id'] = $user_id;
$d['text_in'] = $text;
//$d['str2'] = prepare_text($text);  // trim() + удаляем парные пробелы + удаляем парные переносы строк + удаляем пробелы в пустых строках
//save_to_tbl1($d);



$r = parse_text($text);  // парсим синтаксис сигналов и в случае ошибок добавляем знаки вопроса в начало соответствующих строк
//$d = [];
$d['user_id'] = $user_id;
$d['text_out'] = $r['text'];
$d['json'] = json_encode(array_values($r['signals']), JSON_PRETTY_PRINT);
//$d['str4'] = $r['error_msg'];
save_to_tbl1($d);
//// в str4 наверное лучше запишем uuid пар, индикаторов и тэкстопов (в json?)
//// перед этим в цикле пройти найти их в соответствующих таблицах




$indiset_combo_ids = [];
foreach($r['signals'] as $v){
    $ids = $uuids = [];
    foreach($v['indisets'] as $vv){
        save_to_tbl3($vv);
        
        $q = $db->getRow("SELECT * FROM ?n WHERE `str` = ?s LIMIT 1", $tbl3, $vv);
        echo '<br>'.$q['id'];

        $ids[$vv]   = $q['id'];
        $uuids[$vv] = $q['uuid'];
    }

    if(!empty($v['is_valid'])){
        $a = $b = [];
        foreach($v['indisets'] as $vv){
            $a[] = $ids[$vv];
            $b[] = $uuids[$vv];
        }
        sort($a); sort($b);  // по возрастанию значения, без сохранения ключей
        $ins = [];
      //$ins['indiset_ids']   = implode(',',$a);
      //$ins['indiset_uuids'] = implode(',',$b);
        $ins['indiset_ids']   = json_encode($a, JSON_PRETTY_PRINT);
        $ins['indiset_uuids'] = json_encode($b, JSON_PRETTY_PRINT);
        
        save_to_tbl4($ins);  // indiset_combo

        $q = $db->getRow("SELECT * FROM ?n WHERE `indiset_ids` = ?s LIMIT 1", $tbl4, $ins['indiset_ids']);

        $ins = [];
        $ins['user_id'] = $user_id;
        $ins['indiset_combo_id']   = $indiset_combo_ids[] = $q['id'];
        $ins['indiset_combo_uuid']                        = $q['uuid'];
        
        $ins['pairs'] = $v['pairs'];
      //$ins['takestop_combo_id'] = 0;
        $ins['takestop_combo_uuid'] = json_encode($v['takes_stops_combo']);  /////////////////////// наверное это надо прогнать через таблицы и положить сюда combo_id

        $ins['enable'] = 1;

        save_to_tbl5($ins);
    }
}

$db->query("UPDATE ?n SET `enable` = 0 WHERE `user_id` = ?i AND `indiset_combo_id` NOT IN(?a)", $tbl5, $user_id, $indiset_combo_ids);
//$db->query("UPDATE ?n SET `enable` = 1 WHERE `user_id` = ?i AND `indiset_combo_id`     IN(?a)", $tbl5, $user_id, $indiset_combo_ids);
//$q = $db->getAll("SELECT * FROM ?n WHERE `user_id` = ?i", $tbl5, $user_id);


echo '<pre>'; print_r($r); echo '</pre>';

//return json_encode($r)
//insert/update into tbl
//insert into tbl_log







function prepare_text($text){
    // trim() + удаляем парные пробелы + удаляем парные переносы строк + удаляем пробелы в пустых строках
    
    //$text = str_replace(' ', '', $text);
    //$text = trim($text);
    //$a = explode("\n", $text);
    //$a = array_map('trim', $a);
    //$text = implode("\n", $a);

    return $text;
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

function is_pairs($str){
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
                                    'pairs'             => '',
                                    'indisets'          => [],
                                    'takes_stops_combo' => [],
                                    'side'              => 'long',
                                    'stock'             => 'sandbox',  // sandbox_binance_spot
                                ];
        }


        $is_p  = is_pairs($v);
        $is_i  = is_indiset($v);
        $is_ts = is_takestop($v);

        
        if(    in_array($v, ['short','long']))           $r['signals'][$i]['side']              =             $v;
        elseif(in_array($v, ['sandbox','binance_spot'])) $r['signals'][$i]['stock']             =             $v;
        elseif( $is_p && !$is_i && !$is_ts)              $r['signals'][$i]['pairs']             = str_replace(['_','-','/'], '', $v);      // if($n == 1)  //////////////////////////// здесь нужна либо регулярка только цифры и англ.буквы или список монет в функцию
        elseif(!$is_p && !$is_i &&  $is_ts)              $r['signals'][$i]['takes_stops_combo'] = json_decode($v, 1);                      // if(empty($a[$k+1])
        elseif(!$is_p &&  $is_i && !$is_ts)              $r['signals'][$i]['indisets'][]        =             $v;
        else {
            $orig[$k] = '?????????? '.$orig[$k];
            $r['signals'][$i]['is_valid'] = false;
        }
    

    }

    $r['text'] = implode("\n", $orig);

    
    foreach($r['signals'] as $i=>$v){
        if(empty($r['signals'][$i]['indisets'])) $r['signals'][$i]['is_valid'] = false;
    }


    return $r;


    /*
    $a = explode("\n\n", $text);

    $r = [];
    foreach($a as $v){
        $aa = explode("\n", $v);
        $pairs = array_shift($aa);
        
        // надо как-то провалидировать что нижняя строка вот такого вида: [[1.02,1.01],[1.03,1.04]]
        $takestops = [];
        $tmp = $aa;
        $tmp1 = array_pop($tmp);
        $tmp1 = json_decode($tmp1, 1);
        if(!empty($tmp1)){
            $takestops = $tmp1;
            $aa = $tmp;
        }

        $indisets = [];  // INDIcators SETs
        foreach($aa as $v){
            $indisets = $v;
        }
        
        $r[] = [];  ////
    }
    */

    /*
    $r['text'] = '';
  //$r['error_msg'] = '';
    $r['signal']['pairs'] = '';
    $r['signal']['indicators'] = [];
    $r['signal']['takestops']  = [];
    return $r;
    */
}





function save_to_tbl1($d){
    global $tbl1;
    global $db;

    $ts = time();

    $ins = $upd = $d;

    $ins['ts_ins'] = $ts;
    $ins['dt_ins'] = date('Y-m-d H:i:s', $ts);

    $upd['ts_upd'] = $ts;
    $upd['dt_upd'] = date('Y-m-d H:i:s', $ts);

    $db->query("INSERT INTO ?n SET ?u ON DUPLICATE KEY UPDATE ?u", $tbl1, $ins, $upd);  //////////////////////// uniq(user_id)
/*
    echo '<br>';
    echo '<br>ins='.$db->insertId();
    echo '<br>upd='.$db->affectedRows();
    echo '<br>';
*/
}

function save_to_tbl3($d){
    global $tbl3;
    global $db;

    $ts = time();

    $ins = [];
    
    $ins['uuid'] = guidv4();
    $ins['str'] = $d;
    
    $ins['ts_ins'] = $ts;
    $ins['dt_ins'] = date('Y-m-d H:i:s', $ts);

    $db->query("INSERT IGNORE INTO ?n SET ?u", $tbl3, $ins);
/*
    echo '<br>';
    echo '<br>ins='.$db->insertId();
    echo '<br>upd='.$db->affectedRows();
    echo '<br>';
*/
}

function save_to_tbl4($ins){
    global $tbl4;
    global $db;

    $ts = time();

  //$ins = [];
    
    $ins['uuid'] = guidv4();
    
    $ins['ts_ins'] = $ts;
    $ins['dt_ins'] = date('Y-m-d H:i:s', $ts);

    $db->query("INSERT IGNORE INTO ?n SET ?u", $tbl4, $ins);
}

function save_to_tbl5($ins){
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



function db_insert($tbl, $data = []){

}

function db_insert_update($tbl, $data = []){
    
}

function db_select($tbl, $data = []){
    
}
