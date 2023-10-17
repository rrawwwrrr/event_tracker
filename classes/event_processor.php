<?php

namespace local_event_tracker;
// use PhpAmqpLib\Connection\AMQPStreamConnection;
// use PhpAmqpLib\Message\AMQPMessage;

defined('MOODLE_INTERNAL') || die();

class event_processor {

    public static function handle_event(\core\event\base $event) {
        // Пример отправки данных в RabbitMQ
        $message = json_encode($event->get_data()); // Преобразование данных события в JSON
        self::send_rest($message);

        return true;
    }


    public static function send_amqp($message){
        $rabbitmq_host = 'rabbitmq.devops-tools'; // Хост RabbitMQ
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
    }

    public static function send_rest($message){
        $exchangeName = 'moodle';
        $queueName = 'q.moodle';
        $username = 'guest';
        $password = 'guest';

        // Создаем соединение с RabbitMQ REST API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://rabbit.k8s.rnd.lanit.ru/api/exchanges/%2F/$exchangeName/publish");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $body = json_encode([
            'properties' => (object) [],
            'routing_key' => $queueName,
            'payload' => $message,
            'payload_encoding' => 'string'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");

        // Отправляем запрос на создание сообщения
        $result = curl_exec($ch);
        curl_close($ch);

        if ($result === false) {
            echo 'Ошибка при создании сообщения: ' . curl_error($ch);
        } else {
            echo $body;
            echo 'Сообщение успешно создано!';
        }
    }
}
