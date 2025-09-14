<?php

use PHPUnit\Framework\TestCase;
use modules\app\controller\AppController;
use modules\user\controller\AuthController;
use modules\app\repository\AppRepository;

class AppControllerTest extends TestCase {
    private AppController $appController;
    private AuthController $authController;
    private string $jwt;

    protected function setUp(): void {
        $this->appController = new AppController();
        $this->authController = new AuthController();
        $repo = new AppRepository();

        $pdo = (new ReflectionClass($repo))->getProperty('db')->getValue($repo);
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        $pdo->exec("DELETE FROM app");
        $pdo->exec("ALTER TABLE app AUTO_INCREMENT = 1");
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

        $pdo->exec("DELETE FROM user");
        $pdo->exec("ALTER TABLE user AUTO_INCREMENT = 1");
        $pdo->prepare("
            INSERT INTO user (username, email, firstname, password, startdate, timemodified, deleted) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ")->execute([
            'admin', 'admin@company.com', 'Admin', password_hash('123456', PASSWORD_DEFAULT), time(), time(), 0
        ]);

        $loginResponse = $this->authController->login([
            'username' => 'admin', 
            'password' => '123456'
        ]);

        $this->jwt = $loginResponse['token'];
    }

    public function testCreateApp(): void {
        $response = $this->appController->create([
            'token' => $this->jwt,
            'name'  => 'Slack',
            'url'   => 'https://slack.com',
            'active'=> true
        ]);

        $this->assertEquals('Slack', $response['name']);
        $this->assertEquals('https://slack.com', $response->url);
        $this->assertEquals(1, $response->active);
    }

    public function testCreateAppWithoutToken(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Authentication token is required');

        $this->appController->create([
            'name' => 'Teams',
            'url'  => 'https://teams.com',
            'active'=> true
        ]);
    }

    public function testCreateAppDuplicateName(): void {
        $this->expectException(\Exception::class);

        $this->appController->create([
            'token' => $this->jwt,
            'name'  => 'Slack',
            'url'   => 'https://slack.com',
            'active'=> true
        ]);

        $this->appController->create([
            'token' => $this->jwt,
            'name'  => 'Slack',
            'url'   => 'https://duplicate.com',
            'active'=> true
        ]);
    }

    public function testListApps(): void {
        $response = $this->appController->list(['token' => $this->jwt]);
        $this->assertIsArray($response);
        $this->assertGreaterThanOrEqual(1, count($response));
    }
}
