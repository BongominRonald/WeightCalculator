<?php // View: O-Level subjects registration (compulsory + 2-3 optionals)
// Show form for optionals only (compulsories fixed)
echo '<div class="card">';
echo '<h3>Ordinary Level Subjects</h3>'; 
echo '<p>Compulsory subjects are fixed: ' . implode(', ', $compulsoryOLevel) . '.</p>'; 
echo '<form method="post" action="/Charm/app/controllers/DashboardController.php?action=olevel_subjects">';
echo '<label>Optional 1</label><input name="optional1" placeholder="e.g. Literature">';
echo '<label>Optional 2</label><input name="optional2" placeholder="e.g. Fine Art">';
echo '<label>Optional 3</label><input name="optional3" placeholder="(Optional) Third subject">';
echo '<button type="submit" class="btn btn-primary">Save Subjects</button>';
echo '</form>';
echo '</div>';

// Provide next step guidance
echo '<div class="card"><p>Next: enter Ordinary Level scores after saving subjects.</p><a class="nav-link" href="/Charm/app/controllers/DashboardController.php?action=olevel_scores">Enter Ordinary Level Scores</a></div>';
