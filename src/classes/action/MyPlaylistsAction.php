<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;

class MyPlaylistsAction extends Action
{
    public function execute(): string
    {
        if (!isset($_SESSION['user'])) {
            return '<b>Vous devez être connecté pour voir vos playlists.</b><br/><br/><button class="button" onclick="window.location.href=\'?action=signin\'">Se connecter</button>';
        }

        $userId = $_SESSION['user']['id'];
        $playlists = DeefyRepository::getInstance()->getUserPlaylists($userId);

        $html = '<h2 class="subtitle">Mes Playlists</h2><ul class="playlist-list">';
        foreach ($playlists as $playlist) {
            $html .= '<li><a href="?action=playlist&id=' . $playlist['id'] . '">' . htmlspecialchars($playlist['nom']) . '</a></li>';
        }
        $html .= '</ul>';

        return $html;
    }
}