<?php


namespace App\Domain\Inheritances;


use App\Domain\DTO\Utils\Utils;
use App\Domain\Inheritances\LogInterfaces\LogdnaInterface;
use Cratia\Rest\Actions\ActionError;
use Cratia\Rest\Actions\ActionErrorPayload;
use Cratia\Rest\Handlers\HttpErrorHandler;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Throwable;

class HttpErrorHandlerCors extends HttpErrorHandler
{
    //TODO IMPROVE THIS CODE
    protected function respond(): Response
    {
        $exception = $this->exception;
        die(dump($exception));
        //TODO: Check this
        Utils::logException($this->getContainer()->get(LogdnaInterface::class), $exception);
        $statusCode = 500;
        $description = $exception->getMessage();
        $json = json_decode($exception->getMessage(), true);
        $i18n_error = "TXT_GENERAL_ERROR";
        if (is_array($json) && !is_null($json)) {
            $i18n_error = $json['i18n_error'];
            $description = $json['exception_message'];
        }
        $error = new I18nActionError(
            $i18n_error,
            $statusCode,
            ActionError::SERVER_ERROR,
            'An internal error has occurred while processing your request.'
        );


        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
            $error->setCode($exception->getCode());
            $error->setDescription($description);

            if ($exception instanceof HttpNotFoundException) {
                $error->setType(ActionError::RESOURCE_NOT_FOUND);
            } elseif ($exception instanceof HttpMethodNotAllowedException) {
                $error->setType(ActionError::NOT_ALLOWED);
            } elseif ($exception instanceof HttpUnauthorizedException) {
                $error->setType(ActionError::UNAUTHENTICATED);
            } elseif ($exception instanceof HttpForbiddenException) {
                $error->setType(ActionError::INSUFFICIENT_PRIVILEGES);
            } elseif ($exception instanceof HttpBadRequestException) {
                $error->setType(ActionError::BAD_REQUEST);
            } elseif ($exception instanceof HttpNotImplementedException) {
                $error->setType(ActionError::NOT_IMPLEMENTED);
            }
        }

        if (
            !($exception instanceof HttpException)
            && ($exception instanceof Exception || $exception instanceof Throwable)
            && $this->displayErrorDetails
        ) {
            if ($exception->getCode() >= 200 && $exception->getCode() < 600) {
                $statusCode = $exception->getCode();
            }
            $error
                ->setCode($statusCode)
                ->setDescription($description)
                ->setExtraInfo(
                    [
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                        'trace' => $exception->getTrace(),
                    ]
                );
        }

        $payload = new ActionErrorPayload($this->getContainer(), $statusCode, $error);
        $encodedPayload = json_encode($payload);

        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($encodedPayload);


        $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        $response = $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        $response = $response->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization');


        // Allow Ajax CORS requests with Authorization header
        $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');


        return $response->withHeader('Content-Type', 'application/json');
    }


}