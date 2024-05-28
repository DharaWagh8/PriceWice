<?php

/*$curl = curl_init();

curl_setopt_array($curl, [
	CURLOPT_URL => "https://ali-express1.p.rapidapi.com/categories",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => [
		"X-RapidAPI-Host: ali-express1.p.rapidapi.com",
		"X-RapidAPI-Key: 5358891f0fmsh7528c248b06fbc5p15d937jsn2b87a1b8fe95"
	],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	echo "cURL Error #:" . $err;
} else {
	echo $response;
}*/

$url = 'https://www.instacart.ca/products/439841-pineapple-each';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$output = curl_exec($ch);
curl_close($ch);
//print_r($output);
$doc = new DOMDocument();
@$doc->loadHTML($output);

$xpath = new DOMXPath($doc);
$deals = $xpath->query('//div[@id="react-root"]');
foreach ($deals as $deal) {
	print_r($deal);
	die("j");
    $title = $xpath->query('.//div[@id="regular_price"]', $deal)->item(0)->textContent;
   /* $price = $xpath->query('.//span[contains(@class, "a-price")]/span[@class="a-offscreen"]', $deal)->item(0)->textContent;
    $link = $xpath->query('.//a[@class="a-link-normal"]', $deal)->item(0)->getAttribute('href');*/
    echo "$title\n";
}
?>
