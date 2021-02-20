<?php


namespace App\Models;


use Core\Model;
use MongoDB\Exception\Exception;

class CreateUser extends Model
{
    private string $user_collection = 'users';
    private string $department_collection = 'departments';



    public function createUser(array $userData)
    {
        try {
            $db = $this->mongoClient;
            $collection_departments = $db->selectCollection($this->dataBaseName, $this->department_collection);
            $department = $collection_departments->findOne(['department_code' => $userData['department']]);
            $query = [
                'user_name' => $userData['username'],
                'email' => $userData['login'],
                'phone' => $userData['phone'],
                'password' => '',
                'department' => $department
            ];
            $collection_users = $db->selectCollection($this->dataBaseName, $this->user_collection);
            $user = $collection_users->insertOne($query);
            http_response_code(200);
        }catch (Exception $error)
        {
            http_response_code(500);
            echo json_encode(array(
                "error" => array(
                    "code" => 500,
                    "message" => $error->getMessage(),
                    "error_code" => 1
                )
            ), JSON_UNESCAPED_UNICODE);
        }

    }
}