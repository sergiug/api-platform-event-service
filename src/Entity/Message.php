<?php
namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     messenger=true,
 *     collectionOperations={
 *         "post"={"status"=202}
 *     },
 *     itemOperations={},
 *     output=false
 * )
 */
class Message
{
    /**
     * @var string Message title
     *
     * @Assert\NotBlank
     */
    public $title;

    public function getTitle(): string
    {
        return $this->title;
    }
}
