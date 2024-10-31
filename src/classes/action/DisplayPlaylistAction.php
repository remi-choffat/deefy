<?php

namespace iutnc\deefy\action;

use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\render\RendererFactory;
use iutnc\deefy\repository\DeefyRepository;

class DisplayPlaylistAction extends Action
{
    public function execute(): string
    {
        $html = "";
        if (!isset($_SESSION['playlist'])) {
            $html .= "Aucune playlist n'a été créée.";
        } else {
            $playlist = $_SESSION['playlist'];
            $playlist = DeefyRepository::getInstance()->getPlaylist($playlist);
            $playlist = $playlist[0];
            echo "<pre>";
            echo var_dump($playlist);
            echo "</pre>";
            $r = new AudioListRenderer($playlist);
            $html .= $r->render(Renderer::COMPACT);
        }
        return $html;
    }
}