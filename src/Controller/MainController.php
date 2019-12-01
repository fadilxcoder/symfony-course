<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        //return new Response('<h1>Welcome FX!</h1>');
        return $this->render('home/index.html.twig');
    }

    /**
     * @Route("/custom/{getName?}", name="custom")
     * @param Request $request
     * @return Response
     */
    public function custom(Request $request)
    {
        //dump($request->get('getName'));
        $getName = $request->get('getName');
        //return new Response('<h1>Welcome '.$name.'!</h1>');

        return $this->render('home/custom.html.twig', [
            'name'      => $getName
        ]);
    }

    // only returns a fragment of HTML
    public function _makeTweets($max)
    {
        $tweets = [
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas cursus mi at feugiat consectetur',
            'In tristique vel quam quis facilisis. Donec vel dui ac sem porttitor iaculis blandit sed odio.',
            'Ut mattis aliquam feugiat. Praesent libero massa, facilisis at enim ut, pretium feugiat nunc. Praesent finibus nulla id dui cursus, ac varius velit luctus.',
            'Praesent gravida elementum ex, nec rhoncus arcu tempor nec.',
            'Phasellus iaculis nibh fringilla justo gravida pharetra. Nullam congue pulvinar odio.',
            'Nunc pellentesque aliquam malesuada. Curabitur faucibus felis id odio pharetra, eu hendrerit metus malesuada.',
            'Ut mi est, sodales non congue eu, vehicula id nisl. Ut sed convallis ipsum.',
            'Donec a sapien vitae lacus varius pharetra eget in velit.',
            'Maecenas ullamcorper orci sit amet ultrices ornare.'
        ];
        shuffle($tweets);
        $randomTweets = array_slice($tweets, 0, $max);

        return $this->render('includes/_tweets.html.twig', [
            'name'      => 'Some fake tweets',
            'tweets'    => $randomTweets
        ]);
    }
}
