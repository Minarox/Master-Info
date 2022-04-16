<?php
declare(strict_types = 1);

namespace Controllers;

use BadRequest;
use Controller;
use NotFound;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Unauthorized;

/**
 * Controller for emails
 */
class EmailController extends Controller
{
    /**
     * Return array of emails
     * Usage: GET /emails | Scope: admin, super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function getEmails(Request $request, Response $response): Response
    {
        // Check scope before accessing function
        $this->checkScope(["admin"]);

        // Display emails list
        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "emails",
                    [
                        "email_id",
                        "title",
                        "description"
                    ],
                    ['*'],
                    false,
                    "email_id"
                )
            )
        );
        return $response;
    }

    /**
     * Return information of an email
     * Usage: GET /emails/{email_id} | Scope: admin, super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function getEmail(Request $request, Response $response, array $args): Response
    {
        // Check scope before accessing function
        $this->checkScope(["admin"]);

        // Check if email exist
        $this->checkExist("email_id", $args, "emails", true, "email_id");

        // Fetch and display email information
        $response->getBody()->write(
            json_encode(
                $this->database()->find(
                    "emails",
                    [
                        "title",
                        "description",
                        "subject",
                        "content",
                        "created_at",
                        "updated_at"
                    ],
                    ["email_id" => $args["email_id"]],
                    true
                )
            )
        );
        return $response;
    }

    /**
     * Create new email
     * Usage: POST /emails | Scope: admin, super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function addEmail(Request $request, Response $response): Response
    {
        // Check scope before accessing function
        $this->checkScope(["admin"]);

        // Check if values exist in request
        $this->checkExist("title", $GLOBALS["body"], null, true);
        $this->checkExist("subject", $GLOBALS["body"], null, true);
        $this->checkExist("content", $GLOBALS["body"], null, true);

        // Create new email
        $this->database()->create(
            "emails",
            [
                "title" => $GLOBALS["body"]["title"],
                "description" => $GLOBALS["body"]["description"] ?? '',
                "subject" => $GLOBALS["body"]["subject"],
                "content" => $GLOBALS["body"]["content"]
            ]
        );

        // Display success code
        return $this->successCode()->created();
    }

    /**
     * Create new email from template
     * Usage: POST /emails/{email_id} | Scope: admin, super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function addTemplateEmail(Request $request, Response $response, array $args): Response
    {
        // Check scope before accessing function
        $this->checkScope(["admin"]);

        // Check if email exist
        $this->checkExist("email_id", $args, "emails", true, "email_id");

        // Check if values exist in request
        $this->checkExist("title", $GLOBALS["body"], null, true);

        // Fetch template
        $template = $this->database()->find(
            "emails",
            [
                "subject",
                "content"
            ],
            ["email_id" => $args["email_id"]],
            true
        );

        // Create new email
        $this->database()->create(
            "emails",
            [
                "title" => $GLOBALS["body"]["title"],
                "description" => $GLOBALS["body"]["description"] ?? '',
                "subject" => $template["subject"],
                "content" => $template["content"]
            ]
        );

        // Display success code
        return $this->successCode()->created();
    }

    /**
     * Edit information of an email
     * Usage: PUT /emails/{email_id} | Scope: admin, super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function editEmail(Request $request, Response $response, array $args): Response
    {
        // Check scope before accessing function
        $this->checkScope(["admin"]);

        // Check if email exist
        $this->checkExist("email_id", $args, "emails", true, "email_id");

        // Edit email information
        $this->database()->update(
            "emails",
            [
                "title" => $GLOBALS["body"]["title"] ?? '',
                "description" => $GLOBALS["body"]["description"] ?? '',
                "subject" => $GLOBALS["body"]["subject"] ?? '',
                "content" => $GLOBALS["body"]["content"] ?? ''
            ],
            ["email_id" => $args["email_id"]]
        );

        // Display success code
        return $this->successCode()->success();
    }

    /**
     * Delete existing email
     * Usage: DELETE /emails/{email_id} | Scope: admin, super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws Unauthorized if user don't have the permission
     */
    public function deleteEmail(Request $request, Response $response, array $args): Response
    {
        // Check scope before accessing function
        $this->checkScope();

        // Check if email exist
        $this->checkExist("email_id", $args, "emails", true, "email_id");

        // Remove email
        $this->database()->delete(
            "emails",
            ["email_id" => $args["email_id"]]
        );

        // Display success code
        return $this->successCode()->success();
    }
}