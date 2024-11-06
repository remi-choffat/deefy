<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;

class ViewPlaylistAction extends Action
{
    public function execute(): string
    {
        if (!isset($_SESSION['user'])) {
            return '<b>Vous devez être connecté pour voir cette playlist.</b><br/><br/><button class="button" onclick="window.location.href=\'?action=signin\'">Se connecter</button>';
        }

        $playlistId = $_GET['id'];
        $playlist = DeefyRepository::getInstance()->getPlaylist($playlistId);

        if (!$playlist) {
            return '<b>Playlist non trouvée.</b>';
        }

        $_SESSION['current_playlist'] = $playlist;

        $html = '<h2 class="subtitle">' . htmlspecialchars($playlist['nom']) . '</h2><ul>';
        foreach ($playlist['tracks'] as $track) {
            $html .= '<li>' . htmlspecialchars($track['titre']) . '</li>';
        }
        $html .= '</ul>';

        return $html;
    }
}