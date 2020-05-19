<?php

declare(strict_types=1);

namespace SimPod\Kafka\Clients\Producer;

use RdKafka\Producer;
use const RD_KAFKA_PARTITION_UA;

class KafkaProducer extends Producer
{
    private const RD_KAFKA_MSG_F_COPY = 0;

    public function __construct(ProducerConfig $config)
    {
        parent::__construct($config->getConf());
    }

    public function produce(ProducerRecord $record) : void
    {
        $topic = $this->newTopic($record->topic);
        /** @psalm-suppress UndefinedMethod https://github.com/vimeo/psalm/issues/3406 */
        $topic->produce($record->partition ?? RD_KAFKA_PARTITION_UA, self::RD_KAFKA_MSG_F_COPY, $record->value, $record->key);
        $this->poll(0);
    }

    public function flush() : void
    {
        while ($this->getOutQLen() > 0) {
            $this->poll(1);
        }
    }
}
