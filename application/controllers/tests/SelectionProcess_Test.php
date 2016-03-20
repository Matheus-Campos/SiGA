<?php

/**
 ***** SelectionProcess class(on /data_types/selection_process) test class.
 *
 *
 * Provide unit tests for the SelectionProcess class hierarchy methods.
 * To access the report generated by these tests, type on the URL: '../selection_process_test'
 */

require_once("TestCase.php");
require_once(APPPATH."/data_types/selection_process/SelectionProcess.php");
require_once(APPPATH."/data_types/selection_process/ProcessSettings.php");
require_once(APPPATH."/data_types/selection_process/RegularStudentProcess.php");
require_once(APPPATH."/data_types/selection_process/SpecialStudentProcess.php");
require_once(APPPATH."/exception/SelectionProcessException.php");
require_once(APPPATH."/constants/SelectionProcessConstants.php");

class SelectionProcess_Test extends TestCase{

    public function __construct(){
        parent::__construct($this);
    }

/* Id tests */

    public function shouldInstantiateWithValidId1(){

        $id = "1";
        $course = "1";
        $name = "Edital PPGE 2016";

        $notes = "";
        try{
            $selectionProcess = new RegularStudentProcess($course, $name, $id);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with the id equals to 1";

        $this->unit->run($id, $selectionProcess->getId(), $test_name, $notes);
    }

    public function shouldInstantiateWithValidRandomId(){

        $id = rand(SelectionProcess::MIN_ID, PHP_INT_MAX);
        $course = "1";
        $name = "Edital PPGE 2016";

        $notes = "";
        try{
            $selectionProcess = new RegularStudentProcess($course, $name, $id);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with a valid random id (id = ".$id.")";

        $this->unit->run($id, $selectionProcess->getId(), $test_name, $notes);
    }

    public function shouldInstantiateWithValidFALSEId(){

        $id = FALSE;
        $course = "1";
        $name = "Edital PPGE 2016";

        $notes = "";
        try{
            $selectionProcess = new RegularStudentProcess($course, $name, $id);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with a valid FALSE id";

        $this->unit->run($id, $selectionProcess->getId(), $test_name, $notes);
    }

    public function shouldNotInstantiateWithInvalidId0(){

        $id = 0;
        $course = "1";
        $name = "Edital PPGE 2016";

        $notes = "";
        try{
            $selectionProcess = new RegularStudentProcess($course, $name, $id);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with id 0 (id = ".$id.")";

        $this->unit->run($selectionProcess, "is_false", $test_name, $notes);
    }

    public function shouldNotInstantiateWithInvalidRandomId(){

        $id = rand(PHP_INT_MAX + 1, SelectionProcess::MIN_ID-1);
        $course = "1";
        $name = "Edital PPGE 2016";

        $notes = "";
        try{
            $selectionProcess = new RegularStudentProcess($course, $name, $id);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with random invalid id (id = ".$id.")";

        $this->unit->run($selectionProcess, "is_false", $test_name, $notes);
    }

    public function shouldNotInstantiateWithInvalidNullId(){

        $id = NULL;
        $course = "1";
        $name = "Edital PPGE 2016";

        $notes = "";
        try{
            $selectionProcess = new RegularStudentProcess($course, $name, $id);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with invalid NULL id ";

        $this->unit->run($selectionProcess, "is_false", $test_name, $notes);
    }

    public function shouldNotInstantiateWithInvalidBlankId(){

        $id = "";
        $course = "1";
        $name = "Edital PPGE 2016";

        $notes = "";
        try{
            $selectionProcess = new RegularStudentProcess($course, $name, $id);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with invalid blank id";

        $this->unit->run($selectionProcess, "is_false", $test_name, $notes);
    }

    public function shouldNotInstantiateWithInvalidNotNumberId(){

        $id = "ad7&3)";
        $course = "1";
        $name = "Edital PPGE 2016";

        $notes = "";
        try{
            $selectionProcess = new RegularStudentProcess($course, $name, $id);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with invalid not number id";

        $this->unit->run($selectionProcess, "is_false", $test_name, $notes);
    }

/* Course tests */

    public function shouldInstantiateWithValidCourseId1(){

        $course = "1";
        $name = "Edital PPGE 2016";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
        }

        $test_name = "Test if instantiate with the course id equals to 1";

        $this->unit->run($course, $selectionProcess->getCourse(), $test_name);
    }

    public function shouldInstantiateWithValidRandomCourseId(){

        $course = rand(SelectionProcess::MIN_ID, PHP_INT_MAX);
        $name = "Edital PPGE 2016";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
        }

        $test_name = "Test if instantiate with a random valid course id (id = ".$course.")";

        $this->unit->run($course, $selectionProcess->getCourse(), $test_name);
    }


    public function shouldNotInstantiateWithInvalidCourseId0(){

        $course = 0;
        $name = "Edital PPGE 2016";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
        }

        $test_name = "Test if instantiate with a invalid course id (id = ".$course.")";

        $this->unit->run($selectionProcess, "is_false", $test_name);
    }
    
    public function shouldNotInstantiateWithInvalidRandomCourseId(){

        $course = rand(PHP_INT_MAX + 1, SelectionProcess::MIN_ID-1);
        $name = "Edital PPGE 2016";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
        }

        $test_name = "Test if instantiate with a invalid random course id (id = ".$course.")";

        $this->unit->run($selectionProcess, "is_false", $test_name);
    }

    public function shouldNotInstantiateWithInvalidNullCourseId(){

        $course = NULL;
        $name = "Edital PPGE 2016";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
        }

        $test_name = "Test if instantiate with a invalid NULL course id (id = ".$course.")";

        $this->unit->run($selectionProcess, "is_false", $test_name);
    }

    public function shouldNotInstantiateWithInvalidFalseCourseId(){

        $course = FALSE;
        $name = "Edital PPGE 2016";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
        }

        $test_name = "Test if instantiate with a invalid FALSE course id (id = ".$course.")";

        $this->unit->run($selectionProcess, "is_false", $test_name);
    }

    public function shouldNotInstantiateWithInvalidBlankCourseId(){

        $course = "";
        $name = "Edital PPGE 2016";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
        }

        $test_name = "Test if instantiate with a invalid blank course id (id = ".$course.")";

        $this->unit->run($selectionProcess, "is_false", $test_name);
    }

    public function shouldNotInstantiateWithInvalidNotNumberCourseId(){

        $course = "abc..";
        $name = "Edital PPGE 2016";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
        }

        $test_name = "Test if instantiate with a invalid not number course id (id = ".$course.")";

        $this->unit->run($selectionProcess, "is_false", $test_name);
    }

/* Name tests */

    public function shouldInstantiateWithValidName(){

        $course = "1";
        $name = "Edital PPGE - 2016/1";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
        }

        $test_name = "Test if instantiate with a valid name";

        $this->unit->run($name, $selectionProcess->getName(), $test_name);
    }

    public function shouldNotInstantiateWithInvalidBlankName(){

        $course = "1";
        $name = "";

        $notes = "";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with a invalid blank name";

        $this->unit->run($selectionProcess, "is_false", $test_name, $notes);
    }

    public function shouldNotInstantiateWithInvalidNullName(){

        $course = "1";
        $name = NULL;

        $notes = "";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with a invalid NULL name";

        $this->unit->run($selectionProcess, "is_false", $test_name, $notes);
    }

    public function shouldAddValidSettings(){

        $course = "1";
        $name = "Edital PPGE 1/2016";

        $notes = "";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);

            $settings = new ProcessSettings("16/03/2016", "16/05/2016");

            $selectionProcess->addSettings($settings);

        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with valid settings";

        $this->unit->run($settings, $selectionProcess->getSettings(), $test_name, $notes);
    }

    public function shouldNotAddInvalidNullSettings(){

        $course = "1";
        $name = "Edital PPGE 1/2016";

        $notes = "";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);

            $settings = NULL;

            $selectionProcess->addSettings($settings);

        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with valid settings";

        $this->unit->run($selectionProcess, "is_false", $test_name, $notes);
    }

    public function shouldNotAddInvalidBlankSettings(){

        $course = "1";
        $name = "Edital PPGE 1/2016";

        $notes = "";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);

            $settings = "";

            $selectionProcess->addSettings($settings);

        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with valid settings";

        $this->unit->run($selectionProcess, "is_false", $test_name, $notes);
    }

    public function shouldNotAddInvalidFalseSettings(){

        $course = "1";
        $name = "Edital PPGE 1/2016";

        $notes = "";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);

            $settings = FALSE;

            $selectionProcess->addSettings($settings);

        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
            $notes = "<b>Thrown Exception:</b> <i>".get_class($e)."</i> - ".$e->getMessage();
        }

        $test_name = "Test if instantiate with valid settings";

        $this->unit->run($selectionProcess, "is_false", $test_name, $notes);
    }

/* Type tests */

    public function shouldReturnTheRegularStudentType(){

        $course = "1";
        $name = "Edital PPGE 2016";

        try{
            $selectionProcess = new RegularStudentProcess($course, $name);
        }catch (SelectionProcessException $e){
            $selectionProcess = FALSE;
        }

        $test_name = "Test if get the Regular Student type";

        $this->unit->run(SelectionProcessConstants::REGULAR_STUDENT, $selectionProcess->getType(), $test_name);
    }

}
