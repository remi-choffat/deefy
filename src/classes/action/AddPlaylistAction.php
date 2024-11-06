<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class AddPlaylistAction extends Action
{
    public function execute(): string
    {
        if ($this->http_method === 'GET') {
            $html = "<h1 class='subtitle'>Création d'une playlist</h1>";
            $html .= "<form method='post' action='?action=add-playlist'>";
            $html .= "<div class='field'>";
            $html .= "<label class='label' for='nom'>Nom de la playlist</label>";
            $html .= "<div class='control'>";
            $html .= "<input class='input' type='text' id='nom' name='nom' required>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class='field'>";
            $html .= "<div class='control'>";
            $html .= "<button class='button is-link' type='submit'>Créer la playlist</button>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</form>";
        } elseif ($this->http_method === 'POST') {
            $nom = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
            $playlist = new Playlist($nom);
            // Enregistre la playlist dans la base de données
            $playlist = DeefyRepository::getInstance()->saveEmptyPlaylist($playlist);
            $_SESSION['playlist'] = $playlist;
            $html = "<h2 class='subtitle'>Playlist <i>$nom</i> créée</h2><br/>";
            $html .= (new AudioListRenderer($playlist))->render(0);
            $html .= "<button class='button' onclick='window.location.href=\"?action=add-track\"'>Ajouter une piste</button>";
        } else {
            $html = "Méthode incorrecte";
        }
        return $html;
    }
}