<?php

// api/src/Handler/ResetPasswordRequestHandler.php

namespace App\Handler;

use App\Entity\Message;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class MessageHandler implements MessageHandlerInterface
{
    public function __invoke(Message $message)
    {
        echo "Consumed message with title: " . $message->getTitle() . PHP_EOL;
    }
}
