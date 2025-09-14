<?php

use PHPUnit\Framework\TestCase;
use modules\user\controller\AuthController;
use modules\user\repository\UserRepository;

class AuthControllerTest extends TestCase {
    private AuthController $authController;
    private UserRepository $repo;

    protected function setUp(): void {
        $this->authController = new AuthController();
        $this->repo = new UserRepository();

        $pdo = (new \ReflectionClass($this->repo))->getProperty('db')->getValue($this->repo);
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        $pdo->exec("DELETE FROM user_session");
        $pdo->exec("DELETE FROM user");
        $pdo->exec("ALTER TABLE user AUTO_INCREMENT = 1");
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

        $pdo->prepare("INSERT INTO user (username, email, firstname, password, startdate, timemodified, deleted) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)")
            ->execute([
                'admin',
                'admin@company.com',
                'Admin',
                password_hash('123456', PASSWORD_DEFAULT),
                time(), time(), 0
            ]);
    }

    public function testLoginAndLogout(): void {
        // login
        $data = [
            'username' => 'admin',
            'password' => '123456',
        ];
        $loginData = $this->authController->login($data);
        $this->assertArrayHasKey('token', $loginData);
        $this->assertArrayHasKey('user', $loginData);

        // logout
        $result = $this->authController->logout($loginData['token']);
        $this->assertTrue($result);

        $this->expectException(Exception::class);
        $this->authController->logout($loginData['token']);
    }
}
