<?php

use Silex\Application;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Debug\Debug;

require_once __DIR__ . '/../vendor/autoload.php';

class App extends Application
{
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        Debug::enable();
        $this['debug'] = true;

        $this->_initRoutes();
        $this->_initTwig();
        $this->_initDoctrine();
        $this->_initValidator();
        $this->_initSessions();
        $this->_initAuth();
    }

    private function _initAuth()
    {
        $this['auth'] = $this->share(function () {
            $id = $this['request']->headers->get('X-Public');
            $contentHash = $this['request']->headers->get('X-Hash');
            $content['message'] = $this['request']->get('message');

            return new \Trader\Service\Auth\Auth($id, $contentHash, $content);
        });
    }

    private function _initRoutes()
    {
        $this->register(new Silex\Provider\UrlGeneratorServiceProvider());

        // load routes yaml config files
        $this['routes'] = $this->extend('routes', function (RouteCollection $routes) {
            $locator = new FileLocator(__DIR__ . '/config');
            $loader = new YamlFileLoader($locator);
            $collection = $loader->load('routes.yml');
            $routes->addCollection($collection);

            return $routes;
        });

        $this->before(function (\Symfony\Component\HttpFoundation\Request $request) {
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $data = json_decode($request->getContent(), true);
                $request->request->replace(is_array($data) ? $data : array());
            }
        });
    }

    private function _initTwig()
    {
        $this->register(new \Silex\Provider\TwigServiceProvider(), [
            'twig.path' => __DIR__ . '/View',
            'twig.options' => [
                'cache' => __DIR__ . '/../cache/twig',
                'debug' => $this['debug'],
                'strict_variables' => true
            ],
            'twig.form.templates' => [
                'bootstrap_3_layout.html.twig',

            ]
        ]);
    }

    private function _initValidator()
    {
        $this->register(new Silex\Provider\ValidatorServiceProvider());
    }

    private function _initDoctrine()
    {
        $this->register(new \Silex\Provider\DoctrineServiceProvider(), [
            'db.options' => [
                'driver' => 'pdo_mysql',
                'dbname' => 'trader',
                'user' => 'root',
                'password' => 'root',
                'charset' => 'UTF8'
            ]
        ]);

        // register doctrine ORM
        $this->register(new \Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), [
            'orm.proxies_dir' => __DIR__ . '/../cache/doctrine/proxy',
            'orm.auto_generate_proxies' => true,
            'orm.em.options' => [
                'mappings' => [
                    [
                        'type' => 'annotation',
                        'namespace' => 'Trader\Entity',
                        'path' => __DIR__ . '/Trader/Entity'
                    ]
                ]
            ]
        ]);
    }

    private function _initSessions()
    {
        $this->register(new Silex\Provider\SessionServiceProvider());
    }
}
