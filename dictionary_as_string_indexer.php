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

$seed_urls_filename = $_SERVER['argv'][1];
$seed_urls = file($seed_urls_filename);
$documents[count($seed_urls)];
$urls = [];
foreach ($seed_urls as $url) {
    $url = rtrim($url);
    $urls[] = [CrawlConstants::URL => $url];
}
$web_pages = FetchUrl::getPages(
    $urls
);
$html = new HtmlProcessor(max_description_len:20000, summarizer_option:CrawlConstants::CENTROID_WEIGHTED_SUMMARIZER);
for($a = 0; $a<count($seed_urls); $a++){
    $web_page = preg_split("/\s+|\d+|\W+/", strtolower($html->process($web_pages[$a]['q'], $seed_urls[$a])['q']));
    $web_page = array_filter($web_page, fn($value) => $value !== "");
    $documents[$a] = array_filter(PhraseParser::stemTerms($web_page, 'en-US'));
}
$inverted_index = [];

for ($i = 0; $i < count($documents); $i++) {
    foreach ($documents[$i] as $word) {
        $entry = $i + 1;

        if (!isset($inverted_index[$word])) {
            $inverted_index[$word] = [$entry];
        } else if (!in_array($entry, $inverted_index[$word])) {
            $inverted_index[$word][] = $entry;
        }
    }
}

ksort($inverted_index);
// print_r($inverted_index);
$line1 = "";
$num_words = str_pad(count($inverted_index), 8, "0", STR_PAD_LEFT);
$line1 .= substr($num_words, -8);
$idx = 0;

foreach (array_keys($inverted_index) as $word) {
    $line1 .= substr(str_pad($idx, 8, "0", STR_PAD_LEFT), -8);
    $idx += strlen($word) + 8;
    
}

$line2 = "";
$pos = 0;
$line3 = "";

foreach ($inverted_index as $word => $docs) {
    $line2 .= $word;
    $line2 .= substr(str_pad($pos, 8, "0", STR_PAD_LEFT), -8);
    $pos += 2 * count($docs);
    
    foreach ($docs as $doc) {
        $line3 .= $doc . ',';
    }
}

$line3 = rtrim($line3, ',');

$partition_tools = new PackedTableTools(
            ["L1" => "INT",
            "L2" => "INT",
            "L3" => "INT"]);

$out_value = $partition_tools->pack([
    "L1" => strlen($line1),
    "L2" => strlen($line2),
    "L3" => strlen($line3)
]);

$index_file_name = $_SERVER['argv'][2];
$index_file = fopen($index_file_name, "w");
fwrite($index_file, $out_value);
$line1_packed=pack("a*", $line1);
$line2_packed=pack("a*", $line2);
$line3_packed=pack("a*", $line3);
fwrite($index_file, "\n");
fwrite($index_file, $line1_packed);
fwrite($index_file, "\n");
fwrite($index_file, $line2_packed);
fwrite($index_file, "\n");
fwrite($index_file, $line3_packed);
fclose($index_file);

print_r($line1);
print_r("\n");
print_r($line2);
print_r("\n");
print_r($line3);
print_r("\n");