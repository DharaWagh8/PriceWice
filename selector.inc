<?php

// --- Selector.inc - (c) Copyright TJ Holowaychuk <tj@vision-media.ca> MIT Licensed

define('SELECTOR_VERSION', '1.1.6');

/**
 * SelectorDOM.
 *
 * Persitant object for selecting elements.
 *
 *   $dom = new SelectorDOM($html);
 *   $links = $dom->select('a');
 *   $list_links = $dom->select('ul li a');
 *
 */

class SelectorDOM {
  public function __construct($data) {
    if ($data instanceof DOMDocument) {
        $this->xpath = new DOMXpath($data);
    } else {
        $dom = new DOMDocument();
        @$dom->loadHTML($data);
        $this->xpath = new DOMXpath($dom);
    }
  }
  
  public function select($selector, $as_array = true) {
    $elements = $this->xpath->evaluate(selector_to_xpath($selector));
    return $as_array ? elements_to_array($elements) : $elements;
  }
}

/**
 * Select elements from $html using the css $selector.
 * When $as_array is true elements and their children will
 * be converted to array's containing the following keys (defaults to true):
 *
 *  - name : element name
 *  - text : element text
 *  - children : array of children elements
 *  - attributes : attributes array
 *
 * Otherwise regular DOMElement's will be returned.
 */

function select_elements($selector, $html, $as_array = true) {
  $dom = new SelectorDOM($html);
  return $dom->select($selector, $as_array);
}

/**
 * Convert $elements to an array.
 */

function elements_to_array($elements) {
  $array = array();
  for ($i = 0, $length = $elements->length; $i < $length; ++$i)
    if ($elements->item($i)->nodeType == XML_ELEMENT_NODE)
      array_push($array, element_to_array($elements->item($i)));
  return $array;
}

/**
 * Convert $element to an array.
 */

function element_to_array($element) {
  $array = array(
    'name' => $element->nodeName,
    'attributes' => array(),
    'text' => $element->textContent,
    'children' =>elements_to_array($element->childNodes)
    );
  if ($element->attributes->length)
    foreach($element->attributes as $key => $attr)
      $array['attributes'][$key] = $attr->value;
  return $array;
}

/**
 * Convert $selector into an XPath string.
 */

function selector_to_xpath($selector) {
    // remove spaces around operators
    $selector = preg_replace('/\s*>\s*/', '>', $selector);
    $selector = preg_replace('/\s*~\s*/', '~', $selector);
    $selector = preg_replace('/\s*\+\s*/', '+', $selector);
    $selector = preg_replace('/\s*,\s*/', ',', $selector);
    $selectors = preg_split('/\s+(?![^\[]+\])/', $selector);

    foreach ($selectors as &$selector) {
        // ,
        $selector = preg_replace('/,/', '|descendant-or-self::', $selector);
        // input:checked, :disabled, etc.
        $selector = preg_replace('/(.+)?:(checked|disabled|required|autofocus)/', '\1[@\2="\2"]', $selector);
        // input:autocomplete, :autocomplete
        $selector = preg_replace('/(.+)?:(autocomplete)/', '\1[@\2="on"]', $selector);
        // input:button, input:submit, etc.
        $selector = preg_replace('/:(text|password|checkbox|radio|button|submit|reset|file|hidden|image|datetime|datetime-local|date|month|time|week|number|range|email|url|search|tel|color)/', 'input[@type="\1"]', $selector);
        // foo[id]
        $selector = preg_replace('/(\w+)\[([_\w-]+[_\w\d-]*)\]/', '\1[@\2]', $selector);
        // [id]
        $selector = preg_replace('/\[([_\w-]+[_\w\d-]*)\]/', '*[@\1]', $selector);
        // foo[id=foo]
        $selector = preg_replace('/\[([_\w-]+[_\w\d-]*)=[\'"]?(.*?)[\'"]?\]/', '[@\1="\2"]', $selector);
        // [id=foo]
        $selector = preg_replace('/^\[/', '*[', $selector);
        // div#foo
        $selector = preg_replace('/([_\w-]+[_\w\d-]*)\#([_\w-]+[_\w\d-]*)/', '\1[@id="\2"]', $selector);
        // #foo
        $selector = preg_replace('/\#([_\w-]+[_\w\d-]*)/', '*[@id="\1"]', $selector);
        // div.foo
        $selector = preg_replace('/([_\w-]+[_\w\d-]*)\.([_\w-]+[_\w\d-]*)/', '\1[contains(concat(" ",@class," ")," \2 ")]', $selector);
        // .foo
        $selector = preg_replace('/\.([_\w-]+[_\w\d-]*)/', '*[contains(concat(" ",@class," ")," \1 ")]', $selector);
        // div:first-child
        $selector = preg_replace('/([_\w-]+[_\w\d-]*):first-child/', '*/\1[position()=1]', $selector);
        // div:last-child
        $selector = preg_replace('/([_\w-]+[_\w\d-]*):last-child/', '*/\1[position()=last()]', $selector);
        // :first-child
        $selector = str_replace(':first-child', '*/*[position()=1]', $selector);
        // :last-child
        $selector = str_replace(':last-child', '*/*[position()=last()]', $selector);
        // :nth-last-child
        $selector = preg_replace('/:nth-last-child\((\d+)\)/', '[position()=(last() - (\1 - 1))]', $selector);
        // div:nth-child
        $selector = preg_replace('/([_\w-]+[_\w\d-]*):nth-child\((\d+)\)/', '*/*[position()=\2 and self::\1]', $selector);
        // :nth-child
        $selector = preg_replace('/:nth-child\((\d+)\)/', '*/*[position()=\1]', $selector);
        // :contains(Foo)
        $selector = preg_replace('/([_\w-]+[_\w\d-]*):contains\((.*?)\)/', '\1[contains(string(.),"\2")]', $selector);
        // >
        $selector = preg_replace('/>/', '/', $selector);
        // ~
        $selector = preg_replace('/~/', '/following-sibling::', $selector);
        // +
        $selector = preg_replace('/\+([_\w-]+[_\w\d-]*)/', '/following-sibling::\1[position()=1]', $selector);
        $selector = str_replace(']*', ']', $selector);
        $selector = str_replace(']/*', ']', $selector);
    }

    // ' '
    $selector = implode('/descendant::', $selectors);
    $selector = 'descendant-or-self::' . $selector;
    // :scope
    $selector = preg_replace('/(((\|)?descendant-or-self::):scope)/', '.\3', $selector);
    // $element
    $sub_selectors = explode(',', $selector);

    foreach ($sub_selectors as $key => $sub_selector) {
        $parts = explode('$', $sub_selector);
        $sub_selector = array_shift($parts);

        if (count($parts) && preg_match_all('/((?:[^\/]*\/?\/?)|$)/', $parts[0], $matches)) {
            $results = $matches[0];
            $results[] = str_repeat('/..', count($results) - 2);
            $sub_selector .= implode('', $results);
        }

        $sub_selectors[$key] = $sub_selector;
    }

    $selector = implode(',', $sub_selectors);
    
    return $selector;
}
function test(){
  return "hi";
}
function grabSelector($dom, $selector, $output, $key){

  $selectorele = $dom->select($selector);
  //echo $key;
   //echo "<pre>";print_R($selectorele);die();
  if (count($selectorele) > 0) {

    if($key == "img_src"){

        return isset($selectorele[0]['attributes'][':src']) ? $selectorele[0]['attributes'][':src'] : '';
    }else if($key == "link"){

        return isset($selectorele[0]['attributes']['href']) ? $selectorele[0]['attributes']['href'] : '';
    }else if($key == "price"){

        return isset($selectorele[0]['children'][0]['attributes']['price']) ? $selectorele[0]['children'][0]['attributes']['price'] : '';
    }else{
       return isset($selectorele[0][$output]) ? $selectorele[0][$output] : '';
    }
      
  }
}

function getSofItem($sku){
    $url = "https://storefrontgateway.saveonfoods.com/api/stores/1982/products/".$sku;
    //https://www.saveonfoods.com/sm/pickup/rsid/1982/product/id-00629307040214
    $ch = curl_init();
    // Will return the response, if false it print the response
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Set the url
    curl_setopt($ch, CURLOPT_URL,$url);
    // Execute
    $result=curl_exec($ch);
    // Closing
    curl_close($ch);
    $result = json_decode($result, true);
    if(isset($result['sku'])){
      $result['link'] = "https://www.saveonfoods.com/sm/pickup/rsid/1982/product/id-".$sku;
    }
    return $result;
}

function getDmItem($sku){
    $html = file_get_contents('https://www.desimandi.ca/categorysearch?term='.$sku);

    $dom = new SelectorDOM($html);
    //echo "<pre>";print_r($html);die();
    $selectors = [
        'title' => [
            'selector' => '.all-products .product-details .caption h4 a',
            'output' => 'text'
        ],
        'link' => [
            'selector' => '.all-products .product-details .caption h4 a',
            'output' => 'text'
        ],
        'price' => [
            'selector' => '.product-details .caption .product_option',
            'output' => 'text'
        ],
        'weight' => [
            'selector' => 'p.unit_type',
            'output' => 'text'
        ],
        'img_src' => [
            'selector' => '.product_box img',
            'output' => 'text'
        ],
    ];
    $price = 0;
    $output = [];
    foreach($selectors as $key=>$selector){
        $output[$key] = trim(grabSelector($dom, $selector['selector'], $selector['output'], $key));
    }
    if(isset($output['title'])){
      $output['name'] = $output['title'];
    }
    $output['img_src'] = !$output['img_src'] ? "https://www.desimandi.ca/themes/desimandi/images/products/place_holder.png" : $output['img_src'];
    return $output;
}

function getWmItem($sku){
    $csv = array_map("str_getcsv", file("data/walmart.csv",FILE_SKIP_EMPTY_LINES));
    $keys = ['name','price', 'sku', 'image', 'weight', 'link'];
    foreach ($csv as $i=>$row) {
        $csv[$i] = array_combine($keys, $row);
    }
    $index = array_search($sku, array_column($csv,"sku"));
/*    echo $sku."\n";
    echo $index;
    echo "<pre>";print_r($csv);die();*/
    return $index !== false ? $csv[$index] : [];
}