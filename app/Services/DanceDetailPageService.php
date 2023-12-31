<?php
require __DIR__ . '/../Repositories/DanceDetailPageRepository.php';

class DanceDetailPageService
{
    private $danceDetailPageRepository; 

    //ctor
    public function __construct() {
        $this->danceDetailPageRepository = new DanceDetalPageRepository(); 
    }

    public function getDanceEventsByArtistId($artist_id){
        $danceEvents = $this->danceDetailPageRepository->getDanceEventsByArtistIdFromDatabase($artist_id);
        return $danceEvents;
    }

    public function getAllCareerHighlights($artist_id){
        $careerHighlights = $this->danceDetailPageRepository->getAllArtistCareerHighlightsFromDatabase($artist_id);
        return $careerHighlights;
    }

    public function getAllArtistAlbums($artist_id){
        $albums = $this->danceDetailPageRepository->getAllArtistAlbumsFromDatabase($artist_id);
        return $albums;
    }
    public function getAllArtistSongs($artist_id){
        $songs = $this->danceDetailPageRepository->getAllArtistSongsFromDatabase($artist_id);
        return $songs;
    }
}