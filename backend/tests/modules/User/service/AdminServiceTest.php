<?php

use modules\user\service\AdminService;
use modules\user\repository\AdminRepository;
use PHPUnit\Framework\TestCase;

class AdminServiceTest extends TestCase {
    private AdminService $service;
    private PDO $pdo;

    protected function setUp(): void {
        $this->service = new AdminService();
        $repo = new AdminRepository();

        $ref = new ReflectionClass($repo);
        $dbProp = $ref->getParentClass()->getProperty('db');
        $dbProp->setAccessible(true);
        $this->pdo = $dbProp->getValue($repo);

        $this->pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        $this->pdo->exec("TRUNCATE TABLE admin");
        $this->pdo->exec("TRUNCATE TABLE user");
        $this->pdo->exec("TRUNCATE TABLE app");
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    }

    public function testIsAdminSetReturnsFalseWhenNoAdmins(): void {
        $this->assertFalse($this->service->isAdminSet());
    }

    public function testIsAdminSetReturnsTrueWhenAdminExists(): void {
        $this->pdo->prepare("INSERT INTO user (username, email, firstname, password, startdate, timemodified, deleted) 
                             VALUES (?, ?, ?, ?, ?, ?, 0)")
            ->execute([
                'jdoe',
                'jdoe@company.com',
                'John',
                password_hash("secret", PASSWORD_DEFAULT),
                time(),
                time()
            ]);
        $userId = (int)$this->pdo->lastInsertId();

        $this->pdo->prepare("INSERT INTO admin (userid, active, timemodified, modifiedby, deleted) 
                             VALUES (?, 1, ?, ?, 0)")
            ->execute([$userId, time(), $userId]);

        $this->assertTrue($this->service->isAdminSet());
    }

    public function testAddAdminByUsernameThrowsExceptionIfUserNotFound(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("User 'ghost' not found");

        $this->service->addAdminByUsername('ghost', null);
    }

    public function testAddAdminByUsernameAddsAdminSuccessfully(): void {
        $this->pdo->prepare("INSERT INTO user (username, email, firstname, password, startdate, timemodified, deleted) 
                             VALUES (?, ?, ?, ?, ?, ?, 0)")
            ->execute([
                'alice',
                'alice@company.com',
                'Alice',
                password_hash("pass", PASSWORD_DEFAULT),
                time(),
                time()
            ]);
        $userId = (int)$this->pdo->lastInsertId();

        $result = $this->service->addAdminByUsername('alice', $userId);

        $this->assertIsInt($result);

        $stmt = $this->pdo->query("SELECT * FROM admin WHERE userid = {$userId}");
        $admin = $stmt->fetch();
        $this->assertNotEmpty($admin);
        $this->assertEquals($userId, $admin['userid']);
    }

    public function testRemoveUserByUsernameThrowsExceptionIfUserNotFound(): void {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("User 'ghost' not found");

        $this->service->removeUserByUsername('ghost', null);
    }

    public function testRemoveUserByUsernameRemovesAdminSuccessfully(): void {
        $this->pdo->prepare("INSERT INTO user (username, email, firstname, password, startdate, timemodified, deleted) 
                             VALUES (?, ?, ?, ?, ?, ?, 0)")
            ->execute([
                'bob',
                'bob@company.com',
                'Bob',
                password_hash("pass", PASSWORD_DEFAULT),
                time(),
                time()
            ]);
        $userId = (int) $this->pdo->lastInsertId();

        $this->pdo->prepare("INSERT INTO admin (userid, active, timemodified, modifiedby, deleted) 
                             VALUES (?, 1, ?, ?, 0)")
            ->execute([$userId, time(), $userId]);

        $result = $this->service->removeUserByUsername('bob', $userId);

        $this->assertIsInt($result);

        $stmt = $this->pdo->query("SELECT * FROM admin WHERE userid = {$userId}");
        $admin = $stmt->fetch();
        $this->assertNotEmpty($admin);
        $this->assertEquals(1, $admin['deleted']);
    }

    public function testIsThisUserAdminReturnsTrueIfUserIsAdmin(): void {
        $this->pdo->prepare("INSERT INTO user (username, email, firstname, password, startdate, timemodified, deleted) 
                             VALUES (?, ?, ?, ?, ?, ?, 0)")
            ->execute([
                'carol',
                'carol@company.com',
                'Carol',
                password_hash("pass", PASSWORD_DEFAULT),
                time(),
                time()
            ]);
        $userId = (int)$this->pdo->lastInsertId();

        $this->pdo->prepare("INSERT INTO admin (userid, active, timemodified, modifiedby, deleted) 
                             VALUES (?, 1, ?, ?, 0)")
            ->execute([$userId, time(), $userId]);

        $this->assertTrue($this->service->isThisUserAdmin('carol'));
    }

    public function testIsThisUserAdminReturnsFalseIfUserIsNotAdmin(): void {
        $this->pdo->prepare("INSERT INTO user (username, email, firstname, password, startdate, timemodified, deleted) 
                             VALUES (?, ?, ?, ?, ?, ?, 0)")
            ->execute([
                'dave',
                'dave@company.com',
                'Dave',
                password_hash("pass", PASSWORD_DEFAULT),
                time(),
                time()
            ]);

        $this->assertFalse($this->service->isThisUserAdmin('dave'));
    }

    public function testGetUserByIdReturnsUser(): void {
        $this->pdo->prepare("INSERT INTO user (username, email, firstname, password, startdate, timemodified, deleted) 
                             VALUES (?, ?, ?, ?, ?, ?, 0)")
            ->execute([
                'erin',
                'erin@company.com',
                'Erin',
                password_hash("pass", PASSWORD_DEFAULT),
                time(),
                time()
            ]);
        $userId = (int) $this->pdo->lastInsertId();

        $user = $this->service->getUserById($userId);
        $this->assertNotNull($user);
        $this->assertEquals('erin', $user->username);
    }

    public function testGetUserByIdReturnsNullIfUserDoesNotExist(): void {
        $user = $this->service->getUserById(99999);
        $this->assertNull($user);
    }
}