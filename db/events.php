<?php

defined('MOODLE_INTERNAL') || die();

// List of observers.
$observers = array(
    array(
        'eventname'   => '*',
        'callback'    => '\event_tracker\local_event_tracker::handle_event',
    ),
);
