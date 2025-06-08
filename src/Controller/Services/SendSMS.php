<?php

namespace App\Controller\Services;


use Infobip\Api\SmsApi;
use Infobip\Configuration;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;
use Symfony\Component\Notifier\Bridge\Infobip\InfobipTransport;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Throwable;

class SendSMS



{

    public function messagerie($RECIPIENT, $MESSAGE_TEXT ){
        $BASE_URL = "https://n88nw5.api.infobip.com";
        $API_KEY = "e34acfef806da70eb284df6d0bf0c408-6da0e29c-2046-49b1-ae4d-dc24b89e4050";

        $SENDER = "SNVLT";
        /*$RECIPIENT = "2250778648776";
        $MESSAGE_TEXT = "This is a sample message";*/

        $configuration = new Configuration(host: $BASE_URL, apiKey: $API_KEY);

        $sendSmsApi = new SmsApi(config: $configuration);

        $destination = new SmsDestination(
            to: $RECIPIENT
        );

        $message = new SmsTextualMessage(destinations: [$destination], from: $SENDER, text: $MESSAGE_TEXT);

        $request = new SmsAdvancedTextualRequest(messages: [$message]);

        try {
            $smsResponse = $sendSmsApi->sendSmsMessage($request);

            echo $smsResponse->getBulkId() . PHP_EOL;

            foreach ($smsResponse->getMessages() ?? [] as $message) {
                echo sprintf('Message ID: %s, status: %s', $message->getMessageId(), $message->getStatus()?->getName()) . PHP_EOL;
            }
        } catch (Throwable $apiException) {
            echo("HTTP Code: " . $apiException->getCode() . "\n");
        }
    }
}