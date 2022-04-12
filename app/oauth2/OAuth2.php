<?php
declare(strict_types = 1);

namespace app;

require_once __DIR__ . "/CustomUserStorage.php";
require_once __DIR__ . "/CustomClientStorage.php";

use OAuth2\GrantType\ClientCredentials;
use OAuth2\GrantType\RefreshToken;
use OAuth2\GrantType\UserCredentials;
use OAuth2\Server;
use OAuth2\Storage\Pdo;

/**
 * Add OAuth2 protocol to secure API functions
 */
class OAuth2
{
    /**
     * @var Server
     */
    private Server $server;

    /**
     * Construct new OAuth2 authorization protocol
     */
    public function __construct()
    {
        // Import storage
        $storage = new Pdo(
            (new Database())->getPdo(),
            array(
                "client_table" => "clients",
                "user_table" => "admins",
                "access_token_table" => "tokens",
                "refresh_token_table" => "refresh_tokens",
                "scope_table" => "scopes"
            )
        );

        // Create Oauth2
        $this->server = new Server(
            $storage,
            array(
                "access_lifetime"                => 3600,
                "always_issue_new_refresh_token" => true,
                "refresh_token_lifetime"         => 2419200
            ));

        // Add custom classes
        $this->server->addGrantType(new UserCredentials(new CustomUserStorage($storage)));
        $this->server->addGrantType(new ClientCredentials(new CustomClientStorage()));
        $this->server->addGrantType(new RefreshToken($storage));
    }

    /**
     * Getter for Server object
     *
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }
}