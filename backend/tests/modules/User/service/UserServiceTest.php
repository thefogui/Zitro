<?php

use PHPUnit\Framework\TestCase;
use modules\user\service\UserService;
use modules\user\dto\UserDTO;
use core\Database;

class UserServiceTest extends TestCase {
    private UserService $service;

    protected function setUp(): void {
        $this->service = new UserService();

        $pdo = Database::getInstance()->getConnection();
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $pdo->exec("DELETE FROM user_company_position");
        $pdo->exec("DELETE FROM user");
        $pdo->exec("ALTER TABLE user AUTO_INCREMENT = 1");
        $pdo->exec("ALTER TABLE user_company_position AUTO_INCREMENT = 1");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    }

    public function testCreateUser(): void {
        $dto = new UserDTO(
            null,
            'john_doe',
            'john@company.com',
            'John',
            'Doe',
            'password123'
        );

        $user = $this->service->createUser($dto);

        $this->assertEquals('john_doe', $user->username);
        $this->assertEquals('john@company.com', $user->email);
        $this->assertNotEmpty($user->id);
    }

    public function testUpdateUser(): void {
        $dto = new UserDTO(null, 'jane_doe', 'jane@company.com', 'Jane', 'Doe', 'password123');
        $user = $this->service->createUser($dto);

        $updateDto = new UserDTO(
            $user->id,
            'jane_smith',
            'jane@company.com',
            'Jane',
            'Smith',
            'newpassword'
        );

        $updatedUser = $this->service->updateUser($updateDto);

        $this->assertEquals('jane_smith', $updatedUser->username);
        $this->assertEquals('Smith', $updatedUser->lastname);
    }

    public function testDeleteUser(): void {
        $dto = new UserDTO(null, 'mark_doe', 'mark@company.com', 'Mark', 'Doe', 'password123');
        $user = $this->service->createUser($dto);

        $result = $this->service->deleteUser($user->id, null);
        $this->assertTrue($result);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User not found');
        $this->service->getUser($user->id);
    }

    public function testListAllUsers(): void {
        $dto1 = new UserDTO(null, 'user1', 'user1@company.com', 'User', 'One', 'pass1');
        $dto2 = new UserDTO(null, 'user2', 'user2@company.com', 'User', 'Two', 'pass2');

        $this->service->createUser($dto1);
        $this->service->createUser($dto2);

        $users = $this->service->listAllUsers();
        $this->assertCount(2, $users);
    }
}

