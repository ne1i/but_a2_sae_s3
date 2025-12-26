<?php

use ButA2SaeS3\Constants;
use ButA2SaeS3\FageDB;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();

HttpUtils::ensure_valid_session($db);
require_once __DIR__ . "/../templates/admin_head.php";

// Only admins can access this page
if (!$db->has_permission(HttpUtils::get_current_user_id($db), 'all')) {
    header('Location: /admin');
    exit;
}

// Handle form submissions
if (HttpUtils::isPost()) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_user_roles' && isset($_POST['user_id']) && isset($_POST['roles'])) {
            $user_id = $_POST['user_id'];
            $role_ids = $_POST['roles'];

            if ($db->update_user_roles($user_id, $role_ids)) {
                $role_update_success = "Rôles de l'utilisateur mis à jour avec succès";
                $db->log_audit('user', $user_id, 'update_roles', HttpUtils::get_current_user_id($db), 'Roles updated: ' . implode(', ', $role_ids));
            } else {
                $role_update_error = "Erreur lors de la mise à jour des rôles";
            }
        } elseif ($_POST['action'] === 'delete_user' && isset($_POST['delete_user_id'])) {
            $user_id = $_POST['delete_user_id'];

            if ($user_id == HttpUtils::get_current_user_id($db)) {
                $delete_user_error = "Vous ne pouvez pas supprimer votre propre compte";
            } else {
                $user = $db->get_user_by_id($user_id);
                if ($user && $db->delete_user($user_id)) {
                    $delete_user_success = "L'utilisateur \"{$user['username']}\" a bien été supprimé";
                    $db->log_audit('user', $user_id, 'delete', HttpUtils::get_current_user_id($db), "User deleted: {$user['username']}");
                } else {
                    $delete_user_error = "Erreur lors de la suppression de l'utilisateur";
                }
            }
        }
    }
}

// Handle user creation form
$username = $_POST["username"] ?? null;
$password = $_POST["password"] ?? null;
$password_confirm = $_POST["password-confirm"] ?? null;
$role = $_POST["role"] ?? null;
$poles_array = $_POST["poles"] ?? [];

if (isset($username) && isset($password) && isset($role)) {
    if ($username === "" || $password === "" || $password_confirm === "" || $role === "" || ($role === "responsable-pole" && $poles_array === [])) {
        $create_user_error = "Identifiants incorrects (champ(s) vide(s))";
    } elseif ($db->exists($username, "users", "username")) {
        $create_user_error = "Un compte avec cet identifiant existe déjà";
    } elseif ($password !== $password_confirm) {
        $create_user_error = "Les mots de passe ne correspondent pas";
    } else {
        $db->add_user($username, $password, $role, $poles_array);
        $create_user_success = "Le compte {$username} a été créé avec succès";
    }
}

// Get data for display
$users = $db->get_users();
$audit_logs = $db->get_audit_logs(50, 1);
$roles = $db->get_all_roles();
?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <main class="p-2 space-y-8">
        <!-- Create User -->
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div class="mb-4">
                <?= c::BackToLink(); ?>
            </div>
            <?= c::Heading2("Créer un nouvel utilisateur") ?>
            <div>
                <form action="/securite" method="post" class="flex flex-col">
                    <?= c::FormInput("username", "Nom d'utilisateur", "text", "", true, "mb-4") ?>
                    <?= c::FormInput("password", "Mot de passe", "password", "", true, "mb-4") ?>
                    <?= c::FormInput("password-confirm", "Confirmer le mot de passe", "password", "", true, "mb-4") ?>

                    <label for="role" class="text-lg">Rôle de l'utilisateur (droits)</label>
                    <?php
                    $role_options = [
                        'admin' => 'Administrateur (tout les droits)',
                        'responsable-pole' => 'Responsable de pôle'
                    ];
                    echo c::FormSelect("role", label: "", options: $role_options, selected: "", class: "mb-4", attributes: ["id" => "role"]);
                    ?>

                    <div id="choix-poles" class="hidden flex-col justify-start gap-2">
                        <label>
                            <input type="checkbox" name="poles[]" value="responsable-benevoles">
                            Pôle Bénévoles
                        </label>

                        <label>
                            <input type="checkbox" name="poles[]" value="responsable-communication">
                            Pôle Communication
                        </label>

                        <label>
                            <input type="checkbox" name="poles[]" value="responsable-partenariats">
                            Pôle Partenariats
                        </label>

                        <label>
                            <input type="checkbox" name="poles[]" value="responsable-tresorerie">
                            Pôle Trésorerie
                        </label>

                        <label>
                            <input type="checkbox" name="poles[]" value="responsable-direction">
                            Pôle Direction
                        </label>
                    </div>

                    <script>
                        document.getElementById("role").addEventListener("change", function() {
                            const bloc = document.getElementById("choix-poles");

                            if (this.value === "responsable-pole") {
                                bloc.style.display = "flex";
                            } else {
                                bloc.style.display = "none";
                            }
                        });
                    </script>

                    <!-- Alert messages for user creation -->
                    <?php
                    if (isset($create_user_error)) {
                        echo c::Message($create_user_error, 'error');
                    }
                    if (isset($create_user_success)) {
                        echo c::Message($create_user_success, 'success');
                    }
                    ?>

                    <?= c::Button("Créer le compte", "fage", "submit", "my-4") ?>

                    <?php
                    if (Constants::is_debug()) {
                    ?>
                        <?= c::Button("Autofill (debug)", "fage", "button", "", ["id" => "autofill"]) ?>

                        <script>
                            function makeid(length) {
                                var result = '';
                                var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                                var charactersLength = characters.length;
                                for (var i = 0; i < length; i++) {
                                    result += characters.charAt(Math.floor(Math.random() * charactersLength));
                                }
                                return result;
                            }

                            autofill.addEventListener("click", () => {
                                document.querySelector("[name='username']").value = makeid(6);
                                const pwd = makeid(6)
                                document.querySelector("[name='password']").value = pwd;
                                document.querySelector("[name='password-confirm']").value = pwd;

                            });
                        </script>
                    <?php
                    }
                    ?>
                </form>
            </div>
        </div>

        <!-- User Management -->
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <?= c::Heading2("Gestion des Utilisateurs") ?>

                <!-- Alert messages for role update -->
                <?php
                if (isset($role_update_error)) {
                    echo c::Message($role_update_error, 'error');
                }
                if (isset($role_update_success)) {
                    echo c::Message($role_update_success, 'success');
                }
                ?>

                <!-- Alert messages for user deletion -->
                <?php
                if (isset($delete_user_error)) {
                    echo c::Message($delete_user_error, 'error');
                }
                if (isset($delete_user_success)) {
                    echo c::Message($delete_user_success, 'success');
                }
                ?>

                <div class="scroll-container">
                    <table class="border-2 shadow-sm table-auto w-full overflow-x-scroll">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border-2 px-4 py-2 text-left">Nom d'utilisateur</th>
                                <th class="border-2 px-4 py-2 text-left">Adhérent associé</th>
                                <th class="border-2 px-4 py-2 text-left">Rôles actuels</th>
                                <th class="border-2 px-4 py-2 text-left">Date de création</th>
                                <th class="border-2 px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $idx = 0;
                            foreach ($users as $user): ?>
                                <tr class="<?= $idx % 2 == 0 ? 'bg-gray-200' : 'bg-gray-50' ?> hover:bg-gray-300">
                                    <td class="border-2 px-4 py-2 font-medium"><?= htmlspecialchars($user['username']) ?></td>
                                    <td class="border-2 px-4 py-2">
                                        <?php
                                        if ($user['first_name'] && $user['last_name']) {
                                            echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']);
                                        } else {
                                            echo '<span class="text-gray-500">Non associé</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="border-2 px-4 py-2">
                                        <div class="flex flex-wrap gap-1">
                                            <?php
                                            $user_roles = $user['roles'] ? explode(',', $user['roles']) : [];
                                            foreach ($user_roles as $role): ?>
                                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                                    <?= htmlspecialchars(trim($role)) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td class="border-2 px-4 py-2">
                                        <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                                    </td>
                                    <td class="border-2 px-4 py-2 space-x-2">
                                        <a href="#" class="text-blue-600 underline" onclick="showRoleModal(
                                        <?php echo $user['id']; ?>, 
                                        '<?php echo htmlspecialchars($user['username']); ?>', 
                                        '<?php echo htmlspecialchars($user['roles'] ?? ''); ?>'); 
                                        return false;">
                                            Modifier rôles
                                        </a>
                                        <?php if ($user['id'] != HttpUtils::get_current_user_id($db)): ?>
                                            <form method="post" style="display: inline;" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                                <input type="hidden" name="action" value="delete_user">
                                                <input type="hidden" name="delete_user_id" value="<?= $user['id'] ?>">
                                                <button type="submit" class="bg-transparent hover:bg-transparent text-red-600 hover:text-red-700 underline px-0 py-0 border-0 focus:outline-none">Supprimer</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php
                            $idx++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Audit Logs -->
        <div class="shadow-lg bg-white p-10 px-14 rounded-2xl">
            <div>
                <?= c::Heading2("Journal d'Audit") ?>

                <div class="scroll-container">
                    <table class="border-2 shadow-sm table-auto w-full overflow-x-scroll">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border-2 px-4 py-2 text-left">Date</th>
                                <th class="border-2 px-4 py-2 text-left">Utilisateur</th>
                                <th class="border-2 px-4 py-2 text-left">Entité</th>
                                <th class="border-2 px-4 py-2 text-left">Action</th>
                                <th class="border-2 px-4 py-2 text-left">Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $idx = 0;
                            foreach ($audit_logs as $log): ?>
                                <tr class="<?= $idx % 2 == 0 ? 'bg-gray-200' : 'bg-gray-50' ?> hover:bg-gray-300">
                                    <td class="border-2 px-4 py-2">
                                        <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                                    </td>
                                    <td class="border-2 px-4 py-2">
                                        <?= htmlspecialchars($log['username'] ?? 'Système') ?>
                                    </td>
                                    <td class="border-2 px-4 py-2">
                                        <?= htmlspecialchars($log['entity']) ?>
                                    </td>
                                    <td class="border-2 px-4 py-2">
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm">
                                            <?= htmlspecialchars($log['action']) ?>
                                        </span>
                                    </td>
                                    <td class="border-2 px-4 py-2">
                                        <?= htmlspecialchars($log['details'] ?? '') ?>
                                    </td>
                                </tr>
                            <?php
                            $idx++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <!-- Role Dialog -->
    <dialog id="roleDialog" class="p-0 border-0 rounded-lg shadow-xl fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 m-0">
        <div class="bg-white rounded-lg w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Modifier les rôles de l'utilisateur</h3>
                <p class="text-sm text-gray-500 mb-4">Utilisateur: <span id="modalUsername" class="font-medium"></span></p>

                <form id="roleForm" method="post">
                    <input type="hidden" name="action" value="update_user_roles">
                    <input type="hidden" id="modalUserId" name="user_id">

                    <div class="space-y-2 mb-4">
                        <?php foreach ($roles as $role): ?>
                            <label class="flex items-center">
                                <input type="checkbox" name="roles[]" value="<?= $role['id'] ?>"
                                    class="text-fage-600" id="role_<?= $role['id'] ?>">
                                <span class="ml-2"><?= htmlspecialchars($role['name']) ?></span>
                                <span class="ml-2 text-xs text-gray-500">- <?= htmlspecialchars($role['description'] ?? '') ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="flex gap-2">
                        <?= c::Button("Enregistrer", "fage", "submit") ?>
                        <?= c::Button("Annuler", "gray", "button", "", ["onclick" => "document.getElementById('roleDialog').close()"]) ?>
                    </div>
                </form>
            </div>
        </div>
    </dialog>



    <script>
        function showRoleModal(userId, username, currentRoles) {
            const dialog = document.getElementById('roleDialog');
            document.getElementById('modalUserId').value = userId;
            document.getElementById('modalUsername').textContent = username;

            // Clear all checkboxes
            document.querySelectorAll('input[name="roles[]"]').forEach(cb => cb.checked = false);

            // Check current roles
            if (currentRoles) {
                const roleNames = currentRoles.split(',');
                roleNames.forEach(roleName => {
                    const cleanName = roleName.trim();
                    document.querySelectorAll('input[name="roles[]"]').forEach(cb => {
                        const label = cb.nextElementSibling.textContent;
                        if (label === cleanName) {
                            cb.checked = true;
                        }
                    });
                });
            }

            dialog.showModal();
        }
    </script>

</body>