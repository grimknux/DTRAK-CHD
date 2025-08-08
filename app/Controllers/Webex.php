<?php

namespace App\Controllers;
use App\Models\HomeModel;
use App\Models\ValidationModel;
use App\Models\EmployeeModel;
use App\Libraries\CustomObj;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use CodeIgniter\I18n\Time;


class Webex extends BaseController
{


    public $session;


    private $clientId;
    private $clientSecret;
    private $redirectUri;
    protected $client; // Declare the client property
    protected $time; // Declare the client property
    public $customobj;
    

    public function __construct()

    {
              
        $this->session = session();
        // Replace with your Webex API credentials
        $this->clientId = 'C630052594e74199794eb14d4bfc5ad4574c21fc5a102a73f7b01a8c6ced2f936';
        $this->clientSecret = '47c70bdafafeca30dbabc8f5dff0eb4fdb9c95154b7a6eba9a3679104c66bed8';
        $this->redirectUri = base_url('call-back');
        $this->customobj = new CustomObj();
        $this->client = new Client();

        $this->time = new Time();

        helper(['form','html','cookie','array', 'test']);
        
        
    }


    public function index()
    {

        if(!(session()->has('logged_user'))){
            return redirect()->to(base_url('login-page'));
        }

        $data = [
            "page_title" => "Webex Video Conference",
            "page_heading" => "Webex Meeting",
            "sub_head" => "Webex Meet",
            "navactive" => "webmeet",
        ];


        

        

        return view('webexmeet', $data);
    }


    public function addwebex()
    {

        if(!(session()->has('logged_user'))){
            return redirect()->to(base_url('login-page'));
        }

        $data = [
            "page_title" => "Webex Video Conference",
            "page_heading" => "Schedule Webex Meeting",
            "sub_head" => "Webex Meet",
            "navactive" => "addmeet",
        ];

        

        return view('webexsched', $data);
    }


    public function createMeeting()
    {

        if(!(session()->has('logged_user'))){
            return redirect()->to(base_url('login-page'));
        }
        $data = [];
        $validation = \Config\Services::validation();

        if(session()->has('xcsrf_token')){
            $csrfToken = session()->get('xcsrf_token');
        }else{
            $csrfToken = $this->request->getHeaderLine('X-CSRF-Token');
        }
        
        if(!empty($csrfToken) && $this->customobj->validateCSRFToken($csrfToken))
        {


            if($this->request->getMethod() === 'post')
            {

                $rules = [
                    'daterequest' => [
                        'rules' => 'required|valid_date[Y-m-d]',
                        'errors' => [
                            'required' => 'Please select Date Request!',
                            'valid_date' => 'Please enter a valid Date!',
                        ],
                    ],
                    'title' => [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Please provide a Title!',
                        ],
                    ],
                    'start' => [
                        'rules' => 'required|valid_date[Y-m-d\TH:i]',
                        'errors' => [
                            'required' => 'Please select Start Date!',
                            'valid_date' => 'Please enter a valid Date!',
                        ],
                    ],
                    'meetingDurationHrs' => [
                        'rules' => 'required|is_natural_no_zero|less_than[25]',
                        'errors' => [
                            'required' => 'Please select Duration Hours!',
                            'is_natural_no_zero' => 'Please enter a Valid Value!',
                            'less_than' => 'Please enter a Number between 1 or 24!',
                        ],
                    ],
                    'meetingDurationMin' => [
                        'rules' => 'required|is_natural|less_than[51]',
                        'errors' => [
                            'required' => 'Please select Duration Minutes!',
                            'is_natural' => 'Please select a valid Value!',
                            'less_than' => 'Please enter a Number between 0 or 50!',
                        ],
                    ],
                    'recurrence' => 'permit_empty',
                    'check-enddate' => 'permit_empty',

                ];

                if($this->request->getPost('recurrence') != ''){

                    $rules['recurrenceType'] = [
                        'rules' => 'required|in_list[DAILY,WEEKLY]',
                        'errors' => [
                            'required' => 'Please select a Recurrence Type!',
                            'in_list' => 'Please select a Valid Recurrence Type!',
                        ],
                    ];

                }

                if($this->request->getPost('recurrence') != '' && $this->request->getPost('recurrenceType') == 'DAILY'){

                    $rules['daysInterval'] = [
                        'rules' => 'required|is_natural_no_zero',
                        'errors' => [
                            'required' => 'Please enter Daily Interval!',
                            'is_natural_no_zero' => 'Please enter a Valid Value!',
                        ],
                    ];

                }

                if($this->request->getPost('recurrence') != '' && $this->request->getPost('recurrenceType') == 'WEEKLY'){

                    $rules['weeksInterval'] = [
                        'rules' => 'required|is_natural_no_zero',
                        'errors' => [
                            'required' => 'Please enter Interval!',
                            'is_natural_no_zero' => 'Please enter a Valid Value!',
                        ],
                    ];

                    $rules['byday'] = [
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'Please select Day!',
                            //'in_list' => 'Please select Days from Sunday to Saturday!',
                        ],
                    ];

                }

                if($this->request->getPost('check-enddate') != ''){

                    $rules['endDate'] = [
                        'rules' => 'required|valid_date[Y-m-d]',
                        'errors' => [
                            'required' => 'Please select End Date!',
                            'valid_date' => 'Please enter a valid Date!',
                        ],
                    ];

                }

                if($this->validate($rules))
                {
                    $daterequest = $this->request->getPost('daterequest', FILTER_SANITIZE_STRING);
                    $title = $this->request->getPost('title', FILTER_SANITIZE_STRING);
                    $password = $this->request->getPost('meetingPassword', FILTER_SANITIZE_STRING);
                    $start_raw = $this->request->getPost('start', FILTER_SANITIZE_STRING);

                    $durationHrs = $this->request->getPost('meetingDurationHrs', FILTER_SANITIZE_STRING);
                    $durationMin = $this->request->getPost('meetingDurationMin', FILTER_SANITIZE_STRING);
                    $dateTime = date('Y-m-d H:i:s', strtotime($start_raw));
                    $start = date('c', strtotime($start_raw));
                    $end = date('c', strtotime('+'.$durationHrs.' hour +'.$durationMin.' minutes', strtotime($dateTime)));
                    $attendees = $this->request->getPost('meetingAttendees', FILTER_SANITIZE_STRING);
                    $recurrence = $this->request->getPost('recurrence', FILTER_SANITIZE_STRING);
                    $recurrenceType = $this->request->getPost('recurrenceType', FILTER_SANITIZE_STRING);
                    $daysInterval = $this->request->getPost('daysInterval', FILTER_SANITIZE_STRING);
                    $weeksInterval = $this->request->getPost('weeksInterval', FILTER_SANITIZE_STRING);
                    $byday = $this->request->getPost('byday', FILTER_SANITIZE_STRING);
                    $checkenddate = $this->request->getPost('check-enddate', FILTER_SANITIZE_STRING);
                    $endDate = $this->request->getPost('endDate', FILTER_SANITIZE_STRING);

                    $meetingData = [
                        'daterequest' => $daterequest,
                        'title' => $title,
                        'password' => $password,
                        'start' => $start,
                        'end' => $end,
                        'attendees' => $attendees,
                        'recurrence' => $recurrence,
                        'recurrenceType' => $recurrenceType,
                        'daysInterval' => $daysInterval,
                        'weeksInterval' => $weeksInterval,
                        'byday' => $byday,
                        'checkenddate' => $checkenddate,
                        'endDate' => $endDate,
                    ];

                    session()->set('meeting_data', $meetingData);
                
                    if(!(session()->has('webex_code')))
                    {
                    
                        // Redirect user to Webex OAuth authorization
                        $authUrl = "http://chd1.webex.org:3000/v1/authorize?client_id=C630052594e74199794eb14d4bfc5ad4574c21fc5a102a73f7b01a8c6ced2f936&response_type=code&redirect_uri=http%3A%2F%2Fchd1.webex.org%2Fidprint%2Fcall-back&scope=meeting%3Arecordings_read%20meeting%3Aschedules_read%20meeting%3Aschedules_write%20spark%3Akms&state=set_state_here";

                        $data['status'] = 'success';
                        $data['url'] = true;
                        $data['authUrl'] = $authUrl;

                        session()->set('xcsrf_token', $csrfToken);
                        session()->set('fromcallback', true);

                    }else{

                        $token = session()->get('webex_code');
                        //$meeting_id = session()->get('meeting_id');
                        //$title = $data['title'];
                        //return $this->response->setJSON(['code' => $meeting_id, 'status' => 'withpost']);
                        //echo $code;
                        
                        $meeting = $this->insertMeeting($token);
                        $meetings =json_decode($meeting);
                        
                        // Store the code in the session
                        
                        //echo $code;
                        if($meetings->status == 'success'){
                            session()->set('valid_webex_request', true);
                            session()->set('meeting_id', $meetings->id);
                
                            return redirect()->to(base_url('createMeeting'));
                            
                        }else{
                            //echo "<b>".$meetings->statuscode."</b>: ".$meetings->message;
                            $data['status'] = 'error';
                            $data['message'] = "<b>".$meetings->statuscode."</b>: ".$meetings->message;
                        }

                    }

                    //$data['status'] = 'success';
                    //$data['message'] = $byday;

                }else{

                    $data['daterequest'] = $validation->getError('daterequest');
                    $data['title'] = $validation->getError('title');
                    $data['start'] = $validation->getError('start');
                    $data['meetingDurationHrs'] = $validation->getError('meetingDurationHrs');
                    $data['meetingDurationMin'] = $validation->getError('meetingDurationMin');
                    $data['recurrenceType'] = $validation->getError('recurrenceType');
                    $data['daysInterval'] = $validation->getError('daysInterval');
                    $data['weeksInterval'] = $validation->getError('weeksInterval');
                    $data['byday'] = $validation->getError('byday');
                    $data['endDate'] = $validation->getError('endDate');
                    $data['recurrence'] = $validation->getError('recurrence');

                }

            }else{

                if(!(session()->has('valid_webex_request'))){
                    
                    $data['status'] = 'error';
                    $data['message'] = 'Invalid POST request';
                }else{
                    //$code = session()->get('webex_code');
                    $meeting_id = session()->get('meeting_id');
                    //$title = $data['title'];                    





                    if(session()->has('fromcallback')){
                        $this->session->setTempdata('success', 'Successfully Created Meeting No: ' . $meeting_id, 3); 
                        
                        session()->remove('fromcallback');
                        return redirect()->to(base_url('add-webex-schedule'));

                    }

                    $data['status'] = 'success';
                    $data['message'] = 'Successfully Created Meeting No: ' . $meeting_id;

                    session()->remove('meeting_data');
                    session()->remove('xcsrf_token');
                    session()->remove('valid_webex_request');
                }
                

            }


        }else{

            $data['status'] = 'error';
            $data['message'] = 'Invalid CSRF TOKEN';
        }

        return $this->response->setJSON($data);
    }





    public function callback()
    {
        // Handle the OAuth callback
        //$code = $codes;
        $code = $_GET['code'];
        
        //$state = $_GET['state'] ?? '';
        // Use $code to fetch access token using Webex API
        $token = $this->fetchAccessToken($code);
        $tokens = json_decode($token);
        $accesstoken = "";

        if($tokens->status == 'success'){
            //echo $tokens->access_token;
            $accesstoken = $tokens->access_token;
        }else{
            echo "<b>".$tokens->statuscode."</b>: ".$tokens->message;
        }

        $meeting = $this->insertMeeting($accesstoken);
        $meetings =json_decode($meeting);
        
        // Store the code in the session
        
        //echo $code;
        if($meetings->status == 'success'){
            session()->set('webex_code', $accesstoken);
            session()->set('valid_webex_request', true);
            session()->set('meeting_id', $meetings->id);

            return redirect()->to(base_url('createMeeting'));
            
        }else{
            echo "<b>".$meetings->statuscode."</b>: ".$meetings->message;
        }
        

    }


    private function fetchAccessToken($code)
    {
        // Get the access token from the Webex API
        $url = "http://chd1.webex.org:3000/v1/access_token";
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri,
        ];

        try {
            $response = $this->client->post($url, [
                'form_params' => $data,
            ]);
    
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
    
            if ($statusCode == 200) {
                // The access token was fetched successfully
                $bodyData = json_decode($body);
                $accesstoken = $bodyData->access_token;

                $data = array('status' => 'success', 'access_token' => $accesstoken);
              
                return json_encode($data);

            } else {
                $bodyData = json_decode($body);
                //return $statusCode . " - " . $body;
                $data = array('status' => 'error', 'statuscode' => $statusCode, 'message' => $bodyData->message);

                return json_encode($data);
            }

        } catch (RequestException $e) {
            
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $body = $response->getBody()->getContents();
                $bodyData = json_decode($body);

                //return $statusCode . " - " . $body;
                $data = array('status' => 'error', 'statuscode' => $statusCode, 'message' => $bodyData->message);
                return json_encode($data);
                // Log or handle the error here
            }

        }

        //return 'test';
    }

    private function insertMeeting($token)
    {

        $meetingdata = session()->get('meeting_data');
        // Create a new meeting using the access token
        $url = "http://chd1.webex.org:3000/v1/meetings";

        $recurrence = "";

       
        if ($meetingdata['recurrence'] == '1') {
            $recurrence .= "FREQ=".$meetingdata['recurrenceType'].";";
            //$data['recurrence']['type'] = strtolower($meetingdata['recurrenceType']);

            if($meetingdata['recurrenceType'] == 'DAILY'){
                $recurrence .= "INTERVAL=".$meetingdata['daysInterval'];
                //$data['recurrence']['interval'] = $meetingdata['daysInterval'];

            }elseif($meetingdata['recurrenceType'] == 'WEEKLY'){
                $byDay = implode(',', $meetingdata['byday']);
                $recurrence .= "INTERVAL=".$meetingdata['weeksInterval'].";";
                $recurrence .= "BYDAY=".$byDay;
                //$data['recurrence']['interval'] = $meetingdata['weeksInterval'];
                //$data['recurrence']['byday'] = $meetingdata['byday'];

            }
            //$data['recurrence'] = $recurrence;

            if($meetingdata['checkenddate'] == "1"){
                $endDate = date('Ymd', strtotime($meetingdata['endDate']));
                $recurrence .= ";UNTIL=".$endDate;
            }
        }


        $data = [
            'timezone' => 'Asia/Kuala_Lumpur',
            "title" => $meetingdata['title'],
            "password" => $meetingdata['password'],
            "start" => $meetingdata['start'],
            "end" => $meetingdata['end'],
            'recurrence' => $recurrence,

            /*"adhoc" =>false,
            "enabledAutoRecordMeeting" => false,
            "allowAnyUserToBeCoHost" => false,
            "enabledJoinBeforeHost" => false,
            "enableConnectAudioBeforeHost" => false,
            "excludePassword" => false,
            "publicMeeting" => false,
            "enabledWebcastView" => false,
            "enableAutomaticLock" => false,
            "allowFirstUserToBeCoHost" => false,
            "allowAuthenticatedDevices" => false,
            "sendEmail" => true,
            "requireAttendeeLogin" => false,
            "restrictToInvitees" => false,
            'duration' => 60,*/
        ];
        
        $headers = [

            'Authorization' => 'Bearer ' . $token,
            
        ];


        try {

            $response = $this->client->post($url, [
                'json' => $data,
                'headers' => $headers,
            ]);

            if ($response->getStatusCode() == 200) {
                // The meeting was created successfully
                $bodyData = json_decode($response->getBody());
                $id = $bodyData->id;

                $data = array('status' => 'success', 'id' => $id);
                return json_encode($data);
                
            } else {
                $body = json_decode($response->getBody());
                $statusCode = $response->getStatusCode();

                $bodyData = json_decode($body);
                //return $statusCode . " - " . $body;
                $data = array('status' => 'error', 'statuscode' => $statusCode, 'message' => $bodyData->message);

                return json_encode($data);

            }


        } catch (RequestException $e) {

            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $body = $response->getBody()->getContents();

                $bodyData = json_decode($body);

                //return $statusCode . " - " . $body;
                $data = array('status' => 'error', 'statuscode' => $statusCode, 'message' => $bodyData->message." - ".$recurrence);
                return json_encode($data);
                // Log or handle the error here
            }


        }


    }




    
}
 