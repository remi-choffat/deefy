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
            '%s:host=%s;dbname=%s;charset=utf8',
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


    // Récupère la liste des playlists
    public function getPlaylist($playlist): array
    {
        $stmt = $this->pdo->query('SELECT * FROM playlist WHERE id = ' . $playlist);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crée une playlist vide
    public function saveEmptyPlaylist(Playlist $playlist): Playlist
    {
        $stmt = $this->pdo->prepare('INSERT INTO playlist (nom) VALUES (:nom)');
        $stmt->execute(['nom' => $playlist->nom]);
        $playlist->setId($this->pdo->lastInsertId());
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

}