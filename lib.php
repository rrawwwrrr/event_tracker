<?php

use Moodle\ExternalEvent\ExternalEventManager;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

defined('MOODLE_INTERNAL') || die();

class local_event_tracker {
    /**
    * Event handler for course viewed event.
    *
    * @param core\event\course_viewed $event The course viewed event.
    */
    public static function course_viewed(core\event\base $event) {
        // Get the event data
        $event_data = $event->get_data();

        // Connect to RabbitMQ server
        $connection = new AMQPStreamConnection('rabbitmq.devops-tools', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        // Declare a queue in RabbitMQ
        $channel->queue_declare('event_queue', false, true, false, false);

        // Publish the event data to the queue in RabbitMQ
        $message = new AMQPMessage(json_encode($event_data));
        $channel->basic_publish($message, '', 'event_queue');

        // Close the connection to RabbitMQ
        $channel->close();
        $connection->close();
    }
}

// Register the event handler for all events
$all_events = ExternalEventManager::get_all_event_names();
foreach ($all_events as $event) {
    ExternalEventManager::registerEventHandler($event, '\local_event_tracker', 'handle_event');
}