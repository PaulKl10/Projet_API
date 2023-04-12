<?php

namespace App\Crud;


use Exception;
use PDO;

class UserCrud
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Creates a new product
     *
     * @param array $data name, base price & description (optional)
     * @return boolean Number of affected rows === 1 (=> created)
     * @throws Exception
     */
    public function create(array $data): void
    {
        if (!isset($data['pseudo']) || !isset($data['pass'])) {
            // Gestion d'erreur 
            throw new Exception("pseudo and password are required");
        }

        $query = "INSERT INTO users VALUES (null, :pseudo, :pass)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'pseudo' => $data['pseudo'],
            'pass' => $data['pass']
        ]);
    }

    public function findAll(): array
    {
        $query = "SELECT * FROM users";
        $stmt = $this->pdo->query($query);
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return ($users === false) ? [] : $users;
    }

    public function find($id): array
    {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        $movie = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return ($movie === false) ? [] : $movie;
    }

    public function update(int $id, array $data): void
    {
        if (!isset($data['pseudo']) || !isset($data['pass'])) {
            // Gestion d'erreur 
            throw new Exception("pseudo and password are required");
        }
        $query = "UPDATE users SET pseudo = :pseudo, pass = :pass WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'pseudo' => $data['pseudo'],
            'pass' => $data['pass'],
            'id' => $id
        ]);
    }

    public function delete(int $id): void
    {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'id' => $id
        ]);
        if (empty($stmt->fetchAll())) {
            throw new Exception("This id not exists");
        }

        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'id' => $id
        ]);
    }
}
