<?php
declare(strict_types = 1);

namespace Controllers;

use BadRequest;
use Controller;
use NotFound;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use ServiceUnavailable;
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

    /**
     * Send emails to specific users
     * Usage: POST /emails/send | Scope: admin, super_admin
     *
     * @param Request  $request  Slim request interface
     * @param Response $response Slim response interface
     *
     * @return Response Response to show
     * @throws NotFound if database return nothing
     * @throws BadRequest if request contain errors
     * @throws ServiceUnavailable if service return error
     */
    public function sendEmails(Request $request, Response $response): Response
    {
        // Check if email exist
        $this->checkExist("email_id", $GLOBALS["body"], "emails", true, "email_id");

        // Check if users exist in request
        $this->checkExist("users", $GLOBALS["body"], null, true);

        // Fetch email template
        $email = $this->database()->find(
            "emails",
            [
                "subject",
                "content"
            ],
            ["email_id" => $GLOBALS["body"]["email_id"]],
            true
        );
        $errors = 0;

        // Send emails
        // TODO: Crash > ~20 mails
        $mail = new PHPMailer(true);
        try {
            // STMP
            $mail->isSMTP();
            $mail->Host       = CONFIG["stmp"]["host"];
            $mail->Port       = CONFIG["stmp"]["port"];
            $mail->SMTPAuth   = true;
            $mail->Username   = CONFIG["stmp"]["username"];
            $mail->Password   = CONFIG["stmp"]["password"];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

            // Recipients
            $mail->setFrom(CONFIG["stmp"]["from"], "Cerealis");
            $mail->addReplyTo("contact@cerealis.com", "Cerealis");
            $mail->addAddress(CONFIG["stmp"]["from"], "Cerealis");

            // Users
            foreach ($GLOBALS["body"]["users"] as $user) {
                $user = $this->database()->find(
                    "users",
                    [
                        "email",
                        "first_name",
                        "last_name"
                    ],
                    ["user_id" => $user],
                    true,
                    null,
                    false
                );

                if ($user) {
                    $mail->addBCC($user["email"], $user["first_name"] . ' ' . $user["last_name"]);
                } else {
                    $errors ++;
                }
            }

            // Content
            $mail->CharSet = 'UTF-8';
            $mail->isHTML();
            $mail->Subject = $email["subject"];
            $mail->Body    = $email["content"];

            $mail->send();
        } catch (Exception) {
            throw new ServiceUnavailable("Service Unavailable");
            // var_dump($mail->ErrorInfo);
        }

        // Response according to errors
        if ($errors === count($GLOBALS["body"]["users"])) {
            return $this->errorCode()->badRequest();
        } else if ($errors > 0) {
            return $this->successCode()->success(count($GLOBALS["body"]["users"]) - $errors . "out of " . count($GLOBALS["body"]["users"]) . " emails were sent");
        } else {
            return $this->successCode()->success("All emails have been sent");
        }
    }
}