<?php


namespace App\Models;


use Core\Model;
use Core\lib\HelperGenerateAuthData;
use Browser;


class Auth extends Model
{

    use HelperGenerateAuthData;

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
            ['password', 6]
        ]
    ];


    /**Check user in database
     * @return bool
     */
    public function checkLogin(): bool
    {
        $this->loadAttributes($this->inputData, $this->attributesLogin);
        if ($this->validate($this->attributesLogin, $this->rulesLogin)) {
            $login = $this->attributesLogin['login'];
            $db = $this->usersCollection;
            $user = $db->findOne(['login' => $login], ['collation' => ['locale' => 'en', 'strength' => 1]]);
            if (!$user) {
                Security::addFailAuthAttemptions(action: 'Логин');
                self::addError(code: 400, message: 'Пользователь не найден. Обратитесь к системному администратору');
                return false;
            }
            if (!isset($user['password'])) {
                $key = generateKey();
                $hashedKey = password_hash($key, PASSWORD_DEFAULT);
                $db->updateOne(
                    ['login' => $login],
                    ['$set' => ['key' => $hashedKey]],
                    ['collation' => ['locale' => 'en', 'strength' => 1]]
                );
                Security::clearFailAttemptions();
                mail(to: 'dmitriy.golubev@uralchem.com', subject: 'Ключ доступа', message: "Ваш ключ: {$key}");
                self::addError(code: 401, message:
                    "Пароль не задан, необходимо задать пароль. Ключ выслан на почтовый ящик {$user['login']}");
                return false;
            }else{
                Security::clearFailAttemptions();
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
        if ($this->validate($this->attributesCreatePassword, $this->rulesCreatePassword)) {
            $login = $this->attributesCreatePassword['login'];
            $password = $this->attributesCreatePassword['password'];
            $password_confirm = $this->attributesCreatePassword['password_confirm'];
            $key = $this->attributesCreatePassword['key'];
            $db = $this->usersCollection;
            $user = $db->findOne(['login' => $login], ['collation' => ['locale' => 'en', 'strength' => 1]]) ?: null;
            if (!$user) {
                Security::addFailAuthAttemptions(action: 'Логин');
                self::addError(code: 400, message: 'Проверьте введенный логин');
                return false;
            }

            if (isset($user['password'])) {
                self::addError(code: 400, message: "Для пользователя {$user['user_name']} пароль уже задан");
                return false;
            }

            if (!isset($user['key'])) {
                self::addError(code: 400, message: 'Необходимо пройти проверку логина');
                return false;
            }

            if (!password_verify($key, $user['key'])) {
                Security::addFailAuthAttemptions(action: 'Ключ');
                self::addError(code: 400, message: "Неверно введен ключ, проверьте электронную почту {$login}");
                return false;
            }

            if ($password !== $password_confirm) {
                self::addError(code: 400, message: "Введенные пароли должны совпадать");
                return false;
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $db->updateOne(
                    ['login' => $login],
                    [
                        '$set' => ['password' => $passwordHash],
                        '$unset' => ['key' => 1]
                    ],
                    ['collation' => ['locale' => 'en', 'strength' => 1]]
                );
                Security::clearFailAttemptions();
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
        if ($this->validate($this->attributesAuth, $this->rulesAuth)) {
            $login = $this->attributesAuth['login'];
            $password = $this->attributesAuth['password'];
            $db = $this->usersCollection;
            $user = $db->findOne(['login' => $login], ['collation' => ['locale' => 'en', 'strength' => 1]]);
            if ($user) {
                if (!isset($user['password'])) {
                    self::addError(code: 400, message: 'Необходимо пройти проверку логина');
                    return false;
                }
                if (password_verify($password, $user['password'])) {
                    $sessionData = $this->generateSessionData();
                    $userSessions = $this->usersSessionsCollection->findOne(['user_id' => $user['_id']]);
                    if ($userSessions) {
                        $this->usersSessionsCollection->updateOne(
                            ['user_id' => $user['_id']],
                            [
                                '$set' => [
                                    $sessionData['platform'] => $sessionData['sessionData']
                                ]
                            ]
                        );
                    }else{
                        $this->usersSessionsCollection->insertOne([
                            'user_id' => $user['_id'],
                            'user_login' => $user['login'],
                            $sessionData['platform'] => $sessionData['sessionData']
                        ]);
                    }

                    Security::clearFailAttemptions();
                    return $sessionData['userSessionData'];
                }
            }
            Security::addFailAuthAttemptions('логин или пароль');
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




}
