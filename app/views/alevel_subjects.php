<?php // View: A-Level subjects registration (3 principles + subsidiary choice + GP auto)
// Render form for principles and subsidiary choice
echo '<div class="card">';
echo '<h3>Advanced Level Subjects</h3>'; 
echo '<p>Enter three principal subjects. General Paper is automatically added. Choose either ICT or Sub Mathematics as the subsidiary choice.</p>'; 
echo '<form method="post" action="/Charm/app/controllers/DashboardController.php?action=alevel_subjects">';
echo '<label>Principal 1</label><input name="principle1" placeholder="e.g. Mathematics" required>'; 
echo '<label>Principal 2</label><input name="principle2" placeholder="e.g. Physics" required>'; 
echo '<label>Principal 3</label><input name="principle3" placeholder="e.g. Chemistry" required>'; 
echo '<label>Subsidiary Choice</label><select name="subsidiary_choice" required>'; 
echo '<option value="">Select</option>'; 
echo '<option value="ICT">ICT</option>'; 
echo '<option value="Sub Mathematics">Sub Mathematics</option>'; 
echo '</select>'; 
echo '<button type="submit" class="btn btn-primary">Save Advanced Level Subjects</button>'; 
echo '</form>'; 
echo '</div>'; 

// Next step guidance
echo '<div class="card"><p>Next: enter Advanced Level scores after saving subjects.</p><a class="nav-link" href="/Charm/app/controllers/DashboardController.php?action=alevel_scores">Enter Advanced Level Scores</a></div>'; 
