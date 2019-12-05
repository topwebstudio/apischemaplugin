<?php

namespace App\Controller;

use App\Services\IPN;

class DefaultController extends Controller {

    public function homepageAction() {
        return $this->render('default/homepage.html.twig');
    }

    public function adminHomepageAction(IPN $ipn) { 
        return $this->render('default/admin_homepage.html.twig');
    }

}
