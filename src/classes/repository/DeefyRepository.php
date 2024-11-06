<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use PDO;
use PDOException;

class DeefyRepository
{
    private static array $config;
    private static ?DeefyRepository $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $dsn = sprintf(
            '%s:host=%s;dbname=%s;charset=utf8mb4',
            self::$config['driver'],
            self::$config['host'],
            self::$config['dbname']
        );

        try {
            $this->pdo = new PDO($dsn, self::$config['username'], self::$config['password']);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new \RuntimeException('Database connection error: ' . $e->getMessage());
        }
    }

    public static function setConfig(string $file): void
    {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException("Configuration file not found: $file");
        }

        self::$config = parse_ini_file($file);
        if (self::$config === false) {
            throw new \RuntimeException("Error parsing configuration file: $file");
        }
    }

    public static function getInstance(): DeefyRepository
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    // Récupère une playlist
    public function getPlaylist(int $playlistId, int $userId): ?Playlist
    {
        $stmt = $this->pdo->prepare('
        SELECT p.*
        FROM playlist p
        JOIN user2playlist u2p ON p.id = u2p.id_pl
        WHERE p.id = :playlist_id AND u2p.id_user = :user_id
    ');
        $stmt->execute(['playlist_id' => $playlistId, 'user_id' => $userId]);
        $playlistData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$playlistData) {
            return null;
        }

        $tracks = $this->getPlaylistTracks($playlistId);
        $playlist = new Playlist($playlistData['nom']);
        foreach ($tracks as $track) {
            $audioTrack = $this->createAudioTrack($track);
            $playlist->addTrack($audioTrack);
        }
        $playlist->setId($playlistData['id']);

        return $playlist;
    }

    public function getPlaylistTracks(int $playlistId): array
    {
        $stmt = $this->pdo->prepare('
        SELECT t.*
        FROM playlist2track p2t
        JOIN track t ON p2t.id_track = t.id
        WHERE p2t.id_pl = :playlist_id
    ');
        $stmt->execute(['playlist_id' => $playlistId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function createAudioTrack(array $trackData): PodcastTrack|AlbumTrack
    {
        if ($trackData['type'] === 'P') {
            $audioTrack = new PodcastTrack($trackData['titre'], $trackData['filename']);
            $audioTrack->setAuteur($trackData['auteur_podcast']);
            $audioTrack->setDate(new \DateTime($trackData['date_podcast']));
        } else {
            $audioTrack = new AlbumTrack($trackData['titre'], $trackData['filename'], $trackData['titre_album'], 0);
            $audioTrack->setArtiste($trackData['artiste_album']);
            $audioTrack->setAnnee($trackData['annee_album']);
        }
        $audioTrack->setDuree($trackData['duree']);
        $audioTrack->setGenre($trackData['genre']);

        return $audioTrack;
    }

    // Crée une playlist vide
    public function saveEmptyPlaylist(Playlist $playlist): Playlist
    {
        // Ajoute la playlist créée à la base de données
        $stmt = $this->pdo->prepare('INSERT INTO playlist (nom) VALUES (:nom)');
        $stmt->execute(['nom' => $playlist->nom]);
        $playlist->setId($this->pdo->lastInsertId());

        // Associe la playlist à l'utilisateur
        $idUser = $_SESSION['user']['id'];
        $stmt = $this->pdo->prepare('INSERT INTO user2playlist (id_user, id_pl) VALUES (:id_user, :id_pl)');
        $stmt->execute(['id_user' => $idUser, 'id_pl' => $playlist->getId()]);

        return $playlist;
    }

    // Enregistre une piste (Podcast)
    public function savePodcastTrack(PodcastTrack $track): PodcastTrack
    {
        $stmt = $this->pdo->prepare('INSERT INTO track (titre, filename, type, auteur_podcast, date_podcast, duree, genre) VALUES (:titre, :filename, :type, :auteur_podcast, :date_podcast, :duree, :genre)');
        $stmt->execute([
            'titre' => $track->titre,
            'filename' => $track->nomFichier,
            'type' => "P",
            'auteur_podcast' => $track->auteur,
            'date_podcast' => $track->date->format('Y-m-d'),
            'duree' => $track->duree,
            'genre' => $track->genre
        ]);
        $track->setId($this->pdo->lastInsertId());
        return $track;
    }

    // Enregistre une piste (Titre d'un album)
    public function saveAlbumTrack(AlbumTrack $track): AlbumTrack
    {
        $stmt = $this->pdo->prepare('INSERT INTO track (titre, filename, type, duree, genre, artiste_album, titre_album) VALUES (:titre, :filename, :type, :duree, :genre, :artiste_album, :titre_album)');
        $stmt->execute([
            'titre' => $track->titre,
            'filename' => $track->nomFichier,
            'type' => "A",
            'duree' => $track->duree,
            'genre' => $track->genre,
            'artiste_album' => $track->artiste,
            'titre_album' => $track->album
        ]);
        $track->setId($this->pdo->lastInsertId());
        return $track;
    }

    // Ajoute une piste existante à une playlist existante
    public function addTrackToPlaylist(int $track, int $playlist): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO playlist2track (id_pl, id_track) VALUES (:id_playlist, :id_track)');
        $stmt->execute([
            'id_playlist' => $playlist,
            'id_track' => $track
        ]);
    }

    // Connecte un utilisateur
    public function getUserByEmail($email)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM User WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupère les playlists d'un utilisateur
    public function getUserPlaylists(int $userId): array
    {
        $stmt = $this->pdo->prepare('
        SELECT p.* 
        FROM user2playlist u2p
        JOIN playlist p ON u2p.id_pl = p.id
        WHERE u2p.id_user = :user_id
    ');
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}