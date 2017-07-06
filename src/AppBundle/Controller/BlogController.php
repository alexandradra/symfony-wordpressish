<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comments;
use AppBundle\Form\ArticleType;
use AppBundle\Form\CommentsType;
use AppBundle\Service\ExtractWithLink;
use Exception;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/{_locale}/blog")
 */
class BlogController extends Controller
{

    /**
     * @Route("/{page}", name="homepage_blog", defaults={"page":1}, requirements={"page": "\d+"})
     */
// Passe argument $page dans la requête
    public function indexAction(Request $request, $page, ExtractWithLink $extractWithLink)
    {
        $em = $this->getDoctrine()->getManager();
        $repArticles = $em->getRepository('AppBundle:Article');
//        $articles = $repArticles->getPublishedArticlesWithLeftJoinForIndex();
        $limit = 5;
        $offset = $limit * ($page - 1);
        $articles = $repArticles->getPublishedArticlesWithLeftJoinForIndexWithPagination($limit, $offset, $request->getLocale());
        $count = $articles->count();
        $totalPage = ceil($count / $limit);

//        foreach ($articles as $article) {
//            $article->setExtract($extractWithLink->getExtractWithLink($article));
//        }

        return $this->render('blog/index.html.twig', ['page' => $totalPage, 'articles' => $articles, 'active' => $page]);
    }

    /**
     * @Route("/create", name="create_blog",)
     * @security("has_role('ROLE_ADMIN')")
     */
//    AJOUTER
    public function createAction(Request $request)
    {
        //Même résultat qu'avec l'annotation "Security"
        //$this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'GET OUT B**** !');
        if ($request->getLocale() != $this->getParameter('locale')) {
          return $this->redirectToRoute('create_blog', [ '_locale' => $this->getParameter('locale')]);
        }

        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
//        Traitement du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $article->setUser($user);

            $session = $this->get('session');

            $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
            $image = $article->getImage();

            if ($request->files->get($form->getName()) ['image']['url']) {
                $image->setUrl($request->files->get($form->getName())['image']['url']);
                $uploadableManager->markEntityToUpload($image, $image->getUrl());
            }
            $em->persist($article);
            try {
                $em->flush();
                $session->getFlashBag()->add('success', 'Post published ! 🤗');
                return $this->redirectToRoute('detail_blog', ['slug' => $article->getSlug()]);
            } catch (Exception $e) {
                $session->getFlashBag()->add('error', 'Oops, something went wrong ! 😭');
            };
        };


//        $article->setTitle('Random post')
//                ->setContent("I'm a very interesting content");
//
//        $image = new \AppBundle\Entity\Image();
//        $image->setUrl('https://robohash.org/' . rand() . '.png?set=set2')
//                ->setAlt("The IMAGE from space");
//
//        $comment = new \AppBundle\Entity\Comments();
//        $comment->setContent('This is the worst post ever, your blog is so poor !');
//        $comment2 = new \AppBundle\Entity\Comments();
//        $comment2->setContent('Fuck haters, your blog is great !');
//
//        $tag = new \AppBundle\Entity\();
//        $tag->setTitle('Unicorn');
//        $tag2 = new \AppBundle\Entity\Tag();
//        $tag2->setTitle('Chats qui puent');
//
//        $article->setImage($image);
//        $comment->setArticle($article);
//        $comment2->setArticle($article);
//        $article->addTag($tag);
//        $article->addTag($tag2);
//
//        $doctrine = $this->getDoctrine();
//        $em = $doctrine->getManager();
//        $em->persist($article);
//        $em->persist($comment);
//        $em->persist($comment2);
//        $em->flush();
//        return $this->redirectToRoute('detail_blog', ['id' => $article->getId()]);
        return $this->render('blog/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Method({"POST"})
     * @Route("/addBlogComment/{slug}", name="add_blog_comment", requirements={"slug": "[a-zA-Z0-9\-_\/]+"})
     */
    public function addCommentAction(Request $request, Article $article)
    {
        $comment = new Comments();
        $comment->setArticle($article);
        $form = $this->createForm(CommentsType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $session = $this->get('session');
            try {
                $em->flush();
                $session->getFlashBag()->add('success', 'Your comment is posted ! 🤗');
                return $this->redirectToRoute('detail_blog', ['slug' => $article->getSlug()]);
            } catch (\Exception $e) {
                $session->getFlashBag()->add('error', 'Oops, something went wrong ! 😭');
            };
        }
    }

    /**
     *
     * @Route("/ajax_comment_blog", name="ajax_comment_blog")
     */
    public function addAjaxCommentAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $content = $request->request->get('content');

            $em = $this->getDoctrine()->getManager();
            $ar = $em->getRepository('AppBundle:Article');
            $article = $ar->find($id);
            $user = $this->getUser();
            $comment = new Comments();
            $comment->setArticle($article);
            $comment->setContent($content);
            $comment->setUser($user);
            $em->persist($comment);
            try {
                $em->flush();
                return new JsonResponse(['success' => true,
                    'comment' => [
                        'id' => $comment->getId(),
                        'user' => $user->getUsername(),
                        'content' => $comment->getContent(),
                        'date' => $comment->getDate()->format('d-m-Y')
                ]]);
            } catch (\Exception $e) {
                return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
            }
        } else {
            throw new HttpException(403);
        }
    }

    /**
     * @Route("/read/{slug}", name="detail_blog", requirements={"slug": "[a-zA-Z0-9\-_\/]+"})
     */
//DETAILACTION
    public function readAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $repArticles = $em->getRepository('AppBundle:Article');
        $article = $repArticles->getArticleBySlugWithLeftJoin($slug);
        $comments = new Comments();
        $comments->setArticle($article);
        $form = $this->createForm(CommentsType::class, $comments, ['action' => $this->generateUrl('add_blog_comment', ['slug' => $slug])]);

        return $this->render('blog/read.html.twig', ['article' => $article, 'form' => $form->createView()]);
    }

    /**
     * @Route("/update/{slug}", name="edit_blog", requirements={"slug": "[a-zA-Z0-9\-_\/]+"})
     * @security("has_role('ROLE_SUPER_ADMIN') or user == article.getUser()")
     *
     */
    public function updateAction(Request $request, Article $article)
    {
        $em = $this->getDoctrine()->getManager();
        //$article = $em->getRepository('AppBundle:Article')->find($id);
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $this->get('session');
            $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
            $image = $article->getImage();
            $url = $request->files->get($form->getName())['image']['url'];

            if ($url) {
                if (is_file($image->getUrl())) {
                    unlink($image->getUrl());
                }
                $image->setUrl($url);
                $uploadableManager->markEntityToUpload($image, $image->getUrl());
            }

            try {
                $em->flush();
                $session->getFlashBag()->add('success', 'Post published ! 🤗');
                return $this->redirectToRoute('detail_blog', ['slug' => $article->getSlug()]);
            } catch (Exception $e) {
                $session->getFlashBag()->add('error', 'Oops, something went wrong ! 😭');
            };
        };

        return $this->render('blog/update.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delete/{id}", name="delete_blog", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('AppBundle:Article')->find($id);
        $article->getImage()->getUrl();
        $em->remove($article);
        $em->flush();

        $session = $this->get('session');
        try {
            $em->flush();
            $session->getFlashBag()->add('success', 'Post removed ! 🤗');
            return $this->redirectToRoute('homepage_blog');
        } catch (Exception $e) {
            $session->getFlashBag()->add('error', 'Oops, something went wrong ! 😭');
            return $this->redirectToRoute('detail_blog', ['id' => $id]);
        };

        return $this->render('blog/delete.html.twig', ['id' => $id]);
    }

    public function footerAction(Request $request, $nb)
    {
        $em = $this->getDoctrine()->getManager();
        $repArticles = $em->getRepository('AppBundle:Article');
        $articles = $repArticles->findBy(['publication' => true], ['date' => 'DESC', 'title' => 'ASC'], $nb);
        return $this->render('blog/footer.html.twig', ['articles' => $articles]);
    }

    public function yearsArchiveAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repArticles = $em->getRepository('AppBundle:Article');
        $years = $repArticles->getYears();
        return $this->render('blog/years_archive.html.twig', ['years' => $years]);
    }

    /**
     * @Route(path="/archive/{year}", name="archive_blog", requirements={"year":"\d+"})
     */
    public function archiveAction($year)
    {
        $em = $this->getDoctrine()->getManager();
        $repArticles = $em->getRepository('AppBundle:Article');
        $articles = $repArticles->getArticlesByYear($year);
        return $this->render('blog/archive.html.twig', ['articles' => $articles, 'year' => $year]);
    }

    /**
     * @Route("/tag/{id}", name="tag_blog", requirements={"id": "\d+"})
     */
    public function tagAction(Request $request, $id, Paginator $pagination)
    {
        $em = $this->getDoctrine()->getManager();
        $repTags = $em->getRepository('AppBundle:Tag');
        $repArticles = $em->getRepository('AppBundle:Article');

        $tag = $repTags->find($id);
        $query = $repArticles->getPublishedArticlesWithLeftJoinByTag($tag);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 5);
        $pagination->setPageRange(7);


        return $this->render('blog/tag.html.twig', [
                    'tag' => $tag,
                    'pagination' => $pagination]);
    }

    /**
     * @Route("/translation", name="translation_blog")
     */
    public function translateAction()
    {
      $message = $this->get('translator')->trans('message');
      $date = new \DateTime;
      return $this->render('blog/translation.html.twig', ['messageController' => $message, 'date' => $date]);
    }
}
