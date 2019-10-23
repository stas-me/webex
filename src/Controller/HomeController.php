<?php


namespace App\Controller;



use App\Entity\Category;
use App\Form\CategoryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function home(EntityManagerInterface $em )
    {
        $repo = $em->getRepository(Category::class);
        $categories = $repo->getCategoriesAndArticles();

//        dd($categories);


        return $this->render('show.html.twig', [
            'categories' => $categories
        ]);
    }

}