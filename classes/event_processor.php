<?php

namespace event_tracker;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

defined('MOODLE_INTERNAL') || die();

class local_event_tracker {

    public static function handle_event(\core\event\base $event) {
        // Пример отправки данных в RabbitMQ
        $message = json_encode($event->get_data()); // Преобразование данных события в JSON
        $rabbitmq_host = 'rabbitmq-tcp.devops-tools'; // Хост RabbitMQ
        $rabbitmq_port = 5672; // Порт RabbitMQ
        $rabbitmq_user = 'guest'; // Пользователь RabbitMQ
        $rabbitmq_pass = 'guest'; // Пароль RabbitMQ
        $exchange = 'amqp.events'; // Имя обмена RabbitMQ

        $connection = new AMQPStreamConnection($rabbitmq_host, $rabbitmq_port, $rabbitmq_user, $rabbitmq_pass);
        $channel = $connection->channel();

        $channel->exchange_declare($exchange, 'fanout', false, true, false);

        $channel->basic_publish(new AMQPMessage($message), $exchange);

        $channel->close();
        $connection->close();

        return true;
    }
}
