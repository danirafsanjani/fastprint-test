<?php
error_reporting(0);
$db = mysqli_connect('localhost', 'root', '', 'fastprint');
$base = getdata();
if($base){
    for ($i = 0; $i < count($base); $i++){
        $get_kategori[] = $base[$i]['kategori'];
        $get_status[] = $base[$i]['status'];
    }
    $base_kategori = array_values(array_unique($get_kategori));
    for ($i = 0; $i < count($base_kategori); $i++){
        $list_kategori[] = "category$i|$base_kategori[$i]";
        $query = "INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES ('category".$i."', '".$base_kategori[$i]."')";
        mysqli_query($db, $query);
    }
    $base_status = array_values(array_unique($get_status));
    for ($i = 0; $i < count($base_status); $i++){
        $list_status[] = "status$i|$base_status[$i]";
        $query = "INSERT INTO `status` (`id_status`, `nama_status`) VALUES ('"."status".$i."', '".$base_status[$i]."')";
        mysqli_query($db, $query);
    }
    for ($i = 0; $i < count($base); $i++){
        for ($j = 0; $j < count($list_kategori); $j++){
            if($base[$i]['kategori'] == explode("|", $list_kategori[$j])[1]){
                $find_kategori = explode("|", $list_kategori[$j])[0];
            }
        }
        for ($j = 0; $j < count($list_status); $j++){
            if($base[$i]['status'] == explode("|", $list_status[$j])[1]){
                $find_status = explode("|", $list_status[$j])[0];
            }
        }
        $query = "INSERT INTO `produk` (`id_produk`, `nama_produk`, `harga`, `kategori_id`, `status_id`) VALUES ('".$base[$i]['id_produk']."', '".$base[$i]['nama_produk']."', '".$base[$i]['harga']."', '".$find_kategori."', '".$find_status."')";
        mysqli_query($db, $query);
    }
    echo "Success Get Data From Server!";
}
else{
    echo "Failed Get Data From Server!";
}




function getdata(){
    $date = date("j-m-y");
    $user = getLogin()[0];
    $pass = md5(getLogin()[1].$date);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://recruitment.fastprint.co.id/tes/api_tes_programmer');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "username=$user&password=$pass");
    $headers = array();
    $headers[] = 'Host: recruitment.fastprint.co.id';
    $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:120.0) Gecko/20100101 Firefox/120.0';
    $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8';
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    $decode = json_decode($result, true)['data'];
    return $decode;
}

function getLogin(){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://recruitment.fastprint.co.id/tes/programmer');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = array();
    $headers[] = 'Host: recruitment.fastprint.co.id';
    $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:120.0) Gecko/20100101 Firefox/120.0';
    $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8';
    $headers[] = 'Accept-Language: en-US,en;q=0.5';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    $username = getStr("Username:", " (username", $result);
    $username = str_replace("</span> ", "", $username);
    $password = getStr("(md5):", "tanggal", $result);
    $password = str_replace("</span> ", "", $password);
    return array($username, $password);
}

function getStr($a, $b, $c){
    return explode($b, explode($a, $c)[1])[0];
}