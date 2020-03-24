<?php

namespace Acme\Entity\Subscriber;

/**
 * Class SubscriberDTO
 * @package Acme\Entity\Subscriber
 */
class SubscriberDTO
{
    /** @var int */
    public $id;

    /** @var string */
    public $email;

    /** @var bool */
    public $active;

    /**
     * @param array $arr
     * @return SubscriberDTO
     */
    public static function fromArray(array $arr): SubscriberDTO {
        $subscriber = new SubscriberDTO();

        if (isset($arr['id'])) {
            $subscriber->id = $arr['id'];
        }
        if (isset($arr['email'])) {
            $subscriber->email = $arr['email'];
        }
        if (isset($arr['active'])) {
            $subscriber->active = !!$arr['active'];
        }

        return $subscriber;
    }
}