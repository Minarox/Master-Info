<?php
declare(strict_types = 1);

namespace app;

use BadRequest;
use NotFound;
use OAuth2\GrantType\UserCredentials;
use OAuth2\Storage\UserCredentialsInterface;

/**
 * Custom client storage function for user_credentials (password) login method
 */
class CustomUserStorage extends UserCredentials implements UserCredentialsInterface
{
    /**
     * Check if submitted credentials are correct
     *
     * @param string $username send by the user
     * @param string $password send by the user
     *
     * @return bool true if credentials are correct, false otherwise
     *
     * @throws NotFound|BadRequest
     */
    public function checkUserCredentials($username, $password): bool
    {
        // Search user in database
        $user = (new Database())->find(
            "admins",
            ["password"],
            [
                "email" => $username,
                "active" => 1
            ],
            true,
            null,
            false
        );

        // Return false if user not found
        if (!$user) {
            return false;
        }

        // Return true if password is valid
        return password_verify($password, $user["password"]);
    }

    /**
     * Get user information
     *
     * @param string $username send by the user
     *
     * @return array|false array of user information, false if error or not found
     *
     * @throws NotFound|BadRequest
     */
    public function getUserDetails($username): bool|array
    {
        // Search user id in database
        $user = (new Database())->find(
            "admins",
            ["scope"],
            [
                "email" => $username,
                "active" => 1
            ],
            true,
            null,
            false
        );

        // Return false if user not found
        if (!$user) {
            return false;
        }

        // Return array
        return array(
            "user_id" => $username,
            "scope"   => $user["scope"]
        );
    }
}