<?php
declare(strict_types=1);

namespace App\Controller;

use App\Contracts\Logger\LoggerInterface;
use App\Contracts\Response\ResponseInterface;
use App\Contracts\Service\QueryServiceInterface;
use App\Contracts\Session\FlashBagInterface;
use App\VO\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractControllerSimple
{
    public function __construct(
        protected readonly LoggerInterface       $logger,
        protected readonly FlashBagInterface     $flashBag,
        protected readonly ResponseInterface     $response,
        protected readonly QueryServiceInterface $queryService,
    )
    {
    }

    public function __invoke(Request $request, Context $context, $entity): Response
    {
        try {
            $data = $this->queryService->fetch($entity, $context);
            $this->logger->debug('Data fetched successfully.');
            return $this->responseSuccess($data, $context);
        } catch (\Exception $exception) {
            return $this->onException($exception, $context);
        }
    }

    abstract protected function successTemplate(): string;

    abstract protected function errorTemplate(): string;

    protected function responseSuccess($data, Context $context): Response
    {
        return $this->response->render($this->successTemplate(), [
            'context' => $context,
            'data' => $data,
        ]);
    }

    protected function onException(\Exception $e, Context $context, ?string $message = null): Response
    {
        $this->logger->exception($e);
        $errorMessage = $message ?? 'Oops! An error occurred.';
        $this->flashBag->error($errorMessage);
        return $this->responseError($errorMessage, $context);
    }

    protected function responseError(string $message, Context $context): Response
    {
        return $this->response->error($this->errorTemplate(), [
            'message' => $message,
            'context' => $context,
        ]);
    }
}
