<?php
/**
 * Contrôleur pour la page Contact
 */

class ContactController extends BaseController
{
    public function index()
    {
        $this->render('contact/index', [
            'title' => 'Nous contacter'
        ]);
    }
    
    public function send()
    {
        if ($this->request->getMethod() === 'POST') {
            $name = $this->request->getPost('name');
            $email = $this->request->getPost('email');
            $subject = $this->request->getPost('subject');
            $message = $this->request->getPost('message');
            
            // Validation basique
            if (empty($name) || empty($email) || empty($message)) {
                $_SESSION['error'] = 'Tous les champs sont obligatoires.';
                header('Location: /contact');
                exit;
            }
            
            // Simulation d'envoi d'email (à remplacer par un vrai système)
            $_SESSION['success'] = 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.';
            header('Location: /contact');
            exit;
        }
        
        header('Location: /contact');
        exit;
    }
}
?>

