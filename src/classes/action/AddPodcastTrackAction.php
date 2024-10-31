<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\PodcastTrackRenderer;
use iutnc\deefy\repository\DeefyRepository;

class AddPodcastTrackAction extends Action
{
    public function execute(): string
    {
        if (!isset($_SESSION['playlist'])) {
            return "<b>Pas de playlist en session</b>";
        }

        if ($this->http_method === 'GET') {
            $html = "<h1 class='subtitle'>Ajout d'une piste à la playlist</h1>";
            $html .= "<form method='post' action='?action=add-track' enctype='multipart/form-data'>";
            $html .= "<input type='file' name='userfile' accept='.mp3'><br/>";
            $html .= "<input type='text' name='titre' placeholder='Titre'><br/>";
            $html .= "<input type='text' name='auteur' placeholder='Auteur'><br/>";
            $html .= "<input type='text' name='genre' placeholder='Genre'><br/>";
            $html .= "<input type='number' name='duree' placeholder='Durée'><br/>";
            $html .= "<input type='date' name='date' placeholder='Date'><br/>";
            $html .= "<button type='submit'>Ajouter</button>";
            $html .= "</form>";
            return $html;
        } elseif ($this->http_method === 'POST') {
            $playlist = $_SESSION['playlist'];
            if (!is_int($playlist)) {
                return "<b>La playlist en session n'est pas une playlist</b>";
            }

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
            $repository->addTrackToPlaylist($track->getId(), $playlist);

            return "<b>Piste ajoutée à la playlist</b>";
        }

        return "<b>Méthode HTTP non gérée</b>";
    }
}