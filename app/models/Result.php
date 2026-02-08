<?php
// Provides operations for storing and retrieving computed results
class Result {
    // PDO connection holder
    private $pdo; // Database handle

    // Accept PDO dependency
    public function __construct(PDO $pdo) {
        // Store PDO for reuse
        $this->pdo = $pdo;
    }

    // Saves or updates result row with weights and eligibility
    public function save(int $userId, float $olevelWeight, float $alevelWeight, float $genderBonus, ?float $cutoff, float $totalWeight, int $totalPoints, ?string $eligibility): void {
        // Check if a result row already exists
        $check = $this->pdo->prepare('SELECT id FROM results WHERE user_id = :uid');
        $check->execute([':uid' => $userId]);
        // If exists, update; else, insert
        if ($check->fetch()) {
            // Update existing result
            $stmt = $this->pdo->prepare('UPDATE results SET olevel_weight = :ow, alevel_weight = :aw, gender_bonus = :gb, cutoff = :co, total_weight = :tw, total_points = :tp, eligibility = :el WHERE user_id = :uid');
            $stmt->execute([':ow' => $olevelWeight, ':aw' => $alevelWeight, ':gb' => $genderBonus, ':co' => $cutoff, ':tw' => $totalWeight, ':tp' => $totalPoints, ':el' => $eligibility, ':uid' => $userId]);
        } else {
            // Insert new result
            $stmt = $this->pdo->prepare('INSERT INTO results (user_id, olevel_weight, alevel_weight, gender_bonus, cutoff, total_weight, total_points, eligibility) VALUES (:uid, :ow, :aw, :gb, :co, :tw, :tp, :el)');
            $stmt->execute([':uid' => $userId, ':ow' => $olevelWeight, ':aw' => $alevelWeight, ':gb' => $genderBonus, ':co' => $cutoff, ':tw' => $totalWeight, ':tp' => $totalPoints, ':el' => $eligibility]);
        }
    }

    // Updates only O-Level weight while keeping other values intact
    public function updateOLevel(int $userId, float $olevelWeight): void {
        // Try to update an existing row
        $updated = $this->pdo->prepare('UPDATE results SET olevel_weight = :ow WHERE user_id = :uid');
        $updated->execute([':ow' => $olevelWeight, ':uid' => $userId]);
        // If no row was updated, insert a placeholder row
        if ($updated->rowCount() === 0) {
            $this->pdo->prepare('INSERT INTO results (user_id, olevel_weight) VALUES (:uid, :ow)')
                ->execute([':uid' => $userId, ':ow' => $olevelWeight]);
        }
    }

    // Updates only total grade points (A-Level points sum)
    public function updateTotalPoints(int $userId, int $totalPoints): void {
        // Try to update an existing row
        $updated = $this->pdo->prepare('UPDATE results SET total_points = :tp WHERE user_id = :uid');
        $updated->execute([':tp' => $totalPoints, ':uid' => $userId]);
        // If no row was updated, insert a placeholder row
        if ($updated->rowCount() === 0) {
            $this->pdo->prepare('INSERT INTO results (user_id, total_points) VALUES (:uid, :tp)')
                ->execute([':uid' => $userId, ':tp' => $totalPoints]);
        }
    }

    // Returns saved result row for a user
    public function getByUser(int $userId): ?array {
        // Query by user id
        $stmt = $this->pdo->prepare('SELECT * FROM results WHERE user_id = :uid');
        $stmt->execute([':uid' => $userId]);
        // Fetch row or null
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
