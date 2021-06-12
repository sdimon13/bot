<?php

namespace App\Exceptions\Traits;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\CoreClasses\Controllers\ApiResponse;
// use App\Exceptions\ExValidation;
use GuzzleHttp\Exception\ServerException as GuzzleHttpServerException;
use GuzzleHttp\Psr7;
use Illuminate\Database\QueryException;

trait RestExceptionHandlerTrait
{
    protected function apiResponse()
    {
        static $response;
        if (!$response) {
            $response = ApiResponse::get()->setSuccess(0);
        }
        return $response;
    }

    // Идентифицируем обрабатываемые ошибки
    // Возвращает наименование ошибки, для которой должен быть реализован одноименный метод обработчик
    protected function getExceptionName(Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            return 'ModelNotFoundException';
        } elseif ($e instanceof NotFoundHttpException) {
            return 'NotFoundHttpException';
        } elseif ($e instanceof MethodNotAllowedHttpException) {
            return 'MethodNotAllowedHttpException';
        } elseif ($e instanceof QueryException) {
            return 'QueryException';
        } elseif ($e instanceof GuzzleHttpServerException) {
            return 'GuzzleHttpServerException';
        } else {
            return null;
        }
    }

    protected function getJsonExceptionResponse(Request $request, Exception $e)
    {
        $exName = $this->getExceptionName($e);
        if ($exName && method_exists($this, $exName)) {
            if ('QueryException' === $exName) {
                $retval = $this->QueryException($e, [
                    // Данные для записи в 'audit.sql_errors_log'
                    'host' => $request->getHost(),
                    'path_info' => $request->getPathInfo(),
                    'query_string' => $request->getQueryString()
                ]);
            } else {
                $retval = $this->$exName($e);
            }
        } else {
            $retval = $this->badRequest($e);
        }
        return $retval;
    }

    protected function prepJsonExceptionResponse($e, $errCode, $params = null)
    {
        $meta['exception_class'] = get_class($e);
        if (method_exists($e, 'getStatusCode')) {
            $meta['ex_status_code'] = $e->getStatusCode();
        }
        if (method_exists($e, 'getMessage')) {
            $meta['ex_message'] = $e->getMessage();
        }
        if (method_exists($e, 'getRequest')) {
            $e_request = $e->getRequest();
            if (get_class($e_request) == 'GuzzleHttp\\Psr7\\Request') {
                $e_request = Psr7\str($e_request);
            }
            $meta['ex_request'] = $e_request;
        }
        if (method_exists($e, 'getResponse')) {
            $e_response = $e->getResponse();
            if (get_class($e_response) == 'GuzzleHttp\\Psr7\\Response') {
                $e_response = Psr7\str($e_response);
            }
            $meta['ex_response'] = $e_response;
        }

        if ('QueryException' === $errCode) {
            $meta['exception_info'] = $e->errorInfo;
            $meta['QueryException']['script_data'] = $params;
        }

        $this->apiResponse()->makeErrorMess($errCode, $meta);
        if (method_exists($e, 'getStatusCode')) {
            $this->apiResponse()->setStatusCode($e->getStatusCode());
        }
    }

    protected function jsonResponse(array $payload=null, $statusCode=null)
    {
        $payload = $payload ? : $this->apiResponse()->getResponse();
        $statusCode = $statusCode ? $statusCode : $this->apiResponse()->getStatusCode();
        return response()->json($payload, $statusCode);
    }

    protected function badRequest(Exception $e)
    {
        return null;
        
        $this->prepJsonExceptionResponse($e, 'UncknownException');
        return $this->jsonResponse();
    }

    protected function ModelNotFoundException(Exception $e)
    {
        $this->prepJsonExceptionResponse($e, 'ModelNotFound');
        return $this->jsonResponse();
    }

    protected function MethodNotAllowedHttpException(Exception $e)
    {
        $this->prepJsonExceptionResponse($e, 'MethodNotAllowed');
        return $this->jsonResponse();
    }

    protected function NotFoundHttpException(Exception $e)
    {
        $this->prepJsonExceptionResponse($e, 'NotFound');
        return $this->jsonResponse();
    }

    protected function GuzzleHttpServerException(Exception $e)
    {
        $this->prepJsonExceptionResponse($e, 'ExternalServiceError');
        return $this->jsonResponse();
    }

    protected function QueryException(Exception $e, array $scriptParams)
    {
        $this->prepJsonExceptionResponse($e, 'QueryException', $scriptParams);
        return $this->jsonResponse();
    }
}
