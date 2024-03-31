<?php

namespace Atournayre\Bundle\MakerBundle\Builder\FileDefinition\Service;

use Atournayre\Bundle\MakerBundle\Builder\FileDefinitionBuilder;
use Atournayre\Bundle\MakerBundle\Config\MakerConfig;
use Atournayre\Bundle\MakerBundle\Contracts\Builder\FileDefinitionBuilderInterface;
use App\Contracts\Logger\LoggerInterface;
use App\Contracts\Response\ResponseInterface;
use App\Contracts\Routing\RoutingInterface;
use App\Contracts\Templating\TemplatingInterface;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
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

        $class = $fileDefinition
            ->file
            ->addClass($fileDefinition->fullName())
            ->setFinal()
            ->setReadOnly()
            ->addImplement(ResponseInterface::class)
            ->addMember(self::addConstruct())
            ->addMember(self::addMethodRedirectToUrl())
            ->addMember(self::addMethodRedirectToRoute())
            ->addMember(self::addMethodRender())
            ->addMember(self::addMethodJson())
            ->addMember(self::addMethodJsonError())
            ->addMember(self::addMethodFile())
            ->addMember(self::addMethodEmpty())
            ->addMember(self::addMethodError())
        ;

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

        return $fileDefinition;
    }

    private static function addConstruct(): Method
    {
        $method = new Method('__construct');
        $method->setPublic();

        $method
            ->addPromotedParameter('templating')
            ->setPrivate()
            ->addAttribute(Autowire::class, [
                'service' => new Literal('\App\Service\Templating\TwigTemplatingService::class'),
            ])
            ->setType(TemplatingInterface::class);

        $method
            ->addPromotedParameter('routing')
            ->setPrivate()
            ->addAttribute(Autowire::class, [
                'service' => new Literal('\App\Service\Routing\SymfonyRoutingService::class'),
            ])
            ->setType(RoutingInterface::class);

        $method
            ->addPromotedParameter('logger')
            ->setPrivate()
            ->addAttribute(Autowire::class, [
                'service' => new Literal('\App\Logger\DefaultLogger::class'),
                ])
            ->setType(LoggerInterface::class);
        return $method;
    }

    private static function addMethodRedirectToUrl(): Method
    {
        $method = new Method('redirectToUrl');
        $method
            ->setReturnType(RedirectResponse::class)
            ->addParameter('url')
            ->setType('string');

        $method
            ->addBody('$this->logger->info(\'Redirecting to URL: \' . $url);')
            ->addBody('return new RedirectResponse($url);');
        return $method;
    }

    private static function addMethodRedirectToRoute(): Method
    {
        $method = new Method('redirectToRoute');
        $method
            ->setReturnType(RedirectResponse::class)
            ->addParameter('route')
            ->setType('string');

        $method
            ->addParameter('parameters')
            ->setType('array')
            ->setDefaultValue([]);

        $method
            ->addBody('$url = $this->routing->generate($route, $parameters);')
            ->addBody('$this->logger->info(\'Redirecting to route: \' . $route, [\'parameters\' => $parameters]);')
            ->addBody('return $this->redirectToUrl($url);');
        return $method;
    }

    private static function addMethodRender(): Method
    {
        $method = new Method('render');
        $method
            ->setReturnType(Response::class)
            ->addParameter('view')
            ->setType('string');

        $method
            ->addParameter('parameters')
            ->setType('array')
            ->setDefaultValue([]);

        $method
            ->addBody('try {')
            ->addBody('    $this->logger->info(\'Rendering view: \' . $view, [\'parameters\' => $parameters]);')
            ->addBody('    $render = $this->templating->render($view, $parameters);')
            ->addBody('    return new Response($render);')
            ->addBody('} catch (\Error|\Exception $e) {')
            ->addBody('    $this->logger->error(\'An error occurred while rendering view\', [\'error\' => $e->getMessage()]);')
            ->addBody('    return $this->error(\'error.html.twig\', [\'error\' => \'An error occurred\']);')
            ->addBody('}');
        return $method;
    }

    private static function addMethodJson(): Method
    {
        $method = new Method('json');
        $method
            ->setReturnType(JsonResponse::class)
            ->addParameter('data')
            ->setType('array');

        $method
            ->addParameter('status')
            ->setType('int')
            ->setDefaultValue(200);

        $method
            ->addParameter('headers')
            ->setType('array')
            ->setDefaultValue([]);

        $method
            ->addParameter('json')
            ->setType('bool')
            ->setDefaultValue(false);

        $method
            ->addBody('try {')
            ->addBody('    $this->logger->info(\'Returning JSON response\', [\'data\' => $data, \'status\' => $status, \'headers\' => $headers]);')
            ->addBody('    return new JsonResponse($data, $status, $headers, $json);')
            ->addBody('} catch (\Error|\Exception $e) {')
            ->addBody('    $this->logger->error(\'An error occurred while returning JSON response\', [\'error\' => $e->getMessage()]);')
            ->addBody('    return $this->jsonError([\'error\' => \'An error occurred\'], 500);')
            ->addBody('}');
        return $method;
    }

    private static function addMethodJsonError(): Method
    {
        $method = new Method('jsonError');
        $method
            ->setReturnType(JsonResponse::class)
            ->addParameter('data')
            ->setType('array');

        $method
            ->addParameter('status')
            ->setType('int')
            ->setDefaultValue(400);

        $method
            ->addParameter('headers')
            ->setType('array')
            ->setDefaultValue([]);

        $method
            ->addParameter('json')
            ->setType('bool')
            ->setDefaultValue(false);

        $method
            ->addBody('$this->logger->error(\'Returning JSON error response\', [\'data\' => $data, \'status\' => $status, \'headers\' => $headers]);')
            ->addBody('return new JsonResponse($data, $status, $headers, $json);');
        return $method;
    }

    private static function addMethodFile(): Method
    {
        $method = new Method('file');
        $method
            ->setReturnType(BinaryFileResponse::class)
            ->addParameter('file')
            ->setType('string');

        $method
            ->addParameter('filename')
            ->setType('string');

        $method
            ->addParameter('headers')
            ->setType('array')
            ->setDefaultValue([]);

        $method
            ->addBody('$contentDisposition = $headers[\'Content-Disposition\'] ?? \'attachment\';')
            ->addBody('$headers[\'Content-Disposition\'] = sprintf(\'%s; filename="%s"\', $contentDisposition, $filename);')
            ->addBody('$this->logger->info(\'Returning file: \' . $file, [\'filename\' => $filename, \'headers\' => $headers]);')
            ->addBody('return new BinaryFileResponse($file, 200, $headers);');
        return $method;
    }

    private static function addMethodEmpty(): Method
    {
        $method = new Method('empty');
        $method
            ->setReturnType(Response::class)
            ->addParameter('status')
            ->setType('int')
            ->setDefaultValue(204);

        $method
            ->addParameter('headers')
            ->setType('array')
            ->setDefaultValue([]);

        $method
            ->addBody('$this->logger->info(\'Returning empty response\', [\'status\' => $status, \'headers\' => $headers]);')
            ->addBody('return new Response(null, $status, $headers);');
        return $method;
    }

    private static function addMethodError(): Method
    {
        $method = new Method('error');
        $method
            ->setReturnType(Response::class)
            ->addParameter('view')
            ->setType('string');

        $method
            ->addParameter('parameters')
            ->setType('array')
            ->setDefaultValue([]);

        $method
            ->addParameter('status')
            ->setType('int')
            ->setDefaultValue(500);

        $method
            ->addBody('$this->logger->info(\'Returning error response\', [\'view\' => $view, \'parameters\' => $parameters, \'status\' => $status]);')
            ->addBody('$render = $this->templating->render($view, $parameters);')
            ->addBody('return new Response($render, $status);');
        return $method;
    }
}
