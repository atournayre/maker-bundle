<?php
declare(strict_types=1);

namespace App\Controller;

use App\Contracts\Logger\LoggerInterface;
use App\Contracts\Response\ResponseInterface;
use App\Contracts\Service\CommandServiceInterface;
use App\Contracts\Session\FlashBagInterface;
use App\VO\Context;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractControllerWithForm
{
    public function __construct(
        protected readonly FormFactoryInterface    $formFactory,
        protected readonly LoggerInterface         $logger,
        protected readonly FlashBagInterface       $flashBag,
        protected readonly ResponseInterface       $response,
        protected readonly CommandServiceInterface $commandService,
    )
    {
    }

    public function __invoke(Request $request, Context $context, $entity): Response
    {
        try {
            $data = $this->createVo($entity, $context);
            $form = $this->createForm($data);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if (!$form->isValid()) {
                    $this->whenFormIsInvalid($form, $data, $context);
                }
                if ($form->isValid()) {
                    return $this->whenFormIsValid($form, $data, $context);
                }
            }

            return $this->responseSuccess($form, $data, $context);
        } catch (\Exception $exception) {
            return $this->onException($exception, $context);
        }
    }

    abstract protected function createVo($entity, Context $context);

    abstract protected function createForm($data = null): FormInterface;

    protected function whenFormIsInvalid(FormInterface $form, $data, Context $context): void
    {
        $this->flashBag->warning('Some fields are invalid. Please check the form.');
        $this->logger->warning('Some fields are invalid. Please check the form.', [
            'form' => $form->getErrors(),
            'data' => $data,
            'context' => $context,
        ]);
    }

    protected function whenFormIsValid(FormInterface $form, $data, Context $context): Response
    {
        try {
            $this->commandService->execute($data, $context);
            $this->flashBag->success('Form submitted successfully.');
            $this->logger->debug('Form submitted successfully.');
            return $this->redirectOnSuccess($data, $context);
        } catch (\Exception $exception) {
            $this->logger->exception($exception);
            $this->flashBag->fromException($exception);
            return $this->responseError($exception->getMessage(), $context);
        }
    }

    abstract protected function redirectOnSuccess($data, Context $context): Response;

    abstract protected function successTemplate(): string;

    abstract protected function errorTemplate(): string;

    protected function responseError(string $message, Context $context): Response
    {
        return $this->response->error($this->errorTemplate(), [
            'message' => $message,
            'context' => $context,
        ]);
    }

    protected function responseSuccess(FormInterface $form, $data, Context $context): Response
    {
        return $this->response->render($this->successTemplate(), [
            'form' => $form,
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
}
