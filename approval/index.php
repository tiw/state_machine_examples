<?php
require_once __DIR__.'/vendor/autoload.php';

use Tiddr\StateMachine\Controller;
use Tiddr\StateMachine\StateMachine;
use Tiddr\StateMachine\State;


// 这个例子模拟了怎样处理一类单据， 这类单据的流程设定在 approval.sm里。

$sm = StateMachine::fromFile('./approval.sm');
// 这里模拟了申请单据， 一行就是一个单据。
// 第一个字段是单据的简述， 第二个是单据在流程中的状体， 这个状态必须是对应于状
// 态机中的一个状态.
// 例如：
// 'buy computer' => 'new', 的意思就是， 有一个购买电脑的申请单， 目前的状态是"新创建的"
$approves = [
    'buy computer' => 'new',
    'buy chairs' => 'project_manager_approved',
    'buy whiteboard' => 'manager_approved',
    'buy VHost' => 'manager_and_project_manager_approved',
    'buy desk' => 'finished'
];


// 这个是控制状态机状态转换的类
$smController = new Controller($sm);


// 这个例子用了silex来实现web端， 这点不是这个例子的重点。
$app = new Silex\Application;
$app['debug'] = true;

// 设定参与审批的有那些角色
$roles = [
    'manager',
    'project_manager',
    'ceo'
];

// 上面设定的角色， 可以触发那些事件， 也就是角色允许做的事情.
// 这里也是模拟数据， 生产中可能需要从数据库读出。 也有可能根据申请单类型生成这类数据
$acl = [
    'manager'=>['manager_approve'],
    'project_manager'=>['project_manager_approve'],
    'ceo'=>['ceo_approve']
];

// 这个资源做的就是根据用的id， 显示这个用户要审批的内容
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
