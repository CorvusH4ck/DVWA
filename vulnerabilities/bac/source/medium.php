<?php
if (!defined('DVWA_WEB_PAGE_TO_ROOT')) {
    define('DVWA_WEB_PAGE_TO_ROOT', '../../../');
}

// Get current user's ID from session (not from user-controlled input)
$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], "SELECT user_id FROM users WHERE user = ?");
mysqli_stmt_bind_param($stmt, 's', $_SESSION['dvwaUserLoggedIn']);
mysqli_stmt_execute($stmt);
$current_user_id_val = null;
mysqli_stmt_bind_result($stmt, $current_user_id_val);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
$current_user_id = $current_user_id_val ? $current_user_id_val : 0;

$html = "";
if (isset($_GET['action']) && isset($_GET['user_id'])) {
    if (!preg_match('/^\d+$/', $_GET['user_id'])) {
        $html .= "<p>Invalid user ID format. Please enter a number.</p>";
    } else {
        $id = intval($_GET['user_id']);

        // Only allow users to access their own profile
        if ($id != $current_user_id) {
            $html .= "<p>Access denied. You can only view your own profile.</p>";
        } else {
            $stmt2 = mysqli_prepare($GLOBALS["___mysqli_ston"], "SELECT first_name, last_name, user_id, avatar, role FROM users WHERE user_id = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt2, 'i', $id);
            mysqli_stmt_execute($stmt2);
            $row = null;
            mysqli_stmt_bind_result($stmt2, $fn, $ln, $uid, $av, $role);
            if (mysqli_stmt_fetch($stmt2)) {
                $row = array('first_name' => $fn, 'last_name' => $ln, 'user_id' => $uid, 'avatar' => $av, 'role' => $role);
            }
            mysqli_stmt_close($stmt2);

            if ($row) {
                $html .= "
                    <div class=\"profile-info\">
                        <h3>User Profile</h3>
                        <p>User ID: " . htmlspecialchars($row['user_id']) . "</p>
                        <p>Name: " . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</p>
                        <p>Role: " . htmlspecialchars($row['role']) . "</p>
                    </div>";
            } else {
                $html .= "<p>No user found.</p>";
            }
        }
    }
}
?>