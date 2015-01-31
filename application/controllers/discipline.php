<?php
class Discipline extends CI_Controller {

	public function discipline_index(){
		$this->load->template("discipline/index_discipline");
	}
	
	public function getAllDisciplines(){
		$this->load->model('discipline_model');
		$registeredDisciplines = $this->discipline_model->listAllDisciplines();
		
		return $registeredDisciplines;
	}
	
	public function newDiscipline(){
		
		$disciplineDataStatus = $this->validatesNewCourseData();
		
		if($disciplineDataStatus){
			define('WORKLOAD_PER_CREDIT', 15);
			
			$disciplineName = $this->input->post('discipline_name');
			$disciplineCode = $this->input->post('discipline_code');
			$acronym 		 = $this->input->post('name_abbreviation');
			$credits		 = $this->input->post('credits');
			$workload 		 = $credits * WORKLOAD_PER_CREDIT;
			
			$disciplineToRegister = array(
				'discipline_code'   => $disciplineCode,
				'discipline_name'   => $disciplineName,
				'name_abbreviation' => $acronym,
				'credits'			=> $credits,
				'workload' 		    => $workload
			);
			
			$this->load->model('discipline_model');
			$alreadyExists = $this->discipline_model->disciplineExists($disciplineCode,$disciplineName);
			
			if($alreadyExists['code']){
				$this->session->set_flashdata("danger", "Código de disciplina já existe no sistema");
				redirect("discipline/discipline_index");
			}else if($alreadyExists['name']){
				$this->session->set_flashdata("danger", "Disciplina já existe no sistema");
				redirect("discipline/discipline_index");
			}else{
				$this->discipline_model->saveNewDiscipline($disciplineToRegister);
				$this->session->set_flashdata("success", "Disciplina \"{$disciplineName}\" cadastrada com sucesso");
				redirect("discipline/discipline_index");
			}
		}else{
			
		}
		
	}
	
	public function formToRegisterNewDiscipline(){
		loadTemplateSafelyByPermission("discipline", "discipline/register_discipline");
	}
	
	/**
	 * Validates the data submitted on the new discipline form
	 */
	private function validatesNewCourseData(){
		$this->load->library("form_validation");
		$this->form_validation->set_rules("discipline_name", "Discipline Name", "required|trim|xss_clean|callback__alpha_dash_space");
		$this->form_validation->set_rules("discipline_code", "Discipline Code", "required");
		$this->form_validation->set_rules("name_abbreviation", "Name Abbreviation", "required|trim|xss_clean");
		$this->form_validation->set_rules("credits", "Credits", "required");
		
		$this->form_validation->set_error_delimiters("<p class='alert-danger'>", "</p>");
		$courseDataStatus = $this->form_validation->run();
	
		return $courseDataStatus;
	}
	
	function alpha_dash_space($str){
		return ( ! preg_match("/^([-a-z_ ])+$/i", $str)) ? FALSE : TRUE;
	}
}