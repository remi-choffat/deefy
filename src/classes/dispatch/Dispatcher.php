<?php

declare(strict_types=1);

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddPodcastTrackAction;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\SigninAction;

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
            case 'default':
                $action = new DefaultAction();
                $html = $action->execute();
                break;
            case 'playlist':
                $action = new DisplayPlaylistAction();
                $html = $action->execute();
                break;
            case 'add-playlist':
                $action = new AddPlaylistAction();
                $html = $action->execute();
                break;
            case 'add-track':
                $action = new AddPodcastTrackAction();
                $html = $action->execute();
                break;
            case 'signin':
                $action = new SigninAction();
                $html = $action->execute();
                break;
            default:
                $html = 'Action inconnue 🤷‍♂️';
        }
        $this->renderPage($html);
    }


    /**
     * Affiche la page HTML
     */
    private function renderPage($html): void
    {
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
                <li><a href='?action=default'>Par défaut</a></li>
                <li><a href='?action=playlist'>Voir la playlist en session</a></li>
                <li><a href='?action=add-playlist'>Ajouter une playlist</a></li>
                <li><a href='?action=add-track'>Ajouter un podcast</a></li>
                <li><a href='?action=signin'>Se connecter</a></li>
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