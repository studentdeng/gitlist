<?php

namespace GitList\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

require 'httpclient.php';

class IssueController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $route = $app['controllers_factory'];

        $route->get('{repo}/issues/{branch}', function($repo, $branch) use ($app) {
            $repository = $app['git']->getRepository($app['git.repos'] . $repo);
            $sourcePath = $repository->getPath();
            $authors = $repository->getAuthorStatistics();
            
            //according source_path find project info
            $client2 = new HTTPClient2();
            $urlProject = "http://localhost/gitlist/worklife/index.php/api/project";
            $projectData = $client2->get($urlProject, array('source_path'=>$sourcePath));
			$httpStatusCode = $client2->http_code;
			if ($httpStatusCode != 200)
			{
				echo 'read project info error';
				echo '<br>';
				
				if ($httpStatusCode == 404)
				{
					echo "project path:$sourcePath not found";
					echo '<br>';
				}

                die();
			}
            
            $state = $_GET['state'];
            $url = "http://localhost/gitlist/worklife/index.php/api/issues/list?project_id=".$projectData['id'] . "&state=".$state;
            
            //use project_id find issues list
            $client = new HTTPClient2();
            $response = $client->get($url, array());
            $resultArray = $response['list'];
            
            $issues = array();
            foreach ($resultArray as $item) {
                $issue = (object)$item;
                
                if (empty($issues[$item['created2']]))
                {
                    $issues[$item['created2']] = array();
                    $issues[$item['created2']][] = $issue;
                }
                else
                {
                    $issues[$item['created2']][] = $issue;
                }
            }
            
            $projectObject = (object)$projectData;

            return $app['twig']->render('issues.twig', array(
                'repo'           => $repo,
                'branch'         => $branch,
                'branches'       => $repository->getBranches(),
                'tags'           => $repository->getTags(),
                'authors'        => $authors,
                'issues'         => $issues,
                'project'        => $projectObject,
                'date'           => '2013-05-20 10:02:02',
                'showstate'      => $state,            
            ));
        })->assert('repo', '[\w-._]+')
          ->assert('branch', '[\w-._]+')
          ->value('branch', 'master')
          ->bind('issues');
        
        $route->get('{repo}/issue/{commitId}/', function($repo, $commitId) use ($app) {
            $repository = $app['git']->getRepository($app['git.repos'] . $repo);
            $authors = $repository->getAuthorStatistics();
            
            $url2 = 'http://localhost/gitlist/worklife/index.php/api/issues?id='.$commitId;
            $client2 = new HTTPClient2();
            $response2 = $client2->get($url2, array());
            
            $issue = (object)$response2;
            
            $url = 'http://localhost/gitlist/worklife/index.php/api/issues/reply_list?id='.$commitId;
            $client = new HTTPClient2();
            $response = $client->get($url, array());
            $resultArray = $response['list'];
            
            $comments = array();
            foreach ($resultArray as $item) {
                $comment = (object)$item;
                $comment->user = (object)($item['user']);
                
                if (empty($comments[$item['created2']]))
                {
                    $comments[$item['created2']] = array();
                    $comments[$item['created2']][] = $comment;
                }
                else
                {
                    $comments[$item['created2']][] = $comment;
                }
            }
            
            return $app['twig']->render('issue.twig', array(
                'branch'         => 'master',
                'repo'           => $repo,
                'comments'       => $comments,
                'issue'          => $issue,
            ));
        })->assert('repo', '[\w-._]+')
          ->assert('commit', '[a-f0-9^]+')
          ->bind('issue');

        return $route;
    }
}