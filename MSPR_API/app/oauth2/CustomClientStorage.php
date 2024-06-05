<?php
declare(strict_types = 1);

namespace app;

use BadRequest;
use NotFound;
use OAuth2\Storage\ClientCredentialsInterface;

/**
 * Custom client storage function for client_credentials (application) login method
 */
class CustomClientStorage implements ClientCredentialsInterface
{

    /**
     * Check if submitted credentials are correct
     *
     * @param string $client_id     send by the user
     * @param null   $client_secret send by the user
     *
     * @return bool true if credentials are correct, false otherwise
     *
     * @throws NotFound|BadRequest
     */
    public function checkClientCredentials($client_id, $client_secret = null): bool
    {
        // Search client in database
        $client = (new Database())->find(
            "clients",
            ['*'],
            [
                "client_id" => $client_id,
                "client_secret" => $client_secret
            ],
            true,
            null,
            false
        );

        // Return false if client not found
        if (!$client) {
            return false;
        }

        // Return true if credentials are correct
        return true;
    }

    /**
     * Check if the client is a public client
     *
     * @param string $client_id send by the user
     *
     * @return bool true if client is a public client, false otherwise
     */
    public function isPublicClient($client_id): bool
    {
        // Clients are always private
        return false;
    }

    /**
     * Get client information
     *
     * @param string $client_id send by the user
     *
     * @return array|false array of client information, false if error or not found
     *
     * @throws NotFound|BadRequest
     */
    public function getClientDetails($client_id): bool|array
    {
        // Search user in database
        $client = (new Database())->find(
            "clients",
            [
                "scope",
                "user_id"
            ],
            ["client_id" => $client_id],
            true,
            null,
            false
        );

        // Return false if user not found
        if (!$client) {
            return false;
        }

        // Return array
        return array(
            "redirect_uri" => null,
            "user_id"      => $client["user_id"],
            "client_id"    => $client_id,
            "scope"        => $client["scope"]
        );
    }

    /**
     * Get client scope
     *
     * @param string $client_id send by the user
     */
    public function getClientScope($client_id) {}

    /**
     * Check if client as a restricted grant type
     *
     * @param string $client_id  send by the user
     * @param string $grant_type send by the user
     */
    public function checkRestrictedGrantType($client_id, $grant_type) {}
}