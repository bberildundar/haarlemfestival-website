<?php
require __DIR__ . '/../Repositories/DanceRepository.php';

class DanceService
{   
    private $danceRepository; 

    //ctor
    public function __construct() {
        $this->danceRepository = new DanceRepository(); 
    }

    //ARTISTS 
    public function getAllArtists()
    {
        $artists = $this->danceRepository->getAllArtists();
        return $artists;
    }
    public function getAllArtistsWithoutMusicTypes(){
        $artists = $this->danceRepository->getAllArtistsWithoutMusicTypes();
        return $artists;
    }
    public function getArtistById($artist_id)
    {
        return $this->danceRepository->getArtistById($artist_id);    
    }
    public function insertArtist($newArtist){
        return $artistId = $this->danceRepository->insertNewArtist($newArtist);
    }
    public function insertMusicTypeForArtist($newArtistId, $musicType){
        $this->danceRepository->insertMusicTypeForNewArtist($newArtistId, $musicType);
    }
    public function deleteArtist($artist){
        $this->danceRepository->deleteArtistFromDatabase($artist);
    }
    public function editArtist($oldArtist, $newArtist){
        $this->danceRepository->editArtistInDatabase($oldArtist, $newArtist);
    }
    public function editArtistMusicTypes($artist, $musicType){
        $this->danceRepository->editArtistMusicTypesInDatabase($artist, $musicType);
    }

    //MUSIC TYPES
    public function getAllMusicTypes()
    {
        $musicTypes = $this->danceRepository->getAllMusicTypes();
        return $musicTypes;
    }
    public function getMusicTypeById($id){
        return $this->danceRepository->getMusicTypesById($id);
    }
    public function insertMusicType($newMusicType){
        $this->danceRepository->insertNewMusicType($newMusicType);
    }
    public function getMusicTypesByArtist($artist){
        return $this->danceRepository->getMusicTypesByArtistFromDatabase($artist);
    }

    // DANCE LOCATIONS
    public function getAllDanceLocations()
    {
        $danceLocations = $this->danceRepository->getAllDanceLocations();
        return $danceLocations;
    }
    public function insertDanceLocation($newDanceLocation){
        $this->danceRepository->insertNewDanceLocation($newDanceLocation);
    }
    public function getDanceLocationById($location_id){
        return $this->danceRepository->getDanceLocationByIdFromDatabase($location_id);   
    }
    public function deleteDanceLocation($danceLocation){
        $this->danceRepository->deleteDanceLocationFromDatabase($danceLocation);
    }
    public function editDanceLocation($oldDanceLocation, $newDanceLocation){
        $this->danceRepository->editDanceLocationInDatabase($oldDanceLocation, $newDanceLocation);
    }

    // DANCE FLASHBACKS
    public function getAllDanceFlashbacks()
    {
        $danceFlashbacks = $this->danceRepository->getAllDanceFlashbacks();
        return $danceFlashbacks;
    }

    // DANCE EVENTS
    public function getAllDanceEvents()
    {
        $danceEvents = $this->danceRepository->getAllDanceEvents();
        return $danceEvents;
    }
    public function getEventById($event_id)
    {
        return $this->danceRepository->getEventByIdFromDatabase($event_id);    
    }
    public function getAllDanceSessions(){
        $danceSessions = $this->danceRepository->getAllSessionsFromDatabase();
        return $danceSessions;
    }
    public function insertDanceEvent($newEvent){
        return $eventId = $this->danceRepository->insertNewDanceEventToTheDatabase($newEvent);
    }
    public function insertPerformingArtistsToNewEvent($newEventId, $artist){
        $this->danceRepository->insertArtistsForNewEvent($newEventId, $artist);
    }
    public function deleteEvent($event){
        $this->danceRepository->deleteEventFromDatabase($event);
    }
    public function getArtistsByEvent($event){
        return $this->danceRepository->getArtistsByEventFromDatabase($event);
    }
    public function editEvent($oldEvent, $newEvent){
        $this->danceRepository->editEventInDatabase($oldEvent, $newEvent);
    }
    public function editEventArtists($event, $artist){
        $this->danceRepository->editEventArtistsInDatabase($event, $artist);
    }
}
?>