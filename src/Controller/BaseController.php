<?php


namespace App\Controller;


use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class BaseController
 * @package App\Controller
 */
abstract class BaseController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * BaseController constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(serializerInterface $serializer)
    {
        dump('test');
        $this->serializer = $serializer;
    }

    /**
     * @param $data
     * @param array $groups
     * @return string
     */
    public function serialize($data, array $groups=array()): string
    {
        $context = !empty($groups) ? SerializationContext::create()->setGroups($groups) : null;
        return $this->serializer->serialize($data, 'json', $context);
    }
}