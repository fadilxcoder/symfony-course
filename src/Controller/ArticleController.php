<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use App\Services\Mailing;
use App\Services\ObjectCompare;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/show-articles", name="showArticles")
     */
    public function index()
    {
//        $entityManager = $this->getDoctrine()->getManager();
//        $_user_dump = $entityManager->getRepository(User::class)->findUsersByRole(User::ROLE);
//        dump($_user_dump);
//        die;

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
            $article->setStatus(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($article); // use only when inserting into Database
            $em->flush();

            $this->addFlash('success', 'Article successfully created !');
            return new RedirectResponse($this->generateUrl('viewArticle', [
                'id' => $article->getId()
            ]));
        }

        return $this->render('article/edit.html.twig', [
            'form'  =>$form->createView()
        ]);
    }

    /**
     * @Route("/view-article/{id}", name="viewArticle")
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
            $em->flush();
            return $this->redirect($this->generateUrl('viewArticle', [
                'id' => $id
            ]));
        }

        return $this->render('article/edit.html.twig', [
            'form'  => $form->createView()
        ]);
    }

    /**
     * @Route("/edit-article/{article}", name="editArticle")
     */
    public function modifyArticle(Request $request, ? Article $article)
    {
        $entityManager = $this->getDoctrine()->getManager();

        if (!$article) {
            $this->addFlash('danger', 'There are no articles with the following id');
            return $this->redirectToRoute('showArticles');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAlias('symfony-'.rand(1,99999));
            $entityManager->flush();
            return $this->redirectToRoute('viewArticle', [
                'id' => $article->getId()
            ]);
        }

        return $this->render('article/edit.html.twig', [
            'form'  => $form->createView(),
            'article'   => $article
        ]);
    }

    /**
     * @Route("/ajax-handler/{articleData}", name="ajaxHandler")
     */
    public function ajaxHandler(Request $request, Article $articleData, ObjectCompare $objectCompare, Mailing $mailing)
    {
        if ($request->isXmlHttpRequest()) {
            $entityManager = $this->getDoctrine()->getManager();

            $orjObj = clone $articleData;

            $submittedArticle = $request->request->get('article');
            $status = $submittedArticle['status'];
            $articleData->setStatus($status);

            if ( $objectCompare->compareTwoObj($orjObj, $submittedArticle) == false) {
                $articleData->setStatus($status);
                $mailing->notifyChanges($articleData);
                $entityManager->flush();
                $this->addFlash(
                    'success',
                    'Article with ID : '.$articleData->getId().' status has been updated.'
                );

                $url = $this->generateUrl('viewArticle', [
                    'id'    => $articleData->getId()
                ]);
            } else {
                $this->addFlash(
                    'warning',
                    'No change in Article with ID : '.$articleData->getId()
                );
                $url = $this->generateUrl('viewArticle', [
                    'id'    => $articleData->getId()
                ]);
            }

            return $this->json([
                'url'   => $url
            ]);

        }
    }
}
