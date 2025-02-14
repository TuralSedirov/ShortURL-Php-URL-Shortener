<?php
/**
 * ShortURL Class
 * 
 * Version: 1.2.0
 * Author: Tural Sedirov
 * Email : turalsedirov@gmail.com
 * Description: A standalone URL shortener that supports encoding, decoding, optional storage (database, JSON, or no storage), and full API CRUD functionality.
 * 
 * Features:
 * - Encode and decode short URLs
 * - Store URLs in JSON or MySQL database (optional)
 * - API endpoints for creating, retrieving, updating, and deleting short URLs
 * - Follows best practices and security measures
 */

class ShortURL {
    private static $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    private static $base;
    private $storageMethod;
    private $storageFile = 'short_urls.json';
    private $pdo;

    public function __construct($storageMethod = 'json', $pdo = null) {
        self::$base = strlen(self::$alphabet);
        $this->storageMethod = $storageMethod;
        $this->pdo = $pdo;
    }

    /**
     * Encode a numeric ID into a short string
     */
    public static function encode($num) {
        // Ensure the alphabet is not empty
        if (empty(self::$alphabet)) {
            throw new Exception('Alphabet is empty');
        }

        // Calculate the base (length of alphabet) if not already set
        if (!isset(self::$base)) {
            self::$base = strlen(self::$alphabet);
        }

        // Ensure the base is greater than zero to avoid division by zero
        if (self::$base <= 0) {
            throw new Exception('Base is zero or negative');
        }

        $str = '';
        while ($num > 0) {
            $str = self::$alphabet[$num % self::$base] . $str;
            $num = (int) ($num / self::$base);
        }

        return $str;
    }


    /**
     * Decode a short string back to its numeric ID
     */
    public static function decode($str) {
        $num = 0;
        for ($i = 0, $len = strlen($str); $i < $len; $i++) {
            $num = $num * self::$base + strpos(self::$alphabet, $str[$i]);
        }
        return $num;
    }

    /**
     * Shorten a URL and store it based on the selected storage method
     */
    public function shortenURL($url) {
        $id = crc32($url);
        $shortCode = self::encode($id);
        
        if ($this->storageMethod === 'json') {
            $this->storeInJSON($shortCode, $url);
        } elseif ($this->storageMethod === 'database') {
            $this->storeInDatabase($shortCode, $url);
        }
        
        return $shortCode;
    }

    /**
     * Decode a short code back to the original URL
     */
    public function decodeURL($shortCode) {
        if ($this->storageMethod === 'json') {
            return $this->retrieveFromJSON($shortCode);
        } elseif ($this->storageMethod === 'database') {
            return $this->retrieveFromDatabase($shortCode);
        }
        return null;
    }

    /**
     * Store the URL mapping in a JSON file
     */
    private function storeInJSON($shortCode, $url) {
        $data = file_exists($this->storageFile) ? json_decode(file_get_contents($this->storageFile), true) : [];
        $data[$shortCode] = $url;
        file_put_contents($this->storageFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Retrieve the URL from JSON storage
     */
    private function retrieveFromJSON($shortCode) {
        if (!file_exists($this->storageFile)) return null;
        $data = json_decode(file_get_contents($this->storageFile), true);
        return $data[$shortCode] ?? null;
    }

    /**
     * Store the URL mapping in a database
     */
    private function storeInDatabase($shortCode, $url) {
        if (!$this->pdo) return;
        $stmt = $this->pdo->prepare("INSERT INTO short_urls (short_code, url) VALUES (:short_code, :url)");
        $stmt->execute(['short_code' => $shortCode, 'url' => $url]);
    }

    /**
     * Retrieve the URL from the database
     */
    private function retrieveFromDatabase($shortCode) {
        if (!$this->pdo) return null;
        $stmt = $this->pdo->prepare("SELECT url FROM short_urls WHERE short_code = :short_code");
        $stmt->execute(['short_code' => $shortCode]);
        return $stmt->fetchColumn();
    }

    /**
     * API: Shorten a URL (Create)
     */
    public function apiShorten() {
        $url = $_POST['url'] ?? null;
        if (!$url) {
            return json_encode(['error' => 'Missing URL']);
        }
        $shortCode = $this->shortenURL($url);
        return json_encode(['short_code' => $shortCode]);
    }

    /**
     * API: Retrieve original URL (Read)
     */
    public function apiDecode() {
        $shortCode = $_GET['code'] ?? null;
        if (!$shortCode) {
            return json_encode(['error' => 'Missing short code']);
        }
        $url = $this->decodeURL($shortCode);
        return json_encode(['url' => $url ?: 'Not found']);
    }

    /**
     * API: Update a stored URL
     */
    public function apiUpdate() {
        $shortCode = $_POST['code'] ?? null;
        $newURL = $_POST['url'] ?? null;
        if (!$shortCode || !$newURL) {
            return json_encode(['error' => 'Missing parameters']);
        }
        if ($this->storageMethod === 'json') {
            $data = json_decode(file_get_contents($this->storageFile), true);
            if (isset($data[$shortCode])) {
                $data[$shortCode] = $newURL;
                file_put_contents($this->storageFile, json_encode($data, JSON_PRETTY_PRINT));
                return json_encode(['success' => 'Updated']);
            }
        } elseif ($this->storageMethod === 'database' && $this->pdo) {
            $stmt = $this->pdo->prepare("UPDATE short_urls SET url = :url WHERE short_code = :short_code");
            $stmt->execute(['short_code' => $shortCode, 'url' => $newURL]);
            return json_encode(['success' => 'Updated']);
        }
        return json_encode(['error' => 'Not found']);
    }

    /**
     * API: Delete a stored URL
     */
    public function apiDelete() {
        $shortCode = $_POST['code'] ?? null;
        if (!$shortCode) {
            return json_encode(['error' => 'Missing short code']);
        }
        if ($this->storageMethod === 'json') {
            $data = json_decode(file_get_contents($this->storageFile), true);
            if (isset($data[$shortCode])) {
                unset($data[$shortCode]);
                file_put_contents($this->storageFile, json_encode($data, JSON_PRETTY_PRINT));
                return json_encode(['success' => 'Deleted']);
            }
        } elseif ($this->storageMethod === 'database' && $this->pdo) {
            $stmt = $this->pdo->prepare("DELETE FROM short_urls WHERE short_code = :short_code");
            $stmt->execute(['short_code' => $shortCode]);
            return json_encode(['success' => 'Deleted']);
        }
        return json_encode(['error' => 'Not found']);
    }
}
