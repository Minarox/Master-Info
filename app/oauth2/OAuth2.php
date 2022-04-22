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
                "client_table"        => CONFIG["oauth2"]["client_table"],
                "user_table"          => CONFIG["oauth2"]["user_table"],
                "access_token_table"  => CONFIG["oauth2"]["access_token_table"],
                "refresh_token_table" => CONFIG["oauth2"]["refresh_token_table"],
                "scope_table"         => CONFIG["oauth2"]["scope_table"]
            )
        );

        // Create Oauth2
        $this->server = new Server(
            $storage,
            array(
                "store_encrypted_token_string"      => (bool) CONFIG["oauth2"]["store_encrypted_token_string"],
                "access_lifetime"                   => (int) CONFIG["oauth2"]["access_lifetime"],
                "token_bearer_header_name"          => CONFIG["oauth2"]["token_bearer_header_name"],
                "allow_implicit"                    => (bool) CONFIG["oauth2"]["allow_implicit"],
                "allow_credentials_in_request_body" => (bool) CONFIG["oauth2"]["allow_credentials_in_request_body"],
                "always_issue_new_refresh_token"    => (bool) CONFIG["oauth2"]["always_issue_new_refresh_token"],
                "unset_refresh_token_after_use"     => (bool) CONFIG["oauth2"]["unset_refresh_token_after_use"],
                "refresh_token_lifetime"            => (int) CONFIG["oauth2"]["refresh_token_lifetime"]
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