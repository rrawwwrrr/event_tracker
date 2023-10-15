<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

defined('MOODLE_INTERNAL') || die();

class local_event_tracker {

    public static function handle_event(core\event\base $event) {
        // Обработка события
        // Например, отправка данных о событии в RabbitMQ

        // Пример отправки данных в RabbitMQ
        $message = json_encode($event->get_data()); // Преобразование данных события в JSON
        $rabbitmq_host = 'localhost'; // Хост RabbitMQ
        $rabbitmq_port = 5672; // Порт RabbitMQ
        $rabbitmq_user = 'guest'; // Пользователь RabbitMQ
        $rabbitmq_pass = 'guest'; // Пароль RabbitMQ
        $exchange = 'events'; // Имя обмена RabbitMQ

        $connection = new AMQPStreamConnection($rabbitmq_host, $rabbitmq_port, $rabbitmq_user, $rabbitmq_pass);
        $channel = $connection->channel();

        $channel->exchange_declare($exchange, 'fanout', false, true, false);

        $channel->basic_publish(new AMQPMessage($message), $exchange);

        $channel->close();
        $connection->close();

        return true;
    }


    public static function subscribe_to_all_events() {
        $eventnames = event_get_all_event_names();
        echo
        foreach ($eventnames as $eventname) {
            events_subscribe($eventname, 'local_event_tracker', 'process_event');
        }
    }
}

// // Register the event handler for all events
// $all_events = ExternalEventManager::get_all_event_names();
// foreach ($all_events as $event) {
//     ExternalEventManager::registerEventHandler($event, '\local_event_tracker', 'handle_event');
// }