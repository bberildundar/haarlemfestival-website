<?php
session_start();
use Ramsey\Uuid\Uuid;
require __DIR__ . '/controller.php';
require __DIR__ . '/../services/DanceService.php';
require __DIR__ . '/../Services/FoodTypeService.php';
require __DIR__ . '/../Services/RatingService.php';
require __DIR__ . '/../Services/YummyService.php';
require __DIR__ . '/../Services/UserService.php';
require __DIR__ . '/../Services/UserTypeService.php';
require __DIR__.'/../Services/WalkingTourService.php';
require __DIR__ . '/../Services/ContentService.php';
require __DIR__ . '/../Services/ReservationService.php';


class AdminController extends Controller
{
    private $eventService;
    private $danceService;
    private $events;
    private $yummyService;
    private $userService;
    private $foodTypeService;
    private $ratingService;
    private $userTypeService;
    private $walkingTourService;
    private $contentService;
    private $reservationService;

    public function __construct()
    {
        $this->danceService = new DanceService();
        $this->yummyService = new YummyService();
        $this->userService = new UserService();
        $this->foodTypeService = new FoodTypeService();
        $this->ratingService = new RatingService();
        $this->userTypeService = new userTypeService();
        $this->walkingTourService = new WalkingTourService();
        $this->contentService = new ContentService();
        $this->reservationService = new ReservationService();
    }
    //Tudor Nosca (678549)
    public function index()
    {

        if($this->checkRole()) {   
        require_once __DIR__ . '/../Services/eventService.php';
        require_once __DIR__ . '/../Services/festivalService.php';

        $festivalService = new FestivalService();
        $eventsService = new EventService();

        $festival = $festivalService->getFestival();
        $events = $eventsService->getAll();

        if (isset($_POST['events'])) {
            $festivalEvent = $festival[0];
            $newEvent = $eventsService->getByName($_POST['events']);
            $festivalService->changeEvent($festivalEvent->getId(), $newEvent->getName(), $newEvent->getId(), $newEvent->getStartTime(), $newEvent->getEndTime());
            echo "Selected event is: " . $_POST['events'];
        }

        require __DIR__ . '/../views/admin/index.php';
    }
    else{
        header('Location: /');
    }
    }


    //Tudor Nosca (678549)
    public function events()
    {
        if($this->checkRole()) {
            require_once __DIR__ . '/../Services/eventService.php';
        
            $eventService = new EventService();
            $events = $eventService->getAll();
    
            require __DIR__ . '/../views/admin/events.php';
        }else{
            header('Location: /');
        }
    }
    //Tudor Nosca (678549)
    function addevent()
    {
        if($this->checkRole()) {
            if (isset($_POST['addbutton'])) {
                try {
    
                    //Get image URL from POST request, then download that image into /media/events
                    $imageUrl = $_FILES['eventinput']['tmp_name'];
    
                    $imageName = strtolower(htmlspecialchars(preg_replace('/[^a-zA-Z0-9]/s', '', $_POST['eventnametextbox'])));;
    
                    $downloadPath = SITE_ROOT . '/media/events/' . $imageName . '.png'; // public/media/events/event.png
    
                    //Put the file from the image path to the download path
                    move_uploaded_file($imageUrl, $downloadPath);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
            require __DIR__ . '/../views/admin/addevent.php';
        }
        else{
            header('Location: /');
        }
        
    }
    //Tudor Nosca (678549)
    function editevent()
    {
        if($this->checkRole()) {
            require __DIR__ . '/../Services/eventService.php';
            $eventService = new EventService();
    
            $event = $eventService->getById($_GET['id']);
    
            if (isset($_POST['editbutton'])) {
                $changedEvent = new Event();
    
                $changedEvent->setName($_POST['eventnametextbox']);
                $changedEvent->setDescription($_POST['eventdesctextbox']);
                $changedEvent->setStartTime($_POST['eventstarttimecalendar']);
                $changedEvent->setEndTime($_POST['eventendtimecalendar']);
                $changedEvent->setUrlRedirect("/" . strtolower(preg_replace('/[^a-zA-Z0-9]/s', '', $_POST['eventnametextbox'])));
                $changedEvent->setImageUrl($_POST['eventinput']);
    
                $eventService->updateEvent($event, $changedEvent);
    
                $event = $eventService->getById($_GET['id']);
            }
    
            require __DIR__ . '/../views/admin/editevent.php';
        }
        else{
            header('Location: /');
        }
    }
    //Tudor Nosca (678549)
    function deleteevent()
    {
        if($this->checkRole()) {
            require __DIR__ . '/../Services/eventService.php';

            $eventService = new EventService();
    
            $event = $eventService->getById($_GET['id']);
    
            $eventService->deleteEvent($event);
    
            header('Location: /admin/events');
        }
        else{
            header('Location: /');
        }
    }

    public function addRestaurantPage()
    {
        if($this->checkRole()) {
            //$events = $this->eventService->getAll();
            $foodTypes = $this->foodTypeService->getAllFoodType();
            $ratings = $this->ratingService->getAllRating();

            require __DIR__ . '/navbarRequirements.php';
            require __DIR__ . '/../views/admin/addRestaurantPage.php';
        }
        else{
            header('Location: /');
        }
    }

    public function manageRestaurants()
    {
        if($this->checkRole()) {
            //$events = $this->eventService->getAll();
            $restaurants = $this->yummyService->getAllRestaurants();
            $foodTypes = $this->foodTypeService->getAllFoodType();
            $ratings = $this->ratingService->getAllRating();

            require __DIR__ . '/navbarRequirements.php';
            require __DIR__ . '/../views/admin/manageRestaurants.php';
        } else {
            header('Location: /');
        }
    }

    private function getRestaurantPictureURL($restaurant_pictureURL, $restaurant_name)
    {
        $imageName = strtolower(htmlspecialchars(preg_replace('/[^a-zA-Z0-9]/s', '', $restaurant_name)));
        $downloadPath = '/media/yummyPics/' . $imageName . '.png';
        move_uploaded_file($restaurant_pictureURL, SITE_ROOT . $downloadPath);
        return $downloadPath;
    }

    public function addRestaurant()
    {
        if($this->checkRole()) {
            if (isset($_POST['addRestaurant'])) {
                try {
                    //Get image URL from POST request, then download that image into /media/events
                    $restaurant_pictureURL = $_FILES['restaurant_pictureURL']['tmp_name'];
                    $downloadPath = $this->getRestaurantPictureURL($restaurant_pictureURL, $_POST['restaurant_name']);
                    $restaurant = new RestaurantModel();
                    $restaurant->setRestaurantPictureURL($downloadPath);
    
    
                    $restaurant->setRestaurantName(htmlspecialchars($_POST['restaurant_name']));
                    $restaurant->setFoodTypeId(htmlspecialchars($_POST['restaurant_foodType']));
                    $restaurant->setRestaurantRatingId(htmlspecialchars($_POST['restaurant_rating']));
                    $restaurant->setRestaurantKidsPrice(htmlspecialchars($_POST['restaurant_kidsPrice']));
                    $restaurant->setRestaurantAdultsPrice(htmlspecialchars($_POST['restaurant_adultsPrice']));
                    $restaurant->setDuration(htmlspecialchars($_POST['duration']));
                    $restaurant->setHavaDetailPageOrNot(htmlspecialchars($_POST['haveDetailPage']));
                    $restaurant->setRestaurantOpeningTime(htmlspecialchars($_POST['opening_time']));
                    $restaurant->setNumberOfTimeSlots(htmlspecialchars($_POST['numTime_slots']));
                    $restaurant->setRestaurantNumberOfAvailableSeats(htmlspecialchars($_POST['num_seats']));
    
                    //$restaurant->setRestaurantPictureURL($downloadPath);
                    $restaurantService = new YummyService();
                    $result = $restaurantService->insertRestaurant($restaurant);
    
                    if ($result){
                        header("location: /admin/manageRestaurants");
                        exit();
                    }
    
                } catch (Exception $e) {
                    echo "Error adding restaurant: " . $e->getMessage();
                }
            }
        }
        else{
            header('Location: /');
        }
    }

    public function editRestaurantPage()
    {
        if($this->checkRole()) {
            //$eventService = new EventService();
            //$events = $eventService->getAll();
            $restaurant = $this->yummyService->getById($_GET['id']);
            $foodTypes =  $this->foodTypeService->getAllFoodType();
            $ratings = $this->ratingService->getAllRating();

            require __DIR__ . '/navbarRequirements.php';
            require __DIR__ . '/../views/admin/editRestaurantPage.php';
        }
        else{
            header('Location: /');
        }
    }

    public function editRestaurant(){
        if($this->checkRole()) {
            $restaurant = new RestaurantModel();

        // Retrieve the restaurant object from the database
        $restaurantId = $_POST['restaurant_id'];
        $restaurantFromDB = $this->yummyService->getById($restaurantId);

        // Check if user uploaded a new picture URL or not
        if (!empty($_FILES['restaurant_pictureURL']['name'])) {
            $downloadPath = $this->getRestaurantPictureURL($_FILES['restaurant_pictureURL']['tmp_name'], $_POST['restaurant_name']);
            $restaurant->setRestaurantPictureURL($downloadPath);
        } else {
            $restaurant->setRestaurantPictureURL($restaurantFromDB->getRestaurantPictureURL());
        }
        // Update the existing restaurant object retrieved from the database
        $restaurant->setRestaurantId($_POST['restaurant_id']);
        $restaurant->setRestaurantName(htmlspecialchars($_POST['restaurant_name']));
        $restaurant->setFoodTypeId(htmlspecialchars($_POST['restaurant_foodType']));
        $restaurant->setRestaurantRatingId(htmlspecialchars($_POST['restaurant_rating']));
        $restaurant->setRestaurantKidsPrice(htmlspecialchars($_POST['restaurant_kidsPrice']));
        $restaurant->setRestaurantAdultsPrice(htmlspecialchars($_POST['restaurant_adultsPrice']));
        $restaurant->setDuration(htmlspecialchars($_POST['duration']));
        $restaurant->setHavaDetailPageOrNot(htmlspecialchars($_POST['haveDetailPage']));
        $restaurant->setRestaurantOpeningTime(htmlspecialchars($_POST['opening_time']));
        $restaurant->setNumberOfTimeSlots(htmlspecialchars($_POST['numTime_slots']));
        $restaurant->setRestaurantNumberOfAvailableSeats(htmlspecialchars($_POST['num_seats']));

        try {
            $result = $this->yummyService->updateRestaurant($restaurant);
            if ($result){
                header("location: /admin/manageRestaurants");
                exit();
            }
        } catch (Exception $e) {
            echo "Error updating restaurant: " . $e->getMessage();
        }
        }
        else{
            header('Location: /');
        }
    }

    public function deleteRestaurantPage()
    {
        if($this->checkRole()) {
            $id = $_GET['id'];
            $this->yummyService->deleteRestaurant($id);
    
            $this->manageRestaurants();
        }
        else{
            header('Location: /');
        }
    }


    
    // Administrator - Manage users - User CRUD. Includes search/filter and sorting. Must display registration date. 
    // done by: Betül Beril Dündar - 691136 
    function users(){   
        if($this->checkRole()){
        $searchString = "";
        $sortType = "";
        $filterType = "";

        if(isset($_GET["search"]) && !empty(trim($_GET["search"]))) { 
            $searchString = htmlspecialchars($_GET["search"], ENT_QUOTES, "UTF-8");
        }
        if(isset($_GET["sortBy"])) {
            $sortType = $_GET["sortBy"];
        }
        if(isset($_GET["filter"])) {
            $filterType = $_GET["filter"];
        }

        switch(true) { //searching, filtering and sorting
            case !empty($searchString): 
                $allUsers = $this->userService->getUsersBySearch($searchString);
                break;
            case ($sortType == 'laterRegistrationDate'):
                $allUsers = $this->userService->getAllUsersByLaterRegistrationDate(); 
                break;
            case ($sortType == 'usernameAlphabetical'):
                $allUsers = $this->userService->getAllUsersByUsrnameAlphabetical(); 
                break;
            case ($filterType == 'admins'):
                $allUsers = $this->userService->getAllAdminUsers(); 
                break;
            case ($filterType == 'employees'):
                $allUsers = $this->userService->getAllEmployeeUsers(); 
                break;
            case ($filterType == 'customers'):
                $allUsers = $this->userService->getAllCustomerUsers(); 
                break;
            default:
                $allUsers = $this->userService->getAllUsersFromDatabase(); 
                break;
        }
        require __DIR__ . "/../views/admin/users.php";}
        else{
            header('Location: /');
        }
    }

    function addUser(){
        if($this->checkRole()) {
            $userTypeService = new UserTypeService();        
            $allUserTypes = $userTypeService->getAllUserType();   
            if($_SERVER["REQUEST_METHOD"] == "POST"){
                $user = new User();
                $user->setUserFirstName($_POST['userAdminFirstNameTextBox']);
                $user->setUserLastName($_POST['userAdminLastnameTextBox']);
                $user->setUserPassword($_POST['userAdminPasswordTextBox']);
                $user->setUsername($_POST['userAdminUsernameTextBox']);
                $user->setUserEmail($_POST['userAdminEmailTextBox']);
                $user->setUserTypeId($_POST['userAdminUserTypeDropdown']);
    
                $this->userService->createUser($user);
                header('Location: /admin/users');
           }
    
            require __DIR__ . "/../views/admin/addUser.php";
        }
        else{
            header('Location: /');
        }
    }
          
    function editUser(){
        if($this->checkRole()) {
           
            $allUserTypes = $this->userTypeService->getAllUserType();
            $userToEdit = $this->userService->getByID($_GET['id']); 
    
            if (isset($_POST['editbutton'])) {
                if(isset($_FILES['userAdminImageInput']) && $_FILES['userAdminImageInput']['error'] == 0){                
                        $imageUrl = $_FILES['userAdminImageInput']['tmp_name'];
                        $imageName = strtolower(htmlspecialchars(preg_replace('/[^a-zA-Z0-9]/s', '', $_POST['userAdminUsernameTextBox'])));
                        $downloadPath = SITE_ROOT . '/media/userProfilePictures/' . $imageName . '.png'; 
                        move_uploaded_file($imageUrl, $downloadPath);
                        $downloadPath = str_replace(SITE_ROOT, '', $downloadPath); // remove SITE_ROOT from $downloadPath       
                    
                }else{
                    $downloadPath = $userToEdit->getUserPicURL();
                }               
                
                $user = new User();
                $user->setUserFirstName($_POST['userAdminFirstNameTextBox']);
                $user->setUserLastName($_POST['userAdminLastnameTextBox']);
                $user->setUsername($_POST['userAdminUsernameTextBox']);
                $user->setUserEmail($_POST['userAdminEmailTextBox']);
                $user->setUserTypeId($_POST['userAdminUserTypeDropdown']);
                $user->setUserPicURL($downloadPath);
                $this->userService->updateUser($userToEdit, $user);   
            }  
            require __DIR__ . "/../views/admin/editUser.php";
        }
        else{
            header('Location: /');
        }
    } 

    function deleteUser(){
        if($this->checkRole()) {
            $userToDelete = $this->userService->getByID($_GET['id']); 
            $this->userService->deleteUser($userToDelete);
            header('Location: /admin/users');
        }
        else{
            header('Location: /');
        }
    }

    function orders(){
        require __DIR__ . '/../Services/OrderService.php';

        $orderService = new OrderService();

        $orders = $orderService->getAll();

        require __DIR__ . '/../views/admin/orders.php';
    }

    function generateApiKey(){
        require_once __DIR__ . '/../vendor/autoload.php';

        $_SESSION['external_api_key'] = Uuid::uuid4()->toString();

        require __DIR__ . '/../views/admin/generateApiKey.php';
    }

    function manageWalkingTourContent(){
        if ($this->checkRole()){
            $allContent = $this->walkingTourService->getAllWalkingTourContent();

            require __DIR__ . '/../views/admin/walkingTourAdmin.php';

        } else {
            header('Location: /');
        }
    }

    function selectContent(){

        require_once __DIR__.'/../Services/WalkingTourService.php';
        $walkingTourService = new WalkingTourService();

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $data = json_decode(file_get_contents("php://input"), true);

            if(isset($data['section'])){
                    $elementName = $data['section'];
                $result = $walkingTourService->getContentByElement($elementName);

                header('Content-Type: application/json;');
                echo json_encode($result);
            }  else {echo json_encode("There was an Issue");}
        }
    }

    function updateContent(){
        require_once __DIR__.'/../Services/WalkingTourService.php';
        $walkingTourService = new WalkingTourService();

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $data = json_decode(file_get_contents("php://input"), true);

            if(isset($data['oldSectionName'])){
                $oldSectionName = $data['oldSectionName'];
                $inputSectionName = $data['newSectionName'];
                $inputTitle = $data['title'];
                $inputText = $data['text'];
                $inputButtonText = $data['buttonText'];
                $inputButtonUrl = $data['buttonUrl'];

                $walkingTourService->updateContent($oldSectionName, $inputSectionName, $inputTitle, $inputText, $inputButtonText, $inputButtonUrl);

                header('Content-Type: application/json;');
                echo json_encode("Successfully updated in the Database");
            }  else {echo json_encode("There was an Issue");}
        }
    }
    function createContent(){
        require_once __DIR__.'/../Services/WalkingTourService.php';
        $walkingTourService = new WalkingTourService();

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $data = json_decode(file_get_contents("php://input"), true);

            if(isset($data['SectionName'])){
                $inputSectionName = $data['SectionName'];
                $inputTitle = $data['title'];
                $inputText = $data['text'];
                $inputButtonText = $data['buttonText'];
                $inputButtonUrl = $data['buttonUrl'];

                $walkingTourService->createContent($inputSectionName, $inputTitle, $inputText, $inputButtonText, $inputButtonUrl);

                header('Content-Type: application/json;');
                echo json_encode("Successfully added to the Database");
            }  else {echo json_encode("There was an Issue");}
        }
    }

    function deleteContent(){
        require_once __DIR__.'/../Services/WalkingTourService.php';
        $walkingTourService = new WalkingTourService();

        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $data = json_decode(file_get_contents("php://input"), true);

            if(isset($data['sectionName'])){
                $sectionName = $data['sectionName'];
                $walkingTourService->deleteContent($sectionName);

                header('Content-Type: application/json;');
                echo json_encode("Successfully deleted from the Database");
            }  else {echo json_encode("There was an Issue");}
        }
    }

    function manageReservations(){
        if ($this->checkRole()){
            $allReservations = $this->reservationService->getAllReservations();

            require __DIR__ . '/../views/admin/reservationsAdmin.php';

        } else {
            header('Location: /');
        }
    }
    function editReservationsPage(){
        $restaurants = $this->yummyService->getAllRestaurants();

        if ($this->checkRole() && isset($_GET['id'])){
            $reservationId = $_GET['id'];
            $reservation = $this->reservationService->getReservationById($reservationId);
            $existingReservation = true;
        }else if ($this->checkRole() && !isset($_GET['id'])){
            $existingReservation = false;
        } else {
            header('Location: /');
        }
        require __DIR__ . '/../views/admin/editReservations.php';
    }
    function editReservation(){
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $data = json_decode(file_get_contents("php://input"), true);

            if(isset($data['id'])){
                $reservationId = $data['id'];
                $name = $data['name'];
                $date = $data['date'];
                $amountAdults = $data['adults'];
                $amountKids = $data['kids'];
                $guestNote = $data['guestNote'];
                $restaurantId = $data['restaurantId'];
                $status = $data['status'];

                $this->reservationService->updateReservation($reservationId, $name, $date,$amountAdults,$amountKids,$guestNote,$restaurantId,$status);

                header('Content-Type: application/json;');
                echo json_encode("Successfully updated in the Database");
            }  else {echo json_encode("There was a problem editing reservation #".$reservationId);}
        }
    }

    function createReservation(){
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $data = json_decode(file_get_contents("php://input"), true);

            if(isset($data['id'])){
                $reservationId = uniqid("R");
                $name = $data['name'];
                $date = $data['date'];
                $amountAdults = $data['adults'];
                $amountKids = $data['kids'];
                $guestNote = $data['guestNote'];
                $restaurantId = $data['restaurantId'];

                $this->reservationService->createReservation($reservationId,$amountAdults, $amountKids, $guestNote, $restaurantId,$name,$date);

                header('Content-Type: application/json;');
                echo json_encode("Successfully updated in the Database");
            }  else {echo json_encode("There was a problem creating the reservation");}
        }
    }
    function changeReservationStatus(){
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $data = json_decode(file_get_contents("php://input"), true);
        if(isset($data['id'])){
            $reservationId = $data['id'];
            $status = $data['status'];

            $this->reservationService->changeReservationStatus($reservationId, $status);
            header('Content-Type: application/json;');
            echo json_encode('Successfully changed the status of reservation #'.$reservationId.'in the Database');
        } else {echo json_encode('There was a problem changing the status of the reservation');}
    }}
    function checkRole(){
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 2){
            return true;
        }
        return false;
    }

    function editHomepageContent(){   
        if($this->checkRole()){
            if(!isset($_SESSION['user_id'])){
                $_SESSION['user_id'] = 0;
            }
            $contents= $this->contentService->getAllContent();
            require __DIR__ . '/navbarRequirements.php';
            require __DIR__ . "/../views/admin/editHomepageContent.php";}
        else{
            header('Location: /');
        }
    }
}
?>