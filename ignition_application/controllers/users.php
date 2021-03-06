<?php

/*
|--------------------------------------------------------------------------
| Ignition ignitionpowered.co.uk
|--------------------------------------------------------------------------
|
| This class extends the functionality of Ignition. You can add your
| own custom logic here.
|
*/

require_once APPPATH.'/controllers/ignition/users.php';

class Users extends IG_Users {

    // view user
    function view($userID, $page = 1)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByIdWithFollowingStatus($userID, $this->session->userdata('UserID'));

        if($user == null)
            show_404();

        // paging
        $resultsPerPage = 20;
        $offset = ($page-1) * $resultsPerPage;

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "User");
        $data['user'] = $user;

        // get event feed
        $this->load->model('Event');
        $data['events'] = $this->Event->getEvents($userID, null, null, $this->session->userdata('DateTimeFormat'), $offset, $resultsPerPage);
        $data['pageNumber'] = $page;

        // get games currently playing
        $this->load->model('Game');
        $data['currentlyPlaying'] = $this->Game->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('control/events', $data);
        $this->load->view('user/profile/footer', $data);
        $this->load->view('templates/footer', $data);
    }

    // view user collection
    function collection($userID)
    {   
        // get user data
        $this->load->model('User');
        $user = $this->User->getUserByIdWithFollowingStatus($userID, $this->session->userdata('UserID'));

        if($user == null)
            show_404();

        // page variables
        $this->load->model('Page');
        $data = $this->Page->create($user->Username, "Collection");
        $data['user'] = $user;

        // get game collection stats
        $this->load->model('Game');

        // get platforms, lists and statuses for filtering
        $data['platforms'] = $this->Game->getPlatformsInCollection($userID);
        $data['lists'] = $this->Game->getListsInCollection($userID);
        $data['statuses'] = $this->Game->getStatusesInCollection($userID);

        // get games currently playing
        $data['currentlyPlaying'] = $this->Game->getCurrentlyPlaying($userID);

        // load views
        $this->load->view('templates/header', $data);
        $this->load->view('user/profile/header', $data);
        $this->load->view('user/collection', $data);
        $this->load->view('templates/footer', $data);
    }

    function getCollection()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('userID', 'userID', 'trim|xss_clean');
        $this->form_validation->set_rules('page', 'page', 'trim|xss_clean');
        $this->form_validation->set_rules('filters', 'filters', 'xss_clean');
        $this->form_validation->run();

        $userID = $this->input->post('userID');
        $page = $this->input->post('page');
        $filters = json_decode($this->input->post('filters'));

        // check that user is VALID
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_user_invalid_id'),false,false);
            return;
        }

        // paging
        $resultsPerPage = 30;
        $offset = ($page-1) * $resultsPerPage;

        // get collection
        $this->load->model('Game');
        $result['collection'] = $this->Game->getCollection($userID, $filters, $offset, $resultsPerPage);
        $result['stats'] = $this->Game->getCollection($userID, $filters, null, null);

        // return success
        $result['error'] = false;   
        echo json_encode($result);
    }

    // add comment
    function comment()
    {
        // call Ignition comment function
        parent::comment();

        // if a comment for an event (comment type id = 2) then bump the last updated date stamp of the event
        if($this->input->post('commentTypeID') == 2) {
            $this->load->model('Event');
            $this->Event->bumpEvent($this->input->post('linkID'));
        }
    }

    // follow or unfollow a user
    function follow()
    {
        // form validation
        $this->load->library('form_validation');
        $this->form_validation->set_rules('followUserID', 'followUserID', 'trim|xss_clean');
        $this->form_validation->run();

        $followUserID = $this->input->post('followUserID');
        $userID = $this->session->userdata('UserID');

        
        // check that user is logged in
        if($userID <= 0)
        {
            $this->returnError($this->lang->line('error_logged_out'),"/login","Login");
            return;
        }

        // check following UserID is valid
        if($followUserID <= 0)
        {
            $this->returnError($this->lang->line('error_user_invalid_id'),false,false);
            return;
        }

        // follow or unfollow user
        $this->load->model('User');
        $result['followingUser'] = $this->User->followUser($userID, $followUserID);

        // return success
        $result['error'] = false;
        echo json_encode($result);
    }
}