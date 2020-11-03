<?php

namespace App\Controller;

use App\Model\LanguageManager;
use App\Model\PostManager;
use App\Model\UserManager;

class MySpaceController extends AbstractController
{


    /**
     * Display My space page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function main($user)
    {
        $theUser = new UserManager();
        $theUser = $theUser->userInfos($user);
        $languageManager = new LanguageManager();
        $languageManager = $languageManager->selectAll();
        $allMyFavorites = new PostManager();
        $allMyFavorites = $allMyFavorites->selectAllMyFavorites($user);
        $allMyPosts = new PostManager();
        $allMyPosts = $allMyPosts->selectAllMyPosts($user);
        return $this->twig->render('MySpace/myspacepage.html.twig', [
            'languages' => $languageManager,
            'favorites' => $allMyFavorites,
            'myposts' => $allMyPosts,
            'user' => $theUser
        ]);
    }
}