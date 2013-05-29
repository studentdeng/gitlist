<?php

namespace GitList\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Response;

class MainController implements ControllerProviderInterface {

    public function connect(Application $app) {
        $route = $app['controllers_factory'];

        $route->get('/', function() use ($app) {
                    $repositories = $app['git']->getRepositories($app['git.repos']);

                    return $app['twig']->render('index.twig', array(
                                'repositories' => $repositories,
                    ));
                })->bind('homepage');

        $route->get('{repo}/stats/{branch}', function($repo, $branch) use ($app) {
                            $repository = $app['git']->getRepository($app['git.repos'] . $repo);
                            $stats = $repository->getStatistics($branch);
                            $authors = $repository->getAuthorStatistics();

                            return $app['twig']->render('stats.twig', array(
                                        'repo' => $repo,
                                        'branch' => $branch,
                                        'branches' => $repository->getBranches(),
                                        'tags' => $repository->getTags(),
                                        'stats' => $stats,
                                        'authors' => $authors,
                            ));
                        })->assert('repo', '[\w-._]+')
                ->assert('branch', '[\w-._]+')
                ->value('branch', 'master')
                ->bind('stats');

        $route->get('{repo}/{branch}/rss/', function($repo, $branch) use ($app) {
                            $repository = $app['git']->getRepository($app['git.repos'] . $repo);
                            $commits = $repository->getPaginatedCommits($branch);

                            $html = $app['twig']->render('rss.twig', array(
                                'repo' => $repo,
                                'branch' => $branch,
                                'commits' => $commits,
                            ));

                            return new Response($html, 200, array('Content-Type' => 'application/rss+xml'));
                        })->assert('repo', '[\w-._]+')
                ->assert('branch', '[\w-._]+')
                ->bind('rss');

        $route->post('login', function() use ($app) {
                    echo 'login';

                    $username = $_POST['username'];
                    $password = $_POST['password'];

                    $client = new HTTPClient2();
                    $url = "http://localhost/gitlist/worklife/index.php/api/account";

                    $headers = array();
                    $info = $username . ":" . $password;
                    $authorization = "Basic " . base64_encode($info);
                    $headers[] = "Authorization: $authorization";
                    
                    $response = $client->post($url, array(), false, $headers);
                    $statuscode = $client->http_code;
                    
                    var_dump($response);
                    var_dump($statuscode);
                    
                })->bind('login');

        return $route;
    }

}