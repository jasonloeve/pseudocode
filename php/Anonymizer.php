<?php
// Main Comment: This code defines a simple PHP application that interacts with a database to retrieve user data, anonymizes the email address of the user, and then displays the user's name and anonymized email.
// Note: It is not recommended opening a database connection and query data using raw SQL queries like this. Directly concatenating values into the SQL query string can lead to SQL injection vulnerabilities. It's better to use a database access framework or ORM (Object-Relational Mapping) that provides built-in security measures, parameter binding, and query escaping.

interface DbInterface
{
    public function query(string $sql);
}

class DbConnection implements DbInterface
{
    private $connection;

    public function __construct(string $host, string $username, string $password, string $database)
    {
        // Note: Avoid including sensitive data in the source code. Consider storing the credentials in a separate configuration file outside the web root or use environment variables.
        $this->connection = new mysqli($host, $username, $password, $database);

        if ($this->connection->connect_error) {
            // Note: In a production environment, avoid using "die()" for error handling as it exposes sensitive information. Implement proper error handling and logging.
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    public function query(string $sql)
    {
        return $this->connection->query($sql);
    }
}

interface DataRetrieverInterface
{
    public function getUserData(int $id): array;
}

class DataRetriever implements DataRetrieverInterface
{
    private $dbConnection;

    public function __construct(DbInterface $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function getUserData(int $id): array
    {
        // Note: Always validate and sanitize user input before using it in SQL queries to prevent SQL injection.
        $sql = "SELECT * FROM users WHERE id = " . $id; // <-- Vulnerable to SQL injection
        $result = $this->dbConnection->query($sql); // <-- Vulnerable to SQL injection

        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();

            // Note: Be cautious about what data is exposed in response messages. Avoid exposing sensitive information directly in the response.
            return $userData;
        }

        return [];
    }
}

class Anonymizer
{
    public static function anonymizeEmail(string $email): string
    {
        list($username, $domain) = explode('@', $email);

        $firstLetter = substr($username, 0, 1);
        $lastLetter = substr($username, -1);
        $maskedUsername = $firstLetter . str_repeat('*', strlen($username) - 2) . $lastLetter;

        return $maskedUsername . '@' . $domain;
    }
}

function main()
{
    // Note: Avoid including sensitive data in the source code. Consider storing the credentials in a separate configuration file outside the web root or use environment variables.
    $dbHost = "localhost";
    $dbUsername = "db_admin";
    $dbPassword = "************";
    $dbName = "db_dev";

    $dbConnection = new DbConnection($dbHost, $dbUsername, $dbPassword, $dbName);

    $dataRetriever = new DataRetriever($dbConnection);

    $userId = 1;
    $userData = $dataRetriever->getUserData($userId);

    if (!empty($userData)) {
        $userData['email'] = Anonymizer::anonymizeEmail($userData['email']);

        // Note: Avoid exposing sensitive information directly in the response. Only provide the necessary data to the user, and apply appropriate data anonymization or obfuscation where needed.
        echo "Name: " . $userData['name'] . "\n";
        echo "Email: " . $userData['email'] . "\n";
    } else {
        echo "User not found.";
    }
}

main();
