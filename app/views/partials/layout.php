<?php
// layout.php - Shared layout functions for rendering HTML head, sidebar, and footer
// Used across all app pages for consistent structure

// render_header($title) - Outputs the HTML head with title, meta tags, and assets
function render_header(string $title): void {
    // Output DOCTYPE and HTML start
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
    // Set page title dynamically
    echo '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>';
    // Include favicon and touch icon for branding
    echo '<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ctext y=\'.9em\' font-size=\'90\' fill=\'%2310A37F\'%3E%E2%9A%96%3C/text%3E%3C/svg%3E">'; // Favicon
    echo '<link rel="apple-touch-icon" href="data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ctext y=\'.9em\' font-size=\'90\' fill=\'%2310A37F\'%3E%E2%9A%96%3C/text%3E%3C/svg%3E">'; // Apple touch icon
    // Theme color for browser UI
    echo '<meta name="theme-color" content="#10A37F">'; // Theme color
    // Link to main stylesheet
    echo '<link rel="stylesheet" href="/Charm/assets/style.css">'; // Link to shared stylesheet
    // Link to main JavaScript
    echo '<script src="/Charm/assets/script.js"></script>'; // Link to shared script
    // End head, start body, container div
    echo '</head><body>';
    // Removed duplicate header for cleaner layout
    echo '<div class="container">';
}

// render_sidebar($active) - Outputs the sidebar navigation with grouped links
function render_sidebar(string $active): void {
    // Sidebar with navigation role and label for accessibility
    echo '<div class="sidebar" role="navigation" aria-label="Main navigation">';
    // Hamburger button for mobile toggle, accessible
    echo '<div class="hamburger" role="button" tabindex="0" aria-label="Toggle menu" aria-expanded="false" data-target="sidebar">≡</div>'; // Mobile toggle button (JS bound)
    // Define navigation groups for organized menu
    $groups = [
        'Main' => [ 'dashboard' => '🏠 Dashboard' ], // Main section
        'Ordinary Level' => [ 'olevel_subjects' => '📚 Subjects', 'olevel_scores' => '📊 Scores' ], // O-Level section
        'Advanced Level' => [ 'alevel_subjects' => '📖 Subjects', 'alevel_scores' => '📈 Scores' ], // A-Level section
        'Tools' => [ 'weight' => '⚖️ Weight Calculator', 'view' => '📋 View Saved Results' ], // Tools section
        'Account' => [ 'logout' => '🚪 Logout' ], // Account section
    ];
    // Loop through groups and items to build menu
    foreach ($groups as $groupName => $items) {
        // Group title
        echo '<div class="nav-group"><div class="nav-group-title">' . htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') . '</div>';
        foreach ($items as $key => $label) {
            // Determine active class and logout class
            $cls = ($active === $key) ? 'nav-link active' : 'nav-link';
            if ($key === 'logout') { $cls .= ' logout-link'; }
            // Set href based on controller
            if ($key === 'logout') {
                $href = '/Charm/app/controllers/AuthController.php?action=logout'; // Logout via AuthController
            } else {
                $href = '/Charm/app/controllers/DashboardController.php?action=' . $key; // Other pages via DashboardController
            }
            // Output link
            echo '<a class="' . $cls . '" href="' . $href . '">' . $label . '</a>';
        }
        echo '</div>';
    }
    echo '</div>';
    // Backdrop for mobile overlay close
    echo '<div id="sidebar-backdrop" class="sidebar-backdrop" tabindex="-1" aria-hidden="true"></div>';
}

// open_content($title) - Starts the main content area with topbar
function open_content(string $title): void {
    // Main content wrapper
    echo '<div class="content">';
    // Topbar with title and theme toggle
    echo '<div class="topbar"><h1 style="margin:0"><span style="font-size:2em;">⚖️</span> ' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>';
    // Theme toggle button, accessible
    echo '<div class="topbar-controls"><div class="theme-toggle off" role="switch" aria-checked="false" aria-label="Toggle theme" tabindex="0"><span class="thumb"></span></div></div>';
    echo '</div>';
}

// render_footer() - Outputs the footer and modal
function render_footer(): void {
    // Footer with developer credits
    echo '<div class="footer">Developed by Bongomin Ronald. WhatsApp: 0774120185/0759343570. Email: abonga029@gmail.com</div>'; // Footer credit
    // Logout confirmation modal, hidden by default
    echo '<div id="confirm-logout-modal" class="modal" role="dialog" aria-modal="true" aria-hidden="true"><div class="modal-panel"><h3>Confirm Sign Out</h3><p>Are you sure you want to sign out of your account?</p><div class="modal-actions"><button class="btn btn-secondary cancel-logout">Cancel</button><button class="btn btn-primary confirm-logout">Sign Out</button></div></div></div>';
    // Close container and HTML
    echo '</div></div></body></html>';
}
