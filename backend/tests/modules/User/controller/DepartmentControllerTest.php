<?php

use PHPUnit\Framework\TestCase;
use modules\user\controller\DepartmentController;
use modules\user\controller\AuthController;
use modules\user\repository\DepartmentRepository;

class DepartmentControllerTest extends TestCase {
    private DepartmentController $controller;
    private AuthController $authController;
    private string $jwt;

    protected function setUp(): void {
        $this->controller = new DepartmentController();
        $this->authController = new AuthController();
        $repo = new DepartmentRepository();

        $pdo = (new ReflectionClass($repo))->getProperty('db')->getValue($repo);
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        $pdo->exec("DELETE FROM department");
        $pdo->exec("ALTER TABLE department AUTO_INCREMENT = 1");
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        $pdo->exec("DELETE FROM user_session");
        $pdo->exec("DELETE FROM user");
        $pdo->exec("ALTER TABLE user AUTO_INCREMENT = 1");
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

        $pdo->prepare("INSERT INTO user (username, email, firstname, password, startdate, timemodified, deleted) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)")
            ->execute([
                'admin', 'admin@company.com', 'Admin', password_hash('123456', PASSWORD_DEFAULT), time(), time(), 0
            ]);

        $loginResponse = $this->authController->login(['username' => 'admin', 'password' => '123456']);
        $this->jwt = $loginResponse['token'];
    }

    public function testCreateDepartment(): void
    {
        $response = $this->controller->create([
            'token' => $this->jwt,
            'name' => 'HR'
        ]);

        $this->assertEquals('HR', $response->name);
    }

    public function testListDepartments(): void
    {
        $this->controller->create([
            'token' => $this->jwt,
            'name' => 'HR'
        ]);
        $response = $this->controller->list([]);
        $this->assertIsArray($response);
        $this->assertGreaterThanOrEqual(1, count($response));
    }
}
