<?php
require 'scraperwiki.php';
######################################
# Basic PHP scraper
######################################
#scraperwiki::sqliteexecute("CREATE TABLE `swvariables` (`value_blob` blob, `type` text, `name` text)");
#scraperwiki::sqliteexecute("CREATE TABLE `swdata` (`date_scraped` text, `primary_web` text, `name` text, `primary_phone` text, `legal_email` text, `legal_web` text, `legal_fax` text, `num` text, `trading` text, `legal_phone` text, `primary_email` text, `primary_fax` text, `primary_address` text, `legal_address` text, `primary_courses` text)");
#return;

$max = 10045263;
$counter = scraperwiki::get_var('counter',10000000);          
if($counter<10000000)
{
    $counter=10000000;
}

    for ($i=0; $i< 1; $i++) {
        $counter++;
        if ($counter == $max) {
            scraperwiki::save_var('counter',10000000); 
            $i= 1001;
        }
$html = oneline(scraperwiki::scrape("http://www.ukrlp.co.uk/ukrlp/ukrlp_provider.page_pls_provDetails?x=&pn_p_id=".$counter."&pv_status=VERIFIED&pv_vis_code=L"));

preg_match_all('|<div class="pod_main_body">(.*?<div )class="searchleft">|',$html,$arr);
   if (isset($arr[1][0])) { $code = $arr[1][0];} else { $code='';}
        if ($code!='') {

preg_match_all('|<div class="provhead">UKPRN: ([0-9]*?)</div>|',$code,$num);
 if (isset($num [1][0])) { $num  = trim($num [1][0]);} else { $num ='';}

preg_match_all('|</div>.*?<div class="provhead">(.*?)<|',$code,$name);
if (isset($name [1][0])) { $name = trim($name [1][0]);} else { $name ='';}

preg_match_all('|<div class="tradingname">Trading Name: <span>(.*?)</span></div>|',$code,$trading);
if (isset($trading[1][0])) { $trading = trim($trading[1][0]);} else { $trading='';}

preg_match_all('|<div class="assoc">Legal address</div>(.*?)<div|',$code,$legal);
if (isset($legal [1][0])) { $legal = trim($legal [1][0]);} else { $legal ='';}


preg_match_all('|<div class="assoc">Primary contact address</div>(.*?)<div|',$code,$primary);
if (isset($primary[1][0])) { $primary= trim($primary[1][0]);} else { $primary='';}

$primary = parseAddress($primary);
$legal= parseAddress($legal);
       echo $html;
       echo "\n";
       echo $name;
        if (trim($name)!='') {
echo "saving";
scraperwiki::save(array('num'), array('num' => "".clean($num),'name' => clean($name),'trading' => clean($trading),
                                             'legal_address' => clean($legal['address']),'legal_phone' => clean($legal['phone']),
                                            'legal_fax' => clean($legal['fax']),'legal_email' => clean($legal['email']),
                                        'legal_web' => clean($legal['web']),
                                    'primary_address' => clean($primary['address']),'primary_phone' => clean($primary['
