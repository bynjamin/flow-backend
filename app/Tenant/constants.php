<?php

if (!defined("MODULES_LIST")) {
    define("MODULES_LIST", [
        'User', 'UserGroup', 'Permission', 'Task', 'Project', 'Attendance'
    ]);
}

if (!defined("CRUD_ACTIONS")) {
    define("CRUD_ACTIONS", [
        'create', 'read', 'update', 'delete'
    ]);
}

if (!defined("ATTENDANCE_TYPE")) {
    define("ATTENDANCE_TYPE", [
        'arrival' => 0, 
        'leave'   => 1,
    ]);
}

if (!defined("ATTENDANCE_TYPE_INVERSE")) {
    define("ATTENDANCE_TYPE_INVERSE", [
        0 => 'arrival', 
        1 => 'leave',
    ]);
}

if (!defined("TASK_STATUS")) {
    define("TASK_STATUS", [
        0 => 'NOT_STARTED', 
        1 => 'IN_PROGRESS', 
        2 => 'WAITING', 
        3 => 'FINISHED'
    ]);
}

if (!defined("TASK_STATUS_INVERSE")) {
    define("TASK_STATUS_INVERSE", [
        'NOT_STARTED' => 0, 
        'IN_PROGRESS' => 1, 
        'WAITING' => 2, 
        'FINISHED' => 3
    ]);
}

if (!defined("SUPERADMIN_USER_GROUP")) {
    define("SUPERADMIN_USER_GROUP", 1);
}

if (!defined("ADMIN_USER_GROUP")) {
    define("ADMIN_USER_GROUP", 2);
}

if (!defined("DEFAULT_PER_PAGE")) {
    define("DEFAULT_PER_PAGE", 10);
}

if (!defined("DEFAULT_ORDER_DIRECTION")) {
    define("DEFAULT_ORDER_DIRECTION", "ASC");
}

if (!defined("DEFAULT_USER_ROLE")) {
    define("DEFAULT_USER_ROLE", 5);
}

if (!defined("MINIMAL_PASSWORD_LENGTH")) {
    define("MINIMAL_PASSWORD_LENGTH", 8);
}

?>
