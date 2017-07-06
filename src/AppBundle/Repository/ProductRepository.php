<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository {

    public function getProductsByCategory(Category $category) {
        $qb = $this->createQueryBuilder('p');
        $qb->leftJoin('p.image', 'i')
                ->addSelect('i')
                ->leftJoin('p.ratings', 'r')
                ->addSelect('r')
                ->leftJoin('p.category', 'c')
                ->addSelect('c')
                ->where('c = ?1')
                ->setParameter(1, $category)
                ->orderBy('p.date', 'DESC');
        $query = $qb->getQuery();
        $products = $query->getArrayResult();
        return $products;
    }

    public function getProductByIdWithLeftJoin($id) {
        $qb = $this->createQueryBuilder('p');
        //Fait tout seul la jointure avec l'id de l'élément image
        $qb->leftJoin('p.image', 'i')
                ->addSelect('i')
                ->leftJoin('p.category', 'c')
                ->addSelect('c')
                ->leftJoin('p.ratings', 'r')
                ->addSelect('r')
                ->where('p.id = ?1')
                ->setParameter(1, $id);
        $query = $qb->getQuery();
        return $query->getOneOrNullResult();
    }

    public function getCategoryCount(Category $category) {

        $qb = $this->createQueryBuilder("a");
        $qb->select('count(a)')
                ->leftJoin('a.category', 't')
                ->where('t = ?1')
                ->setParameter(1, $category)
        ;
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getLastProductsAjax($limit = 3) {
        $qb = $this->createQueryBuilder('p')
                ->orderBy('p.date', 'ASC')
                ->setMaxResults($limit);
        $query = $qb->getQuery();

        return $query->getResult();
    }

}
