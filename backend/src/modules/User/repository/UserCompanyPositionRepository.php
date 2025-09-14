<?php

namespace modules\user\repository;

use core\BaseRepository;
use modules\user\dto\UserCompanyPositionOutputDTO;
use PDO;

/**
 * Repository for managing user assignments to departments and company positions.
 * Handles creation, revocation, and retrieval of user-company-position relationships.
 * 
 * @package modules\user\repository
 * @author Vitor Carvalho <vitorcarvalhodso@gmail.com>
 */
class UserCompanyPositionRepository extends BaseRepository {

    /**
     * Assign a user to a department and/or company position.
     *
     * @param int $userId
     * @param int|null $departmentId
     * @param int|null $companyPositionId
     * @param int|null $modifiedBy
     * @return int Returns the ID of the newly created assignment
     */
    public function assign(int $userId, ?int $departmentId, ?int $companyPositionId, ?int $modifiedBy): int {
        $sql = "INSERT INTO user_company_position 
                (userid, departmentid, companypositionid, timemodified, modifiedby, deleted)
                VALUES (:userid, :departmentid, :companypositionid, :timemodified, :modifiedby, 0)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':userid' => $userId,
            ':departmentid' => $departmentId,
            ':companypositionid' => $companyPositionId,
            ':timemodified' => time(),
            ':modifiedby'  => $modifiedBy
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Revoke (soft delete) a user assignment by its ID.
     *
     * @param int $id
     * @param int|null $modifiedBy
     * @return int Returns the number of affected rows
     */
    public function revoke(int $id, ?int $modifiedBy): int {
        $sql = "UPDATE user_company_position
                SET deleted = 1,
                    timemodified = :timemodified,
                    modifiedby = :modifiedby
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':timemodified' => time(),
            ':modifiedby' => $modifiedBy,
            ':id' => $id
        ]);

        return $stmt->rowCount();
    }

    /**
     * Get the current assignment of a user.
     *
     * @param int $userId
     * @return UserCompanyPositionOutputDTO|null Returns the assignment or null if none
     */
    public function getAssignmentForUser(int $userId): ?UserCompanyPositionOutputDTO {
        $stmt = $this->db->prepare("
            SELECT *
            FROM user_company_position
            WHERE userid = :userid
              AND deleted = 0
            LIMIT 1
        ");

        $stmt->execute([':userid' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new UserCompanyPositionOutputDTO($row) : null;
    }
}
