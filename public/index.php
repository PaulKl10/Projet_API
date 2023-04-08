<?php
// Charge l'autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Dbinitializer;
use App\Config\ExceptionHandlerInitializer;
use App\Crud\Exception\UnprocessableEntityException;
use App\Crud\ProductCrud;
use Symfony\Component\Dotenv\Dotenv;

header("Content-Type: application/json");

$dotenv = new Dotenv();
$dotenv->loadEnv('.env');

ExceptionHandlerInitializer::registerGlobalExceptionHandler();
$pdo = Dbinitializer::getPdoInstance();

$uri = $_SERVER['REQUEST_URI'];
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = explode("/", $uri);
$data = json_decode(file_get_contents("php://input"), true);
$productsCrud = new ProductCrud($pdo);


if (count($uri) === 2) {
    if ($uri[1] === 'products') {
        // Affichage de tous les products
        if ($httpMethod === 'GET') {
            echo json_encode($productsCrud->findAll());
        }
        // Création d'un produit
        if ($httpMethod === 'POST') {
            try {
                $productsCrud->create($data);
                http_response_code(201);
                $insertedProductId = $pdo->lastInsertId();
                echo json_encode([
                    'uri' => '/products/' . $insertedProductId
                ]);
            } catch (UnprocessableEntityException $e) {
                http_response_code(422);
                echo json_encode([
                    'error' => $e->getMessage()
                ]);
                exit;
            } finally {
                exit;
            }
        }
    }
    exit;
}

// Ressource seule, tupe /products/{id}
$isItemOperation = count($uri) === 3;


if (!$isItemOperation) {
    http_response_code(404);
    echo json_encode([
        'error' => 'Route non trouvé'
    ]);
}

if ($isItemOperation) {
    if ($uri[1] === 'products') {
        if ($httpMethod === 'GET') {
            $query = "SELECT * FROM products WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'id' => $uri[2]
            ]);
            echo json_encode($stmt->fetchAll(\PDO::FETCH_ASSOC));
        }

        if ($httpMethod === 'PUT') {
            $query = "UPDATE products SET name = :product_name, priceHT = :priceHT, description = :desc_product WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'product_name' => $data['name'],
                'priceHT' => $data['priceHT'],
                'desc_product' => $data['description'],
                'id' => $uri[2]
            ]);
            echo "success update products";
            http_response_code(204);
        }

        if ($httpMethod === 'DELETE') {
            $query = "DELETE FROM products WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'id' => $uri[2]
            ]);
            echo "success delete products";
        }
    }
}
