<?php
/**
 * Copyright © Overdose Digital. All rights reserved.
 * See LICENSE_OVERDOSE.txt for license details.
 */

namespace Overdose\CustomerPasswordReHash\Model;

class PasswordVerifier
{
    /**
     * Check if hash is Bcrypt algorithm
     *
     * @param string $hash
     * @return bool
     */
    public function isBcrypt(string $hash): bool
    {
        if (stripos($hash, '$2y$') === 0) {
            return true;
        }
        return false;
    }

    /**
     * Verify password
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyBcrypt(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Migrated from m1 enterprise password has :1 in the end of word
     *
     * @param string $hash
     * @return bool
     */
    public function isSha256(string $hash): bool
    {
        if (isset($hash)) {
            $explodedPasswordHash = explode(':', $hash);
            return count($explodedPasswordHash) == 2;
        }

        return false;
    }

    /**
     * Code from m1 enterprise
     *
     * @param string $hash
     * @param string $password
     * @return bool
     */
    public function verifySha256(string $hash, string $password): bool
    {
        return $this->validateHashByVersion($password, $hash);
    }

    /**
     * Code from m1 enterprise
     * Validate hash by specified version
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function validateHashByVersion(string $password, string $hash): bool
    {
        $result = false;

        $hashArr = explode(':', $hash);

        switch (count($hashArr)) {
            case 1:
                $result = $this->hashEquals($this->hash($password), $hash);
                break;
            case 2:
                [$hash, $salt] = $hashArr;
                $result = $this->hashEquals($this->hash($salt . $password), $hash);
        }

        return $result;
    }

    /**
     *
     * Code from m1 enterprise
     * Hash a string
     *
     * @param string $data
     * @return string
     */
    public function hash(string $data): string
    {
        return hash('sha256', $data);
    }

    /**
     * Code from m1 enterprise
     * Compares two strings using the same time whether they're equal or not.
     * A difference in length will leak
     *
     * @param string $knownString
     * @param string $userString
     * @return bool Returns true when the two strings are equal, false otherwise.
     */
    public function hashEquals(string $knownString, string $userString): bool
    {
        $result = 0;

        if (!is_string($knownString)) {
            trigger_error("hash_equals(): Expected known_string to be a string", E_USER_WARNING);
            return false;
        }

        if (!is_string($userString)) {
            trigger_error("hash_equals(): Expected user_string to be a string", E_USER_WARNING);
            return false;
        }

        if (strlen($knownString) != strlen($userString)) {
            return false;
        }

        for ($i = 0; $i < strlen($knownString); $i++) {
            $result |= (ord($knownString[$i]) ^ ord($userString[$i]));
        }

        return 0 === $result;
    }
}
