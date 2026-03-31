<?php

class ProfileData {
    private PDO $conn;
    private ?array $user = null;

    public function __construct(PDO $database_connection, ?int $user_id = null) {
        $this->conn = $database_connection;
        if ($user_id) {
            $this->loadUser($user_id);
        }
    }

    public function loadUser(int $user_id): ?array {
        $stmt = $this->conn->prepare(
            "SELECT id, name, email, profile_pic
             FROM `4`
             WHERE id = :id"
        );
        $stmt->execute(['id' => $user_id]);

        $this->user = $stmt->fetch();
        return $this->user ?: null;
    }

    public function loadUserByEmail(string $email): ?array {
        $stmt = $this->conn->prepare(
            "SELECT id, name, email, profile_pic
             FROM `4`
             WHERE email = :email"
        );
        $stmt->execute(['email' => $email]);

        $this->user = $stmt->fetch();
        return $this->user ?: null;
    }

    public function getUser(): ?array {
        return $this->user;
    }

    public function getUserFavorites(int $user_id): array {
        $stmt = $this->conn->prepare(
            "SELECT *
             FROM favorites
             WHERE user_id = :user_id
             ORDER BY created_at DESC"
        );
        $stmt->execute(['user_id' => $user_id]);

        return $stmt->fetchAll();
    }

    public function updateProfile(int $user_id, string $name, string $email): bool {
        $stmt = $this->conn->prepare(
            "UPDATE `4`
             SET name = :name, email = :email
             WHERE id = :id"
        );

        return $stmt->execute([
            'name'  => $name,
            'email' => $email,
            'id'    => $user_id
        ]);
    }

    public function checkEmailExists(string $email, int $user_id): bool {
        $stmt = $this->conn->prepare(
            "SELECT 1
             FROM `4`
             WHERE email = :email AND id != :id"
        );
        $stmt->execute([
            'email' => $email,
            'id'    => $user_id
        ]);

        return $stmt->fetchColumn() !== false;
    }

    public function changePassword(int $user_id, string $new_password): bool {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare(
            "UPDATE `4`
             SET password = :password
             WHERE id = :id"
        );

        return $stmt->execute([
            'password' => $hash,
            'id'       => $user_id
        ]);
    }

    public function getCurrentPassword(int $user_id): ?string {
        $stmt = $this->conn->prepare(
            "SELECT password FROM `4` WHERE id = :id"
        );
        $stmt->execute(['id' => $user_id]);

        return $stmt->fetchColumn() ?: null;
    }

    public function updateProfilePicture(int $user_id, string $profile_pic_path): bool {
        $stmt = $this->conn->prepare(
            "UPDATE `4`
             SET profile_pic = :profile_pic
             WHERE id = :id"
        );

        return $stmt->execute([
            'profile_pic' => $profile_pic_path,
            'id'          => $user_id
        ]);
    }

    public function deleteOldProfilePicture(?string $profile_pic_path): bool {
        if (
            $profile_pic_path &&
            file_exists($profile_pic_path) &&
            str_starts_with($profile_pic_path, 'uploads/')
        ) {
            return unlink($profile_pic_path);
        }
        return false;
    }
}
