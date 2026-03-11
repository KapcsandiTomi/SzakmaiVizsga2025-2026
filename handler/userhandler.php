<?php
require_once 'userdata.php';

class UserHandler {
    private PDO $conn;
    private ?UserData $user = null;

    public function __construct(PDO $connection) {
        $this->conn = $connection;
    }

    // ====================
    // REGISZTRÁCIÓ
    // ====================
    public function register(UserData $userData): bool {
        if (
            empty($userData->getName()) ||
            empty($userData->getEmail()) ||
            empty($userData->getPassword())
        ) {
            throw new Exception('All fields are required.');
        }

        if ($this->emailExists($userData->getEmail())) {
            throw new Exception('This email is already registered.');
        }

        $userData->hashPassword();

        $stmt = $this->conn->prepare(
            "INSERT INTO `4` 
            (name, email, password, failed_attempts, last_failed_login, is_admin)
            VALUES (:name, :email, :password, 0, NULL, 0)"
        );

        $stmt->execute([
            'name'     => $userData->getName(),
            'email'    => $userData->getEmail(),
            'password' => $userData->getPassword()
        ]);

        $this->user = new UserData([
            'id' => $this->conn->lastInsertId(),
            'name' => $userData->getName(),
            'email' => $userData->getEmail(),
            'failed_attempts' => 0,
            'last_failed_login' => null,
            'is_admin' => 0
        ]);

        return true;
    }

    // ====================
    // BEJELENTKEZÉS
    // ====================
    public function login(string $email, string $password): bool {
        $stmt = $this->conn->prepare(
            "SELECT * FROM `4` WHERE email = :email"
        );
        $stmt->execute(['email' => $email]);

        $data = $stmt->fetch();
        if (!$data) {
            usleep(300000);
            throw new Exception('Incorrect email or password.');
        }

        $this->user = new UserData($data);

        if ($this->user->isLockedOut()) {
            $minutesLeft = $this->user->getLockoutTimeLeft();
            throw new Exception(
                "Too many failed login attempts. Try again in $minutesLeft minute(s)."
            );
        }

        if ($this->user->verifyPassword($password)) {
            $this->resetFailedAttempts($this->user->getId());
            return true;
        }

        $this->incrementFailedAttempts($this->user->getId());
        throw new Exception('Incorrect email or password.');
    }

    // ====================
    // JELSZÓ VISSZAÁLLÍTÁS
    // ====================
    public function resetPassword(string $email, string $newPassword): bool {
        if (!$this->emailExists($email)) {
            throw new Exception('This email is not registered.');
        }

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare(
            "UPDATE `4`
             SET password = :password,
                 failed_attempts = 0,
                 last_failed_login = NULL
             WHERE email = :email"
        );

        $stmt->execute([
            'password' => $hash,
            'email'    => $email
        ]);

        return true;
    }

    // ====================
    // SEGÉD METÓDUSOK
    // ====================
    public function emailExists(string $email): bool {
        $stmt = $this->conn->prepare(
            "SELECT 1 FROM `4` WHERE email = :email"
        );
        $stmt->execute(['email' => $email]);
        return $stmt->fetchColumn() !== false;
    }

    private function incrementFailedAttempts(int $userId): void {
        $stmt = $this->conn->prepare(
            "UPDATE `4`
             SET failed_attempts = failed_attempts + 1,
                 last_failed_login = NOW()
             WHERE id = :id"
        );
        $stmt->execute(['id' => $userId]);
    }

    private function resetFailedAttempts(int $userId): void {
        $stmt = $this->conn->prepare(
            "UPDATE `4`
             SET failed_attempts = 0,
                 last_failed_login = NULL
             WHERE id = :id"
        );
        $stmt->execute(['id' => $userId]);
    }

    public function getUserByEmail(string $email): ?UserData {
        $stmt = $this->conn->prepare(
            "SELECT * FROM `4` WHERE email = :email"
        );
        $stmt->execute(['email' => $email]);

        $data = $stmt->fetch();
        return $data ? new UserData($data) : null;
    }

    public function getCurrentUser(): ?UserData {
        return $this->user;
    }

    public function getUserCount(): int {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM `4`");
        return (int)$stmt->fetchColumn();
    }
}
