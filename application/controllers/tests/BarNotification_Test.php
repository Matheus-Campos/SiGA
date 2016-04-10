<?php

/**
 ***** Notification class(on /data_types/notification) test class.
 *
 *
 * Provide unit tests for the Notification class methods.
 * To access the report generated by these tests, type on the URL: '../notification_test'
 */

require_once("TestCase.php");

require_once(APPPATH."/data_types/User.php");
require_once(APPPATH."/data_types/notification/Notification.php");
require_once(APPPATH."/data_types/notification/BarNotification.php");
require_once(APPPATH."/data_types/notification/RegularNotification.php");
require_once(APPPATH."/data_types/notification/ActionNotification.php");
require_once(APPPATH."/exception/NotificationException.php");

class BarNotification_Test extends TestCase{

    public function __construct(){
        parent::__construct($this);
    }

    public function createTestUser(){
        
        $user = new User(1, "John Doe", FALSE, "johndoe@mail.com");

        return $user;
    }

/* Id tests */
    public function shouldInstantiateWithValidId1(){

        $user = $this->createTestUser();
        $content = "Hi John Doe!";
        $id = "1";
        $seen = FALSE;

        $notes = "";
        try{
            $notification = new RegularNotification($user, $content, $id, $seen);
        }catch (NotificationException $e){
            $notification = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with the id equals to ".$id;

        $this->unit->run($id, $notification->id(), $test_name, $notes);
    }

    public function shouldInstantiateWithValidRandomId(){

        $user = $this->createTestUser();
        $content = "Hi John Doe!";
        $id = rand(1, PHP_INT_MAX);
        $seen = FALSE;

        $notes = "";
        try{
            $notification = new RegularNotification($user, $content, $id, $seen);
        }catch (NotificationException $e){
            $notification = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with a random valid id equals to ".$id;

        $this->unit->run($id, $notification->id(), $test_name, $notes);
    }

    public function shouldNotInstantiateWithInvalidId0(){

        $user = $this->createTestUser();
        $content = "Hi John Doe!";
        $id = 0;
        $seen = FALSE;

        $notes = "";
        try{
            $notification = new RegularNotification($user, $content, $id, $seen);
        }catch (NotificationException $e){
            $notification = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with a valid id equals to ".$id;

        $this->unit->run($notification, "is_false", $test_name, $notes);
    }

    public function shouldNotInstantiateWithInvalidRandomId(){

        $user = $this->createTestUser();
        $content = "Hi John Doe!";
        $id = rand(PHP_INT_MAX + 1, 0);
        $seen = FALSE;

        $notes = "";
        try{
            $notification = new RegularNotification($user, $content, $id, $seen);
        }catch (NotificationException $e){
            $notification = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with a random valid id equals to ".$id;

        $this->unit->run($notification, "is_false", $test_name, $notes);
    }

    public function shouldNotInstantiateWithInvalidNullId(){

        $user = $this->createTestUser();
        $content = "Hi John Doe!";
        $id = NULL;
        $seen = FALSE;

        $notes = "";
        try{
            $notification = new RegularNotification($user, $content, $id, $seen);
        }catch (NotificationException $e){
            $notification = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with a random valid id equals to NULL".$id;

        $this->unit->run($notification, "is_false", $test_name, $notes);
    }

    public function shouldNotInstantiateWithBlankId(){

        $user = $this->createTestUser();
        $content = "Hi John Doe!";
        $id = "";
        $seen = FALSE;

        $notes = "";
        try{
            $notification = new RegularNotification($user, $content, $id, $seen);
        }catch (NotificationException $e){
            $notification = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with a random valid id equals to NULL".$id;

        $this->unit->run($notification, "is_false", $test_name, $notes);
    }

    public function shouldNotInstantiateWithInvalidNotNumberId(){

        $user = $this->createTestUser();
        $content = "Hi John Doe!";
        $id = "1asd";
        $seen = FALSE;

        $notes = "";
        try{
            $notification = new RegularNotification($user, $content, $id, $seen);
        }catch (NotificationException $e){
            $notification = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with a random valid id equals to NULL".$id;

        $this->unit->run($notification, "is_false", $test_name, $notes);
    }

// User tests
    public function shouldInstantiateWithValidUser(){

        $user = $this->createTestUser();
        $content = "Hi John Doe!";
        $id = "1";
        $seen = FALSE;

        $notes = "";
        try{
            $notification = new RegularNotification($user, $content, $id, $seen);
        }catch (NotificationException $e){
            $notification = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with a valid user";

        $this->unit->run($user, $notification->user(), $test_name, $notes);
    }    

    public function shouldNotInstantiateWithInvalidNullUser(){

        $user = NULL;
        $content = "Hi John Doe!";
        $id = "1";
        $seen = FALSE;

        $notes = "";
        try{
            $notification = new RegularNotification($user, $content, $id, $seen);
        }catch (NotificationException $e){
            $notification = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with invalid NULL user";

        $this->unit->run($notification, "is_false", $test_name, $notes);
    }

    public function shouldNotInstantiateWithInvalidNotUserUser(){

        $user = new DateTime();
        $content = "Hi John Doe!";
        $id = "1";
        $seen = FALSE;

        $notes = "";
        try{
            $notification = new RegularNotification($user, $content, $id, $seen);
        }catch (NotificationException $e){
            $notification = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with a  not User object";

        $this->unit->run($notification, "is_false", $test_name, $notes);
    }

    public function shouldNotInstantiateWithInvalidFalseUser(){

        $user = FALSE;
        $content = "Hi John Doe!";
        $id = "1";
        $seen = FALSE;

        $notes = "";
        try{
            $notification = new RegularNotification($user, $content, $id, $seen);
        }catch (NotificationException $e){
            $notification = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with invalid FALSE user";

        $this->unit->run($notification, "is_false", $test_name, $notes);
    }    
}