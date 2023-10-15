<?php
$plugin->component = 'event_tracker';
$plugin->version = 2023101500; 
$plugin->requires = 2023101500; 
$plugin->maturity = MATURITY_STABLE; 
$plugin->release = '1.0.0';

$plugin->event_handlers = [
    'core\event\course_viewed' => [
        'handlerfile' => '/event/course_viewed_handler.php',
        'handlerfunction' => 'event_tracker_course_viewed_handler',
    ],
    'core\event\user_enrolment_created' => [
        'handlerfile' => '/event/user_enrolment_created_handler.php',
        'handlerfunction' => 'event_tracker_user_enrolment_created_handler',
    ],
];