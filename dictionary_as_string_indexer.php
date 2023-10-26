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

require_once "vendor/autoload.php";
/*
   Since a normal Yioop instance needs a Profile.php file to be generated,
   the following is used to set up Yioop in library mode so you don't need this.
   To enable debugging use Library::init(true);
 */
Library::init();
error_reporting(-1);

$seed_urls = file("my_seeds.txt");
$documents[count($seed_urls)];
$urls = [];
foreach ($seed_urls as $url) {
    $url = rtrim($url);
    $urls[] = [CrawlConstants::URL => $url];
}
$web_pages = FetchUrl::getPages(
    $urls
     // we could list more urls to download
);
$html = new HtmlProcessor(max_description_len:20000, summarizer_option:CrawlConstants::CENTROID_WEIGHTED_SUMMARIZER);
// // print_r($pages[0]['q']);
// print_r(preg_split("/\s+|\d+|\W+/", $html->process($pages[0]['q'], "https://www.yahoo.com/")['q']));
for($a = 0; $a<count($seed_urls); $a++){
    // print_r($web_pages[$a]['q']);
    $web_page = preg_split("/\s+|\d+|\W+/", $html->process($web_pages[$a]['q'], $seed_urls[$a])['q']);
    $documents[$a] = array_filter(PhraseParser::stemTerms($web_page, 'en-US'));
    // print_r($documents[$a]);
    // print_r($a);
}
// print_r($documents[5]);
$inverted_index = [];

for ($i = 0; $i < count($documents); $i++) {
    // print_r($inverted_index);
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
print(count($inverted_index));
print_r("\n");
$line1 = "";
$num_words = str_pad(count($inverted_index), 8, "0", STR_PAD_LEFT);
$line1 .= substr($num_words, -8);
$line1 .= " ";
$idx = 0;

foreach (array_keys($inverted_index) as $word) {
    $line1 .= substr(str_pad($idx, 8, "0", STR_PAD_LEFT), -8);
    $idx += strlen($word) + 8;
    $line1 .= " ";
    
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
print_r($line1);
print_r("\n");
print_r($line2);
print_r("\n");
print_r($line3);

$no_of_docs = max(array_map('intval', explode(',', $line3)));
$doc_term = array_fill(0, $no_of_docs, array_fill(0, (int)substr($line1, 0, 8), 0));

?>