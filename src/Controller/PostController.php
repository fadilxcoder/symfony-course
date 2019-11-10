<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post", name="post.")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(PostRepository $postRepository)
    {
        $posts = $postRepository->findAll();

        return $this->render('post/index.html.twig', [
            'posts'     => $posts
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function create(Request $request)
    {
        // create a new post with title
        //$post =  new Post();
        //$post->setTitle('This is my '.random_int(1,1000).' title !!');

        // entity manager
        //$em = $this->getDoctrine()->getManager();

        //$em->persist($post);
        //$em->flush();

        //return New Response('Post created');
        //return $this->redirect($this->generateUrl('post.index'));

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        $form->getErrors();
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post.index');
            //dump($post);
        }

        return $this->render('post/create.html.twig', [
            'form'      => $form->createView()
        ]);
    }

    /**
     * @Route("/show/{id}", name="show")
     * @param Post $post
     * @return Response
     */
    public function show(Post $post /* $id, PostRepository $postRepository */)
    {
        //$post =$postRepository->find($id);

        //dump($post);

        return $this->render('post/show.html.twig', [
            'post'  => $post
        ]);
    }

    /**
     * @Route("delete/{id}", name="delete")
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function remove(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        $this->addFlash('success', 'Your post was removed !');
        return $this->redirect($this->generateUrl('post.index'));
    }
}
