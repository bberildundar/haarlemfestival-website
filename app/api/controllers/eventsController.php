<?php
require __DIR__ . '/../../Services/eventService.php';
require_once __DIR__ . '/../../Models/eventModel.php';

class EventsController{
    private $eventService;

    function __construct(){
        $this -> eventService = new EventService();
    }

    function index(){
        if($_SERVER["REQUEST_METHOD"] == "GET"){
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($this -> eventService -> getAll());     
        }

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $eventJsonString = file_get_contents('php://input');

            $eventData = json_decode($eventJsonString, true);

            $event = new Event();
            
            try{
            
            //Get image URL from POST request, then download that image into /media/events
            $imageUrl = $_FILES['eventinput']['tmp_name'];
            
            $imageName = strtolower(htmlspecialchars(preg_replace('/[^a-zA-Z0-9]/s', '', $eventData['event_name'])));

            $downloadPath ='/media/events/' . $imageName . '.png'; // /media/events/event.png

            //Put the file from the image path to the download path
            move_uploaded_file($imageUrl, SITE_ROOT . $downloadPath);
            }catch(Exception $e){
                echo $e->getMessage();
            }
            
            $event->setName($eventData['event_name']);
            $event->setDescription($eventData['event_description']);
            $event->setUrlRedirect($eventData['event_urlRedirect']);
            $event->setImageUrl($downloadPath);
            $event->setStartTime($eventData['event_startTime']);
            $event->setEndTime($eventData['event_endTime']);

            $this->eventService->insert($event);
        }
    }
}
?>