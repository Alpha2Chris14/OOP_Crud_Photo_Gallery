<?php
    class Session{
        private $logged_in = false;
        public $user_id;
        public $message;
        public function __construct(){
            session_start();
            $this->check_message();
            $this->check_login();
            if($this->logged_in){

            }else{

            }

        }

        public function is_logged_in(){
            return $this->logged_in;
        }

        public function login($user){
            if($user){
                $this->user_id = $_SESSION['user_id'] = $user->id;
                $this->logged_in = true;
            }
        }

        public function logout(){
            unset($_SESSION['user_id']);
            unset($this->user_id);
            $this->logged_in = false;
        }

        private function check_login(){
            if(isset($_SESSION['user_id'])){
                $this->user_id = $_SESSION['user_id'];
                $this->logged_in = true;
            }else{
                unset($this->user_id);
                $this->logged_in = false;
            }
        }

        public function message(){
            if(!empty($msg)){
                //then thi is "set message"
                //make sure you understandwhy $this->message = $msg wouldn't work
                $_SESSION['message'] = $msg;
            }else{
                //then this is "get message"
                return $this->message;
            }
        }

        private function check_message(){
            //is there a message set in session
            if(isset($_SESSION['message'])){
                //if yes
                $this->message = $_SESSION['message'];
                unset($_SESSION['message']);
            }else{
                $this->message = "";
            }
        }
    }

    $session = new Session();
    $message = $session->message();
?>