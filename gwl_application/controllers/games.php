<?php

class Games extends CI_Controller {
    
    function returnError($errorMessage)
    {
        $result['error'] = true; 
        $result['errorMessage'] = $errorMessage; 
        echo json_encode($result);
    }

    // add game
	function add()
	{
		// form validation
		$this->load->library('form_validation');
		$this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');
        $this->form_validation->set_rules('listID', 'listID', 'trim|xss_clean');

		$GBID = $this->input->post('gbID');
        $listID = $this->input->post('listID');
		$userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError("You've been logged out. Please login and try again.");
            return;
        }

        // load game model
        $this->load->model('Game');

        // get game details from Giant Bomb API
        $game = $this->Game->getGameByID($GBID, null);

        // if API returned nothing
        if($game == null)
        {
            $this->returnError("Shit sticks! The Giant Bomb API may be down. Please try again.");
            return;
        }

        // if game isnt in db
        if(!$this->Game->isGameInDB($GBID))
        {
            // add game to db
            if(!$this->Game->addGame($game))
            {
                // insert failed
                $this->returnError("We were unable to add this game to your collection. Please try again.");
                return;
            }
        }

        // check if game is in collection
        $collection = $this->Game->isGameIsInCollection($GBID, $userID);
        
        // default value for auto selected platform
        $result['autoSelectPlatform'] = null;

        // if game isnt in collection
        if($collection == null) 
        {
            // add game to users collection
            $collectionID = $this->Game->addToCollection($GBID, $userID, $listID);

            // if game has one platform, automaticly add it
            if($collectionID != null && count($game->platforms) == 1)
            {
                // load platform model
                $this->load->model('Platform');

                // get first (and only) platform
                $platform = $game->platforms[0];

                // if platform isnt in db
                if(!$this->Platform->isPlatformInDB($platform->id))
                {
                    // add platform to db
                    $this->Platform->addPlatform($platform);
                }

                // add game to platform in collection
                if($this->Game->addPlatform($collectionID, $platform->id))
                {
                    // tell UI to check platform that was auto-selected
                    $result['autoSelectPlatform'] = $platform->id; 
                }
            }
        // game is in collection, update list
        } else {
            $this->Game->updateList($GBID, $userID, $listID);
        }

        // return success
        $result['error'] = false;   
        echo json_encode($result);
	}

    // change played status of game
    function changeStatus()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');
        $this->form_validation->set_rules('statusID', 'statusID', 'trim|xss_clean');

        $GBID = $this->input->post('gbID');
        $statusID = $this->input->post('statusID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError("You've been logged out. Please login and try again.");
            return;
        }

        // load game model
        $this->load->model('Game');
       
        // check if game is in collection
        $collection = $this->Game->isGameIsInCollection($GBID, $userID);
       
        // if game is in collection
        if($collection != null) 
        {
            // update played status
            $this->Game->updateStatus($GBID, $userID, $statusID);
        } else {
            // return error
            $this->returnError("You haven't added this game to your collection. How did you get here?");
            return;
        }

        // return success
        $result['error'] = false;   
        echo json_encode($result);
    }

    // remove game from collection
    function remove()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');

        $GBID = $this->input->post('gbID');
        $userID = $this->session->userdata('UserID');
        
        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError("You've been logged out. Please login and try again.");
            return;
        }

        // load game model
        $this->load->model('Game');

        // remove game from collection
        $this->Game->removeFromCollection($GBID, $userID);
       
        // return success
        $result['error'] = false;  
        echo json_encode($result);
    }

    function addPlatform()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('gbID');
        $GBPlatformID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError("You've been logged out. Please login and try again.");
            return;
        }

        // load game model
        $this->load->model('Game');

        // check if game is in collection
        $collection = $this->Game->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError("You haven't added this game to your collection. You probably need to do that first kido.");
            return;
        }
        
        // if game is not on platform, add it
        if(!$this->Game->isGameOnPlatformInCollection($collection->ID, $GBPlatformID))
        {
            // load platform model
            $this->load->model('Platform');

            // if platform isnt in db
            if(!$this->Platform->isPlatformInDB($GBPlatformID))
            {
                // get platform data 
                $platform = $this->Platform->getPlatform($GBPlatformID);

                // if API returned nothing
                if($platform == null)
                {
                    $this->returnError("Shit sticks! The Giant Bomb API may be down. Please try again.");
                    return;
                }

                // add platform to db
                $this->Platform->addPlatform($platform);
            }

            // add game to platform in collection
            $this->Game->addPlatform($collection->ID, $GBPlatformID);
        }
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

    function removePlatform()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');
        $this->form_validation->set_rules('platformID', 'platformID', 'trim|xss_clean');

        $GBID = $this->input->post('gbID');
        $GBPlatformID = $this->input->post('platformID');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError("You've been logged out. Please login and try again.");
            return;
        }

        // load game model
        $this->load->model('Game');

        // check if game is in collection
        $collection = $this->Game->isGameIsInCollection($GBID, $userID);

        // if game is not in collection
        if($collection == null)
        {
            $this->returnError("You haven't added this game to your collection. You probably need to do that first kido.");
            return;
        }
        
        // remove platform from game in collection
        $this->Game->removePlatform($collection->ID, $GBPlatformID);
        
        $result['error'] = false; 
        echo json_encode($result);
        return;
    }

    // change played status of game
    function saveProgression()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('gbID', 'gbID', 'trim|xss_clean');
        $this->form_validation->set_rules('currentlyPlaying', 'currentlyPlaying', 'trim|xss_clean');
        $this->form_validation->set_rules('hoursPlayed', 'hoursPlayed', 'trim|xss_clean');
        $this->form_validation->set_rules('dateCompleted', 'dateCompleted', 'trim|xss_clean');

        $GBID = $this->input->post('gbID');
        $currentlyPlaying = $this->input->post('currentlyPlaying');
        $hoursPlayed = $this->input->post('hoursPlayed');
        $dateCompleted = $this->input->post('dateCompleted');
        $userID = $this->session->userdata('UserID');

        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError("You've been logged out. Please login and try again.");
            return;
        }

        // load game model
        $this->load->model('Game');
       
        // check if game is in collection
        $collection = $this->Game->isGameIsInCollection($GBID, $userID);
       
        // if game is in collection
        if($collection != null) 
        {
            // update played status
            $this->Game->updateProgression($collection->ID, $currentlyPlaying, $hoursPlayed, $dateCompleted);
        } else {
            // return error
            $this->returnError("You haven't added this game to your collection. How did you get here?");
            return;
        }

        // return success
        $result['error'] = false;   
        echo json_encode($result);
    }

    // view game
    function view($gbID)
    {   
        // lookup game
        $this->load->model('Game');
        $game = $this->Game->getGameByID($gbID, $this->session->userdata('UserID'));

        if($game == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($game->name, "Search");
        $data['game'] = $game;

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('games', $data);
        $this->load->view('templates/footer', $data);
    }
}
?>