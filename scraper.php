<?php
/**
 * Created by IntelliJ IDEA.
 * User: Andre
 * Date: 2017-09-28
 * Time: 2:48 PM
 */

$q = $_REQUEST['search'];
$whatIWant = substr($q, strpos($q, ","));

$query = $whatIWant . '+earthquake+news';



//https://www.google.com/search?newwindow=1&safe=active&dcr=0&q=mexico+earthquake+news&oq=mexico+earthquake+news


$html = file_get_contents('https://www.google.com/search?q='.$query.'&oq='.$query); //get the html returned from the following url

$doc = new DOMDocument();

libxml_use_internal_errors(TRUE); //disable libxml errors

if(!empty($html)){ //if any html is actually returned
    $doc->loadHTML($html);
    libxml_clear_errors(); //remove errors for yucky html

    $xpath = new DOMXPath($doc);

    //get all the h3's with a class
    $row = $xpath->query('//h3[@class="r"]');

    if($row->length > 0){
        foreach($row as $entry){
            $val = $entry->nodeValue;
            $nodes = $xpath->query("a/attribute::href", $entry);
            foreach( $nodes as $node ) {
                echo "https://www.google.co.za/".$node->nodeValue."</br>";
            }
        }
    } else {
        echo 'nothing';
    }
} else {
    die('nothing');
}
