<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/show-articles", name="showArticles")
     */
    public function index()
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
        return $this->render('article/index.html.twig',[
            'articles'  => $articles
        ]);
    }

    /**
     * @Route("/create-article", name="createArticle")
     */
    public function createArticle(Request $request)
    {
        $article =  new Article();
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $article = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', 'Article successfully created !');
            return $this->redirectToRoute('showArticles');
        }

        return $this->render('article/edit.html.twig', [
            'form'  =>$form->createView()
        ]);
    }

    /**
     * @Route("view-article/{id}", name="viewArticle")
     */
    public function viewArticle($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        if (!$article) {
            $this->addFlash('danger', 'There are no articles with the following id: ' . $id);
            return $this->redirectToRoute('showArticles');
        }

        return $this->render('article/view.html.twig', [
            'article'   => $article
        ]);
    }

    /**
     * @Route("edit-article/{id}", name="editArticle")
     */
    public function updateArticle(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->find($id);

        if (!$article) {
            $this->addFlash('danger', 'There are no articles with the following id: ' . $id);
            return $this->redirectToRoute('showArticles');
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            //$article = $form->getData();
            $em->flush();
            return $this->redirect($this->generateUrl('viewArticle', [
                'id' => $id
            ]));
        }

        return $this->render('article/edit.html.twig', [
            'form'  => $form->createView()
        ]);
    }
}
