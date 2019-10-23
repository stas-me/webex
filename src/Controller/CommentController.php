<?php


namespace App\Controller;



use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\ArticleFormType;
use App\Form\CommentFormType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{

    /**
     * @Route("/admin/article/{articleId}/comment/{commentId}/delete", name="delete_comment")
     */
    public function delete($articleId, $commentId){
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Comment::class);
        $comment = $repo->find($commentId);

        if( !isset($comment) ){
            $this->addFlash('error', 'Comment does not exist!');
        }else{
            $em->remove($comment);
            $em->flush();
            $this->addFlash('success', 'Comment has been deleted!');

        }
        return $this->redirectToRoute('manage_comments', ['id' => $articleId]);
    }

    /**
     * @Route("/admin/article/{id}/comments", name="manage_comments")
     */
    public function manageComments(EntityManagerInterface $em, $id )
    {
        $articleRepo = $em->getRepository(Article::class);
        $article = $articleRepo->find($id);

        if( !$article ){
            $this->addFlash('error', 'Article does not exist!');
            return $this->redirectToRoute('app_article_home');
        }

        return $this->render('Admin/Comment/show.html.twig', [
            'article' => $article,
        ]);
    }
}