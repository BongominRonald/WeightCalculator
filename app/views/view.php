<?php // View: Saved data summary with update-only via forms on other screens
// Render profile card if present
if (!empty($viewData['user'])) {
    $u = $viewData['user'];
    echo '<div class="card">';
    echo '<h3>Profile</h3>';
    echo '<p>Username: ' . htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') . ' | Email: ' . htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') . ' | Gender: ' . htmlspecialchars($u['gender'], ENT_QUOTES, 'UTF-8') . '</p>';
    echo '</div>';
}

// Ordinary Level subjects and scores
if (!empty($viewData['olevelSubjects'])) {
    echo '<div class="card">';
    echo '<h3>Ordinary Level Subjects</h3>';
    echo '<details class="collapse"><summary>Show subjects</summary><ul>';
    foreach ($viewData['olevelSubjects'] as $s) {
        echo '<li>' . htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8') . ($s['is_compulsory'] ? ' (Compulsory)' : '') . '</li>';
    }
    echo '</ul></details>';
    echo '</div>';
}
if (!empty($viewData['olevelScores'])) {
    echo '<div class="card">';
    echo '<h3>Ordinary Level Scores</h3>';
    echo '<details class="collapse"><summary>Show scores table</summary><div class="table-wrapper"><table><tr><th>Subject</th><th>Grade</th><th>Bucket</th><th>Weight</th></tr>';
    foreach ($viewData['olevelScores'] as $s) {
        echo '<tr><td>' . htmlspecialchars($s['subject_name'], ENT_QUOTES, 'UTF-8') . '</td><td>' . $s['grade'] . '</td><td>' . $s['bucket'] . '</td><td>' . $s['weight_value'] . '</td></tr>';
    }
    echo '</table></div></details>';
    echo '</div>';
}

// Advanced Level subjects and scores
if (!empty($viewData['alevelSubjects'])) {
    echo '<div class="card">';
    echo '<h3>Advanced Level Subjects</h3>';
    echo '<details class="collapse"><summary>Show subjects</summary><ul>';
    foreach ($viewData['alevelSubjects'] as $s) {
        echo '<li>' . htmlspecialchars($s['subject_name'], ENT_QUOTES, 'UTF-8') . ' (' . $s['category'] . ')</li>';
    }
    echo '</ul></details>';
    echo '</div>';
}
if (!empty($viewData['alevelScores'])) {
    echo '<div class="card">';
    echo '<h3>Advanced Level Scores</h3>';
    echo '<details class="collapse"><summary>Show scores table</summary><div class="table-wrapper"><table><tr><th>Subject</th><th>Grade</th><th>Points</th><th>Category</th></tr>';
    foreach ($viewData['alevelScores'] as $s) {
        echo '<tr><td>' . htmlspecialchars($s['subject_name'], ENT_QUOTES, 'UTF-8') . '</td><td>' . $s['grade'] . '</td><td>' . $s['points'] . '</td><td>' . $s['category'] . '</td></tr>';
    }
    echo '</table></div></details>';
    echo '</div>';
}

// Results summary
if (!empty($viewData['results'])) {
    $r = $viewData['results'];
    echo '<div class="card">';
    echo '<h3>Weights & Eligibility</h3>';
    echo '<p>Ordinary Level Weight: ' . $r['olevel_weight'] . ' | Advanced Level Weight: ' . $r['alevel_weight'] . ' | Gender Bonus: ' . $r['gender_bonus'] . ' | Total: ' . $r['total_weight'] . '</p>';
    echo '<p>Cutoff: ' . ($r['cutoff'] ?? 'N/A') . ' | Total Points: ' . ($r['total_points'] ?? 0) . '</p>';
    echo '</div>';
}

// Guidance for updating via proper flows
// Removed guidance text as per request
