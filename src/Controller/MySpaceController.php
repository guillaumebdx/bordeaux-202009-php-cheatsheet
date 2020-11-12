<?php

namespace App\Controller;

use App\Model\FavoriteManager;
use App\Model\LanguageManager;
use App\Model\PostManager;
use App\Model\UserManager;
use App\Service\FormValidator;

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
        if (empty($_SESSION['userid'])) {
            header('location: /');
        } else {
            $favoriteManager = new FavoriteManager();
            $favorites = $favoriteManager->selectAll();
            $theUser = new UserManager();
            $theUser = $theUser->userInfos($user);
            $languageManager = new LanguageManager();
            $languageManager = $languageManager->selectAll();
            $allMyFavorites = new PostManager();
            $allMyFavorites = $allMyFavorites->selectAllMyFavorites($user);
            $allMyPosts = new PostManager();
            $allMyPosts = $allMyPosts->selectAllMyPosts($user);
            if (($_SERVER["REQUEST_METHOD"] === "POST")) {
                $thePost = new PostManager();
                $user = $theUser['id'];
                $title = $_POST['newPostTitle'];
                $content = $_POST['newPostContent'];
                $language = $_POST['newPostLanguage'];
                $thePost->createPost($user, $title, $content, $language);
            }
            $_SESSION['userid'] = $theUser['id'];
            if (isset($_SESSION['userid'])) {
                $postManager = new PostManager();
                $likesAndDislikes = $postManager->selectAllLikesAndDislikesPerUser($_SESSION['userid']);
            } else {
                $likesAndDislikes = [];
            }
        }

        $this->twig->addGlobal('session', $_SESSION);

        return $this->twig->render('MySpace/myspacepage.html.twig', [
            'favorite' => $favorites,
            'languages' => $languageManager,
            'favorites' => $allMyFavorites,
            'myposts' => $allMyPosts,
            'user' => $theUser,
            'likesAndDislikes' => $likesAndDislikes
        ]);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userManager = new UserManager();
           /* $formValidator = new FormValidator();
            $formValidator->getFields($_POST);
            $formValidator->checkFields(); */

            if ($newUserData = $userManager->selectOneByName($_POST['name'])) {
                header('Location: /#registration');
                //TODO add error message "name already exists" in nav modal
            } else {
                $newUserData = [];
                $newUserData['name'] = $_POST['name'];
                $newUserData['email'] = $_POST['email'];
                $newUserData['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $userManager->createUser($newUserData);
                $userData = $userManager->selectOneByName($_POST['name']);
                $_SESSION['user'] = $userData;
                header('Location: /MySpace/main/' . $userData['id']);
            }
        } else {
            echo '404';
            //TODO add error messages abt password and email incorrect format
        }
    }

    public function check()
    {
        $userManager = new UserManager();
        if ($userData = $userManager->selectOneByName($_POST['name'])) {
            if (password_verify($_POST['password'], $userData['password'])) {
                $_SESSION['user'] = $userData;
                header('Location: /MySpace/main/' . $userData['id']);
            } else {
                header('Location: /#login');
            }
        } else {
            header('Location: /#login');
        }
    }

    public function logout()
    {
        session_destroy();
        session_unset();
        header('Location: /');
    }
}
