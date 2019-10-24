<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CronController extends AbstractController
{

    /**
     * @Route("/cron/week_top_viewed", name="week_top")
     */
    public function weekTop(EntityManagerInterface $em, \Swift_Mailer $mailer)
    {
        $repo = $em->getRepository(Article::class);
        $topArticles = $repo->getTopViewedForLastWeek();

        $message = (new \Swift_Message('Week\'s most viewed articles'))
            ->setFrom('from@somedomain.com')
            ->setTo('s.mekinulashvili@gmail.com')
            ->setBody(
                $this->renderView('Email/topArticleList.html.twig', [
                    'articles' => $topArticles,
                ]),
                'text/html'
            );
        $mailer->send($message);

//        dd($topArticles);
        return $this->render('Email/topArticleList.html.twig', [
            'articles' => $topArticles,
        ]);
    }
}
