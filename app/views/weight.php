<?php // View: Weight calculation and eligibility
// Ensure A-Level subjects and scores exist before weight calculation
if (empty($viewData['subjects'] ?? [])) { // Need Advanced Level subjects
    echo '<div class="card"><p>Please add Advanced Level subjects first.</p><a class="nav-link" href="/Charm/app/controllers/DashboardController.php?action=alevel_subjects">Add Advanced Level Subjects</a></div>'; // Prompt
    return; // Stop rendering
}

// Extract principles for dropdowns and attempt to auto-select the top two by points
$principles = array_values(array_filter($viewData['subjects'], fn($s) => $s['category'] === 'principle')); // Principle subjects
// Determine top two principles by points (server-side fallback)
$topTwo = [];
if (!empty($principles)) {
    $sorted = $principles;
    usort($sorted, fn($a, $b) => ($b['points'] ?? 0) <=> ($a['points'] ?? 0));
    $topTwo = array_slice(array_column($sorted, 'subject_name'), 0, 2);
}

// Render weight form
echo '<div class="card">';
echo '<h3>Weight Calculation</h3>'; 
echo '<p>Select two essentials and one desirable (all must be different). Subsidiaries count automatically. Gender bonus is applied from your profile.</p>'; 
echo '<form method="post" action="/Charm/app/controllers/DashboardController.php?action=weight">';
// Essential 1
echo '<label>Essential 1</label><select name="essential1">';
foreach ($principles as $p) {
    $name = htmlspecialchars($p['subject_name'], ENT_QUOTES, 'UTF-8');
    $sel = in_array($p['subject_name'], $topTwo) && ($topTwo[0] === $p['subject_name']) ? ' selected' : '';
    echo '<option value="' . $name . '"' . $sel . '>' . $name . '</option>'; }
echo '</select>';
// Essential 2
echo '<label>Essential 2</label><select name="essential2">';
foreach ($principles as $p) {
    $name = htmlspecialchars($p['subject_name'], ENT_QUOTES, 'UTF-8');
    $sel = in_array($p['subject_name'], $topTwo) && ($topTwo[1] === $p['subject_name']) ? ' selected' : '';
    echo '<option value="' . $name . '"' . $sel . '>' . $name . '</option>'; }
echo '</select>';
// Desirable
echo '<label>Desirable (remaining principle)</label><select name="desirable">';
// Preselect the remaining principle as desirable when topTwo are available
foreach ($principles as $p) {
    $name = htmlspecialchars($p['subject_name'], ENT_QUOTES, 'UTF-8');
    $sel = '';
    if (!empty($topTwo) && !in_array($p['subject_name'], $topTwo)) { $sel = ' selected'; }
    echo '<option value="' . $name . '"' . $sel . '>' . $name . '</option>'; }
echo '</select>';
// Cutoff
echo '<label>University Cutoff (optional)</label><input type="number" step="0.01" name="cutoff" placeholder="e.g. 45">';
// Submit
echo '<button type="submit" class="btn btn-primary">Compute Weight</button>';
echo '</form>';
echo '</div>';

// Show latest results if available
if (!empty($viewData['results'])) { // If results exist
    $r = $viewData['results']; // Alias
    echo '<div class="card">';
    echo '<h3>Latest Results</h3>'; 
    echo '<p>Ordinary Level Weight: ' . $r['olevel_weight'] . ' | Advanced Level Weight: ' . $r['alevel_weight'] . ' | Gender Bonus: ' . $r['gender_bonus'] . ' | Total: ' . $r['total_weight'] . '</p>'; 
    echo '<p>Cutoff: ' . ($r['cutoff'] ?? 'N/A') . '</p>'; 
    echo '</div>'; 
}

// Guidance for next steps
echo '<div class="card"><p>Next: review everything in View Saved Results.</p><a class="nav-link" href="/Charm/app/controllers/DashboardController.php?action=view">View Saved Results</a></div>'; 
