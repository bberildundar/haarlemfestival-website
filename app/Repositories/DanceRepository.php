<?php
require_once __DIR__ . '/repository.php';
require __DIR__ . '/../Models/ArtistModel.php';
require __DIR__ . '/../Models/MusicType.php';
require __DIR__ . '/../Models/DanceLocation.php';
require __DIR__ . '/../Models/DanceFlashback.php';
require __DIR__ . '/../Models/DanceEvent.php';
require __DIR__ . '/../Models/DanceSession.php';

class DanceRepository extends Repository{

    // ARTISTS
    public function getAllArtists() 
    {
        $sql = "SELECT da.dance_artist_id, da.dance_artist_name, GROUP_CONCAT(DISTINCT dmt.dance_musicType_name SEPARATOR ', ') AS dance_artistMusicTypes, da.dance_artist_hasDetailPage, da.dance_artist_imageUrl FROM dance_artist da
        JOIN dance_artistMusicType damt ON damt.dance_artistMusicType_artistId = da.dance_artist_id
        JOIN dance_musicType dmt ON dmt.dance_musicType_id = damt.dance_artistMusicType_musicTypeId
        GROUP BY da.dance_artist_id, da.dance_artist_name, da.dance_artist_hasDetailPage, da.dance_artist_imageUrl 
        ORDER BY da.dance_artist_hasDetailPage DESC;";
    
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute();
    
            $artists = $statement->fetchAll(PDO::FETCH_CLASS, 'ArtistModel');
            return $artists;
        } catch (PDOException $e) {
            error_log('Error retrieving all artists: ' . $e->getMessage());
            return [];
        }
    }
    public function getAllArtistsWithoutMusicTypes(){
        $sql = "SELECT dance_artist_id, dance_artist_name, dance_artist_hasDetailPage, dance_artist_imageUrl FROM dance_artist";
    
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute();
    
            $artists = $statement->fetchAll(PDO::FETCH_CLASS, 'ArtistModel');
            return $artists;
        } catch (PDOException $e) {
            error_log('Error retrieving all artists: ' . $e->getMessage());
            return [];
        }
    }

    public function getArtistById($artist_id) 
    {
        $sql = "SELECT da.dance_artist_id, da.dance_artist_name, GROUP_CONCAT(DISTINCT dmt.dance_musicType_name SEPARATOR ', ') AS dance_artistMusicTypes, da.dance_artist_hasDetailPage, da.dance_artist_imageUrl, da.dance_artist_detailPageBanner, da.dance_artist_subHeader, da.dance_artist_longDescription, da.dance_artist_longDescriptionPicture, da.dance_artist_detailPageSchedulePicture 
                FROM dance_artist da 
                JOIN dance_artistMusicType damt ON damt.dance_artistMusicType_artistId = da.dance_artist_id 
                JOIN dance_musicType dmt ON dmt.dance_musicType_id = damt.dance_artistMusicType_musicTypeId 
                WHERE da.dance_artist_id = :artist_id 
                GROUP BY da.dance_artist_id, da.dance_artist_name, da.dance_artist_hasDetailPage, da.dance_artist_imageUrl";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindParam(':artist_id', $artist_id, PDO::PARAM_INT);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS, 'ArtistModel');
            $artist = $statement->fetch();
            return $artist;
        } catch (PDOException $e) {
            error_log('Error retrieving artist with id ' . $artist_id . ': ' . $e->getMessage());
            return null;
        }
    }
    
    
    public function insertNewArtist($newArtist){
        $sql = "INSERT INTO `dance_artist`(`dance_artist_name`, `dance_artist_hasDetailPage`, `dance_artist_imageUrl`) VALUES (?, ?, ?)"; 
        try {
            $statement = $this ->connection->prepare($sql);            
            $statement->execute(array(
                htmlspecialchars($newArtist->getName()),
                (int) $newArtist->getHasDetailPage(), // convert boolean to integer otherwise it doesnt send the "false" value.
                htmlspecialchars($newArtist->getArtistHomepageImageUrl())
            ));
            $id =  $this ->connection->lastInsertId(); 
            return $id;
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }
    
    public function insertMusicTypeForNewArtist($newArtistId, $musicType){
        $sql = "INSERT INTO `dance_artistMusicType`(`dance_artistMusicType_artistId`, `dance_artistMusicType_musicTypeId`) VALUES (?, ?)";
        try{
            $statement = $this ->connection->prepare($sql);
            $statement->execute(array((int) $newArtistId, htmlspecialchars($musicType->getId())));
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    public function deleteArtistFromDatabase($artist){
        $sql = "DELETE dance_artist, dance_artistMusicType FROM dance_artist LEFT JOIN dance_artistMusicType ON dance_artist.dance_artist_id = dance_artistMusicType.dance_artistMusicType_artistId
        WHERE dance_artist.dance_artist_id = :artist_id;"; //this also deletes the artist info in the dance_artistMusicType table.
        try {
            $artistId = (int) $artist->getId();
            $statement = $this->connection->prepare($sql);
            $statement->bindParam(':artist_id', $artistId , PDO::PARAM_INT);
            $statement->execute();
        } catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function editArtistInDatabase($oldArtist, $newArtist){
        $sql = "UPDATE `dance_artist` SET `dance_artist_name` = :dance_artist_name, `dance_artist_hasDetailPage` = :dance_artist_hasDetailPage,  `dance_artist_imageUrl` =  :dance_artist_imageUrl WHERE `dance_artist_id` = :dance_artist_id";
        try{
            $statement = $this->connection->prepare($sql);
    
            $sanitizedName = htmlspecialchars($newArtist->getName());
            $sanitizedImageUrl = htmlspecialchars($newArtist->getArtistHomepageImageUrl());
            $detailPageValue = (int) $newArtist->getHasDetailPage();
            $oldArtistId = $oldArtist->getId();
        
            $statement->bindParam(':dance_artist_name', $sanitizedName);
            $statement->bindParam(':dance_artist_imageUrl', $sanitizedImageUrl); 
            $statement->bindParam(':dance_artist_hasDetailPage', $detailPageValue, PDO::PARAM_INT); 
            $statement->bindParam(':dance_artist_id', $oldArtistId, PDO::PARAM_INT);
        
            $statement->execute();
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function editArtistMusicTypesInDatabase($artist, $newMusicTypeId){
        /* // Delete all existing music type records for the artist to replace them with the new ones.
        This approach simplifies the update process and avoids having to update individual records.
        We don't need to preserve historical data or track changes over time, so we can safely delete the existing records without any impact on the application's functionality.*/
        $sql = "DELETE FROM dance_artistMusicType WHERE dance_artistMusicType_artistId = :dance_artist_id; INSERT INTO dance_artistMusicType (dance_artistMusicType_artistId, dance_artistMusicType_musicTypeId) 
        VALUES (:dance_artist_id, :musicTypeId);";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindValue(':dance_artist_id', (int) $artist->getId(), PDO::PARAM_INT);
            $statement->bindValue(':musicTypeId', (int) $newMusicTypeId, PDO::PARAM_INT);
            $statement->execute();
        } catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    // MUSIC TYPES
    public function getAllMusicTypes() {
        $sql = "SELECT `dance_musicType_id`, `dance_musicType_name` FROM `dance_musicType`";
    
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute();
    
            $musicTypes = $statement->fetchAll(PDO::FETCH_CLASS, 'MusicType');
            return $musicTypes;
        } catch (PDOException $e) {
            error_log('Error retrieving all music types: ' . $e->getMessage());
            return [];
        }
    }
    public function getMusicTypesById($id){
        $sql = "SELECT `dance_musicType_id`, `dance_musicType_name` FROM `dance_musicType` WHERE `dance_musicType_id`= :musicTypeId";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindParam(':musicTypeId', $id, PDO::PARAM_INT);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS, 'MusicType');
            $musicType = $statement->fetch();
            return $musicType;
        } catch (PDOException $e) {
            error_log('Error retrieving musicType with id ' . $id . ': ' . $e->getMessage());
            return null;
        }
    }

    public function getMusicTypesByArtistFromDatabase($artist){
        $sql = "SELECT `dance_musicType_id`, `dance_musicType_name` FROM `dance_musicType` JOIN `dance_artistMusicType` damt ON damt.`dance_artistMusicType_musicTypeId` = `dance_musicType`.`dance_musicType_id` JOIN `dance_artist` da ON da.`dance_artist_id` = damt.`dance_artistMusicType_artistId` WHERE da.`dance_artist_id` = :artist_id;";
        try {
            $artistId = $artist->getId();
            $statement = $this->connection->prepare($sql);
            $statement->bindParam(':artist_id', $artistId, PDO::PARAM_INT);
            $statement->execute();
            $musicTypes = $statement->fetchAll(PDO::FETCH_CLASS, 'MusicType');
            return $musicTypes;
        } catch (PDOException $e) {
            error_log("Cannot retrieve music types for artist with ID {$artist->getId()}: {$e->getMessage()}");
            return null;
        }
    }    

    public function insertNewMusicType($newMusicType){
        $sql= "INSERT INTO dance_musicType (dance_musicType_name) VALUES (?);";
        try{
            $statement = $this ->connection->prepare($sql);
            $statement->execute(array(htmlspecialchars($newMusicType->getMusicTypeName())));
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    } 

    //DANCE LOCATIONS
    public function getAllDanceLocations(){
        $sql = "SELECT `dance_location_id`, `dance_location_name`, `dance_location_street`, `dance_location_number`, `dance_location_postcode`, `dance_location_city`, `dance_location_urlToTheirSite`, `dance_location_imageUrl` FROM `dance_location`";
         try {
            $statement = $this->connection->prepare($sql);
            $statement->execute();
    
            $danceLocations = $statement->fetchAll(PDO::FETCH_CLASS, 'DanceLocation');
            return $danceLocations;
        } catch (PDOException $e) {
            error_log('Error retrieving dance locations: ' . $e->getMessage());
            return [];
        }
    } 

    public function getDanceLocationByIdFromDatabase($location_id){
        $sql = "SELECT `dance_location_id`, `dance_location_name`, `dance_location_street`, `dance_location_number`, `dance_location_postcode`, `dance_location_city`, `dance_location_urlToTheirSite`, `dance_location_imageUrl` FROM `dance_location` WHERE `dance_location_id` = :location_id";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindParam(':location_id', $location_id, PDO::PARAM_INT);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_CLASS, 'DanceLocation');
            $location = $statement->fetch();
            return $location;
        } catch (PDOException $e) {
            error_log('Error retrieving location with id ' . $location_id . ': ' . $e->getMessage());
            return null;
        }
    }
    public function insertNewDanceLocation($newDanceLocation){
        $sql = "INSERT INTO `dance_location`(`dance_location_name`, `dance_location_street`, `dance_location_number`, `dance_location_postcode`, `dance_location_city`, `dance_location_urlToTheirSite`, `dance_location_imageUrl`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        try{
            $statement = $this ->connection->prepare($sql);
            $statement->execute(array(htmlspecialchars($newDanceLocation->getDanceLocationName()), htmlspecialchars($newDanceLocation->getDanceLocationStreet()), $newDanceLocation->getDanceLocationNumber(), htmlspecialchars($newDanceLocation->getDanceLocationPostcode()), htmlspecialchars($newDanceLocation->getDanceLocationCity()), htmlspecialchars($newDanceLocation->getDanceLocationUrlToTheirSite()), htmlspecialchars($newDanceLocation->getDanceLocationImageUrl())));
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    } 

    public function deleteDanceLocationFromDatabase($danceLocation){
        $sql = "DELETE FROM `dance_location` WHERE `dance_location_id` = :location_id";
        try {
            $locationId = $danceLocation->getDanceLocationId();
            $statement = $this->connection->prepare($sql);
            $statement->bindParam(':location_id', $locationId , PDO::PARAM_INT);
            $statement->execute();
        } catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    public function editDanceLocationInDatabase($oldLocation, $newLocation){
        $sql = "UPDATE `dance_location` SET `dance_location_name` = :dance_location_name, `dance_location_street` = :dance_location_street, `dance_location_number` = :dance_location_number, `dance_location_postcode` = :dance_location_postcode, `dance_location_city` = :dance_location_city, `dance_location_urlToTheirSite` = :dance_location_urlToTheirSite, `dance_location_imageUrl` = :dance_location_imageUrl WHERE `dance_location_id` = :dance_location_id";
        try{
            $statement = $this->connection->prepare($sql);

            $sanitizedName = htmlspecialchars($newLocation->getDanceLocationName());
            $sanitizedStreet = htmlspecialchars($newLocation->getDanceLocationStreet());
            $sanitizedNumber = htmlspecialchars($newLocation->getDanceLocationNumber());
            $sanitizedPostcode = htmlspecialchars($newLocation->getDanceLocationPostcode());
            $sanitizedCity = htmlspecialchars($newLocation->getDanceLocationCity());
            $sanitizedUrlToTheirSite = htmlspecialchars($newLocation->getDanceLocationUrlToTheirSite());
            $sanitizedImageUrl = htmlspecialchars($newLocation->getDanceLocationImageUrl());

            $oldLocationId = $oldLocation ->getDanceLocationId();
    
            $statement->bindParam(':dance_location_name', $sanitizedName);
            $statement->bindParam(':dance_location_street', $sanitizedStreet);
            $statement->bindParam(':dance_location_number', $sanitizedNumber, PDO::PARAM_INT);
            $statement->bindParam(':dance_location_postcode', $sanitizedPostcode);
            $statement->bindParam(':dance_location_city', $sanitizedCity);
            $statement->bindParam(':dance_location_urlToTheirSite', $sanitizedUrlToTheirSite);
            $statement->bindParam(':dance_location_imageUrl', $sanitizedImageUrl);      

            $statement->bindParam(':dance_location_id', $oldLocationId, PDO::PARAM_INT);
    
            $statement->execute();
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    

    // DANCE FLASHBACKS
    public function getAllDanceFlashbacks(){
        $sql = "SELECT `dance_flashback_id`, `dance_flashback_url`, `dance_flashback_credit`, `dance_flashback_extranote` FROM `dance_flashbacks`";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute();
    
            $danceFlashbacks = $statement->fetchAll(PDO::FETCH_CLASS, 'DanceFlashback');
            return $danceFlashbacks;
        } catch (PDOException $e) {
            error_log('Error retrieving dance event flashbacks: ' . $e->getMessage());
            return [];
        }
    }

    //DANCE EVENTS
    public function getAllDanceEvents(){
        $sql = "SELECT de.dance_event_id, de.dance_event_date, de.dance_event_time, dl.dance_location_name, de.dance_event_locationId, de.dance_event_sessionTypeId, GROUP_CONCAT(da.dance_artist_name 
        ORDER BY da.dance_artist_name ASC SEPARATOR ', ') AS performing_artists, ds.dance_sessionType_name, de.dance_event_duration, de.dance_event_availableTickets, de.dance_event_price, de.dance_event_extraNote 
        FROM dance_event de 
        JOIN dance_location dl ON dl.dance_location_id = de.dance_event_locationId 
        JOIN dance_sessionType ds ON ds.dance_sessionType_id = de.dance_event_sessionTypeId 
        JOIN dance_performingArtist dp ON dp.dance_performingArtist_eventId = de.dance_event_id 
        JOIN dance_artist da ON da.dance_artist_id = dp.dance_performingArtist_artistId 
        GROUP BY de.dance_event_id, de.dance_event_date, de.dance_event_time, dl.dance_location_name, ds.dance_sessionType_name, de.dance_event_duration, de.dance_event_availableTickets, de.dance_event_price, de.dance_event_extraNote 
        ORDER BY de.dance_event_date ASC, de.dance_event_time ASC, dl.dance_location_name ASC;";
    
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute();
    
            $danceEvents = [];
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $danceEvent = new DanceEvent();
                $danceEvent->setDanceEventId($row['dance_event_id']);
                $danceEvent->setDanceLocationName($row['dance_location_name']);
                $danceEvent->setPerformingArtists($row['performing_artists']);
                $danceEvent->setDanceLocationId($row['dance_event_locationId']);
                $danceEvent->setDanceSessionTypeId($row['dance_event_sessionTypeId']);
                $danceEvent->setDanceSessionTypeName($row['dance_sessionType_name']);
                $danceEvent->setDanceEventDuration($row['dance_event_duration']);
                $danceEvent->setDanceEventAvailableTickets($row['dance_event_availableTickets']);
                $danceEvent->setDanceEventPrice($row['dance_event_price']);
                $danceEvent->setDanceEventExtraNote($row['dance_event_extraNote']);
    
                // Convert date and time strings to DateTime object
                $date = new DateTime($row['dance_event_date']);
                $time = new DateTime($row['dance_event_time']);
                $dateTime = new DateTime();
                $dateTime->setDate($date->format('Y'), $date->format('m'), $date->format('d')); //date
                $dateTime->setTime($time->format('H'), $time->format('i'), $time->format('s')); //time
    
                $danceEvent->setDanceEventDateTime($dateTime);
                $danceEvents[] = $danceEvent;
            }
    
            return $danceEvents;
        } catch (PDOException $e) {
            error_log('Error retrieving dance events: ' . $e->getMessage());
            return [];
        }
    }

    public function getAllSessionsFromDatabase() 
    {
        $sql = "SELECT `dance_sessionType_id`, `dance_sessionType_name` FROM `dance_sessionType`";    
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute();
    
            $artists = $statement->fetchAll(PDO::FETCH_CLASS, 'DanceSession');
            return $artists;
        } catch (PDOException $e) {
            error_log('Error retrieving all sessions: ' . $e->getMessage());
            return [];
        }
    }

    public function insertNewDanceEventToTheDatabase($newDanceEvent){
        $sql = "INSERT INTO `dance_event`(`dance_event_sessionTypeId`, `dance_event_locationId`, `dance_event_date`, `dance_event_time`, `dance_event_duration`, `dance_event_price`, `dance_event_availableTickets`, `dance_event_extraNote`)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"; 
        try {
            $statement = $this ->connection->prepare($sql);            
            $statement->execute(array(
            $newDanceEvent->getDanceSessionTypeId(),
            $newDanceEvent->getDanceLocationId(),
            $newDanceEvent->getDanceEventDate()->format('Y-m-d'),
            $newDanceEvent->getDanceEventTime()->format('H:i'),
            (int) $newDanceEvent->getDanceEventDuration(),
            (double) $newDanceEvent->getDanceEventPrice(),
            (int) $newDanceEvent->getDanceEventAvailableTickets(),
            htmlspecialchars($newDanceEvent->getDanceEventExtraNote())
            ));
            $id =  $this ->connection->lastInsertId(); 
            return $id;
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function insertArtistsForNewEvent($newEventId, $artist){
        $sql = "INSERT INTO `dance_performingArtist`(`dance_performingArtist_eventId`, `dance_performingArtist_artistId`) VALUES (?, ?)";
        try{
            $statement = $this ->connection->prepare($sql);
            $statement->execute(array((int) $newEventId, (int) $artist->getId()));
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function deleteEventFromDatabase($event){
        $sql = "DELETE dance_event, dance_performingArtist FROM dance_event LEFT JOIN dance_performingArtist ON dance_event.dance_event_id = dance_performingArtist.dance_performingArtist_eventId
        WHERE dance_event.dance_event_id = :event_id"; //this also deletes the event info in the dance_performingArtist table.
        try {
            $eventId = (int) $event->getDanceEventId();
            $statement = $this->connection->prepare($sql);
            $statement->bindParam(':event_id', $eventId , PDO::PARAM_INT);
            $statement->execute();
        } catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function getEventByIdFromDatabase($event_id) 
    {
        $sql = "SELECT `dance_event_id`, `dance_event_locationId`, `dance_event_sessionTypeId`, `dance_event_date`, `dance_event_time`, `dance_event_duration`, `dance_event_price`, `dance_event_availableTickets`, `dance_event_extraNote` 
        FROM `dance_event` WHERE `dance_event_id`= :dance_event_id";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindParam(':dance_event_id', $event_id, PDO::PARAM_INT);
            $statement->execute();
            $danceEvent = new DanceEvent();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $danceEvent->setDanceEventId($row['dance_event_id']);
                $danceEvent->setDanceLocationId($row['dance_event_locationId']);
                $danceEvent->setDanceSessionTypeId($row['dance_event_sessionTypeId']);
                $danceEvent->setDanceEventDuration($row['dance_event_duration']);
                $danceEvent->setDanceEventAvailableTickets($row['dance_event_availableTickets']);
                $danceEvent->setDanceEventPrice($row['dance_event_price']);
                $danceEvent->setDanceEventExtraNote($row['dance_event_extraNote']);
    
                // Convert date and time strings to DateTime object
                $date = new DateTime($row['dance_event_date']);
                $time = new DateTime($row['dance_event_time']);
                $dateTime = new DateTime();
                $dateTime->setDate($date->format('Y'), $date->format('m'), $date->format('d')); //date
                $dateTime->setTime($time->format('H'), $time->format('i'), $time->format('s')); //time
    
                $danceEvent->setDanceEventDateTime($dateTime);
            }
    
            return $danceEvent;
        } catch (PDOException $e) {
            error_log('Error retrieving event with id ' . $event_id . ': ' . $e->getMessage());
            return null;
        }
    }

    public function getArtistsByEventFromDatabase($event){
        $sql = "SELECT `dance_artist_id`, `dance_artist_name`
                FROM `dance_artist` 
                JOIN `dance_performingArtist` dpa 
                ON dpa.`dance_performingArtist_artistId` = `dance_artist`.`dance_artist_id` 
                JOIN `dance_event` de 
                ON de.`dance_event_id` = dpa.`dance_performingArtist_eventId` 
                WHERE de.`dance_event_id` = :event_id"; // Use named parameter :event_id
    
        try {
            $eventId = $event->getDanceEventId();
            $statement = $this->connection->prepare($sql);
            $statement->bindParam(':event_id', $eventId, PDO::PARAM_INT);
            $statement->execute();
            $artists = $statement->fetchAll(PDO::FETCH_CLASS, 'ArtistModel');
            return $artists;
        } catch (PDOException $e) {
            error_log("Cannot retrieve artists for event with ID {$event->getDanceEventId()}: {$e->getMessage()}");
            return null;
        }
    }
    
    public function editEventInDatabase($oldEvent, $newEvent){
        $sql = "UPDATE `dance_event` SET `dance_event_locationId`= :location_id,`dance_event_sessionTypeId`= :session_id,`dance_event_date`= :date,`dance_event_time`= :time, 
        `dance_event_duration`= :duration,`dance_event_price`= :price,`dance_event_availableTickets`= :available_tickets,`dance_event_extraNote`= :note 
        WHERE `dance_event_id` = :event_id";
        try {
            $statement = $this->connection->prepare($sql);

            $newLocationId = (int) $newEvent->getDanceLocationId();
            $newSessionId = (int) $newEvent->getDanceSessionTypeId();
            $newDate = $newEvent->getDanceEventDate()->format('Y-m-d');
            $newTime = $newEvent->getDanceEventTime()->format('H:i');
            $newDuration = (int) $newEvent->getDanceEventDuration();
            $newPrice = (double) $newEvent->getDanceEventPrice();
            $newAvailableTickets = (int) $newEvent->getDanceEventAvailableTickets();
            $sanitizedExtraNote = htmlspecialchars($newEvent->getDanceEventExtraNote());
            $oldId = $oldEvent->getDanceEventId();

            $statement->bindParam(':location_id', $newLocationId);
            $statement->bindParam(':session_id', $newSessionId);
            $statement->bindParam(':date', $newDate);
            $statement->bindParam(':time', $newTime);
            $statement->bindParam(':duration', $newDuration);
            $statement->bindParam(':price', $newPrice);
            $statement->bindParam(':available_tickets', $newAvailableTickets);
            $statement->bindParam(':note', $sanitizedExtraNote);
            $statement->bindParam(':event_id', $oldId);

            $statement->execute();
        } catch(PDOException $e) {
            echo $e->getMessage();
        }

    }

    public function editEventArtistsInDatabase($event, $newArtistId){
        /* // Delete all existing artist records for the event to replace them with the new ones.
        This approach simplifies the update process and avoids having to update individual records.
        We don't need to preserve historical data or track changes over time, so we can safely delete the existing records without any impact on the application's functionality.*/
        $sql = "DELETE FROM dance_performingArtist WHERE dance_performingArtist_eventId = :dance_event_id; INSERT INTO dance_performingArtist (dance_performingArtist_eventId, dance_performingArtist_artistId) 
        VALUES (:dance_event_id, :artist_id);";
        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindValue(':dance_event_id', (int) $event->getDanceEventId(), PDO::PARAM_INT);
            $statement->bindValue(':artist_id', (int) $newArtistId, PDO::PARAM_INT);
            $statement->execute();
        } catch(PDOException $e){
            echo $e->getMessage();
        }
    }
}
?>