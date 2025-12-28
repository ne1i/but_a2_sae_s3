<?php

use ButA2SaeS3\Constants;
use ButA2SaeS3\FageDB;
use ButA2SaeS3\repositories\UserRepository;
use ButA2SaeS3\services\FormService;
use ButA2SaeS3\services\UserValidationService;
use ButA2SaeS3\utils\HttpUtils;
use ButA2SaeS3\Components as c;

$db = new FageDB();
$userRepository = new UserRepository($db->getConnection());

HttpUtils::ensure_valid_session($db);

$currentUserId = HttpUtils::get_current_user_id($db);
if (!$currentUserId || !$userRepository->hasPermission($currentUserId, 'all')) {
    header('Location: /admin');
    exit;
}

$action = $_POST['action'] ?? null;

if (HttpUtils::isPost() && $action === 'update_user_roles' && isset($_POST['user_id']) && isset($_POST['roles'])) {
    $user_id = (int)$_POST['user_id'];
    $role_ids = array_map('intval', $_POST['roles']);

    if ($userRepository->updateUserRoles($user_id, $role_ids)) {
        FormService::setSuccessMessage("Rôles de l'utilisateur mis à jour avec succès", "user_roles_update");
        $userRepository->logAudit('user', $user_id, 'update_roles', $currentUserId, 'Roles updated: ' . implode(', ', $role_ids));
        header('Location: /securite?success=1&success_form=user_roles_update#users');
    } else {
        FormService::setErrorMessage("Erreur lors de la mise à jour des rôles", "user_roles_update");
        header('Location: /securite#users');
    }
    exit;
}

if (HttpUtils::isPost() && $action === 'delete_user' && isset($_POST['delete_user_id'])) {
    $user_id = (int)$_POST['delete_user_id'];

    if ($user_id == $currentUserId) {
        FormService::setErrorMessage("Vous ne pouvez pas supprimer votre propre compte", "user_delete");
        header('Location: /securite#users');
    } else {
        $user = $userRepository->getUserById($user_id);
        if ($user && $userRepository->deleteUser($user_id)) {
            FormService::setSuccessMessage("L'utilisateur \"{$user['username']}\" a bien été supprimé", "user_delete");
            $userRepository->logAudit('user', $user_id, 'delete', $currentUserId, "User deleted: {$user['username']}");
            header('Location: /securite?success=1&success_form=user_delete#users');
        } else {
            FormService::setErrorMessage("Erreur lors de la suppression de l'utilisateur", "user_delete");
            header('Location: /securite#users');
        }
    }
    exit;
}

if ($action === null || $action === 'create_user') {
    FormService::handleFormSubmission(
        [UserValidationService::class, 'validateCreateUser'],
        function ($dto) use ($userRepository, $currentUserId) {
            if ($userRepository->existsUsername($dto->username)) {
                throw new \Exception("Un compte avec cet identifiant existe déjà");
            }
            $userRepository->addUser($dto->username, $dto->password, $dto->role, $dto->poles);
            $newUserId = $userRepository->getUserIdByUsername($dto->username);
            if ($newUserId !== null) {
                $userRepository->logAudit('user', $newUserId, 'create', $currentUserId, "User created: {$dto->username}");
            }
        },
        "Le compte a été créé avec succès",
        "/securite#create-user",
        "user_create"
    );
}

$createState = FormService::restoreFormData("user_create");
$formData = $createState['data'] ?? [];
$formErrors = $createState['errors'] ?? [];
$createSuccessMessage = FormService::getSuccessMessage("user_create");
$createErrorMessage = FormService::getErrorMessage("user_create");

$rolesUpdateSuccessMessage = FormService::getSuccessMessage("user_roles_update");
$rolesUpdateErrorMessage = FormService::getErrorMessage("user_roles_update");

$deleteUserSuccessMessage = FormService::getSuccessMessage("user_delete");
$deleteUserErrorMessage = FormService::getErrorMessage("user_delete");

$users = $userRepository->getUsers();
$audit_logs = $userRepository->getAuditLogs(50, 1);
$roles = $userRepository->getAllRoles();
?>

<?php require_once __DIR__ . "/../templates/admin_head.php"; ?>

<body class="bg-gradient-to-tl from-fage-300 to-fage-500 min-h-screen">

    <main class="p-2 space-y-8">

        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div class="mb-4">
                <?= c::BackToLink(); ?>
            </div>
            <?= c::Heading2("Créer un nouvel utilisateur", id: "create-user") ?>
            <div>
                <form action="/securite" method="post" class="flex flex-col">
                    <input type="hidden" name="action" value="create_user">
                    <?= c::FormInput("username", "Nom d'utilisateur", "text", $formData['username'] ?? "", true, "mb-4", ["error" => $formErrors['username'] ?? null]) ?>
                    <?= c::FormInput("password", "Mot de passe", "password", "", true, "mb-4", ["error" => $formErrors['password'] ?? null]) ?>
                    <?= c::FormInput("password-confirm", "Confirmer le mot de passe", "password", "", true, "mb-4", ["error" => $formErrors['password-confirm'] ?? null]) ?>

                    <label for="role" class="text-lg">Rôle de l'utilisateur (droits)</label>
                    <?php
                    $role_options = [
                        'admin' => 'Administrateur (tout les droits)',
                        'responsable-pole' => 'Responsable de pôle'
                    ];
                    echo c::FormSelect("role", label: "", options: $role_options, selected: $formData['role'] ?? "", class: "mb-4", attributes: ["id" => "role", "error" => $formErrors['role'] ?? null]);
                    ?>

                    <div id="choix-poles" class="hidden flex-col justify-start gap-2">
                        <label>
                            <input type="checkbox" name="poles[]" value="responsable-benevoles" <?= in_array('responsable-benevoles', $formData['poles'] ?? []) ? 'checked' : '' ?>>
                            Pôle Bénévoles
                        </label>

                        <label>
                            <input type="checkbox" name="poles[]" value="responsable-communication" <?= in_array('responsable-communication', $formData['poles'] ?? []) ? 'checked' : '' ?>>
                            Pôle Communication
                        </label>

                        <label>
                            <input type="checkbox" name="poles[]" value="responsable-partenariats" <?= in_array('responsable-partenariats', $formData['poles'] ?? []) ? 'checked' : '' ?>>
                            Pôle Partenariats
                        </label>

                        <label>
                            <input type="checkbox" name="poles[]" value="responsable-tresorerie" <?= in_array('responsable-tresorerie', $formData['poles'] ?? []) ? 'checked' : '' ?>>
                            Pôle Trésorerie
                        </label>

                        <label>
                            <input type="checkbox" name="poles[]" value="responsable-direction" <?= in_array('responsable-direction', $formData['poles'] ?? []) ? 'checked' : '' ?>>
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


                    <div>
                        <?php if ($createSuccessMessage): ?>
                            <?= c::Message($createSuccessMessage, 'success') ?>
                        <?php endif; ?>
                        <?php if ($createErrorMessage): ?>
                            <?= c::Message($createErrorMessage, 'error') ?>
                        <?php endif; ?>
                        <?php if (!empty($formErrors['_form'] ?? null)): ?>
                            <?= c::Message($formErrors['_form'], 'error') ?>
                        <?php endif; ?>
                    </div>

                    <?= c::Button("Créer le compte", "fage", "submit", "my-4") ?>

                    <?php
                    if (Constants::is_debug()) {
                    ?>
                        <?= c::Button("Autofill (debug)", "fage", "button", "", ["id" => "autofill"]) ?>

                        <script>
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


        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div>
                <?= c::Heading2("Gestion des Utilisateurs", id: "users") ?>
                <div>
                    <?php if ($rolesUpdateSuccessMessage): ?>
                        <?= c::Message($rolesUpdateSuccessMessage, 'success') ?>
                    <?php endif; ?>
                    <?php if ($rolesUpdateErrorMessage): ?>
                        <?= c::Message($rolesUpdateErrorMessage, 'error') ?>
                    <?php endif; ?>
                    <?php if ($deleteUserSuccessMessage): ?>
                        <?= c::Message($deleteUserSuccessMessage, 'success') ?>
                    <?php endif; ?>
                    <?php if ($deleteUserErrorMessage): ?>
                        <?= c::Message($deleteUserErrorMessage, 'error') ?>
                    <?php endif; ?>
                </div>

                <?php if (!empty($users)): ?>
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
                                                    <?= c::Badge(trim($role), 'fage') ?>
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
                <?php else: ?>
                    <p class="text-gray-500 italic">Aucun utilisateur trouvé</p>
                <?php endif; ?>
            </div>
        </div>


        <div class="shadow-lg bg-white p-10 container-padding rounded-2xl">
            <div>
                <?= c::Heading2("Journal d'Audit") ?>

                <?php if (!empty($audit_logs)): ?>
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
                                            <?= c::Badge($log['action'], 'muted', 'text-sm') ?>
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
                <?php else: ?>
                    <p class="text-gray-500 italic">Aucun événement d'audit</p>
                <?php endif; ?>
            </div>
        </div>

    </main>


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


            document.querySelectorAll('input[name="roles[]"]').forEach(cb => cb.checked = false);

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