<?php

namespace App\Domain\Auth;

use App\Infrastructure\Database;
use Firebase\JWT\JWT;
use Exception;

class AuthService
{
    private Database $db;
    private array $jwtSettings;

    public function __construct(Database $db, array $settings)
    {
        $this->db = $db;
        $this->jwtSettings = $settings['jwt'];
    }

    public function register(string $nombre, string $email, string $password): array
    {
        // Verificar si ya existe
        $existing = $this->db->fetchOne("SELECT id FROM usuarios WHERE email = ?", [$email]);
        if ($existing) {
            throw new Exception("El correo electrónico ya está registrado");
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $userId = $this->db->insert(
            "INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (?, ?, ?, 'admin')",
            [$nombre, $email, $hash]
        );

        $user = [
            'id' => (int)$userId,
            'nombre' => $nombre,
            'email' => $email,
            'rol' => 'admin'
        ];

        $token = $this->generateToken($user);
        return ['token' => $token, 'user' => $user];
    }

    public function login(string $email, string $password): array
    {
        $user = $this->db->fetchOne("SELECT * FROM usuarios WHERE email = ? AND activo = 1", [$email]);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new Exception("Credenciales incorrectas");
        }

        $userData = [
            'id' => (int)$user['id'],
            'nombre' => $user['nombre'],
            'email' => $user['email'],
            'rol' => $user['rol']
        ];

        $token = $this->generateToken($userData);
        return ['token' => $token, 'user' => $userData];
    }

    private function generateToken(array $user): string
    {
        $payload = [
            'iss' => 'darkbooks-api',
            'iat' => time(),
            'exp' => time() + $this->jwtSettings['expiration'],
            'sub' => $user['id'],
            'email' => $user['email'],
            'rol' => $user['rol']
        ];

        return JWT::encode($payload, $this->jwtSettings['secret'], $this->jwtSettings['algorithm']);
    }
}
