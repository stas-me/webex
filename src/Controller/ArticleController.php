<?php


namespace App\Controller;



use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleFormType;
use App\Form\CommentFormType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/admin/articles")
     */
    public function home(EntityManagerInterface $em )
    {
        $repo = $em->getRepository(Article::class);
        $articles = $repo->findAllPlusComments();


        return $this->render('Admin/Article/show.html.twig', [
            'articles' => $articles
        ]);
    }


    /**
     * @Route("/admin/article/add")
     */
    public function add(EntityManagerInterface $em, Request $request, FileUploader $fileUploader )
    {
        $form = $this->createForm(ArticleFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $article = $form->getData();
            $brochureFile = $form['picture']->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $article->setPictureFilename($brochureFileName);
            }

            $this->addFlash('success', 'Article was created!');
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('app_article_home');
        }


        return $this->render('Admin/Article/add.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/admin/article/{id}/delete")
     */
    public function delete($id){
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Article::class);
        $article = $repo->find($id);

        if( !isset($article) ){
            $this->addFlash('error', 'Article does not exist!');
        }else{
            $em->remove($article);
            $em->flush();
            $this->addFlash('success', 'Article has been deleted!');

        }
        return $this->redirectToRoute('app_article_home');
    }

    /**
     * @Route("/admin/article/{id}/edit")
     */
    public function edit(EntityManagerInterface $em, Request $request, Article $article, FileUploader $fileUploader)
    {

        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $article = $form->getData();
            $brochureFile = $form['picture']->getData();
            if ($brochureFile) {
                $brochureFileName = $fileUploader->upload($brochureFile);
                $article->setPictureFilename($brochureFileName);
            }

            $this->addFlash('success', 'Article was edited!');
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('app_article_home');
        }

        return $this->render('Admin/Article/edit.html.twig', [
            'articleForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/{id}", name="view_article")
     */
    public function viewArticle(EntityManagerInterface $em, $id, Request $request )
    {

        $categoryRepo = $em->getRepository(Category::class);
        $categories = $categoryRepo->findAll();

        $articleRepo = $em->getRepository(Article::class);
        $article = $articleRepo->find($id);

        if( !$article ){
            $this->addFlash('error', 'Article does not exist!');
            return $this->redirectToRoute('app_home_home');
        }
//        dd($article);

        $form = $this->createForm(CommentFormType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $comment = $form->getData();
            $comment->setArticle($article);

            $this->addFlash('success', 'Comment has been added!');
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('view_article', ['id' => $id]);
        }

        return $this->render('Article/show.html.twig', [
//            'articleForm' => $form->createView(),
            'article' => $article,
            'categories' => $categories,
            'commentForm' => $form->createView(),
        ]);
    }
}