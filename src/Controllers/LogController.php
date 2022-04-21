<?php
declare(strict_types = 1);

namespace Controllers;

use BadRequest;
use Controller;
use NotFound;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Enums\Type;
use Enums\Action;
use Unauthorized;

/**
 * Controller for emails
 */
class LogController extends Controller
{
    /**
     * Return array of logs
     * Usage: GET /logs | Scope: super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function getLogs(Request $request, Response $response): Response
    {
        // Check scope before accessing function
        $this->checkScope([]);

        // Display emails list
        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "logs",
                    [
                        "source",
                        "source_id",
                        "source_type",
                        "action",
                        "target",
                        "target_id",
                        "target_type",
                        "created_at"
                    ],
                    ['*'],
                    false,
                    "created_at DESC"
                )
            )
        );
        return $response;
    }

    /**
     * Create new log
     * Usage: POST /emails | Scope: admin, super_admin
     *
     * @param string $source_id
     * @param Type   $source_type
     * @param Action $action
     * @param string $target_id
     * @param Type   $target_type
     *
     * @return void
     * @throws BadRequest if request contain errors
     * @throws NotFound if database return nothing
     */
    public function addLog(string $source_id, Type $source_type, Action $action, string $target_id, Type $target_type)
    {
        // Create new log
        $this->database()->create(
            "logs",
            [
                "source" => $this->getName($source_id, $source_type),
                "source_id" => $source_id,
                "source_type" => $source_type->name,
                "action" => $action->name,
                "target" => $this->getName($target_id, $target_type),
                "target_id" => $target_id,
                "target_type" => $target_type->name
            ]
        );
    }

    /**
     * Fetch full name for source or target field of Logs table
     *
     * @throws BadRequest
     * @throws NotFound
     */
    private function getName(string $id, Type $type): string
    {
        switch ($type->name) {
            case "User":
            case "Admin":
                $name = $this->database()->find(
                    strtolower($type->name) . 's',
                    [
                        "first_name",
                        "last_name"
                    ],
                    [strtolower($type->name) . "_id" => $id],
                    true
                );

                return $name["first_name"] . ' ' . $name["last_name"];
            case "Email":
                return ($this->database()->find(
                    "emails",
                    ["title"],
                    ["email_id" => $id],
                    true
                ))["title"];
            default:
                return Type::App->name;
        }
    }
}