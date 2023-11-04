<?php
namespace rkumar\hw3;

use seekquarry\yioop\configs as SYC;
use seekquarry\yioop\library as SYL;
use seekquarry\yioop\library\Library;
use seekquarry\yioop\library\CrawlConstants;
use seekquarry\yioop\library\PhraseParser;
use seekquarry\yioop\library\FetchUrl;
use seekquarry\yioop\library\processors\HtmlProcessor;
use seekquarry\yioop\library\PackedTableTools;
use seekquarry\yioop\library\LinearAlgebra;

require_once "vendor/autoload.php";
/*
   Since a normal Yioop instance needs a Profile.php file to be generated,
   the following is used to set up Yioop in library mode so you don't need this.
   To enable debugging use Library::init(true);
 */
Library::init();
error_reporting(-1);

$index_file_name = $_SERVER['argv'][1];
$index_file_lines = file($index_file_name);

$partition_tools = new PackedTableTools(
    ["L1" => "INT",
    "L2" => "INT",
    "L3" => "INT"]);

$line0 = $partition_tools->unpack($index_file_lines[0]);

$line1 = rtrim(unpack("a*", $index_file_lines[1])[1]);
$line2 = rtrim(unpack("a*", $index_file_lines[2])[1]);
$line3 = rtrim(unpack("a*", $index_file_lines[3])[1]);




$line3_arr = array_map('intval', explode(',', $line3));
$no_of_docs = max($line3_arr);
$vocab_size=(int)substr($line1, 0, 8)-1;
$doc_term = array_fill(0, $no_of_docs, array_fill(0, $vocab_size, 0));

$line1_arr = array_map('intval', str_split($line1, 8));

$words=[];
$starts=[];



for($x=1; $x<count($line1_arr)-1;$x++){

    $temp=substr($line2, $line1_arr[$x], $line1_arr[$x+1]-$line1_arr[$x]);
    $words[]=substr($temp, 0, strlen($temp)-8);
    $starts[]=(int)substr($temp, strlen($temp)-8, 8);
}
$idf_arr=[];
for($x=0; $x<count($words)-1;$x++){

    $idf_i=log($no_of_docs*2/($starts[$x+1]-$starts[$x]), 2);
    $idf_arr[]=$idf_i;

    for($y=$starts[$x];$y<$starts[$x+1];$y+=2){
        $doc_term[$line3_arr[$y/2]-1][$x]=$idf_i;
    }
}

$query=$_SERVER['argv'][2];
$query = preg_split("/\s+|\d+|\W+/", strtolower($query));
$query = array_filter($query, fn($value) => $value !== "");
$query_vector=array_fill(0, $vocab_size, 0);

foreach($query as $q){

    if(in_array($q, $words)){
        $key = array_search($q, $words);
        $query_vector[$key]=$idf_arr[$key];
    }
}
$tf_idf=[];
for($i=0;$i<$no_of_docs;$i++){
    
    $tf_idf[$i+1]=LinearAlgebra::similarity($doc_term[$i], $query_vector);

}

arsort($tf_idf);

foreach ($tf_idf as $key => $value) {
    print_r("(".$key.",".$value.")\n");
}
?>