<?php
/**
 * Contrôleur pour la page À propos
 */

class AboutController extends BaseController
{
    public function index()
    {
        $this->render('about/index', [
            'title' => 'À propos de ProActiv'
        ]);
    }
}
?>

