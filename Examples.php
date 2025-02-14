<?php
    require_once 'config.php';
    require_once 'helpers.php';
    require_once 'ShortURL.php';
////////////////////////////////////////////////////////////////////////////////
//1. Encoding a Numeric ID into a Short Code
    debug(['value'=>'Example 1']);
    $num = 123456789;
    debug(['value'=>'Original value : '.$num]);
    $shortCode = ShortURL::encode($num);
    debug(['value'=>"Encoded short code: " . $shortCode]); // Output: Encoded short code: dnh

////////////////////////////////////////////////////////////////////////////////
//2. Decoding a Short Code back to Numeric ID
    debug(['value'=>'Example 2']);
    $shortCode = 'dnh';
    debug(['value'=>'Original value : '.$shortCode]);
    $num = ShortURL::decode($shortCode);
    debug(['value'=>"Decoded numeric ID: " . $num]); // Output: Decoded numeric ID: 12345

////////////////////////////////////////////////////////////////////////////////
//3. Shortening a URL and Storing it (JSON or Database)
//Example using JSON storage:
    debug(['value'=>'Example 3']);
    $shortener = new ShortURL('json');
    $shortCode = $shortener->shortenURL('https://www.example.com');
    debug(['value'=>"Shortened URL: " . $shortCode]); // Output: Shortened URL: dnh
    //Example using Database storage:
    // Assuming PDO connection is available as $pdo
    $shortener = new ShortURL('database', $pdo);
    $shortCode = $shortener->shortenURL('https://www.example.com');
    debug(['value'=>"Shortened URL: " . $shortCode]); // Output: Shortened URL: dnh

////////////////////////////////////////////////////////////////////////////////
//4. Decoding a Short Code to Retrieve the Original URL
//Example using JSON storage:
    debug(['value'=>'Example 4']);
    debug(['value'=>"Decoded  Json example:"]);
    $shortener = new ShortURL('json');
    $url = $shortener->decodeURL('dnh');
    debug(['value'=>"Decoded URL: " . $url]); // Output: Decoded URL: https://www.example.com
    //Example using Database storage:
    // Assuming PDO connection is available as $pdo
    $shortener = new ShortURL('database', $pdo);
    $url = $shortener->decodeURL('dnh');
    debug(['value'=>"Decoded URL: " . $url]); // Output: Decoded URL: https://www.example.com

////////////////////////////////////////////////////////////////////////////////
//5. Storing URL in JSON
    debug(['value'=>'Example 5']);
    $shortener = new ShortURL('json');
    $shortCode = $shortener->shortenURL('https://www.example.com');
    debug(['value' => "Short Code: " . $shortCode]);
    // Fetch the stored JSON content
    debug(['value' => "Decoded URL: " . file_get_contents('short_urls.json')]);

////////////////////////////////////////////////////////////////////////////////
//6. Retrieving URL from JSON
    debug(['value' => 'Example 6']);

    $shortener = new ShortURL('json');
    $url = $shortener->decodeURL('dnh'); // Use decodeURL instead of retrieveFromJSON

    debug(['value' => "URL from JSON: " . $url]);

////////////////////////////////////////////////////////////////////////////////
//7. Storing URL in Database
// Assuming PDO connection is available as $pdo
    debug(['value' => 'Example 7']);

// Establish a PDO connection
    $shortener = new ShortURL('database', $pdo);

// Create a short URL
    $shortCode = $shortener->shortenURL('https://www.example.com');
    debug(['value' => "Short Code: " . $shortCode]);

// Decode the short code to retrieve the original URL
    $originalURL = $shortener->decodeURL($shortCode); // Use decodeURL instead of retrieveFromDatabase
    debug(['value' => "Decoded URL: " . $originalURL]);


////////////////////////////////////////////////////////////////////////////////
//8. Retrieving URL from Database
// Assuming PDO connection is available as $pdo
    debug(['value' => 'Example 8']);

    $shortener = new ShortURL('database', $pdo);

// Decode the short code using the public method
    $url = $shortener->decodeURL('dnh'); // Use decodeURL() instead of retrieveFromDatabase()

    debug(['value' => "URL from Database: " . $url]); // Output: URL from Database: https://www.example.com

////////////////////////////////////////////////////////////////////////////////
//9. API: Shorten URL (Create)
// POST request to API
    debug(['value' => 'Example 9']);
    $_POST['url'] = 'https://www.example.com';
    $shortener = new ShortURL('json');
    $response = $shortener->apiShorten();
    debug(['value'=>$response]); // Output: {"short_code":"dnh"}

////////////////////////////////////////////////////////////////////////////////
//10. API: Retrieve Original URL (Read)
// GET request to API
    debug(['value' => 'Example 10']);
    $_GET['code'] = 'dnh';
    $shortener = new ShortURL('json');
    $response = $shortener->apiDecode();
    debug(['value'=>$response]); // Output: {"url":"https://www.example.com"}

////////////////////////////////////////////////////////////////////////////////
//11. API: Update Stored URL
// POST request to API
    debug(['value' => 'Example 11']);
    $_POST['code'] = 'dnh';
    $_POST['url'] = 'https://www.newexample.com';
    $shortener = new ShortURL('json');
    $response = $shortener->apiUpdate();
    debug(['value'=>$response]); // Output: {"success":"Updated"}

////////////////////////////////////////////////////////////////////////////////
//12. API: Delete Stored URL
// POST request to API
    debug(['value' => 'Example 12']);
    $_POST['code'] = 'dnh';
    $shortener = new ShortURL('json');
    $response = $shortener->apiDelete();
    debug(['value'=>$response]); // Output: {"success":"Deleted"}