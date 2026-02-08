<?php
// Provides operations for O-Level subjects and scores
class OLevel {
    // PDO connection instance
    private $pdo; // Database handle

    // Accept PDO dependency
    public function __construct(PDO $pdo) {
        // Store PDO for reuse
        $this->pdo = $pdo;
    }

    // Saves compulsory + optional subjects for a user
    public function saveSubjects(int $userId, array $compulsory, array $optionals): void {
        // Clear any previous subjects for this user
        $this->pdo->prepare('DELETE FROM olevel_subjects WHERE user_id = :uid')->execute([':uid' => $userId]);
        // Insert compulsory subjects
        $insert = $this->pdo->prepare('INSERT INTO olevel_subjects (user_id, name, is_compulsory) VALUES (:uid, :name, :comp)');
        foreach ($compulsory as $name) {
            $insert->execute([':uid' => $userId, ':name' => $name, ':comp' => 1]);
        }
        // Insert optional subjects
        foreach ($optionals as $name) {
            $insert->execute([':uid' => $userId, ':name' => $name, ':comp' => 0]);
        }
    }

    // Returns all subjects for a user
    public function getSubjects(int $userId): array {
        // Fetch subjects ordered by compulsory then name
        $stmt = $this->pdo->prepare('SELECT name, is_compulsory FROM olevel_subjects WHERE user_id = :uid ORDER BY is_compulsory DESC, name');
        $stmt->execute([':uid' => $userId]);
        // Return all subject rows
        return $stmt->fetchAll();
    }

    // Saves scores and computes O-Level weight
    public function saveScores(int $userId, array $grades, array $gradeMap): array {
        // Remove existing scores for user
        $this->pdo->prepare('DELETE FROM olevel_scores WHERE user_id = :uid')->execute([':uid' => $userId]);
        // Prepare insert statement
        $insert = $this->pdo->prepare('INSERT INTO olevel_scores (user_id, subject_name, grade, bucket, weight_value) VALUES (:uid, :subject, :grade, :bucket, :weight)');
        // Initialize counters
        $dist = 0; $cred = 0; $pass = 0; $weightSum = 0.0;
        // Loop through each subject grade
        foreach ($grades as $subject => $grade) {
            // Skip unknown grades
            if (!isset($gradeMap[$grade])) { continue; }
            // Resolve bucket and weight
            $bucket = $gradeMap[$grade]['bucket'];
            $weight = $gradeMap[$grade]['weight'];
            // Insert row
            $insert->execute([':uid' => $userId, ':subject' => $subject, ':grade' => $grade, ':bucket' => $bucket, ':weight' => $weight]);
            // Tally counts and weights
            if ($bucket === 'distinction') { $dist++; }
            if ($bucket === 'credit') { $cred++; }
            if ($bucket === 'pass') { $pass++; }
            $weightSum += $weight;
        }
        // Return computed summary
        return ['dist' => $dist, 'cred' => $cred, 'pass' => $pass, 'weight' => $weightSum];
    }

    // Returns saved scores
    public function getScores(int $userId): array {
        // Fetch scores ordered by subject
        $stmt = $this->pdo->prepare('SELECT subject_name, grade, bucket, weight_value FROM olevel_scores WHERE user_id = :uid ORDER BY subject_name');
        $stmt->execute([':uid' => $userId]);
        // Return rows
        return $stmt->fetchAll();
    }
}
