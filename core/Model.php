<?php


namespace Core;


use App\Models\Calendar;
use Core\lib\DataBase;
use MongoDB\Client;
use Valitron\Validator;
use Browser;

abstract class Model
{

    /**
     * @var Client
     */
    protected ?object $mongoClient = null;

    protected ?object $ordersCollection = null;

    protected ?object $usersCollection = null;

    protected ?object $departmentsCollection = null;

    protected ?object $permissionsCollection = null;

    protected ?object $calendarCollection = null;

    protected ?object $bansCollection = null;

    protected ?object $usersSessionsCollection = null;

    protected string $currentDate;

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
        $this->ordersCollection = $this->mongoClient->water->orders;
        $this->usersCollection = $this->mongoClient->water->users;
        $this->departmentsCollection = $this->mongoClient->water->departments;
        $this->permissionsCollection = $this->mongoClient->water->permissions;
        $this->calendarCollection = $this->mongoClient->water->calendar;
        $this->bansCollection = $this->mongoClient->water->bans;
        $this->usersSessionsCollection = $this->mongoClient->water->users_sessions;
        $this->filterInput();
        $this->currentDate = date('Y-m-d H:i:s');
    }

    /**Verification of entered data
     * @param string $attributes
     * @param string $rules
     * @return bool
     */
    public function validate(array $attributes, array $rules): bool
    {
        Validator::lang('ru');
        Validator::addRule('checkOrder', function ($field, $value, array $params, array $fields){
            if(is_array($value)){
                foreach ($value as $item) {
                    if(!is_array($item)) return false;
                }
                return true;
            }
            return false;

        });
        Validator::addRule('checkRoles', function ($field, $value, array $params, array $fields){
            $allowedRoles = ['Administrator', 'User', 'Moderator'];
            if(is_array($value)){
                return array_diff($value, $allowedRoles) && sizeof($value) ? false : true;
            }
            if(is_string($value)){
                return in_array($value, $allowedRoles);
            }
            return false;
        });
        Validator::addRule('validateMongoId', function ($field, $value, array $params, array $fields){
            return strlen($value) == 24;
        });
        Validator::addRule('validateDate', function ($field, $value, array $params, array $fields){
            if(date('Y', strtotime($value)) != "1970") return true;
        });
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
            'department_code' => FILTER_SANITIZE_SPECIAL_CHARS,
            'delivery_date' => FILTER_SANITIZE_SPECIAL_CHARS,
            'date_end' => FILTER_SANITIZE_SPECIAL_CHARS,
            'date_start' => FILTER_SANITIZE_SPECIAL_CHARS,
            'user_name' => FILTER_SANITIZE_SPECIAL_CHARS,
            'phone' => FILTER_SANITIZE_SPECIAL_CHARS,
            'key' => FILTER_VALIDATE_INT,
            'role' => FILTER_SANITIZE_SPECIAL_CHARS,
            'token' => FILTER_SANITIZE_SPECIAL_CHARS,
            'sign' => FILTER_SANITIZE_SPECIAL_CHARS,
            'order' => [
              'filter' => FILTER_DEFAULT,
              'flags' => FILTER_REQUIRE_ARRAY
            ],
            'delivery_place_code' => FILTER_VALIDATE_INT,
            'delivery_place' => FILTER_SANITIZE_SPECIAL_CHARS,
            'roles' => [
                'filter' => FILTER_DEFAULT,
                'flags' => FILTER_REQUIRE_ARRAY
            ],
            'permission' => FILTER_SANITIZE_SPECIAL_CHARS,
            'new_permission' => FILTER_SANITIZE_SPECIAL_CHARS,
            'id' => FILTER_SANITIZE_SPECIAL_CHARS,
            'cabinet' => FILTER_SANITIZE_SPECIAL_CHARS,
            'responsible' => FILTER_SANITIZE_SPECIAL_CHARS,
            'note' => FILTER_SANITIZE_SPECIAL_CHARS
        );
            $this->inputData = filter_var_array($data, $args);

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
                $attributes[$item] = is_array($data[$item]) ? $data[$item] : trim($data[$item]);
            }
        }
    }

    /**Add error on array of errors
     * @param int $code
     * @param string $message
     * @param string|null $url
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