<?php

namespace App\Crud;


use Exception;
use PDO;

class MovieCrud
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
        if (!isset($data['title']) || !isset($data['note'])) {
            // Gestion d'erreur 
            throw new Exception("Title and note are required");
        }

        $query = "INSERT INTO movies VALUES (null, :title, :note)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'title' => $data['title'],
            'note' => intVal($data['note'])
        ]);
    }

    public function findAll(): array
    {
        $query = "SELECT * FROM movies";
        $stmt = $this->pdo->query($query);
        $movies = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return ($movies === false) ? [] : $movies;
    }

    public function find($id): array
    {
        $query = "SELECT * FROM movies WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        $movie = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return ($movie === false) ? [] : $movie;
    }

    public function update(int $id, array $data): void
    {
        if (!isset($data['title']) || !isset($data['note'])) {
            // Gestion d'erreur 
            throw new Exception("Title and note are required");
        }
        $query = "UPDATE movies SET title = :title, note = :note WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'title' => $data['title'],
            'note' => $data['note'],
            'id' => $id
        ]);
    }

    public function delete(int $id): void
    {
        $query = "SELECT * FROM movies WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'id' => $id
        ]);
        if (empty($stmt->fetchAll())) {
            throw new Exception("This id not exists");
        }

        $query = "DELETE FROM movies WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'id' => $id
        ]);
    }
}
