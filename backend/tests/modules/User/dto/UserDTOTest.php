<?php

use PHPUnit\Framework\TestCase;
use modules\user\dto\UserDTO;

class UserDTOTest extends TestCase {
    public function testCanCreateDTO() {
        $dto = new UserDTO(
            1,
            'john_doe',
            'john@company.com',
            'John',
            'Doe',
            'password123',
            2
        );

        $this->assertEquals(1, $dto->id);
        $this->assertEquals('john_doe', $dto->username);
        $this->assertEquals('john@company.com', $dto->email);
        $this->assertEquals('John', $dto->firstname);
        $this->assertEquals('Doe', $dto->lastname);
        $this->assertEquals('password123', $dto->password);
        $this->assertEquals(2, $dto->modifiedBy);
    }
}
