<?php

use PHPUnit\Framework\TestCase;
use modules\user\controller\UserController;
use modules\user\controller\AuthController;
use modules\user\repository\UserRepository;

class UserControllerTest extends TestCase {
    private UserController $userController;
    private AuthController $authController;
    private string $jwt;

    protected function setUp(): void {
        $this->userController = new UserController();
        $this->authController = new AuthController();
        $repo = new UserRepository();

        // limpiar tablas
        $pdo = (new ReflectionClass($repo))->getProperty('db')->getValue($repo);
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        $pdo->exec("DELETE FROM user_session");
        $pdo->exec("DELETE FROM user");
        $pdo->exec("ALTER TABLE user AUTO_INCREMENT = 1");
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

        // crear usuario admin
        $pdo->prepare("INSERT INTO user (username, email, firstname, password, startdate, timemodified, deleted) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)")
            ->execute([
                'admin',
                'admin@company.com',
                'Admin',
                password_hash('123456', PASSWORD_DEFAULT),
                time(), time(), 0
            ]);

        // login
        $data = ['username' => 'admin', 'password' => '123456'];
        $loginResponse = $this->authController->login($data);
        $this->jwt = $loginResponse['token'];
    }

    public function testCreateUserWithoutToken(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid token');

        $this->userController->create([
            'username' => 'jane',
            'email' => 'jane@company.com',
            'firstname' => 'Jane',
            'password' => 'secret'
        ]);
    }

    public function testCreateUserInvalidEmail(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Email has an invalid format');

        $this->userController->create([
            'token' => $this->jwt,
            'username' => 'jane',
            'email' => 'invalidemail',
            'firstname' => 'Jane',
            'password' => 'secret'
        ]);
    }

    public function testCreateUserNonCompanyEmail(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('We only allow emails with corporative extension');

        $this->userController->create([
            'token' => $this->jwt,
            'username' => 'jane',
            'email' => 'jane@gmail.com',
            'firstname' => 'Jane',
            'password' => 'secret'
        ]);
    }

    public function testCreateUserMissingFields(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("The field 'username' is required");

        $this->userController->create([
            'token' => $this->jwt,
            'email' => 'jane@company.com',
            'firstname' => 'Jane',
            'password' => 'secret'
        ]);
    }


    public function testListUsers(): void {
        $response = $this->userController->list([]);
        $this->assertIsArray($response);
        $this->assertGreaterThanOrEqual(1, count($response));
    }


    public function testGetExistingUser(): void {
        $response = $this->userController->get(['param' => 1]);
        $this->assertEquals('admin', $response->username);
    }

    public function testGetNonExistingUser(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not found');

        $this->userController->get(['param' => 999]);
    }

    public function testGetWithoutParam(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The id is required to fetch an user');

        $this->userController->get([]);
    }

    public function testUpdateWithoutToken(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid token');

        $this->userController->update([
            'param' => 1,
            'firstname' => 'NoToken'
        ]);
    }

    public function testUpdateWithoutParam(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The id is required');

        $this->userController->update([
            'token' => $this->jwt,
            'firstname' => 'NoParam'
        ]);
    }

    public function testDeleteNonExistingUser(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User not found');

        $this->userController->delete([
            'token' => $this->jwt,
            'param' => 999
        ]);
    }

    public function testDeleteWithoutToken(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid token');

        $this->userController->delete([
            'param' => 1
        ]);
    }

    public function testDeleteWithoutParam(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The id is required');

        $this->userController->delete([
            'token' => $this->jwt,
        ]);
    }
}
