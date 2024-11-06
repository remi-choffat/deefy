<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\repository\DeefyRepository;

class AddPodcastTrackAction extends Action
{
    public function execute(): string
    {

        if ($this->http_method === 'GET') {

            if (!isset($_SESSION['user'])) {
                return '<b>Vous devez être connecté pour ajouter un podcast.</b><br/><br/><button class="button" onclick="window.location.href=\'?action=signin\'">Se connecter</button>';
            }

            $userId = $_SESSION['user']['id'];
            $playlists = DeefyRepository::getInstance()->getUserPlaylists($userId);
            if (isset($_SESSION['playlist'])) {
                $currentPlaylistId = $_SESSION['playlist']->getId();
            } elseif (count($playlists) > 0) {
                $currentPlaylistId = $playlists[0]['id'];
            } else {
                return "<b>Vous devez d'abord créer une playlist.</b><br/><br/><button class='button' onclick='window.location.href=\"?action=add-playlist\"'>Créer une playlist</button>";
            }

            $playlistOptions = '';
            foreach ($playlists as $playlist) {
                $selected = $playlist['id'] == $currentPlaylistId ? 'selected' : '';
                $playlistOptions .= "<option value=\"{$playlist['id']}\" $selected>{$playlist['nom']}</option>";
            }

            $html = "<h1 class='subtitle'>Ajout d'un podcast à la playlist</h1>";
            $html .= "<form method='post' action='?action=add-track' enctype='multipart/form-data'>";
            $html .= "<div class='field'>";
            $html .= "<label class='label' for='playlist_id'>Playlist</label>";
            $html .= "<div class='control'>";
            $html .= "<div class='select'>";
            $html .= "<select id='playlist_id' name='playlist_id' required>";
            $html .= $playlistOptions;
            $html .= "</select>";

            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class='field'>";
            $html .= "<label class='
            label' for='titre'>Titre</label>";
            $html .= "<div class='control'>";

            $html .= "<input class='input' type='text' id='titre' name='titre' required>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class='field'>";
            $html .= "<label class='label' for='genre'>Genre</label>";
            $html .= "<div class='control'>";
            $html .= "<input class='input' type='text' id='genre' name='genre' required>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class='field'>";
            $html .= "<label class='label' for='duree'>Durée (en secondes)</label> ";

            $html .= "<div class='control'>";

            $html .= "<input class='input' type='number' id='duree' name='duree' required>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class='field'>";
            $html .= "<label class='label' for='auteur'>Auteur</label>";
            $html .= "<div class='control'>";
            $html .= "<input class='input' type='text' id='auteur' name='auteur' required>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class='field'>";
            $html .= "<label class='label' for='date'>Date</label>";
            $html .= "<div class='control'>";
            $html .= "<input class='input' type='date' id='date' name='date' required>";


            $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class='field'>";
            $html .= "<label class='label' for='userfile'>Fichier MP3</label>";
            $html .= "<div class='control'>";
            $html .= "<input class='input' type='file' id='userfile' name='userfile' required accept='audio/mp3'>";
            $html .= "</div>";
            $html .= "</div>";
            $html .= "<div class='field'>";
            $html .= "<div class='control'>";
            $html .= "<button class='button is-link' type='submit'>Ajouter le podcast</button>";
            $html .= "</div>";
            $html .= "</div>";

            $html .= "</form>";
            return $html;
        } elseif ($this->http_method === 'POST') {

            $playlistId = $_POST['playlist_id'];

            $playlist = DeefyRepository::getInstance()->getPlaylist($playlistId, $_SESSION['user']['id']);

            $titre = filter_var($_POST['titre'], FILTER_SANITIZE_SPECIAL_CHARS);
            $genre = filter_var($_POST['genre'], FILTER_SANITIZE_SPECIAL_CHARS);
            $duree = filter_var($_POST['duree'], FILTER_SANITIZE_NUMBER_INT);
            $auteur = filter_var($_POST['auteur'], FILTER_SANITIZE_SPECIAL_CHARS);
            $date = filter_var($_POST['date'], FILTER_SANITIZE_SPECIAL_CHARS);

            // Vérification du fichier uploadé
            if (!str_ends_with($_FILES['userfile']['name'], '.mp3') || $_FILES['userfile']['type'] !== 'audio/mpeg') {
                return "<b>Le fichier doit être un fichier MP3</b>";
            }

            // Génération d'un nom de fichier aléatoire
            $newFilename = uniqid('audio_', true) . '.mp3';
            $uploadDir = __DIR__ . '/../../../resources/audio/';
            $uploadFile = $uploadDir . $newFilename;

            // Vérification et création du répertoire si nécessaire
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Déplacement du fichier uploadé
            if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile)) {
                return "<b>Erreur lors de l'upload du fichier</b>";
            }

            $track = new PodcastTrack($titre, $newFilename);
            $track->setGenre($genre);
            $track->setDuree($duree);
            $track->setAuteur($auteur);
            $date = new \DateTime($date);
            $track->setDate($date);

            $repository = DeefyRepository::getInstance();
            $track = $repository->savePodcastTrack($track);
            $repository->addTrackToPlaylist($track->getId(), $playlistId);

            // Met à jour la playlist en session
            $playlist->addTrack($track);
            $_SESSION['playlist'] = $playlist;

            // Redirige vers la page de la playlist
            header('Location: ?action=playlist');

            return "<b>Le podcast a été ajouté à la playlist 👍</b>";
        }

        return "<b>Méthode HTTP non gérée</b>";
    }
}