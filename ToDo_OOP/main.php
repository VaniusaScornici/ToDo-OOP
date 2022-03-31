<?php

# ! NO PACKAGES, NO CLASSES

# [X] list - show all todos
# [x] add  <description> - add a new todo item
# [x] delete <id> - delete a todo item

# Homework
# [X] search <query> find todo items
# [X] edit <id> <content> update todo item
# [X] set-status <id> <status> (check if status is new, in-progress, done or rejected)
# [X] *Task 3 - add due-date to todo item (if due-date is in past, then show status 'outdated'

use Iscornici\ToDoOop\App;

require_once __DIR__."/vendor/autoload.php";

(new App(getcwd() . '/todo.json', 'TODO-'))->run();
