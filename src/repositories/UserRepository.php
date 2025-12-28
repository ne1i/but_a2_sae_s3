<?php

namespace ButA2SaeS3\repositories;

use PDO;

class UserRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function hasPermission(int $userId, string $permission): bool
    {
        $stmt = $this->db->prepare("
            SELECT r.permissions 
            FROM users u 
            JOIN user_roles ur ON u.id = ur.user_id 
            JOIN roles r ON ur.role_id = r.id 
            WHERE u.id = :user_id AND r.permissions = 'all'
        ");
        $stmt->execute([":user_id" => $userId]);

        if ($stmt->fetchColumn()) {
            return true;
        }

        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM users u 
            JOIN user_roles ur ON u.id = ur.user_id 
            JOIN roles r ON ur.role_id = r.id 
            WHERE u.id = :user_id AND (r.permissions = :permission OR r.permissions = 'all')
        ");
        $stmt->execute([
            ":user_id" => $userId,
            ":permission" => $permission
        ]);

        return $stmt->fetchColumn() > 0;
    }

    public function existsUsername(string $username): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([":username" => $username]);
        return $stmt->fetchColumn() !== false;
    }

    public function addUser(string $username, string $password, string $role, array $poles = []): bool
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users(username, password_hash) VALUES(:username, :password_hash)");
        $ok = $stmt->execute([
            ":username" => $username,
            ":password_hash" => $hash
        ]);

        if (!$ok) {
            return false;
        }

        if ($role === "admin") {
            $this->addRoleToUser($username, $role);
            return true;
        }

        foreach ($poles as $pole) {
            $this->addRoleToUser($username, $pole);
        }

        return true;
    }

    public function getAllRoles(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM roles ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsers(int $limit = 50, int $page = 1): array
    {
        $offset = ($page - 1) * $limit;
        $stmt = $this->db->prepare("
            SELECT u.*, a.first_name, a.last_name, GROUP_CONCAT(r.name) as roles
            FROM users u
            LEFT JOIN adherents a ON u.adherent_id = a.id
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            GROUP BY u.id
            ORDER BY u.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT u.*, a.first_name, a.last_name, GROUP_CONCAT(r.name) as roles
            FROM users u
            LEFT JOIN adherents a ON u.adherent_id = a.id
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.id = :id
            GROUP BY u.id
        ");
        $stmt->execute([":id" => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updateUserRoles(int $userId, array $roleIds): bool
    {
        $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = :user_id");
        $stmt->execute([":user_id" => $userId]);

        foreach ($roleIds as $role_id) {
            $stmt = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)");
            $stmt->execute([
                ":user_id" => $userId,
                ":role_id" => $role_id
            ]);
        }

        return true;
    }

    public function deleteUser(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = :user_id");
        $stmt->execute([":user_id" => $id]);

        $stmt = $this->db->prepare("DELETE FROM sessions WHERE user_id = :user_id");
        $stmt->execute([":user_id" => $id]);

        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([":id" => $id]);
    }

    public function getAuditLogs(int $limit = 50, int $page = 1): array
    {
        $offset = ($page - 1) * $limit;
        $stmt = $this->db->prepare("
            SELECT al.*, u.username 
            FROM audit_logs al 
            LEFT JOIN users u ON al.performed_by = u.id 
            ORDER BY al.created_at DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function logAudit(string $entity, int|string $entityId, string $action, ?int $performedBy = null, ?string $details = null): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO audit_logs (entity, entity_id, action, performed_by, details) 
            VALUES (:entity, :entity_id, :action, :performed_by, :details)
        ");
        return $stmt->execute([
            ":entity" => $entity,
            ":entity_id" => $entityId,
            ":action" => $action,
            ":performed_by" => $performedBy,
            ":details" => $details
        ]);
    }

    private function addRoleToUser(string $username, string $role): void
    {
        $user_id = $this->getUserIdByUsername($username);
        $role_id = $this->getRoleId($role);
        if ($user_id === null || $role_id === null) {
            return;
        }
        $stmt = $this->db->prepare("INSERT INTO user_roles(user_id, role_id) VALUES(:user_id, :role_id)");
        $stmt->execute([
            ":user_id" => $user_id,
            ":role_id" => $role_id,
        ]);
    }

    public function getUserIdByUsername(string $username): ?int
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute([":username" => $username]);
        $id = $stmt->fetchColumn();
        return $id ? (int)$id : null;
    }

    private function getRoleId(string $role): ?int
    {
        $stmt = $this->db->prepare("SELECT id FROM roles WHERE name = :role");
        $stmt->execute([":role" => $role]);
        $id = $stmt->fetchColumn();
        return $id ? (int)$id : null;
    }
}
