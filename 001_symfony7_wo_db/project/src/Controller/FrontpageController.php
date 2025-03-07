<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontpageController extends AbstractController
{
    #[Route('/', name: 'frontpage')]
    public function index(
        #[MapQueryParameter] string $firstName = '',
        #[MapQueryParameter] string $lastName = '',
        #[MapQueryParameter] string $busy = 'no',
    ): Response
    {
        if ($busy !== 'no') {
            $busy = '';
            for ($i = random_int(100, 1000); $i > 0; $i--) {
                $busy .= random_bytes(random_int(1, 5));
            }
            $max = random_int(1, 10000) === 1 ? 1000000 : 100000;
            usleep(random_int(10000, $max));
            for ($i = random_int(1000, 10000); $i > 0; $i--) {
                $busy .= random_bytes(random_int(1, 5));
            }
        }

        return $this->render(
            'frontpage.html.twig',
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'hrtime' => hrtime(true),
                'busy' => $busy === 'no' ? $busy : md5($busy),
            ]
        );
    }

    #[Route('/phpinfo', name: 'phpinfo')]
    public function phpinfo(): Response
    {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_contents();
        ob_clean();
        if(php_sapi_name() === 'cli') {
            return new Response("<pre>{$phpinfo}</pre>");
        }
        return new Response($phpinfo);
    }
}
