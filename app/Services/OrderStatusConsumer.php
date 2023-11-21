<?php

namespace App\Services;
use App\Mail\OrderConfirmation;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class OrderStatusConsumer
{
    public function consumePickup()
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASSWORD'), env('MQ_VHOST'));

        $channel = $connection->channel();

        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
            $data = json_decode(json_decode($msg->body, true));
            $toEmail = $data->email;
            $message = "Pesanan Anda dengan ID #" . $data->order_id . " telah di pickup.";
            // Kirim email
            \Mail::to($toEmail)->send(new OrderConfirmation($message));
            echo ' [x] Done', "\n";
        };

        $channel->queue_declare('notify_pickup', false, true, false, false);
        $channel->basic_consume('notify_pickup', '<notification-service>', false, true, false, false, $callback);
        echo 'Waiting for new message on notify_pickup', " \n";

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    public function consumeConfirm()
    {
        $connection = new AMQPStreamConnection(env('MQ_HOST'), env('MQ_PORT'), env('MQ_USER'), env('MQ_PASSWORD'), env('MQ_VHOST'));

        $channel = $connection->channel();

        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
            $data = json_decode(json_decode($msg->body, true));
            $toEmail = $data->email;
            $message = "Pesanan Anda dengan ID #" . $data->order_id . " telah di konfirmasi.";
            // Kirim email
            \Mail::to($toEmail)->send(new OrderConfirmation($message));
            echo ' [x] Done', "\n";
        };

        $channel->queue_declare('notify_confirm', false, true, false, false);
        $channel->basic_consume('notify_confirm', '<notification-service>', false, true, false, false, $callback);
        echo 'Waiting for new message on notify_confirm', " \n";

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}