state machine 例子
=====================

approval
========
审批流的示例。 审批流的状态图如下

```
 +-------------------------------------+
 |                                     |   
 |      New                            |
 +----------------------------------+--+
   |                                |
   | manager_approve                | project manager approve
   |                                |
+--+-----------------+        +-----+------------------------+
|manager_approved    |        |   project_manager_approved   |       
|                    |        |                              |
|                    |        |                              |
+----+---------------+        +-----+------------------------+
     |                              |
     |                              |
     | project manasger approve     | manager approve
     |                              |
 +---+------------------------------+---+
 | manager_and_project_manager_approved |                   
 |                                      |
 |                                      |
 +------------+-------------------------+
              |              
              | ceo approve             
              |              
  +-----------+------------+ 
  | ceo_approved           | 
  |                        | 
  |                        | 
  +------------------------+ 

```

提请申请单后， manager和project manager要并行批准。如果两人都批准后， 则需要
ceo批准。 ceo批准后， 审批流结束。



如何运行
========

前提条件是要安装composer.phar.

```
git clone git@github.com/tiw/state_machine_examples
cd approval
composer.phar install
php -S localhost:9090

```


下面的命令分别列出需要 manager、project manager和ceo审批的申请单：

```
curl localhost:9090/users/0/approval_requests
curl localhost:9090/users/1/approval_requests
curl localhost:9090/users/2/approval_requests

```
