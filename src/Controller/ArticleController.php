<?php


namespace App\Controller;



use App\Entity\Article;
use App\Form\ArticleFormType;
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
        $articles = $repo->findAll();


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
     * @Route("/admin/article/delete/{id}")
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
     * @Route("/admin/article/edit/{id}")
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
}