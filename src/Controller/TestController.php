<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestController extends AbstractController
{
    private $hub;
    
    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }
    
    public function mercureTestAction(): Response
    {
        // Publier une mise à jour Mercure
        $update = new Update(
            'https://example.com/my-channel', // URL du canal Mercure
            json_encode(['message' => 'Test message'])
        );
        
        $this->hub->publish($update);
        
        return new Response('Test message publié via Mercure.');
    }
}
