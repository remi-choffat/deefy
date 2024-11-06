<?php

namespace iutnc\deefy\action;

class DefaultAction extends Action
{
    public function execute(): string
    {

        $message = "Bienvenue ! 🕺";

        // Récupère le nom de l'utilisateur connecté
        $user = $_SESSION['user'] ?? null;
        $username = $user ? htmlspecialchars($user['email']) : 'inconnu';

        // Si l'utilisateur est connecté, affiche un message de bienvenue
        if ($user) {
            $message = "Bienvenue, $username ! 🕺";
        }

        return "<h2 class='subtitle'>$message</h2>";
    }
}