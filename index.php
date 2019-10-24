<?php
/*
Author: Osman Çakmak
Skype: oxcakmak
Email: oxcakmak@hotmail.com
Website: http://oxcakmak.com/
Country: Turkey [TR]
*/
define('USERNAME', "REPLACE");
define('PASSWORD', "REPLACE");
define('USERAGENT', "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36");
define('COOKIE', USERNAME.".txt");

function login_inst() {

    @unlink(dirname(__FILE__)."/".COOKIE);

    $url="https://www.instagram.com/accounts/login/?force_classic_login";

    $ch  = curl_init(); 

    $arrSetHeaders = array(
        "User-Agent: USERAGENT",
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.5',
        'Accept-Encoding: deflate, br',
        'Connection: keep-alive',
        'cache-control: max-age=0',
    );

    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrSetHeaders);         
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__)."/".COOKIE);
    curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/".COOKIE);
    curl_setopt($ch, CURLOPT_USERAGENT, USERAGENT);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $page = curl_exec($ch);
    curl_close($ch);  

    //var_dump($page);

    // try to find the actual login form
    if (!preg_match('/<form method="POST" id="login-form" class="adjacent".*?<\/form>/is', $page, $form)) {
        die('Failed to find log in form!');
    }

    $form = $form[0];

    // find the action of the login form
    if (!preg_match('/action="([^"]+)"/i', $form, $action)) {
        die('Failed to find login form url');
    }

    $url2 = $action[1]; // this is our new post url
    // find all hidden fields which we need to send with our login, this includes security tokens
    $count = preg_match_all('/<input type="hidden"\s*name="([^"]*)"\s*value="([^"]*)"/i', $form, $hiddenFields);

    $postFields = array();

    // turn the hidden fields into an array
    for ($i = 0; $i < $count; ++$i) {
        $postFields[$hiddenFields[1][$i]] = $hiddenFields[2][$i];
    }

    // add our login values
    $postFields['username'] = USERNAME;
    $postFields['password'] = PASSWORD;   

    $post = '';

    // convert to string, this won't work as an array, form will not accept multipart/form-data, only application/x-www-form-urlencoded
    foreach($postFields as $key => $value) {
        $post .= $key . '=' . urlencode($value) . '&';
    }

    $post = substr($post, 0, -1);   

    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $page, $matches);

    $cookieFileContent = '';

    foreach($matches[1] as $item) 
    {
        $cookieFileContent .= "$item; ";
    }

    $cookieFileContent = rtrim($cookieFileContent, '; ');
    $cookieFileContent = str_replace('sessionid=""; ', '', $cookieFileContent);

    $oldContent = file_get_contents(dirname(__FILE__)."/".COOKIE);
    $oldContArr = explode("\n", $oldContent);

    if(count($oldContArr))
    {
        foreach($oldContArr as $k => $line)
        {
            if(strstr($line, '# '))
            {
                unset($oldContArr[$k]);
            }
        }

        $newContent = implode("\n", $oldContArr);
        $newContent = trim($newContent, "\n");

        file_put_contents(
            dirname(__FILE__)."/".COOKIE,
            $newContent
        );    
    }

    $arrSetHeaders = array(
        'origin: https://www.instagram.com',
        'authority: www.instagram.com',
        'upgrade-insecure-requests: 1',
        'Host: www.instagram.com',
        "User-Agent: USERAGENT",
        'content-type: application/x-www-form-urlencoded',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.5',
        'Accept-Encoding: deflate, br',
        "Referer: $url",
        "Cookie: $cookieFileContent",
        'Connection: keep-alive',
        'cache-control: max-age=0',
    );

    $ch  = curl_init();
    curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__)."/".COOKIE);
    curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/".COOKIE);
    curl_setopt($ch, CURLOPT_USERAGENT, USERAGENT);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrSetHeaders);     
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    sleep(5);
    $page = curl_exec($ch);

    /*
    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $page, $matches);
    COOKIEs = array();
    foreach($matches[1] as $item) {
        parse_str($item, COOKIE1);
        COOKIEs = array_merge(COOKIEs, COOKIE1);
    }
    */
    //var_dump($page);      
    curl_close($ch);  

}
function curl_inst($url) { 

    $arrSetHeaders = array(
        'origin: https://www.instagram.com',
        'authority: www.instagram.com',
        'method: GET',
        'upgrade-insecure-requests: 1',
        'Host: www.instagram.com',
        "User-Agent: USERAGENT",
        'content-type: application/x-www-form-urlencoded',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'accept-language:ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,uk;q=0.6',
        'accept-encoding: deflate, br',
        "Referer: https://www.instagram.com",
        'Connection: keep-alive',
        'cache-control: max-age=0',
    );
    $ch  = curl_init();
    curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__)."/!instagram/".COOKIE);
    curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/!instagram/".COOKIE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); //connection timeout in seconds 
    curl_setopt($ch, CURLOPT_USERAGENT, USERAGENT);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arrSetHeaders);     
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $page = curl_exec($ch);

    if (!curl_errno($ch)) {
      switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
        case 200:  # OK
          //echo 'All OK: ', $http_code, "\n";
          //var_dump($page);    
          curl_close($ch);
          return $page;    
        default:
          echo 'Error: ', $http_code, "\n";
          curl_close($ch);    
          break;    
      }
    }      

}

?>
