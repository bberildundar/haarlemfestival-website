<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit <?php echo $element ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>
<?php 
    function generateEditForms($element, $danceLocation, $artist, $allMusicTypes, $artistMusicTypeIds, $danceLocations, $artists, $sessions, $event, $artistIds){
        $editFormHtml = ''; // set a default value for the variable
        switch ($element) {
            case "Location":                
                $editFormHtml = generateLocationEditForm($danceLocation);
                break;
            case "Artist":                
                $editFormHtml = generateArtsitEditForm($artist, $allMusicTypes, $artistMusicTypeIds);
                break;
            case "Event":                
                $editFormHtml = generateEventEditForm($danceLocations, $artists, $sessions, $event, $artistIds);
                break;
            default:
                $editFormHtml =
                    "<p>There has been an error creating the Edit Form. Please try again later.</p>";
                break;
        }
        return $editFormHtml; // return the $editFormHtml variable
    }   

function generateLocationEditForm($location){
    $locationEditForm = '
    <div class="mb-3" style="width: 20%">
    <label for="danceLocationNameTextBox" class="form-label">Location Name*</label>
    <input type="text" class="form-control" id="danceLocationNameTextBox" name="danceLocationNameTextBox"
        placeholder="Location Name" value='. $location->getDanceLocationName() . ' required>
</div>
<div class="mb-3" style="width: 20%">
    <label for="danceLocationStreetTextBox" class="form-label">Street*</label>
    <input type="text" class="form-control" id="danceLocationStreetTextBox"
        name="danceLocationStreetTextBox" placeholder="Street" value='. $location->getDanceLocationStreet() . ' required>
</div>
<div class="mb-3" style="width: 10%">
    <label for="danceLocationNumberTextBox" class="form-label">Number*</label>
    <input type="text" class="form-control" id="danceLocationNumberTextBox"
        name="danceLocationNumberTextBox" placeholder="Number" value='. $location->getDanceLocationNumber() . ' required>
</div>
<div class="mb-3" style="width: 10%">
    <label for="danceLocationPostcodeTextBox" class="form-label">Postcode*</label>
    <input type="text" class="form-control" id="danceLocationPostcodeTextBox"
        name="danceLocationPostcodeTextBox" placeholder="Postcode" value='. $location->getDanceLocationPostcode() . ' required>
</div>
<div class="mb-3" style="width: 15%">
    <label for="danceLocationCityTextBox" class="form-label">City*</label>
    <input type="text" class="form-control" id="danceLocationCityTextBox" name="danceLocationCityTextBox"
        placeholder="City" value='. $location->getDanceLocationCity() . ' required>
</div>
<div class="mb-3" style="width: 50%">
    <label for="danceLocationUrlToTheirSiteTextBox" class="form-label">URL to Their Site*</label>
    <input type="text" class="form-control" id="danceLocationUrlToTheirSiteTextBox"
        name="danceLocationUrlToTheirSiteTextBox" placeholder="URL to Their Site"  value='. $location->getDanceLocationUrlToTheirSite() . ' required>
</div>
<div class="mb-3" style="width: 15%">
    <label for="danceLocationImageInput" class="form-label">Location Image*</label>
    <input type="file" class="form-control" id="danceLocationImageInput" name="danceLocationImageInput"
        accept="image/png, image/jpg" required>
</div>
<p> * marked fields are mandatory. </p>';

    return $locationEditForm;
}
function generateArtsitEditForm($artist, $allMusicTypes, $artistMusicTypeIds){
    $artistEditForm = '
    <div class="mb-3" style="width: 20%">
        <label for="danceArtistNameTextBox" class="form-label">Artist Name*</label>
        <input type="text" class="form-control" id="danceArtistNameTextBox" name="danceArtistNameTextBox" placeholder="Artist Name" value="'. $artist->getName() . '" required>
    </div>
    <div class="mb-3" style="width: 20%">
        <label for="danceArtistHasDetailPageDropdown">Does the artist have a detail page?* </label>
        <select name="danceArtistHasDetailPageDropdown" id="danceArtistHasDetailPageDropdown" required>
            <option value="No" '. ($artist->getHasDetailPage() ? '' : 'selected') .'>No</option>
            <option value="Yes" '. ($artist->getHasDetailPage() ? 'selected' : '') .'>Yes</option>  
        </select>
    </div>
    <div class="mb-3" style="width: 20%">
        <p>Select the genres:*</p>
    </div>';
    foreach ($allMusicTypes as $musicType) {
        $checked = '';
        if (in_array($musicType->getId(), $artistMusicTypeIds)) {
            $checked = 'checked';
        }
        $artistEditForm .= '
            <div>
                <input type="checkbox" id="musicType' . $musicType->getId() . '" name="musicType' . $musicType->getId() . '" value="' . $musicType->getId() . '" ' . $checked . '>
                <label for="musicType' . $musicType->getId() . '">' . $musicType->getMusicTypeName() . '</label>
            </div>';
    }               
$artistEditForm .= '
    <div class="mb-3" style="width: 15%">
        <label for="danceArtistImageInput" class="form-label">Artist Image*</label>
        <input type="file" class="form-control" id="danceArtistImageInput" name="danceArtistImageInput" accept="image/png, image/jpg" required>
    </div>
    <p class="fw-bold">* marked fields are mandatory.</p>';

return $artistEditForm;
}

function generateEventEditForm($danceLocations, $artists, $sessions, $event, $artistIds){

    $danceEventEditFormHtml = '
        <div class="mb-3" style="width: 10%">
            <label for="danceEventDateCalendar" class="form-label">Dance Event Date: * </label>
            <input type="date" class="form-control" name="danceEventDateCalendar" id="danceEventDateCalendar"  required>
        </div>
        <div class="mb-3" style="width: 10%">
            <label for="danceEventTime" class="form-label">Dance Event Time: * </label>
            <input type="time" class="form-control" name="danceEventTime" id="danceEventTime" required>
        </div>
<div class="mb-3" style="width: 20%">
            <label for="danceEventLocationDropDown">Dance Event Location* </label>
            <select name="danceEventLocationDropDown" id="danceEventLocationDropDown" required>';
foreach ($danceLocations as $location) {
    $selected = '';
    if ($location->getDanceLocationId() == $event->getDanceLocationId()) {
        $selected = 'selected';
    }
    $danceEventEditFormHtml .= '<option value="' . $location->getDanceLocationId() . '" ' . $selected . '>' . $location->getDanceLocationName() . '</option>';
}
$danceEventEditFormHtml .= '
            </select>
        </div>
        <div class="mb-3" style="width: 20%">
        <p>Select the artists:*</p>
    </div>';
    foreach ($artists as $artist) {
        $checked = '';
        if (in_array($artist->getId(), $artistIds)) {
            $checked = 'checked';
        }
        $danceEventEditFormHtml .= '
            <div>
                <input type="checkbox" id="artist' . $artist->getId() . '" name="artist' . $artist->getId() . '" value="' . $artist->getId() . '" ' . $checked . '>
                <label for="artist' . $artist->getId() . '">' . $artist->getName() . '</label>
            </div>';
    }               
$danceEventEditFormHtml .= '
<div class="mb-3" style="width: 20%">
            <label for="danceEventSessionDropDown">Dance Event Session* </label>
            <select name="danceEventSessionDropDown" id="danceEventSessionDropDown" required>';
foreach ($sessions as $session) {
    $selected = '';
    if ($session->getDanceSessionId() == $event->getDanceSessionTypeId()) {
        $selected = 'selected';
    }
    $danceEventEditFormHtml .= '<option value="' . $session->getDanceSessionId() . '" ' . $selected . '>' . $session->getDanceSessionName() . '</option>';
}
$danceEventEditFormHtml .= '
            </select>
        </div>

        <div class="mb-3" style="width: 10%">
            <label for="danceEventDurationTextBox" class="form-label">Duration (total mins)*</label>
            <input type="number" min="0" class="form-control" id="danceEventDurationTextBox"
                name="danceEventDurationTextBox" placeholder="Duration" value="'. $event->getDanceEventDuration() . '" required>
        </div>
        <div class="mb-3" style="width: 10%">
            <label for="danceEventAvailableTicketsTextBox" class="form-label">Available Tickets*</label>
            <input type="number" min="0" class="form-control" id="danceEventAvailableTicketsTextBox"
                name="danceEventAvailableTicketsTextBox" placeholder="Available Tickets"  value="'. $event->getDanceEventAvailableTickets() . '" required>
        </div>
        <div class="mb-3" style="width: 10%">
            <label for="danceEventPriceTextBox" class="form-label">Price*</label>
            <input type="number" step="00.01" min="0" class="form-control" id="danceEventPriceTextBox"
                name="danceEventPriceTextBox" placeholder="Price"  value="'. $event->getDanceEventPrice() . '" required>
        </div>
        <div class="mb-3" style="width: 40%">
            <label for="danceEventExtraNoteTextBox" class="form-label">Extra Note About This Event</label>
            <input type="text" class="form-control" id="danceEventExtraNoteTextBox"
                name="danceEventExtraNoteTextBox" placeholder="Extra Note"  value="'. $event->getDanceEventExtraNote() . '" >
        </div>        
        <p class="fw-bold">* marked fields are mandatory.</p>';

        return $danceEventEditFormHtml;
}
    ?>

<body>
    <div class="container-fluid">
        <a href="/adminDance">
            <button type="button" class="my-3 btn btn-primary">Go Back</button>
        </a>
        <h1>Edit <?php echo $element ?></h1>
        <div>
            <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateInput();">
                <?php $editForm = generateEditForms($element, $danceLocationToEdit, $artistToEdit, $allMusicTypes, $artistMusicTypeIds, $allDanceLocations, $allArtists, $allSessions, $eventToEdit, $artistIds); 
        echo $editForm ?>
                <button type="submit" class="btn btn-success mt-5" name="editbutton" onclick="editElement()">Edit
                    <?php echo $element ?></button>
                <a href="/adminDance">
                    <button type="button" class="btn btn-danger mt-5">Cancel</button>
                </a>
        </div>
        </form>
    </div>

    <script>
    function validateInput() {
        if ('<?php echo $element ?>' === 'Location') {
            var locationNumber = document.getElementById('danceLocationNumberTextBox').value;
            if (!Number.isInteger(Number(locationNumber))) {
                alert("Please enter a valid NUMBER for the location number.");
                return false;
            }
        } else if ('<?php echo $element ?>' === 'Artist') {
            var selectedValues = [];
            var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="musicType"]:checked');
            checkboxes.forEach(function(checkbox) {
                selectedValues.push(checkbox.value);
            });

            if (selectedValues.length === 0) {
                alert("Please select at least one music type for the artist.");
                return false;
            }
        }
        return true;
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous">
    </script>

</body>

</html>