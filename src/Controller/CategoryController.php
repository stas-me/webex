<?php


namespace App\Controller;



use App\Entity\Article;
use App\Entity\Category;
use App\Form\CategoryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category")
     */
    public function home(EntityManagerInterface $em )
    {
        $repo = $em->getRepository(Category::class);
        $categories = $repo->findAll();


        return $this->render('Admin/Category/show.html.twig', [
            'categories' => $categories
        ]);
    }


    /**
     * @Route("/admin/category/add")
     */
    public function add(EntityManagerInterface $em, Request $request )
    {
        $form = $this->createForm(CategoryFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->addFlash('success', 'Category was created!');
            $em->persist($form->getData());
            $em->flush();
            return $this->redirectToRoute('app_category_home');
        }


        return $this->render('Admin/Category/add.html.twig', [
            'categoryForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/admin/category/delete/{id}")
     */
    public function delete($id){
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Category::class);
        $category = $repo->find($id);

        if( !isset($category) ){
            $this->addFlash('error', 'Category does not exist!');
        }else{
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'Category has been deleted!');

        }
        return $this->redirectToRoute('app_category_home');
    }

    /**
     * @Route("/admin/category/edit/{id}")
     */
    public function edit(EntityManagerInterface $em, Request $request, Category $cateory)
    {

        $form = $this->createForm(CategoryFormType::class, $cateory);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->addFlash('success', 'Order was edited!');
            $em->persist($form->getData());
            $em->flush();
            return $this->redirectToRoute('app_category_home');
        }

        return $this->render('Admin/Category/edit.html.twig', [
            'categoryForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/{id}", name="view_category")
     */
    public function viewCategory(EntityManagerInterface $em, $id, Request $request, PaginatorInterface $paginator  )
    {

        $categoryRepo = $em->getRepository(Category::class);
        $category = $categoryRepo->find($id);

        if( !$category ){
            $this->addFlash('error', 'Category does not exist!');
            return $this->redirectToRoute('app_home_home');
        }

        $articleRepo = $em->getRepository(Article::class);
        $articlesQB = $articleRepo->getQBfindByCategory( $category );

        $categories = $categoryRepo->findAll();

        $articles = $paginator->paginate(
            $articlesQB,
            $request->query->getInt('page', 1),
            2
        );
//        dump($articles);
//        dd($category);

        return $this->render('Category/show.html.twig', [
            'categories' => $categories,
            'articles' => $articles
        ]);
    }
}