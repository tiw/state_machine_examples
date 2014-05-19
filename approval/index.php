<?php
require_once __DIR__.'/vendor/autoload.php';

use Tiddr\StateMachine\Controller;
use Tiddr\StateMachine\StateMachine;
use Tiddr\StateMachine\State;

$sm = StateMachine::fromFile('./approval.sm');
$approves = [
    'buy computer' => 'new',
    'buy chairs' => 'project_manager_approved',
    'buy whiteboard' => 'manager_approved',
    'buy VHost' => 'manager_and_project_manager_approved',
    'buy desk' => 'finished'
];

$smController = new Controller($sm);


$app = new Silex\Application;
$app['debug'] = true;

$roles = [
    'manager',
    'project_manager',
    'ceo'
];

$acl = [
    'manager'=>['manager_approve'],
    'project_manager'=>['project_manager_approve'],
    'ceo'=>['ceo_approve']
];

$app->get('/users/{user_id}/approval_requests', function($user_id) use($approves, $roles, $acl, $sm, $app){
    $list = [];
    $stateHash = [];
    $states = $sm->getStates();
    array_walk($states, function($state) use(&$stateHash){
        $stateHash[$state->getName()] = $state;
    });
    foreach($approves as $name=>$stateName) {
        // get all posibile events
        $triggers = $stateHash[$stateName]->getAllPosibleTriggers();
        foreach($triggers as $trigger) {
            if(in_array($trigger->getCode(), $acl[$roles[$user_id]])) {
                $list[] = $name;
            }
        }
    }
    return $app->json($list);
});



$app->run();
