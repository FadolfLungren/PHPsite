<?php

declare(strict_types=1);
namespace App\Controllers;



use App\View;
use PDO;

class HomeController
{
    public function index():View
    {
        var_dump($_ENV['DB_HOST']);
        try {
            $db = new PDO('mysql:host=' .$_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_DATABASE'],
                $_ENV['DB_USER'],
                $_ENV['DB_PASS'],[
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
                ]);


        }catch (\PDOException $e){
            throw new \PDOException($e->getMessage(), $e->getCode());
        }

        $email  = "jo@doe.com";
        $name = 'Jo Doe';
        $amount = 25;
        try {

            $db->beginTransaction();
            $newUserStmt = $db->prepare('INSERT INTO users (email, ful_name, is_active, created_at)
                        VALUES (?,?,1,NOW())');

            $newInvoiceStmt = $db->prepare('INSERT INTO invoices (amount, user_id) 
                            VALUES (?,?)');
            $newUserStmt->execute([$email, $name]);


            $userId = (int) $db->lastInsertId();
            $newInvoiceStmt->execute([$amount, $userId]);

            $db->commit();
        }catch(\Throwable $e) {
            if($db->inTransaction()) {
                $db->rollBack();

            }
            throw $e;
        }

        $fetchStmt = $db->prepare(
            'SELECT invoices.id AS invoice_id ,amount, user_id, ful_name
                    FROM invoices
                    INNER JOIN users ON user_id = users.id
                    WHERE user_id = ?'
        );
        $fetchStmt->execute([$userId]);

        var_dump($fetchStmt->fetch(PDO::FETCH_ASSOC));
        return View::make('index');
    }
}