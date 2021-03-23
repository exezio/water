<?php


namespace App\Models;


use Core\Model;



class Auth extends Model
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

    /**Name of database
     * @var string
     */
    private string $dataBaseName = 'water';

    /**Check user in database
     * @return bool
     */
    public function checkLogin(): bool
    {
        $this->loadAttributes($this->inputData, $this->attributesLogin);
        if ($this->validate($this->attributesLogin, $this->rulesLogin)){
            $login = $this->attributesLogin['login'];
            $db = $this->mongoClient->selectCollection(DB_NAME, DB_COLLECTION_USERS);

            $user = $db->findOne(['email' => $login]);

            if(!$user){
                self::addError(code: 400, message: 'Пользователь не найден. Обратитесь к системному администратору');
                return false;
            }

            if(!$user['password']){
                $key = generateKey();
                $db->updateOne(
                    ['email' => $login],
                    ['$set' => ['key' => $key]]
                );
//                mail(to: 'dmitryzlo111@gmail.com', subject: 'Ключ доступа', message:"Ваш ключ: {$key}" );
                self::addError(code: 401, message: "Пароль не задан, необходимо задать пароль. Ключ выслан на почтовый ящик {$user['email']}");
                return false;
            }else{
                return true;
            }

        }else{
            self::addError(code: 400, message: 'Проверьте корректность введенных данных');
            return false;
        }
    }

    public function createPassword(): bool
    {
        $this->loadAttributes($this->inputData, $this->attributesCreatePassword);
        if ($this->validate($this->attributesCreatePassword, $this->rulesCreatePassword)){
            $login = $this->attributesCreatePassword['login'];
            $password = $this->attributesCreatePassword['password'];
            $password_confirm = $this->attributesCreatePassword['password_confirm'];
            $key = $this->attributesCreatePassword['key'];
            $db = $this->mongoClient->selectCollection(DB_NAME, DB_COLLECTION_USERS);
            $user = $db->findOne(['email' => $login]);

            if(!$user){
                self::addError(code: 400, message: 'Проверьте введенный логин');
                return false;
            }

            if($user['password']){
                self::addError(code: 400, message: "Для пользователя {$login} пароль уже задан");
                return false;
            }

            if($key !== $user['key']){
                self::addError(code: 400, message: "Неверно введен ключ, проверьте электронную почту {$login}");
                return false;
            }

            if ($password !== $password_confirm) {
                self::addError(code: 400, message: "Введенные пароли должны совпадать");
                return false;
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
                return true;
            }

        }else{
            self::addError(code: 400, message: 'Проверьте корректность введенных данных');
            return false;
        }
    }

    /**Check correctness login and password on database
     * @return void
     */
    public function auth(): array | bool
    {
        $this->loadAttributes($this->inputData, $this->attributesAuth);
        if($this->validate($this->attributesAuth, $this->rulesAuth)){
            $login = $this->attributesAuth['login'];
            $password = $this->attributesAuth['password'];
            $db = $this->mongoClient->selectCollection(DB_NAME, DB_COLLECTION_USERS);
            $user = $db->findOne(['email' => $login]);
            if($user){
                if(password_verify($password, $user['password'])){
                    $authData = $this->generateAuthData();
                    $db->updateOne(
                        ['email' => $login],
                        ['$set' => [
                            'token' => $authData['token'],
                            'secret' => $authData['secret']
                        ]]
                    );
                    return $responseData = [
                         'token' => $authData['token'],
                         'secret' => $authData['secret']
                     ];
                }
            }
        }
        self::addError(code: 400, message: 'Неверный логин или пароль');
        return false;
    }

//    /**Wrapper over a validator
//     * @return bool
//     */
//    public function validateLogin(): bool
//    {
//        return $this->validator($this->attributesLogin, $this->rulesLogin);
//    }

//    /**Wrapper over a validator
//     * @return bool
//     */
//    public function validateAuth(): bool
//    {
//        return $this->validator($this->attributesAuth, $this->rulesAuth);
//    }

//    /**Wrapper over a validator
//     * @return bool
//     */
//    public function validatePassword(): bool
//    {
//        return $this->validator($this->attributesCreatePassword, $this->rulesCreatePassword);
//    }

//    /**Wrapper over an attributes loader
//     * @param $data
//     * @return void
//     */
//    public function loadAttributesLogin($data): void
//    {
//        $this->loadAttributes($data, $this->attributesLogin);
//    }

//    /**Wrapper over an attributes loader
//     * @param $data
//     * @return void
//     */
//    public function loadAttributesAuth($data): void
//    {
//        $this->loadAttributes($data, $this->attributesAuth);
//    }

//    /**Wrapper over an attributes loader
//     * @param $data
//     * @return void
//     */
//    public function loadAttributesCreatePassword($data): void
//    {
//        $this->loadAttributes($data, $this->attributesCreatePassword);
//    }


    private function generateAuthData(): array
    {
        $authData = [
            'token' => bin2hex(random_bytes(16)),
            'secret' => hash("md5", generateKey())
        ];
        return $authData;
    }

}
