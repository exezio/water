<?php


namespace Core;


use Core\lib\DataBase;
use MongoDB\Client;
use Valitron\Validator;
use function PHPUnit\Framework\throwException;

abstract class Model
{

    /**
     * @var Client
     */
    protected ?object $mongoClient = null;

    /**Array of errors
     * @var array
     */
    protected static array $errors = [];

    protected ?array $inputData;


    /**
     * Model constructor.
     */
    public function __construct()
    {
        $db = DataBase::instance();
        $this->mongoClient = $db::getClient();
        $this->inputData = $this->filterInput();
    }

    /**Verification of entered data
     * @param string $attributes
     * @param string $rules
     * @return bool
     */
    public function validate(array $attributes, array $rules): bool
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
            'order' => array('filter' => FILTER_DEFAULT,'flag' => FILTER_REQUIRE_ARRAY),
            'delivery_place' => FILTER_SANITIZE_SPECIAL_CHARS
        );
        return filter_var_array($data, $args);
    }

    /**Filling the array with user data
     * @param array $data
     * @param array $attributes Transmitted by link
     * @return void
     */
    public function loadAttributes(array $data, array &$attributes): void
    {
        foreach ($attributes as $item => $value) {
            if (isset($data[$item])) {
                $attributes[$item] = trim($data[$item]);
            }
        }
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

    public static function getUserToken(): string
    {
        $headers = getallheaders();
        return trim(str_replace('Bearer', '', $headers['Authorization']));
    }

}