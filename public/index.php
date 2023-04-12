<?php
// Charge l'autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Dbinitializer;
use App\Config\ExceptionHandlerInitializer;
use App\Crud\MovieCrud;
use App\Crud\UserCrud;
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


if (count($uri) === 2) {
    if ($uri[1] === 'movies') {
        $movieCrud = new MovieCrud($pdo);
        // Affichage de tous les films
        if ($httpMethod === 'GET') {
            echo json_encode($movieCrud->findAll());
        }
        // Création d'un film
        if ($httpMethod === 'POST') {
            try {
                $movieCrud->create($data);
                http_response_code(201);
                $insertedProductId = $pdo->lastInsertId();
                echo json_encode([
                    'Successfully add movie'
                ]);
            } catch (Exception $e) {
                http_response_code(422);
                echo json_encode([
                    'error' => $e->getMessage()
                ]);
                exit;
            }
        }
    }
    if ($uri[1] === 'users') {
        $userCrud = new UserCrud($pdo);
        // Affichage de tous les utilisateurs
        if ($httpMethod === 'GET') {
            echo json_encode($userCrud->findAll());
        }
        // Création d'un film
        if ($httpMethod === 'POST') {
            try {
                $userCrud->create($data);
                http_response_code(201);
                $insertedProductId = $pdo->lastInsertId();
                echo json_encode([
                    'Successfully add user'
                ]);
            } catch (Exception $e) {
                http_response_code(422);
                echo json_encode([
                    'error' => $e->getMessage()
                ]);
                exit;
            }
        }
    }
    exit;
}

// Ressource seule, tupe /movies/{id}
$isItemOperation = count($uri) === 3;


if (!$isItemOperation) {
    http_response_code(404);
    echo json_encode([
        'error' => 'Route non trouvé'
    ]);
}

if ($isItemOperation) {
    if ($uri[1] === 'movies') {
        $movieCrud = new MovieCrud($pdo);
        if ($httpMethod === 'GET') {
            echo json_encode($movieCrud->find($uri[2]));
        }

        if ($httpMethod === 'PUT') {
            try {
                $movieCrud->update($uri[2], $data);
                http_response_code(202);
                echo json_encode([
                    "Successfully updated movie"
                ]);
            } catch (Exception $e) {
                http_response_code(422);
                echo json_encode([
                    'error' => $e->getMessage()
                ]);
            } finally {
                exit;
            }
        }

        if ($httpMethod === 'DELETE') {
            try {
                $movieCrud->delete($uri[2]);
                http_response_code(202);
                echo json_encode([
                    "Successfully delete movie"
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'error' => $e->getMessage()
                ]);
            } finally {
                exit;
            }
        }
    }
    if ($uri[1] === 'users') {
        $userCrud = new UserCrud($pdo);
        if ($httpMethod === 'GET') {
            echo json_encode($userCrud->find($uri[2]));
        }

        if ($httpMethod === 'PUT') {
            try {
                $userCrud->update($uri[2], $data);
                http_response_code(202);
                echo json_encode([
                    "Successfully updated user"
                ]);
            } catch (Exception $e) {
                http_response_code(422);
                echo json_encode([
                    'error' => $e->getMessage()
                ]);
            } finally {
                exit;
            }
        }

        if ($httpMethod === 'DELETE') {
            try {
                $userCrud->delete($uri[2]);
                http_response_code(202);
                echo json_encode([
                    "Successfully delete user"
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'error' => $e->getMessage()
                ]);
            } finally {
                exit;
            }
        }
    }
}
