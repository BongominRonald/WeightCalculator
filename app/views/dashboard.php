<?php // Dashboard view showing welcome and steps
// Show welcome card
echo '<div class="card"><h3>Welcome</h3><p>Hey ' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . ', follow the steps to register subjects, enter scores, and compute your weights.</p></div>';
// Show guided steps
echo '<div class="row">';
$steps = [
    ['title' => '📚 Ordinary Level Subjects', 'desc' => 'Add 2-3 optional subjects (compulsories are fixed).', 'link' => 'olevel_subjects'],
    ['title' => '📊 Ordinary Level Scores', 'desc' => 'Enter grades for all Ordinary Level subjects.', 'link' => 'olevel_scores'],
    ['title' => '📖 Advanced Level Subjects', 'desc' => 'Enter three principal subjects and choose a subsidiary. General Paper is default.', 'link' => 'alevel_subjects'],
    ['title' => '📈 Advanced Level Scores', 'desc' => 'Enter grades for principal subjects and subsidiaries.', 'link' => 'alevel_scores'],
    ['title' => '⚖️ Weight Calculator', 'desc' => 'Pick essentials and compute weights.', 'link' => 'weight'],
    ['title' => '📋 View Saved Results', 'desc' => 'Review all saved data (update via forms, no deletes).', 'link' => 'view'],
];
foreach ($steps as $step) {
    echo '<div class="col"><div class="card"><h3>' . $step['title'] . '</h3><p>' . $step['desc'] . '</p><a class="nav-link nav-action btn btn-primary" href="/Charm/app/controllers/DashboardController.php?action=' . $step['link'] . '">Open</a></div></div>';
}
echo '</div>';
