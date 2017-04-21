
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(MODULESPATH."program/constants/SelectionProcessConstants.php");
require_once(MODULESPATH."program/exception/SelectionProcessException.php");
require_once(MODULESPATH."program/domain/selection_process/SelectionProcess.php");
require_once(MODULESPATH."program/domain/selection_process/RegularStudentProcess.php");
require_once(MODULESPATH."program/domain/selection_process/SpecialStudentProcess.php");
require_once(MODULESPATH."program/domain/selection_process/ProcessSettings.php");

require_once(MODULESPATH."/program/domain/selection_process/phases/Homologation.php");
require_once(MODULESPATH."/program/domain/selection_process/phases/PreProjectEvaluation.php");
require_once(MODULESPATH."/program/domain/selection_process/phases/WrittenTest.php");
require_once(MODULESPATH."/program/domain/selection_process/phases/OralTest.php");
class SelectiveProcess_model extends CI_Model {

	public $TABLE = "selection_process";

	const ID_ATTR = "id_process";
	const COURSE_ATTR = "id_course";
	const PROCESS_TYPE_ATTR = "process_type";
	const NOTICE_NAME_ATTR = "notice_name";
	const NOTICE_PATH_ATTR = "notice_path";
	const START_DATE_ATTR = "start_date";
	const END_DATE_ATTR = "end_date";
	const PHASE_ORDER_ATTR = "phase_order";

	// Association table data
	const PROCESS_PHASE_TABLE = "process_phase";
	const ID_PHASE_ATTR = "id_phase";
	const PROCESS_PHASE_WEIGHT_ATTR = "weight";
	const PROCESS_PHASE_GRADE_ATTR= "grade";
	const PROCESS_PHASE_START_DATE_ATTR = "start_date";
	const PROCESS_PHASE_END_DATE_ATTR = "end_date";

	// Methods
	const INSERT_ON_DB = 1;
	const UPDATE_ON_DB = 2;

	// Errors constants
	const COULDNT_SAVE_SELECTION_PROCESS = "Não foi possível salvar o processo seletivo. Cheque os dados informados.";
	const REPEATED_NOTICE_NAME = "O nome do edital informado já existe. Nome informado: ";

	public function save($process){
		$processToSave = $this->getArrayToSave($process);
		$noticeName = $process->getName();
		$previousProcess = $this->getByName($noticeName);

		// Does not exists this selection process yet
		if($previousProcess === FALSE){

            $this->db->trans_start();

            // Saves the selection process basic data
            $this->db->insert($this->TABLE, $processToSave);

            $savedProcess = $this->getByName($noticeName);
            $processId = $savedProcess[self::ID_ATTR];

            $this->load->model(
                'program/selectiveprocessconfig_model',
                'process_config_model'
            );

            $this->saveProcessPhases($process, $processId, self::INSERT_ON_DB);
            // By default, the process goes with all docs
            $this->process_config_model->addAllDocumentsToProcess($processId);
            $this->db->trans_complete();

			if($this->db->trans_status() === FALSE){
				// For some reason did not saved the selection process
				throw new SelectionProcessException(self::COULDNT_SAVE_SELECTION_PROCESS);
			}

            return $processId;
		}else{
			throw new SelectionProcessException(self::REPEATED_NOTICE_NAME.$noticeName);
		}
	}

	public function update($process, $processId){
		$processToSave = $this->getArrayToSave($process);
		$this->db->where(self::ID_ATTR, $processId);
		$updated = $this->db->update($this->TABLE, $processToSave);
		if($updated){

			$this->saveProcessPhases($process, $processId, self::UPDATE_ON_DB);
			return $processId;
		}
		else{
			// For some reason did not saved the selection process
			throw new SelectionProcessException(self::COULDNT_SAVE_SELECTION_PROCESS);
		}
	}

	private function getArrayToSave($process){
		$courseId = $process->getCourse();
		$processType = $process->getType();
		$noticeName = $process->getName();
		$settings = $process->getSettings();
		$startDate = NULL;
		$endDate = NULL;
		$phasesOrder = NULL;

		if($settings){
			$phasesOrder = serialize($settings->getPhasesOrder());
		}

		$processToSave = array(
			self::COURSE_ATTR => $courseId,
			self::PROCESS_TYPE_ATTR => $processType,
			self::NOTICE_NAME_ATTR => $noticeName,
			self::PHASE_ORDER_ATTR => $phasesOrder
		);

		return $processToSave;
	}

	private function saveProcessPhases($process, $processId, $method){

		$settings = $process->getSettings();
		if($settings){
			$phases = $settings->getPhases();
			if($method == self::UPDATE_ON_DB){
				$this->deletePhasesRemoved($processId, $phases);
			}
			foreach($phases as $phase){
				$phaseId = $phase->getPhaseId();

				if($phase->getPhaseName() === SelectionProcessConstants::HOMOLOGATION_PHASE){

					$phaseWeight = SelectionProcessConstants::HOMOLOGATION_PHASE_WEIGHT;

					$phaseGrade = SelectionProcessConstants::HOMOLOGATION_PHASE_GRADE;
				}else{
					$phaseWeight = $phase->getWeight();
					$phaseGrade = $phase->getGrade();
				}

				$arrayToSave = array(
					self::ID_ATTR => $processId,
					self::ID_PHASE_ATTR => $phaseId,
					self::PROCESS_PHASE_WEIGHT_ATTR => $phaseWeight,
					self::PROCESS_PHASE_GRADE_ATTR => $phaseGrade
				);

				$this->db->where(self::ID_ATTR, $processId);
				$this->db->where(self::ID_PHASE_ATTR, $phaseId);
			   	$result = $this->db->get(self::PROCESS_PHASE_TABLE);
				$phaseExistent = $result->num_rows() > 0;

				if($phaseExistent){
					$this->db->where(self::ID_ATTR, $processId);
					$this->db->where(self::ID_PHASE_ATTR, $phaseId);
					$this->db->update(self::PROCESS_PHASE_TABLE, $arrayToSave);

				}
				else{
					$this->db->insert(self::PROCESS_PHASE_TABLE, $arrayToSave);
				}
			}
		}
	}

	private function deletePhasesRemoved($processId, $newPhases){

		$oldPhases = $this->getProcessPhases($processId);
		$oldPhases = makeDropdownArray($oldPhases, self::ID_PHASE_ATTR, self::PROCESS_PHASE_WEIGHT_ATTR);
		$phasesToRemove = array();
		foreach ($newPhases as $newPhase) {
			$id = $newPhase->getPhaseId();
			$phaseExists = array_key_exists($id, $oldPhases);
			if($phaseExists){
				unset($oldPhases[$id]);
			}
		}

		foreach ($oldPhases as $oldPhaseId => $oldPhase) {
			$this->db->where(self::ID_PHASE_ATTR, $oldPhaseId);
			$this->db->delete(self::PROCESS_PHASE_TABLE);
		}

	}

	public function updateNoticeFile($processId, $noticePath){

		$this->db->where(self::ID_ATTR, $processId);
		$updated = $this->db->update($this->TABLE, array(
			self::NOTICE_PATH_ATTR => $noticePath
		));

		return $updated;
	}

	public function getCourseSelectiveProcesses($courseId){

		$foundProcesses = $this->get(self::COURSE_ATTR, $courseId, FALSE);

		return $foundProcesses;
	}

	public function getById($processId){

		$foundProcess = $this->get(self::ID_ATTR, $processId);
		$selectiveProcess = $this->convertArrayToObject($foundProcess);

		return $selectiveProcess;
	}

	private function convertArrayToObject($foundProcess){
		if($foundProcess !== FALSE){

			$phasesOrder = unserialize($foundProcess[SelectiveProcess_model::PHASE_ORDER_ATTR]);
	        $startDate = convertDateTimeToDateBR($foundProcess[SelectiveProcess_model::START_DATE_ATTR]);
	        $endDate = convertDateTimeToDateBR($foundProcess[SelectiveProcess_model::END_DATE_ATTR]);
	        $phases = $this->getPhases($foundProcess['id_process']);
	        $phases = $this->sortPhasesBasedInOrder($phases, $phasesOrder);
	        try{
	        	$settings = new ProcessSettings(
		            $startDate,
		            $endDate,
		            $phases,
		            $phasesOrder
	        	);
	        }
	        catch(SelectionProcessException $e){
				$selectiveProcess = FALSE;
				throw new SelectionProcessException($e);
			}
			if($foundProcess[self::PROCESS_TYPE_ATTR] === SelectionProcessConstants::REGULAR_STUDENT){

				try{

					$selectiveProcess = new RegularStudentProcess(
						$foundProcess[self::COURSE_ATTR],
						$foundProcess[self::NOTICE_NAME_ATTR],
						$foundProcess[self::ID_ATTR]
					);
					$selectiveProcess->addSettings($settings);
					$noticePath = $foundProcess[SelectiveProcess_model::NOTICE_PATH_ATTR];
					if(!is_null($noticePath)){
                    	$selectiveProcess->setNoticePath($noticePath);
					}


				}catch(SelectionProcessException $e){
					$selectiveProcess = FALSE;
				}
			}else{
				try{

					$selectiveProcess = new SpecialStudentProcess(
						$foundProcess[self::COURSE_ATTR],
						$foundProcess[self::NOTICE_NAME_ATTR],
						$foundProcess[self::ID_ATTR]
					);
					$selectiveProcess->addSettings($settings);
					$noticePath = $foundProcess[SelectiveProcess_model::NOTICE_PATH_ATTR];
					if(!is_null($noticePath)){
                    	$selectiveProcess->setNoticePath($noticePath);
					}

				}catch(SelectionProcessException $e){
					$selectiveProcess = FALSE;
				}
			}

		}else{
			$selectiveProcess = FALSE;
		}

		return $selectiveProcess;
	}

	private function getByName($name){

		$process = $this->get(self::NOTICE_NAME_ATTR, $name);

		return $process;
	}

	private function getProcessPhases($processId){
		$this->db->select(self::ID_PHASE_ATTR.",".self::PROCESS_PHASE_WEIGHT_ATTR.",".self::PROCESS_PHASE_START_DATE_ATTR.",".self::PROCESS_PHASE_END_DATE_ATTR);
		$this->db->from(self::PROCESS_PHASE_TABLE);
		$this->db->where(self::ID_ATTR, $processId);
		$processPhases = $this->db->get()->result_array();

        $processPhases = checkArray($processPhases);

        return $processPhases;
	}

    public function getPhases($processId){

        $processPhases = $this->getProcessPhases($processId);
        $phases = array();
        $validProcessPhases = !empty($processPhases) && !is_null($processPhases);
        if($validProcessPhases){
	        foreach ($processPhases as $processPhase) {
	            $processPhaseId = $processPhase['id_phase'];
	            $weight = $processPhase['weight'];
	            $startDate = convertDateTimeToDateBR($processPhase['start_date']);
	            $endDate = convertDateTimeToDateBR($processPhase['end_date']);
	            switch ($processPhaseId) {
	                case SelectionProcessConstants::HOMOLOGATION_PHASE_ID:
	                    $phase = new Homologation($processPhaseId, $startDate, $endDate);
	                    break;
	                case SelectionProcessConstants::PRE_PROJECT_EVALUATION_PHASE_ID:
	                    $phase = new PreProjectEvaluation($weight, FALSE, $processPhaseId, $startDate, $endDate);
	                    break;
	                case SelectionProcessConstants::WRITTEN_TEST_PHASE_ID:
	                    $phase = new WrittenTest($weight, FALSE, $processPhaseId, $startDate, $endDate);
	                    break;
	                case SelectionProcessConstants::ORAL_TEST_PHASE_ID:
	                    $phase = new OralTest($weight, FALSE, $processPhaseId, $startDate, $endDate);
	                    break;
	                default:
	                    $phase = NULL;
	                    break;
	            }
	            $phases[] = $phase;
	        }
        }

        return $phases;
    }


    public function saveProcessDivulgation($data, $noticeDivulgation = FALSE){
    	if($noticeDivulgation){
    		$processId = $data['id_process'];
    		$this->db->where(self::ID_ATTR, $processId);
			$this->db->where('initial_divulgation', True);
		   	$result = $this->db->get('selection_process_divulgation');
			$divulgationExistent = $result->num_rows() > 0;
			if($divulgationExistent){
				$this->db->where(self::ID_ATTR, $processId);
				$saved = $this->db->update("selection_process_divulgation", $data);
			}
			else{
	    		$saved = $this->db->insert("selection_process_divulgation", $data);
			}
		}
		else{
    		$saved = $this->db->insert("selection_process_divulgation", $data);
		}

    	return $saved;
    }

    public function getProcessDivulgations($processId, $noticeDivulgation = FALSE){

    	$this->db->select("*");
		$this->db->from('selection_process_divulgation');
		$this->db->where(self::ID_ATTR, $processId);
    	if($noticeDivulgation){
			$this->db->where('initial_divulgation', True);
    	}
    	else{
			$this->db->where('date <= CURDATE()', NULL, FALSE);
    		$this->db->order_by('date', 'DESC');
    	}
		$noticeDivulgations = $this->db->get()->result_array();

		$noticeDivulgations = checkArray($noticeDivulgations);

		if($noticeDivulgation){
			$noticeDivulgations = $noticeDivulgations[0];
		}

		return $noticeDivulgations;
    }

    public function getProcessDivulgationById($divulgationId){

    	$this->db->select("*");
    	$searchResult = $this->db->get_where('selection_process_divulgation', array('id' => $divulgationId));
  		$divulgation = $searchResult->row_array();
		$divulgation = checkArray($divulgation);

		return $divulgation;
    }

    public function sortPhasesBasedInOrder($phases, $phasesOrder){

    	$phasesInOrder = array();
    	foreach ($phases as $phase){
    		$phaseName = $phase->getPhaseName();

    		switch ($phaseName) {
    			case SelectionProcessConstants::HOMOLOGATION_PHASE:
    				$phasesInOrder[0] = $phase;
    				break;

    			case SelectionProcessConstants::PRE_PROJECT_EVALUATION_PHASE:
    				$indexOrder = array_search('pre_project', $phasesOrder);
    				$phasesInOrder[$indexOrder + 1] = $phase;
    				break;

    			case SelectionProcessConstants::WRITTEN_TEST_PHASE:
    				$indexOrder = array_search('written_test', $phasesOrder);
    				$phasesInOrder[$indexOrder + 1] = $phase;
    				break;

    			case SelectionProcessConstants::ORAL_TEST_PHASE:
    				$indexOrder = array_search('oral_test', $phasesOrder);
    				$phasesInOrder[$indexOrder + 1] = $phase;
    				break;
    		}

    	}

    	ksort($phasesInOrder);
    	return $phasesInOrder;
    }

    public function savePhaseDate($processId, $phaseId, $dataToSave){
    	$this->db->where(self::ID_ATTR, $processId);
    	$this->db->where(self::ID_PHASE_ATTR, $phaseId);
    	$saved = $this->db->update(self::PROCESS_PHASE_TABLE, $dataToSave);
    	return $saved;
    }

    public function saveSubscriptionDate($processId, $startDate, $endDate){

    	$this->db->where(self::ID_ATTR, $processId);
    	$dataToSave = array(
    		self::START_DATE_ATTR => $startDate,
    		self::END_DATE_ATTR => $endDate
    	);
    	$saved = $this->db->update($this->TABLE, $dataToSave);
    	return $saved;
    }

    public function getPhaseById($processId, $phaseId){
    	$this->db->select(self::ID_PHASE_ATTR.",".self::PROCESS_PHASE_WEIGHT_ATTR.",".self::PROCESS_PHASE_START_DATE_ATTR.",".self::PROCESS_PHASE_END_DATE_ATTR);
		$this->db->from(self::PROCESS_PHASE_TABLE);
		$this->db->where(self::ID_ATTR, $processId);
		$this->db->where(self::ID_PHASE_ATTR, $phaseId);
		$processPhases = $this->db->get()->result_array();

        $processPhases = checkArray($processPhases);

        return $processPhases;
    }

    public function addTeacherToProcess($processId, $teacherId){
        $this->db->insert("teacher_selection_process", [
            'id_process' => $processId,
            'id_teacher' => $teacherId
        ]);
    }

    public function removeTeacherFromProcess($processId, $teacherId){
        $this->db->delete("teacher_selection_process", [
            'id_process' => $processId,
            'id_teacher' => $teacherId
        ]);
    }

    public function getProcessTeachers($processId){
        $this->db->select('users.id, users.name, users.email');
        $this->db->from('users');
        $this->db->join('teacher_selection_process', 'users.id = teacher_selection_process.id_teacher');
        $this->db->where('teacher_selection_process.id_process', $processId);
        $teachers = checkArray($this->db->get()->result_array());

        return $teachers;
    }

    public function getOpenSelectiveProcesses(){

		$query = "SELECT DISTINCT selection_process.* FROM selection_process
                JOIN  selection_process_divulgation
                    ON ((selection_process_divulgation.date <= CURDATE())
                    AND (selection_process_divulgation.id_process = selection_process.id_process) AND (selection_process_divulgation.initial_divulgation = TRUE))
                WHERE (selection_process.end_date >= CURDATE()) ORDER BY 'selection_process.id_course'";
        $foundProcesses = $this->db->query($query)->result_array();
        $foundProcesses = checkArray($foundProcesses);
		$selectiveProcesses = array();
        if($foundProcesses !== FALSE){

        	foreach ($foundProcesses as $foundProcess) {
        		$selectiveProcess = $this->convertArrayToObject($foundProcess);
        		$selectiveProcesses[] = $selectiveProcess;
        	}
        }
		return $selectiveProcesses;
    }
}