<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use App\Contracts\Logger\LoggerInterface;
use App\Contracts\Response\ResponseInterface;
use App\Contracts\Routing\RoutingInterface;
use App\Contracts\Templating\TemplatingInterface;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class SymfonyResponseServiceBuilder implements FileDefinitionBuilderInterface
{
    public static function build(
        MakerConfig $config,
        string      $namespace = 'Service\\Response',
        string      $name = 'SymfonyResponseService'
    ): FileDefinitionBuilder
    {
        $fileDefinition = FileDefinitionBuilder::build($namespace, $name, 'Service', $config);

        $class = $fileDefinition->file->addClass($fileDefinition->fullName());
        $class->setFinal()->setReadOnly();
        $class->addImplement(ResponseInterface::class);

        $class->getNamespace()
            ->addUse(LoggerInterface::class)
            ->addUse(ResponseInterface::class)
            ->addUse(RoutingInterface::class)
            ->addUse(TemplatingInterface::class)
            ->addUse(BinaryFileResponse::class)
            ->addUse(JsonResponse::class)
            ->addUse(RedirectResponse::class)
            ->addUse(Response::class)
            ->addUse(Autowire::class)
            ->addUse(\App\Service\Templating\TwigTemplatingService::class)
            ->addUse(\App\Service\Routing\SymfonyRoutingService::class)
            ->addUse(\App\Logger\DefaultLogger::class)
        ;

        self::addConstruct($class);
        self::addMethodRedirectToUrl($class);
        self::addMethodRedirectToRoute($class);
        self::addMethodRender($class);
        self::addMethodJson($class);
        self::addMethodJsonError($class);
        self::addMethodFile($class);
        self::addMethodEmpty($class);
        self::addMethodError($class);

        return $fileDefinition;
    }

    private static function addConstruct(ClassType $class): void
    {
        $class->addMethod('__construct')
            ->setPublic();

        $class->getMethod('__construct')
            ->addPromotedParameter('templating')
            ->setPrivate()
            ->addAttribute(Autowire::class, [
                'service' => new Literal('\App\Service\Templating\TwigTemplatingService::class'),
            ])
            ->setType(TemplatingInterface::class);

        $class->getMethod('__construct')
            ->addPromotedParameter('routing')
            ->setPrivate()
            ->addAttribute(Autowire::class, [
                'service' => new Literal('\App\Service\Routing\SymfonyRoutingService::class'),
            ])
            ->setType(RoutingInterface::class);

        $class->getMethod('__construct')
            ->addPromotedParameter('logger')
            ->setPrivate()
            ->addAttribute(Autowire::class, [
                'service' => new Literal('\App\Logger\DefaultLogger::class'),
                ])
            ->setType(LoggerInterface::class);
    }

    private static function addMethodRedirectToUrl(ClassType $class): void
    {
        $class->addMethod('redirectToUrl')
            ->setReturnType(RedirectResponse::class)
            ->addParameter('url')
            ->setType('string');

        $class->getMethod('redirectToUrl')
            ->addBody('$this->logger->info(\'Redirecting to URL: \' . $url);')
            ->addBody('return new RedirectResponse($url);');
    }

    private static function addMethodRedirectToRoute(ClassType $class): void
    {
        $class->addMethod('redirectToRoute')
            ->setReturnType(RedirectResponse::class)
            ->addParameter('route')
            ->setType('string');

        $class->getMethod('redirectToRoute')
            ->addParameter('parameters')
            ->setType('array')
            ->setDefaultValue([]);

        $class->getMethod('redirectToRoute')
            ->addBody('$url = $this->routing->generate($route, $parameters);')
            ->addBody('$this->logger->info(\'Redirecting to route: \' . $route, [\'parameters\' => $parameters]);')
            ->addBody('return $this->redirectToUrl($url);');
    }

    private static function addMethodRender(ClassType $class): void
    {
        $class->addMethod('render')
            ->setReturnType(Response::class)
            ->addParameter('view')
            ->setType('string');

        $class->getMethod('render')
            ->addParameter('parameters')
            ->setType('array')
            ->setDefaultValue([]);

        $class->getMethod('render')
            ->addBody('try {')
            ->addBody('    $this->logger->info(\'Rendering view: \' . $view, [\'parameters\' => $parameters]);')
            ->addBody('    $render = $this->templating->render($view, $parameters);')
            ->addBody('    return new Response($render);')
            ->addBody('} catch (\Error|\Exception $e) {')
            ->addBody('    $this->logger->error(\'An error occurred while rendering view\', [\'error\' => $e->getMessage()]);')
            ->addBody('    return $this->error(\'error.html.twig\', [\'error\' => \'An error occurred\']);')
            ->addBody('}');
    }

    private static function addMethodJson(ClassType $class): void
    {
        $class->addMethod('json')
            ->setReturnType(JsonResponse::class)
            ->addParameter('data')
            ->setType('array');

        $class->getMethod('json')
            ->addParameter('status')
            ->setType('int')
            ->setDefaultValue(200);

        $class->getMethod('json')
            ->addParameter('headers')
            ->setType('array')
            ->setDefaultValue([]);

        $class->getMethod('json')
            ->addParameter('json')
            ->setType('bool')
            ->setDefaultValue(false);

        $class->getMethod('json')
            ->addBody('try {')
            ->addBody('    $this->logger->info(\'Returning JSON response\', [\'data\' => $data, \'status\' => $status, \'headers\' => $headers]);')
            ->addBody('    return new JsonResponse($data, $status, $headers, $json);')
            ->addBody('} catch (\Error|\Exception $e) {')
            ->addBody('    $this->logger->error(\'An error occurred while returning JSON response\', [\'error\' => $e->getMessage()]);')
            ->addBody('    return $this->jsonError([\'error\' => \'An error occurred\'], 500);')
            ->addBody('}');
    }

    private static function addMethodJsonError(ClassType $class): void
    {
        $class->addMethod('jsonError')
            ->setReturnType(JsonResponse::class)
            ->addParameter('data')
            ->setType('array');

        $class->getMethod('jsonError')
            ->addParameter('status')
            ->setType('int')
            ->setDefaultValue(400);

        $class->getMethod('jsonError')
            ->addParameter('headers')
            ->setType('array')
            ->setDefaultValue([]);

        $class->getMethod('jsonError')
            ->addParameter('json')
            ->setType('bool')
            ->setDefaultValue(false);

        $class->getMethod('jsonError')
            ->addBody('$this->logger->error(\'Returning JSON error response\', [\'data\' => $data, \'status\' => $status, \'headers\' => $headers]);')
            ->addBody('return new JsonResponse($data, $status, $headers, $json);');
    }

    private static function addMethodFile(ClassType $class): void
    {
        $class->addMethod('file')
            ->setReturnType(BinaryFileResponse::class)
            ->addParameter('file')
            ->setType('string');

        $class->getMethod('file')
            ->addParameter('filename')
            ->setType('string');

        $class->getMethod('file')
            ->addParameter('headers')
            ->setType('array')
            ->setDefaultValue([]);

        $class->getMethod('file')
            ->addBody('$contentDisposition = $headers[\'Content-Disposition\'] ?? \'attachment\';')
            ->addBody('$headers[\'Content-Disposition\'] = sprintf(\'%s; filename="%s"\', $contentDisposition, $filename);')
            ->addBody('$this->logger->info(\'Returning file: \' . $file, [\'filename\' => $filename, \'headers\' => $headers]);')
            ->addBody('return new BinaryFileResponse($file, 200, $headers);');
    }

    private static function addMethodEmpty(ClassType $class): void
    {
        $class->addMethod('empty')
            ->setReturnType(Response::class)
            ->addParameter('status')
            ->setType('int')
            ->setDefaultValue(204);

        $class->getMethod('empty')
            ->addParameter('headers')
            ->setType('array')
            ->setDefaultValue([]);

        $class->getMethod('empty')
            ->addBody('$this->logger->info(\'Returning empty response\', [\'status\' => $status, \'headers\' => $headers]);')
            ->addBody('return new Response(null, $status, $headers);');
    }

    private static function addMethodError(ClassType $class): void
    {
        $class->addMethod('error')
            ->setReturnType(Response::class)
            ->addParameter('view')
            ->setType('string');

        $class->getMethod('error')
            ->addParameter('parameters')
            ->setType('array')
            ->setDefaultValue([]);

        $class->getMethod('error')
            ->addParameter('status')
            ->setType('int')
            ->setDefaultValue(500);

        $class->getMethod('error')
            ->addBody('$this->logger->info(\'Returning error response\', [\'view\' => $view, \'parameters\' => $parameters, \'status\' => $status]);')
            ->addBody('$render = $this->templating->render($view, $parameters);')
            ->addBody('return new Response($render, $status);');
    }
}
