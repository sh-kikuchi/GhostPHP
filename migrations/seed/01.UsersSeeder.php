<?php

require 'vendor/autoload.php'; // Composer autoloader

use GuzzleHttp\Client;
use app\aura\database\DataBaseConnect;

/**
 * Class UsersSeeder
 * 
 * This class seeds the 'users' table with data from an external API.
 */
class UsersSeeder
{
    /**
     * @var Client The Guzzle HTTP client instance.
     */
    private $client;

    /**
     * @var DataBaseConnect The database connection handler instance.
     */
    private DataBaseConnect $dbConnect;

    /**
     * @var \PDO The PDO instance for database interaction.
     */
    private $pdo;

    /**
     * UsersSeeder constructor.
     * 
     * Initializes the database connection and the HTTP client.
     */
    public function __construct()
    {
        $this->dbConnect = new DataBaseConnect();
        $this->pdo = $this->dbConnect->getPDO();

        // Base URL for JSONPlaceholder
        $baseUrl = 'https://jsonplaceholder.typicode.com';

        // Create Guzzle client
        $this->client = new Client([
            'base_uri' => $baseUrl,
        ]);
    }

    /**
     * Seeds the 'users' table with data from an external API.
     */
    public function seed()
    {
        // Retrieve users data from the API
        $response = $this->client->get('/users');

        // Continue processing only if the status code is 200 OK
        if ($response->getStatusCode() == 200) {
            // Decode the response body in JSON format
            $users = json_decode($response->getBody(), true);

            // Display data
            foreach ($users as $user) {
                $stmt = $this->pdo->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');
                $stmt->execute([
                    ':name'  => $user['name'],
                    ':email' => $user['email'],
                    ':password' => password_hash('password', PASSWORD_DEFAULT)
                ]);
            }

        } else {
            echo "Failed to retrieve data. Status code: {$response->getStatusCode()}\n";
        }
    }
}
