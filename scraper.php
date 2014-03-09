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

    for ($i=0; $i< 1000; $i++) {

        $html = oneline(scraperwiki::scrape("http://www.ukrlp.co.uk/ukrlp/ukrlp_provider.page_pls_provDetails?x=&pn_p_id=".$counter."&pv_status=VERIFIED&pv_vis_code=L"));
        
        preg_match_all('|<div class="pod_main_body">(.*?<div )class="searchleft">|',$html,$arr);
    
        if (isset($arr[1][0])) { $code = $arr[1][0];} else { $code='';}
        if ($code!='') {
            #echo "code \n";
            #echo json_encode($code);
            #echo "\n";

            preg_match_all('|<div class="provhead">UKPRN: ([0-9]*?)</div>|',$code,$num);
            if (isset($num [1][0])) { $num  = trim($num [1][0]);} else { $num ='';}
             
            #echo "num \n";
            #echo json_encode($num);
            #echo "\n";
            
            preg_match_all('|<div class="pt">(.*?)<|',$code,$name);
            if (isset($name [1][0])) { $name = trim($name [1][0]);} else { $name ='';}
            
            #echo "name \n";
            #echo json_encode($name);
            #echo "\n";
            
            preg_match_all('|<div class="tradingname">Trading Name: <span>(.*?)</span></div>|',$code,$trading);
            if (isset($trading[1][0])) { $trading = trim($trading[1][0]);} else { $trading='';}
            
            #echo "trading \n";
            #echo json_encode($trading);
            #echo "\n";
            
            preg_match_all('|<div class="assoc">Legal Address</div>(.*?)<div|',$code,$legal);
            if (isset($legal [1][0])) { $legal = trim($legal [1][0]);} else { $legal ='';}
            
            #echo "legal \n";
            #echo json_encode($legal);
            #echo "\n";
            
            preg_match_all('|<div class="assoc">Primary contact address</div>(.*?)<div|',$code,$primary);
            if (isset($primary[1][0])) { $primary= trim($primary[1][0]);} else { $primary='';}
            
            #echo "primary \n";
            #echo json_encode($primary);
            #echo "\n";
            
            $primary = parseAddress($primary);
            $legal= parseAddress($legal);
            
            if (trim($name)!='') {
                scraperwiki::save(array('num'), array('num' => "".clean($num),'name' => clean($name),'trading' => clean($trading),
                                                             'legal_address' => clean($legal['address']),'legal_phone' => clean($legal['phone']),
                                                            'legal_fax' => clean($legal['fax']),'legal_email' => clean($legal['email']),
                                                        'legal_web' => clean($legal['web']),
                                                    'primary_address' => clean($primary['address']),'primary_phone' => clean($primary['phone']),
                                                            'primary_fax' => clean($primary['fax']),'primary_email' => clean($primary['email']),
                                                      'primary_web' => clean($primary['web']), 'primary_courses' => clean($primary['courses']) ));    
                }
            scraperwiki::save_var('counter',$counter);  
        }
        
        $counter++;
        if ($counter >= $max) {
            scraperwiki::save_var('counter',10000000); 
            $i= 1001;
        }
    }

    function parseAddress($val) {

        preg_match_all('|<strong>Telephone: </strong>(.*?)<br />|',$val,$phone);
        if (isset($phone[1][0])) { $dat['phone'] = trim($phone[1][0]);} else { $dat['phone']='';}
        preg_match_all('|<strong>E-mail: </strong><a href="mailto:(.*?)">.*?</a><br />|',$val,$email);
        if (isset($email[1][0])) { $dat['email'] = trim($email[1][0]);} else { $dat['email']='';}
        preg_match_all('|<strong>Website: </strong><a target="_blank" href="(.*?)">.*?</a><br />|',$val,$web);
        if (isset($web[1][0])) { $dat['web'] = trim($web[1][0]);} else { $dat['web']='';}
        preg_match_all('|<strong>Fax: </strong>(.*?)<br />|',$val,$fax);
        if (isset($fax[1][0])) { $dat['fax'] = trim($fax[1][0]);} else { $dat['fax']='';}
        if (isset($courses[1][0])) { $dat ['courses'] = trim($courses[1][0]);} else { $dat['courses']='';}
        preg_match_all('|<strong>Courses: </strong>(.*?)<br />|',$val,$courses);
        $p = explode('<strong>',$val);
       
        $p = explode('<br />',$p[0]);
        
        $dat['address'] = '';
        foreach ($p as $a) {
            $a = trim($a);
            if ($a !='') {
                if ($dat['address']!='') { $dat['address'] .=', '; }
                $dat['address'] .= $a;
            }
        }
        if ($dat['address'] == 'Not specified. Please use the above.') {
        $dat['address'] = '';
        }

        return $dat;

    }

    function clean($val) {
        $val = str_replace('&nbsp;',' ',$val);
        $val = str_replace('&amp;','&',$val);
        $val = html_entity_decode($val);
        $val = strip_tags($val);
        $val = trim($val);
        $val = utf8_decode($val);
        return($val);
    }
    
    function oneline($code) {
        $code = str_replace("\n",'',$code);
        $code = str_replace("\r",'',$code);
        return $code;
    }

      
?>
