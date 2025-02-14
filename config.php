<?php
// Database configuration
    $production=false;
    if($production==false)
    {
        $host = '127.0.0.1'; // Replace with your host
        $db = 'db_url_shortener'; // Replace with your database name
        $user = 'root'; // Replace with your database username
        $pass = ''; // Replace with your database password
    }
    else
    {
        {
            $host = '127.0.0.1'; // Replace with your host
            $db = 'your_database_name'; // Replace with your database name
            $user = 'your_username'; // Replace with your database username
            $pass = 'your_password'; // Replace with your database password
        }
    }
// DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$db;charset=utf8";

// Create a PDO instance
try {
    $pdo = new PDO($dsn, $user, $pass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connection successful!<br>";
} catch (PDOException $e) {
    // Catch any errors and display the message
    echo "Connection failed: " . $e->getMessage().'<br>';
}
?>