<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Rating;
use AppBundle\Form\RatingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception as Exception2;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Route("/catalog", name="catalog")
 */
class CatalogController extends Controller {

    /**
     * @Route("/", name="homepage_catalog")
     */
    public function indexAction(Request $request) {
        $cm = $this->getDoctrine()->getManager()->getRepository('AppBundle:Category');
        $categories = $cm->findBy([], ['title' => 'ASC']);
        return $this->render('catalog/index.html.twig', ['categories' => $categories]);
    }

    /**
     * @Route("/category/{id}", name="catalog_category", requirements={"id": "\d+"})
     */
    public function categoryAction(Request $request, Category $category) {
        $pm = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product');
        $products = $pm->getProductsByCategory($category);

        //dump($products);
        $count = $pm->getCategoryCount($category);

        return $this->render('catalog/category.html.twig', ['category' => $category, 'products' => $products, 'count' => $count]);
    }

    /**
     * @Route("/detail/{id}", name="detail_catalog", requirements={"id": "\d+"})
     */
    public function detailAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $pr = $em->getRepository('AppBundle:Product');
        $product = $pr->getProductByIdWithLeftJoin($id);

        $rating = new Rating();
        $rating->setProduct($product);
        $form = $this->createForm(RatingType::class, $rating);

        $form->handleRequest($request);

        $session = $this->get('session');

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($rating);
            try {
                $em->flush();
                $session->getFlashBag()->add('success', 'You have rated this movie ! 🤗');
                return $this->redirectToRoute('detail_catalog', ['id' => $product->getId()]);
            } catch (Exception2 $e) {
                $session->getFlashBag()->add('error', 'Oops, something went wrong ! 😭');
            }
            return $this->redirectToRoute('rating_success');
        }


        return $this->render('catalog/read.html.twig', ['product' => $product, 'form' => $form->createView()]);
    }

    /**
     *
     * @Route("/ajax_rating_blog", name="ajax_rating_blog")
     */
    public function addAjaxRatingAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $id = $request->request->get('id');
            $value = $request->request->get('rating');

            $em = $this->getDoctrine()->getManager();
            $ar = $em->getRepository('AppBundle:Product');
            $product = $ar->find($id);
            $rating = new Rating();
            $rating->setProduct($product);
            $rating->setRating($value);
            $em->persist($rating);
            try {
                $em->flush();
                return new JsonResponse(['success' => true,
                    'rating' => [
                        'id' => $rating->getId(),
                        'rating' => $rating->getRating()
                ]]);
            } catch (\Exception $e) {
                return new JsonResponse(['success' => false, 'message' => $e]);
            }
        } else {
            throw new HttpException(403);
        }
    }

    /**
     *
     * @Route("/ajax_newbies", name="ajax_newbies")
     */
    public function ajaxNewbiesAction(Request $request) {
        if ($request->isXmlHttpRequest()) {
            $pr = $this->getDoctrine()->getManager()->getRepository('AppBundle:Product');
            $products = $pr->getLastProductsAjax();
            return $this->render('catalog/ajax_newbies.html.twig', ['products' => $products]);
        } else {
            throw new HttpException(403);
        }
    }

}
