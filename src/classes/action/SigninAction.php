<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class SigninAction extends Action
{

    public function execute(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Display the login form
            return $this->displayForm();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle the login form submission
            return $this->handleFormSubmission();
        }
        return '';
    }

    private function displayForm(): string
    {
        return '<form method="POST" action="">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required><br/><br/>
                    <label for="passwd">Password:</label>
                    <input type="password" id="passwd" name="passwd" required><br/><br/>
                    <button class="button" type="submit">Login</button>
                </form>';
    }

    private function handleFormSubmission(): ?string
    {
        $email = $_POST['email'];
        $password = $_POST['passwd'];

        try {
            $user = AuthnProvider::signin($email, $password);
            $_SESSION['user'] = $user;

            // Retour à la page d'accueil
            header('Location: ?action=default');

        } catch (AuthnException $e) {
            return 'Erreur d\'authentification : ' . htmlspecialchars($e->getMessage());
        }
        return null;
    }

}