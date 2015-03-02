<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('semester.php');
require_once('course.php');
require_once('discipline.php');

class Offer extends CI_Controller {

	public function newOffer($courseId){

		$this->load->model('offer_model');

		$semester = new Semester();
		$currentSemester = $semester->getCurrentSemester();

		$offer = array(
			'semester' => $currentSemester['id_semester'],
			'course' => $courseId,
			'offer_status' => "proposed"
		);

		$wasSaved = $this->offer_model->newOffer($offer);

		if($wasSaved){
			$status = "success";
			$message = "Lista de oferta criada com sucesso. Adicione disciplinas em EDITAR.";
		}else{
			$status = "danger";
			$message = "Não foi possível criar a lista de oferta. Tente novamente.";
		}
		
		$this->session->set_flashdata($status, $message);	
		redirect('usuario/secretary_offerList');
	}

	public function approveOfferList($idOffer){
		
		$this->load->model('offer_model');
		$wasApproved = $this->offer_model->approveOfferList($idOffer);

		if($wasApproved){
			$status = "success";
			$message = "Lista de Oferta aprovada com sucesso.";
		}else{
			$status = "danger";
			$message = "Não foi possível aprovar essa lista de ofertas. Verifique se há discisplinas adicionadas a essa lista, não é possível aprovar uma lista sem disciplinas.";
		}

		$this->session->set_flashdata($status, $message);
		redirect('usuario/secretary_offerList');
	}
	
	public function displayOfferedDisciplines($courseId){
		$this->load->model('offer_model');
		$idOffer = $this->offer_model->getOfferByCourseId($courseId);
		
		if($idOffer){
			$offerSize = sizeof($idOffer);
			for($i=0; $i<$offerSize; $i++){
				$disciplines[$i] = $this->getOfferDisciplines($idOffer[$i]['id_offer']);
			}
			
		}else{
			$disciplines = FALSE;
		}
		
		return $disciplines;
	}

	public function displayDisciplines($idOffer, $courseId){
		
		$disciplines = $this->getOfferDisciplines($idOffer);
		
		$course = new Course();
		$offerCourse = $course->getCourseById($courseId);

		$offerData = array(
			'idOffer' => $idOffer,
			'course' => $offerCourse,
			'disciplines' => $disciplines
		);

		loadTemplateSafelyByGroup('secretario', 'offer/new_offer', $offerData);
	}

	public function removeDisciplineFromOffer($idDiscipline, $idOffer, $idCourse){
		
		$this->load->model('offer_model');
		
		$wasRemoved = $this->offer_model->removeDisciplineFromOffer($idDiscipline, $idOffer);

		if($wasRemoved){
			$status = "success";
			$message = "Disciplina retirada com sucesso.";
		}else{
			$status = "danger";
			$message = "Não foi possível retirar essa disciplina. Cheque os códigos informados.";
		}

		$this->session->set_flashdata($status, $message);	
		redirect("offer/addDisciplines/{$idOffer}/{$idCourse}");
	}

	public function addDisciplines($idOffer, $courseId){

		$discipline = new Discipline();
		$allDisciplines = $discipline->getCourseSyllabusDisciplines($courseId);

		$course = new Course();
		$offerCourse = $course->getCourseById($courseId);

		$data = array(
			'allDisciplines' => $allDisciplines,
			'course' => $offerCourse,
			'idOffer' => $idOffer
		);

		loadTemplateSafelyByGroup('secretario', 'offer/offer_disciplines', $data);
	}

	public function addDisciplineToOffer($idDiscipline, $idOffer, $idCourse){
		
		$this->load->model('offer_model');

		$wasSaved = $this->offer_model->addDisciplineToOffer($idDiscipline, $idOffer);

		if($wasSaved){
			$status = "success";
			$message = "Disciplina adicionada com sucesso.";
		}else{
			$status = "danger";
			$message = "Não foi possível adicionar essa disciplina. Cheque os códigos informados.";
		}

		$this->session->set_flashdata($status, $message);	
		redirect("offer/addDisciplines/{$idOffer}/{$idCourse}");
	}

	public function disciplineExistsInOffer($disciplineId, $offerId){

		$this->load->model('offer_model');

		$disciplineExists = $this->offer_model->disciplineExistsInOffer($disciplineId, $offerId);

		return $disciplineExists;
	}

	private function getOfferDisciplines($idOffer){
		
		$this->load->model('offer_model');

		$disciplines = $this->offer_model->getOfferDisciplines($idOffer);

		return $disciplines;
	}

	public function getCourseOfferList($courseId, $semester){
		
		$this->load->model('offer_model');

		$offerLists = $this->offer_model->getCourseOfferList($courseId, $semester);

		return $offerLists;
	}

	public function getProposedOfferLists(){
		$this->load->model('offer_model');

		$offerLists = $this->offer_model->getProposedOfferLists();

		return $offerLists;
	}

	public function getOfferSemester($offerId){
		$this->load->model('offer_model');

		$offerSemester = $this->offer_model->getOfferSemester($offerId);

		return $offerSemester;
	}

	private function createNewOffer(){
		$this->load->model('offer_model');

	}
}
