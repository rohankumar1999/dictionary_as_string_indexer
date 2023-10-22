<?php
namespace rkumar\hw3;

use seekquarry\yioop\configs as SYC;
use seekquarry\yioop\library as SYL;
use seekquarry\yioop\library\Library;
use seekquarry\yioop\library\CrawlConstants;
use seekquarry\yioop\library\PhraseParser;
use seekquarry\yioop\library\FetchUrl;
use seekquarry\yioop\library\processors\HtmlProcessor;

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
    $urls[] = [CrawlConstants::URL => "$url"];
}
// foreach ($urls as $url) {
//     print_r(FetchUrl::getPages(
//         [$url],
//          // we could list more urls to download
//     ));
// }

$web_pages = FetchUrl::getPages(
    [
        [CrawlConstants::URL => "https://time.is/"],
        [CrawlConstants::URL => "https://www.time.gov/"],
        [CrawlConstants::URL => "https://vclock.com/time/"],
        [CrawlConstants::URL => "https://www.timeanddate.com/worldclock/usa/san-jose"],
        [CrawlConstants::URL => "https://clock.zone/"],
        [CrawlConstants::URL => "https://time.is/"],
        [CrawlConstants::URL => "https://www.timeanddate.com/worldclock/usa/san-jose"],
        [CrawlConstants::URL => "https://vclock.com/time/san-jose-california-united-states/"],
        [CrawlConstants::URL => "https://24timezones.com/United-States/time"],
        [CrawlConstants::URL => "https://time.is/United_States"],
        [CrawlConstants::URL => "https://registertovote.ca.gov/"],
        [CrawlConstants::URL => "https://www.usa.gov/register-to-vote"],
        [CrawlConstants::URL => "https://www.sos.ca.gov/elections/voting-resources/voting-california/registering-vote"],
        [CrawlConstants::URL => "https://vote.gov/register/ca/"],
        [CrawlConstants::URL => "https://vote.gov/"],
        [CrawlConstants::URL => "https://www.usa.gov/register-to-vote"],
        [CrawlConstants::URL => "https://www.vote.org/register-to-vote/"],
        [CrawlConstants::URL => "https://www.sos.ca.gov/elections/voter-registration"],
        [CrawlConstants::URL => "https://registertovote.ca.gov/"],
        [CrawlConstants::URL => "https://www.sos.ca.gov/elections/voting-resources/voting-california/registering-vote"],
        [CrawlConstants::URL => "https://www.theknot.com/content/how-to-tie-a-tie"],
        [CrawlConstants::URL => "https://www.youtube.com/watch?v=xAg7z6u4NE8"],
        [CrawlConstants::URL => "https://www.ties.com/how-to-tie-a-tie"],
        [CrawlConstants::URL => "https://www.wikihow.com/Tie-a-Tie"],
        [CrawlConstants::URL => "https://www.reddit.com/r/interestingasfuck/comments/10ub13n/the_easiest_technique_to_tie_the_knot/"],
        [CrawlConstants::URL => "https://www.bing.com/videos/search?q=How+to+tie+a+tie%3f&qpvt=How+to+tie+a+tie%3f&FORM=VDRE"],
        [CrawlConstants::URL => "https://www.wikihow.com/Tie-a-Tie"],
        [CrawlConstants::URL => "https://www.ties.com/how-to-tie-a-tie"],
        [CrawlConstants::URL => "https://bespokeunit.com/suits/ties/how-to-tie/"],
        [CrawlConstants::URL => "https://www.nordstrom.com/browse/content/blog/how-to-tie-a-tie"],
        [CrawlConstants::URL => "https://www.systemrequirementslab.com/cyri"],
        [CrawlConstants::URL => "https://www.pcgamebenchmark.com/"],
        [CrawlConstants::URL => "https://technical.city/en/can-i-run-it"],
        [CrawlConstants::URL => "https://www.reddit.com/r/lowendgaming/comments/4j6no4/how_reliable_is_can_you_run_it/"],
        [CrawlConstants::URL => "https://steamcommunity.com/discussions/forum/12/558747922439324697"],
        [CrawlConstants::URL => "https://www.systemrequirementslab.com/cyri"],
        [CrawlConstants::URL => "https://www.pcgamebenchmark.com/"],
        [CrawlConstants::URL => "https://technical.city/en/can-i-run-it"],
        [CrawlConstants::URL => "https://www.systemrequirementslab.com/all-games-list/"],
        [CrawlConstants::URL => "https://www.systemrequirementslab.com/cyri-score"],
        [CrawlConstants::URL => "https://support.google.com/assistant/answer/7554088?hl=en&co=GENIE.Platform%3DAndroid"],
        [CrawlConstants::URL => "https://www.shazam.com/home"],
        [CrawlConstants::URL => "https://blog.google/products/search/hum-to-search/"],
        [CrawlConstants::URL => "https://www.howtogeek.com/what-song-is-this-identify-music-youve-heard/"],
        [CrawlConstants::URL => "https://support.apple.com/en-us/HT210331"],
        [CrawlConstants::URL => "https://www.aha-music.com/"],
        [CrawlConstants::URL => "https://www.midomi.com/"],
        [CrawlConstants::URL => "https://www.shazam.com/"],
        [CrawlConstants::URL => "https://songguesser.com/"],
        [CrawlConstants::URL => "https://www.aha-music.com/identify-songs-music-recognition-online"]
    ]    
     // we could list more urls to download
);
// print_r($web_pages);
$html = new HtmlProcessor(max_description_len:2000, summarizer_option:CrawlConstants::CENTROID_WEIGHTED_SUMMARIZER);
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
print_r($inverted_index);
print(count($inverted_index));
?>