<?php
// Provides database operations related to users
class User {
    // Holds PDO connection
    private $pdo; // PDO instance for queries

    // Constructor accepts PDO dependency
    public function __construct(PDO $pdo) {
        // Store PDO for later use
        $this->pdo = $pdo;
    }

    // Creates a new user with hashed password
    public function create(string $username, string $email, string $password, string $gender): bool {
        // Prepare insert statement for users table
        $stmt = $this->pdo->prepare('INSERT INTO users (username, email, password_hash, gender) VALUES (:u, :e, :p, :g)');
        // Execute with bound parameters; hash password before saving
        return $stmt->execute([
            ':u' => $username,
            ':e' => $email,
            ':p' => password_hash($password, PASSWORD_DEFAULT),
            ':g' => $gender,
        ]);
    }

    // Retrieves user by email for login checks
    public function findByEmail(string $email): ?array {
        // Prepare select query
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :e');
        // Run query with email parameter
        $stmt->execute([':e' => $email]);
        // Fetch row
        $row = $stmt->fetch();
        // Return array or null
        return $row ?: null;
    }

    // Retrieves user by id for session refresh
    public function findById(int $id): ?array {
        // Prepare select query by id
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        // Execute with id parameter
        $stmt->execute([':id' => $id]);
        // Fetch row
        $row = $stmt->fetch();
        // Return array or null
        return $row ?: null;
    }
}
