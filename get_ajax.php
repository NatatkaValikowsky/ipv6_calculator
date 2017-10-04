<?php 
header("Content-type: text/plain; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
sleep(2);

echo ('<style>td{background-color:#99FFCC;padding:10px;}</style>');

function formated_data($str, $size, $sep) {
	$str_formated = "";
	if(strlen($str) > $size) {
		if (strlen($str) % $size == 0) {
			$str_formated = substr($str,0,$size);
			$start = $size;
		}
		else {
		$start = strlen($str) % $size;
		$str_formated = substr($str,0,$start);
		}
		for($i = $start; $i < strlen($str); ) {
		    $str_formated = $str_formated . $sep . substr($str,$i,$size);
		    $i = $i+$size;
		}
		return $str_formated;
	}
	return $str;
}

function count_adress($n) {
	return formated_data(bcpow(2,128-$n),3,",");
}

//ТИП АДРЕСА - НЕПРАВИЛЬНЫЕ ДАННЫЕ!!!!!
// function get_type_of_adress($adress) {
// 	if($adress == "0000 : 0000 : 0000 : 0000 : 0000 : 0000 : 0000 : 0000") {
// 		return "UNSPECIFIED ADDRESS";
// 	}

// 	if($adress == "0000 : 0000 : 0000 : 0000 : 0000 : 0000 : 0000 : 0001") {
// 		return "LOOPBACK";
// 	}

// 	if(substr($adress,0,11) == "2001 : 0DB8") {
// 		return "DOCUMENTATION";
// 	}

// 	if(hexdec(substr($adress,0,4)) >= hexdec("2000") && hexdec(substr($adress,0,4)) <= hexdec("3FFF")) {
// 		return "GLOBAL UNICAST";
// 	}

// 	if(hexdec(substr($adress,0,4)) >= hexdec("FE80") && hexdec(substr($adress,0,4)) <= hexdec("FEBF")) {
// 		return "LINK-LOCAL";
// 	}

// 	if(substr($adress,0,2) == "FF") {
// 		return "MULTICAST";
// 	}

// 	return "Тип не определен";
// }

function get_network_range($adress,$prefix) {
    //разобьем строку по кусочкам в массив
	$adress_array = explode(" : ",$adress);
	
	//переводим каждый кусок адреса в двоичную с/с, при необходимости дополняем нулями спереди
    for($i = 0; $i < count($adress_array); $i++) {
        $adress_array[$i] = base_convert($adress_array[$i],16,2);
        if(strlen($adress_array[$i]) < 16) {
            $x = "";
            for($j = 0; $j < (16 - strlen($adress_array[$i]));$j++) {
                $x = $x . "0";
            }
            $adress_array[$i] = $x . $adress_array[$i];
        }
    }
    
    //теперь соединяем обратно 
    $adress_numbers = implode($adress_array);
    
    //отделяем неизменную часть
    $net_const = substr($adress_numbers,0,$prefix);
	
	//вычисляем диапазон
	$min = $net_const;
	
	while(strlen($min)< 128 ) {
	    $min = $min . "0";
	}
	
	$max = $net_const;
	
	while(strlen($max) < 128) {
	    $max = $max . "1";
	}
	
	//теперь опять делим на кусочки по 16 разрядов и переводим в 16ричную с/с
	$array_min = array();
	for($i = 0; $i < strlen($min); ) {
        $array_min[$i] = strtoupper(base_convert(substr($min,$i,16),2,16));
        if(strlen($array_min[$i]) < 4) {
            $x = "";
            for($j = 0; $j < (4 - strlen($array_min[$i]));$j++) {
                $x = $x . "0";
            }
            $array_min[$i] = $x . $array_min[$i];
        }
        $i = $i + 16;
	}
	
	$min = implode($array_min, " : ");
	
	$array_max = array();
	for($i = 0; $i < strlen($max); ) {
        $array_max[$i] = strtoupper(base_convert(substr($max,$i,16),2,16));
        if(strlen($array_max[$i]) < 4) {
            $x = "";
            for($j = 0; $j < (4 - strlen($array_max[$i]));$j++) {
                $x = $x . "0";
            }
            $array_max[$i] = $x . $array_max[$i];
        }
        $i = $i + 16;
	}
	
	$max = implode($array_max, " : ");
	
	return $min . " - <br> " . $max;
}

function get_shortcut_adress($adress) {
	$adress_numbers = "";
	$size = 4;
	$is_cutted = false;
	if($adress == "0000 : 0000 : 0000 : 0000 : 0000 : 0000 : 0000 : 0000") return "::";
	//убираем из строки пробелы и разелители
	for($i = 0; $i < strlen($adress); $i++){
		if(substr($adress, $i, 1) !== " " && substr($adress, $i, 1) !== ":") {
			$adress_numbers = $adress_numbers . substr($adress, $i, 1);
		}
	}
	$adress_array = array();
	//сначала заменяем все хесктеты, равные 0000, символом двоеточия, записываем в массив
	for($i = 0; $i <= strlen($adress_numbers); ){
		if(substr($adress_numbers, $i, 4) == "0000") {
			array_push($adress_array, ":");
		}
		else array_push($adress_array, substr($adress_numbers, $i, 4));
		$i += 4;
	}
	//далее мы сокращаем первую цепочку из пустых хекстетов
	for($i = 0; $i < count($adress_array); ) {
	    if($adress_array[$i] == ":" && $adress_array[$i+1] == ":") {
	        $is_cutted = true;
	        $adress_array[$i] = "";
	        break;
	    }
	    $i++;
	}
    $flag = $i+1;
    for($i = $flag; $i <= count($adress_array) && $adress_array[$i] == ":"; $i++ ) {
        unset($adress_array[$i]);   
    }
    $array_cutten = array_values($adress_array);
    //теперь мы должны вернуть нули на оставшиеся места с :
    for($i = 0; $i < count($array_cutten); $i++) {
        if($i != $flag-1 && $array_cutten[$i] == ":") {
            $array_cutten[$i] = "0000";
        }
    }
    
    //здесь мы должны убрать первые нули из сочетаний
    
    for($i = 0; $i < count($array_cutten); $i++) {
        if($array_cutten[$i] != "0000") {
            if(substr($array_cutten[$i],0,3) == "000") {
                $array_cutten[$i] = substr($array_cutten[$i],3);
            }
            else if(substr($array_cutten[$i],0,2) == "00") {
                $array_cutten[$i] = substr($array_cutten[$i],2);
            }
            else if(substr($array_cutten[$i],0,1) == "0") {
                $array_cutten[$i] = substr($array_cutten[$i],1);
            }
        }
    }
    
    //теперь нужно вернуть строку, собранную с разделителем :


    $result = implode($array_cutten, ":");
    if(substr($result,strlen($result)-1) == ":" && substr($result,strlen($result)-2) != "::") {
    	$result = $result . ":";
    }
    return $result;
}


function get_nearest_pow($x) {
	$y = 1;
    $st = 0;
	while($y < $x) {
		$y = $y * 2;
	}
	return $y;
}

function get_nearest_pokazat($x) {
    $y = 1;
    $st = 0;
    while($y < $x) {
        $y = $y * 2;
        $st = $st + 1;
    }
    return $st;
}


function get_subnets($adress, $prefix, $count) {
    //сначала посчитали, сколько разрядов надо для подсетей выделить
    $subnet_id_length = get_nearest_pokazat($count);
    
    //посчитаем заодно префикс подсети, он будет нужен
    $subnet_prefix = $prefix + $subnet_id_length;
    
    //распиливаем строчку на массив, используя в качестве разделителя " : "
    $adress_array = explode(" : ",$adress);
    
    //теперь преобразуем все числа в двоичные
    for($i = 0; $i < count($adress_array); $i++) {
        $adress_array[$i] = base_convert($adress_array[$i],16,2);
        if(strlen($adress_array[$i]) < 16) {
            $x = "";
            for($j = 0; $j < (16 - strlen($adress_array[$i]));$j++) {
                $x = $x . "0";
            }
            $adress_array[$i] = $x . $adress_array[$i];
        }
    }
    
    //склеиваем массив в строку
    $adress_in_bin = implode($adress_array);
    
    //отделяем из строки идентификатор сети
    $lan_id = substr($adress_in_bin,0,$prefix);
    
    //отделяем из строки идентификатор адреса устройства
    $machine_id = "";
    for($i = 1; $i <= 128-($prefix+$subnet_id_length); $i++){
        $machine_id = $machine_id . "0";
    }
    
    for($i = 1; $i <= $count; $i++) {
        $subnet_id = base_convert($i,10,2);
        if(strlen($subnet_id) < $subnet_id_length) {
            $x = "";
            for($j = 0; $j < ($subnet_id_length - strlen($subnet_id));$j++) {
                $x = $x . "0";
            }
            $subnet_id = $x . $subnet_id;
        }
        $ip = $lan_id . $subnet_id . $machine_id;
        $adress_array = array();
        for($j = 0; $j < 128; ){
            $adress_array[$j] = substr($ip,$j,16);
            $adress_array[$j] = base_convert($adress_array[$j],2,16);
            if(strlen($adress_array[$j])<4) {
                if(strlen($adress_array[$j]) == 3) $adress_array[$j] = "0" . $adress_array[$j];
                if(strlen($adress_array[$j]) == 2) $adress_array[$j] = "00" . $adress_array[$j];
                if(strlen($adress_array[$j]) == 1) $adress_array[$j] = "000" . $adress_array[$j];
            }
            $j = $j+16;
        }
        $ip = implode($adress_array," : ");
        echo('<tr><td>' . $i . '</td><td>' . strtoupper($ip) . '</td><td>' . ($subnet_prefix) . '</td><td>' . get_network_range($ip,$subnet_prefix) . '</td><td>' . count_adress($subnet_prefix) . '</td></tr>');
    }

    echo "Остаток свободных подсетей в данной сети - " . (get_nearest_pow($count) - $count);
}

?>

<table border="0">
	<tr>
		<td>IP-адрес</td>
		<td><?php echo(get_shortcut_adress($_POST['first_area'])); ?></td>
	</tr>
	<tr>
		<td>Длина префикса</td>
		<td><?php echo $_POST['select_list']; ?></td>
	</tr>
	<tr>
		<td>Сетевой диапазон</td>
		<td><?php echo get_network_range($_POST['first_area'], $_POST['select_list']); ?></td>
	</tr>
	<tr>
		<td>Количество адресов</td>
		<td><?php
			 echo count_adress($_POST['select_list']); ?></td>
	</tr>
</table>

<h3>РАЗДЕЛЕНИЕ СЕТИ НА ПОДСЕТИ</h3>

<?php
	if($_POST['second_area'] <= bcpow(2,128-$_POST['select_list'])) {
		echo('<table border=0>');
		echo('<tr><td>№</td><td>IP-адрес подсети</td><td>Префикс подсети</td><td>Диапазон адресов</td><td>Количество адресов</td></tr>');
		get_subnets($_POST['first_area'], $_POST['select_list'], $_POST['second_area']);
		echo('</table');
	}
	else echo "Такое количество подсетей невозможно выделить в данной сети";
 ?>