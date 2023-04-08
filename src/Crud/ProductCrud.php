<?php

namespace App\Crud;

use App\Crud\Exception\UnprocessableEntityException;
use PDO;

class ProductCrud
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
        if (!isset($data['name']) || !isset($data['priceHT']) || !isset($data['description'])) {
            // Gestion d'erreur 
            throw new UnprocessableEntityException("Name and price are required");
        }

        $query = "INSERT INTO products VALUES (null, :product_name, :priceHT, :desc_product)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'product_name' => $data['name'],
            'priceHT' => $data['priceHT'],
            'desc_product' => $data['description']
        ]);
    }

    public function findAll(): array
    {
        $query = "SELECT * FROM products";
        $stmt = $this->pdo->query($query);
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return ($products === false) ? [] : $products;
    }

    public function update(int $id, array $data): bool
    {
        return true;
    }

    public function delete(int $id): bool
    {
        return true;
    }
}
