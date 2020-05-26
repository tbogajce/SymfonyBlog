<?php


namespace App\Controller;


use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikesController extends AbstractController
{
    /**
     * @Route("/like/{id}", name="likes_like")
     * @param MicroPost $microPost
     * @return JsonResponse
     */
    public function like(MicroPost $microPost) {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();

        if (!$currentUser instanceof User) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        // user is not able to like post where he is the owner
        if ($microPost->getUser()->getId() !== $currentUser->getId()) {
            $microPost->like($currentUser);
            $this->getDoctrine()->getManager()->flush();
        }



        return new JsonResponse([
            'count' => $microPost->getLikedBy()->count()
        ]);
    }

    /**
     * @Route("/unlike/{id}", name="likes_unlike")
     * @param MicroPost $microPost
     * @return JsonResponse
     */
    public function unlike(MicroPost $microPost) {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();

        if (!$currentUser instanceof User) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $microPost->getLikedBy()->removeElement($currentUser);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'count' => $microPost->getLikedBy()->count()
        ]);
    }
}