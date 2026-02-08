<?php
// Provides operations for A-Level subjects and scores
class ALevel {
    // Holds PDO connection
    private $pdo; // Database handle

    // Accept PDO dependency
    public function __construct(PDO $pdo) {
        // Store PDO
        $this->pdo = $pdo;
    }

    // Saves three principles and two subsidiaries (GP + choice)
    public function saveSubjects(int $userId, array $principles, string $subsidiary): void {
        // Clear previous subjects for user
        $this->pdo->prepare('DELETE FROM alevel_subjects WHERE user_id = :uid')->execute([':uid' => $userId]);
        // Prepare insert statement
        $insert = $this->pdo->prepare('INSERT INTO alevel_subjects (user_id, subject_name, category) VALUES (:uid, :name, :cat)');
        // Insert all principles
        foreach ($principles as $name) {
            $insert->execute([':uid' => $userId, ':name' => $name, ':cat' => 'principle']);
        }
        // Insert GP as subsidiary
        $insert->execute([':uid' => $userId, ':name' => 'General Paper', ':cat' => 'subsidiary']);
        // Insert chosen subsidiary
        $insert->execute([':uid' => $userId, ':name' => $subsidiary, ':cat' => 'subsidiary']);
    }

    // Fetches all subjects for a user
    public function getSubjects(int $userId): array {
        // Select subject name and category ordered by category
        $stmt = $this->pdo->prepare('SELECT subject_name, category FROM alevel_subjects WHERE user_id = :uid ORDER BY category DESC, subject_name');
        $stmt->execute([':uid' => $userId]);
        // Return results
        return $stmt->fetchAll();
    }

    // Saves grades and points for A-Level subjects
    public function saveScores(int $userId, array $grades, array $categories, array $principleMap, array $subsidiaryMap): int {
        // Remove old scores
        $this->pdo->prepare('DELETE FROM alevel_scores WHERE user_id = :uid')->execute([':uid' => $userId]);
        // Prepare insert
        $insert = $this->pdo->prepare('INSERT INTO alevel_scores (user_id, subject_name, grade, points, category) VALUES (:uid, :subject, :grade, :points, :cat)');
        // Track total points
        $totalPoints = 0;
        // Iterate over submitted grades
        foreach ($grades as $subject => $grade) {
            // Resolve category for subject
            $cat = $categories[$subject] ?? 'principle';
            // Determine points based on category map
            $points = ($cat === 'principle') ? ($principleMap[$grade] ?? 0) : ($subsidiaryMap[$grade] ?? 0);
            // Insert row
            $insert->execute([':uid' => $userId, ':subject' => $subject, ':grade' => $grade, ':points' => $points, ':cat' => $cat]);
            // Add to total
            $totalPoints += $points;
        }
        // Return total points for display
        return $totalPoints;
    }

    // Returns saved scores
    public function getScores(int $userId): array {
        // Fetch scores ordered by category then subject
        $stmt = $this->pdo->prepare('SELECT subject_name, grade, points, category FROM alevel_scores WHERE user_id = :uid ORDER BY category DESC, subject_name');
        $stmt->execute([':uid' => $userId]);
        // Return rows
        return $stmt->fetchAll();
    }
}
