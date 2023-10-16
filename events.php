<?php

defined('MOODLE_INTERNAL') || die();

// List of observers.
$observers = array(
    array(
        'eventname'   => '*',
        'callback'    => 'local_event_tracker::handle_event',
    ),
);
