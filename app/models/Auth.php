<?php


namespace App\Models;


use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;


use http\Cookie;
use http\Message;
use Valitron\Validator;
use MongoDBRef;

class Auth extends User
{
    /**Filling user-entered data
     * @var array|string[]
     */
    protected array $attributesAuth = [
        'login' => '',
        'password' => ''
    ];

    /**Filling user-entered data
     * @var array|string[]
     */
    protected array $attributesLogin = [
        'login' => ''
    ];

    /**Filling user-entered data
     * @var array|string[]
     */
    protected array $attributesCreatePassword = [
        'login' => '',
        'password' => '',
        'password_confirm' => '',
        'key' => ''
    ];

    /**Rules for validate
     * @var array
     */
    protected array $rulesAuth = [
        'required' => [
            ['login'],
            ['password']
        ],
        'lengthMin' => [
            ['password', 6]
        ]
    ];

    /**Rules for validate
     * @var array|string[][][]
     */
    protected array $rulesLogin = [
        'required' => [
            ['login']
        ]
    ];

    /**Rules for adding a password
     * @var array
     */
    protected array $rulesCreatePassword = [
        'required' => [
            ['login'],
            ['password'],
            ['password_confirm'],
            ['key']
        ],
        'lengthMin' => [
            ['password', 6],
            ['key', 4]
        ]
    ];

    /**Name of collection of database
     * @var string
     */
    private string $collectionName = 'users';

    /**Check user in database
     * @return bool
     */
    public function checkLogin(): void
    {
        if ($this->validateLogin()){
            $login = $this->attributesLogin['login'];
            $db = $this->mongoClient->selectCollection($this->dataBaseName, $this->collectionName);
            $user = $db->findOne(['email' => $login]);

            if(!$user){
                sendResponse(code: 400, data: ['message' => 'Пользователь не найден. Обратитесь к системному администратору']);
            }

            if(!$user['password']){
                $key = $this->generateKey();
                $db->updateOne(
                    ['email' => $login],
                    ['$set' => ['key' => $key]]
                );
//                mail(to: 'dmitryzlo111@gmail.com', subject: 'Ключ доступа', message:"Ваш ключ: {$key}" );
                sendResponse(code: 401, data: ['message' => "Пароль не задан, необходимо задать пароль. Ключ выслан на почтовый ящик {$user['email']}"]);

            }else{
                sendResponse(code: 200);
            }

        }else{
            sendResponse(code: 400, data: ['message' => 'Неверный логин']);
        }
    }

    public function createPassword(): void
    {
        if ($this->validatePassword()){
            $login = $this->attributesCreatePassword['login'];
            $password = $this->attributesCreatePassword['password'];
            $password_confirm = $this->attributesCreatePassword['password_confirm'];
            $key = $this->attributesCreatePassword['key'];
            $db = $this->mongoClient->selectCollection($this->dataBaseName, $this->collectionName);
            $user = $db->findOne(['email' => $login]);

            if(!$user){
                sendResponse(code: 400, data: ['message' => 'Проверьте введенный логин']);
            }

            if($user['password']){
                sendResponse(code: 401, data: ['message' => "Для пользователя {$login} пароль уже задан"]);
            }

            if($key !== $user['key']){
                sendResponse(code: 400, data: ["message" => "Неверно введен ключ, проверьте электронную почту {$login}"]);
            }

            if ($password !== $password_confirm) {
                sendResponse(code: 400, data: ["message" => "Введенные пароли должны совпадать"]);
            }

            else{
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $db->updateOne(
                    ['email' => $login],
                    [
                        '$set' => ['password' => $passwordHash],
                        '$unset' => ['key' => 1]
                    ]
                );
                sendResponse(code: 200);
            }

        }else{
            sendResponse(code: 400, data: ["message" => "Проверьте корректность введенных данных"]);
        }
    }

    /**Check correctness login and password on database
     * @return void
     */
    public function auth(): void
    {

        if($this->validateAuth()){
            $login = $this->attributesAuth['login'];
            $password = $this->attributesAuth['password'];
            $db = $this->mongoClient->selectCollection($this->dataBaseName, $this->collectionName);
            $user = $db->findOne(['email' => $login]);
            if($user){
                if(password_verify($password, $user['password'])){
                    $authData = $this->generateAuthData();
                    $db->updateOne(
                        ['email' => $login],
                        ['$set' => ['token' => [
                            'token_id' => (int) $authData['token_id'],
                            'token' => $authData['token'],
                            'secret' => $authData['secret']
                        ]]]
                    );
                    sendResponse(
                        code: 200,
                        data: [
                            'token' => $authData['token_id'] . ':' . $authData['token'],
                            'secret' => $authData['secret']
                        ]
                    );
                }
            }
        }
            sendResponse(code: 400, data: ["message" => "Неверный логин или пароль"]);
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

    /**Wrapper over a validator
     * @return bool
     */
    public function validateLogin(): bool
    {
        return $this->validator($this->attributesLogin, $this->rulesLogin);
    }

    /**Wrapper over a validator
     * @return bool
     */
    public function validateAuth(): bool
    {
        return $this->validator($this->attributesAuth, $this->rulesAuth);
    }

    /**Wrapper over a validator
     * @return bool
     */
    public function validatePassword(): bool
    {
        return $this->validator($this->attributesCreatePassword, $this->rulesCreatePassword);
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

    /**Wrapper over an attributes loader
     * @param $data
     * @return void
     */
    public function loadAttributesLogin($data): void
    {
        $this->loadAttributes($data, $this->attributesLogin);
    }

    /**Wrapper over an attributes loader
     * @param $data
     * @return void
     */
    public function loadAttributesAuth($data): void
    {
        $this->loadAttributes($data, $this->attributesAuth);
    }

    /**Wrapper over an attributes loader
     * @param $data
     * @return void
     */
    public function loadAttributesCreatePassword($data): void
    {
        $this->loadAttributes($data, $this->attributesCreatePassword);
    }

    private function generateKey()
    {
        $generator = new ComputerPasswordGenerator();
        $generator
            ->setNumbers(true)
            ->setLength(4)
            ->setUppercase(false)
            ->setLowercase(false);
        $key = $generator->generatePassword();
        return $key;
    }

    private function generateAuthData(): array
    {
        $authData = [
            'token_id' => $this->generateKey(),
            'token' => bin2hex(random_bytes(16)),
            'secret' => hash("md5", $this->generateKey())
        ];
        return $authData;
    }

}
