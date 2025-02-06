<?php
session_start();
require "config.php";

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: includes/login.php");
    exit();
}

// Connect to the database
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get admin level from the session
$discord_id = $_SESSION['user']['id'];
$stmt = $mysqli->prepare("SELECT admin FROM users WHERE discord_id = ?");
$stmt->bind_param("s", $discord_id);
$stmt->execute();
$stmt->bind_result($admin_level);
$stmt->fetch();
$stmt->close();

// Check admin level and redirect if not sufficient
if ($admin_level < 2) {
    $mysqli->close();
    header("Location: index.php");
    exit();
}

$message = "";

// Handle user update if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];

    if ($action === 'promote') {
        $stmt = $mysqli->prepare("UPDATE users SET admin = 1 WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        $message = "Utilisateur promu avec succès.";
    } elseif ($action === 'demote') {
        $stmt = $mysqli->prepare("UPDATE users SET admin = 0 WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        $message = "Utilisateur rétrogradé avec succès.";
    } elseif ($action === 'update_qcm') {
        $qcm_failures = $_POST['qcm_failures'];
        $stmt = $mysqli->prepare("UPDATE users SET qcm_failures = ? WHERE id = ?");
        $stmt->bind_param("ii", $qcm_failures, $user_id);
        $stmt->execute();
        $stmt->close();
        $message = "QCM mis à jour avec succès.";
    } elseif ($action === 'delete_background') {
        $stmt = $mysqli->prepare("DELETE FROM backgrounds WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        $message = "Background supprimé avec succès.";
    }
}

// Fetch users from the database
$search_query = "WHERE 1=1";
$search_param = "";
$role_filter = "";
if (isset($_GET['search'])) {
    $search_param = "%" . $_GET['search'] . "%";
    $search_query .= " AND username LIKE ?";
}
if (isset($_GET['role'])) {
    $role_filter = $_GET['role'];
    if ($role_filter === 'utilisateur') {
        $search_query .= " AND admin = 0";
    } elseif ($role_filter === 'douanier') {
        $search_query .= " AND admin = 1";
    } elseif ($role_filter === 'admin') {
        $search_query .= " AND admin = 2";
    }
}

$stmt = $mysqli->prepare("SELECT id, username, admin, qcm_failures FROM users $search_query");
if ($search_param) {
    $stmt->bind_param("s", $search_param);
}
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$mysqli->close();

function getAdminLevel($level) {
    switch ($level) {
        case 0:
            return "Utilisateur";
        case 1:
            return "Douanier";
        case 2:
            return "Admin";
        default:
            return "Unknown";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Admins</title>
    <link rel="icon" type="image/png" href="IMG/alta1.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgba(3,9,19,255);
            color: white;
        }

        nav {
            background-color: rgba(3,9,19,255);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
        }

        .logo {
            height: 50px;
            margin: 10px 20px;
        }

        .navbar {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: space-around;
            flex-grow: 1;
        }

        .navbar li {
            position: relative;
        }

        .navbar a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .navbar a::after {
            content: '';
            display: block;
            width: 0;
            height: 2px;
            background: #fff;
            transition: width 0.3s;
            margin: auto;
        }

        .navbar a:hover::after {
            width: 100%;
        }

        .content {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section {
            margin: 40px 0;
        }

        .section h2 {
            font-size: 2em;
            border-bottom: 2px solid #1c1c1e;
            padding-bottom: 10px;
        }

        .section p {
            font-size: 1.2em;
            line-height: 1.6em;
        }

        .message {
            background-color: #333;
            color: #fff;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
        }

        .button {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            border-radius: 8px;
            border: none;
            transition: background-color 0.3s, transform 0.3s;
        }

        .button:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .button-demote {
            background-color: #f44336;
        }

        .button-demote:hover {
            background-color: #e41e1e;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .search-bar input {
            width: calc(100% - 100px);
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-right: 10px;
        }

        .search-bar button {
            /* éloigne des autres élément */
            

            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-bar button:hover {
            background-color: #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        tr {
            margin-bottom: 10px;
        }

        tr:hover {
            background-color: #444;
        }

        .actions {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
    <nav>
        <img src="IMG/alta1.png" alt="Logo ALTA" class="logo">
        <ul class="navbar">
            <li><a href="index.php">Accueil</a></li>
        </ul>
    </nav>
    <div class="content">
        <section class="section">
            <h2>Gestion des Admins</h2>
            <p>Utilisez ce formulaire pour mettre à jour les utilisateurs au niveau admin (douanier) ou utilisateur.</p>
            <?php if ($message): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            <div class="search-bar">
                <form method="GET" action="admin_management.php">
                    <input type="text" name="search" placeholder="Rechercher par nom d'utilisateur" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <select name="role">
                        <option value="">Tous les utilisateurs</option>
                        <option value="utilisateur" <?php echo isset($_GET['role']) && $_GET['role'] == 'utilisateur' ? 'selected' : ''; ?>>Utilisateurs</option>
                        <option value="douanier" <?php echo isset($_GET['role']) && $_GET['role'] == 'douanier' ? 'selected' : ''; ?>>Douaniers</option>
                        <option value="admin" <?php echo isset($_GET['role']) && $_GET['role'] == 'admin' ? 'selected' : ''; ?>>Admins</option>
                    </select>
                    <button type="submit">Rechercher</button>
                </form>
            </div>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nom d'utilisateur</th>
                    <th>Niveau</th>
                    <th>Échecs au QCM</th>
                    <th>Actions</th>
                </tr>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo getAdminLevel($user['admin']); ?></td>
                        <td><?php echo $user['qcm_failures']; ?></td>
                        <td class="actions">
                            <form method="POST" action="admin_management.php" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="action" value="promote">
                                <button type="submit" class="button">Promouvoir Douanier</button>
                            </form>
                            <form method="POST" action="admin_management.php" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="action" value="demote">
                                <button type="submit" class="button button-demote">Rétrograder Utilisateur</button>
                            </form>
                            <form method="POST" action="admin_management.php" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="action" value="update_qcm">
                                <input type="number" name="qcm_failures" value="<?php echo $user['qcm_failures']; ?>" min="0" max="100">
                                <button type="submit" class="button">Mettre à jour QCM</button>
                            </form>
                            <form method="POST" action="admin_management.php" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="action" value="delete_background">
                                <button type="submit" class="button button-demote">Supprimer Background</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </section>
    </div>
</body>
</html>
