<?php

declare(strict_types=1);

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddPodcastTrackAction;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\LogoutAction;
use iutnc\deefy\action\MyPlaylistsAction;
use iutnc\deefy\action\SigninAction;
use iutnc\deefy\action\ViewPlaylistAction;

class Dispatcher
{

    private ?string $action;

    function __construct(?string $action)
    {
        $this->action = $action;
    }


    /**
     * Exécute l'action demandée
     */
    public function run(): void
    {
        switch ($this->action) {
            case 'playlist':
                $action = new DisplayPlaylistAction();
                break;
            case 'add-playlist':
                $action = new AddPlaylistAction();
                break;
            case 'add-track':
                $action = new AddPodcastTrackAction();
                break;
            case 'signin':
                $action = new SigninAction();
                break;
            case 'logout':
                $action = new LogoutAction();
                break;
            case 'myPlaylists':
                $action = new MyPlaylistsAction();
                break;
            case 'viewPlaylist':
                $action = new ViewPlaylistAction();
                break;
            default:
                $action = new DefaultAction();
                break;
        }
        $html = $action->execute();
        $this->renderPage($html);
    }


    /**
     * Affiche la page HTML
     */
    private function renderPage($html): void
    {
        $user = $_SESSION['user'] ?? null;
        $authLink = $user ? '<li><a href="?action=logout">Se déconnecter</a></li>' : '<li><a href="?action=signin">Se connecter</a></li>';
        $page = <<<HTML
<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css'>
    <link rel='icon' type='image/png' href='resources/logo.png'>
    <link rel='stylesheet' type='text/css' href='resources/style.css'>
    <title>Deefy - Musique</title>
    </head>
    <body>
    <div class='header'>
       <h1 class="title">
            <img src='resources/logo.png' style='height: 40px;' alt='Deefy'/>
            Deefy
       </h1>
       <nav>
            <ul>
                <li><a href='?action=default'>Accueil</a></li>
                <li><a href='?action=playlist'>Voir la playlist en session</a></li>
                <li><a href='?action=add-playlist'>Ajouter une playlist</a></li>
                <li><a href='?action=add-track'>Ajouter un podcast</a></li>
                $authLink
                <li><a href="?action=myPlaylists">Mes Playlists</a></li>
            </ul>
        </nav>
    </div>
    <hr/>
    <br/>
    $html
    </body>
    </html>
HTML;

        echo $page;
    }

}