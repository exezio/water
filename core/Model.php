<?php


namespace Core;


use Core\lib\DataBase;
use MongoDB\Client;
use Valitron\Validator;

abstract class Model
{

    /**
     * @var Client
     */
    protected static object $mongoClient;

    /**Array of errors
     * @var array
     */
    protected static array $errors = [];


    /**
     * Model constructor.
     */
    public function __construct()
    {
        $db = DataBase::instance();
        self::$mongoClient = $db->connect();
    }

    /**Verification of entered data
     * @param string $attributes
     * @param string $rules
     * @return bool
     */
    public function validator(array $attributes, array $rules): bool
    {
        Validator::lang('ru');
        $validator = new Validator($attributes);
        $validator->rules($rules);
        return $validator->validate();
    }

    public function filterInput()
    {
        $data = json_decode(file_get_contents('php://input'), JSON_FORCE_OBJECT);
        $args = array(
            'login' => FILTER_VALIDATE_EMAIL,
            'password' => FILTER_SANITIZE_SPECIAL_CHARS,
            'password_confirm' => FILTER_SANITIZE_SPECIAL_CHARS,
            'key' => FILTER_VALIDATE_INT,
            'token' => FILTER_SANITIZE_SPECIAL_CHARS,
            'sign' => FILTER_SANITIZE_SPECIAL_CHARS,
            'id_position' => FILTER_VALIDATE_INT,
            'count' => FILTER_VALIDATE_INT
        );
        return filter_var_array($data, $args);
    }

    /**Add error on array of errors
     * @param int $code
     * @param string $message
     */
    public static function addError(int $code, string $message): void
    {
        array_push(self::$errors, compact('code', 'message'));
    }

    /**Returns an array of errors
     * @return string
     */
    public static function getError(): string
    {
        $error = array_shift(self::$errors);
        http_response_code($error['code']);
        echo json_encode($error, JSON_UNESCAPED_UNICODE);
        exit();
    }

}