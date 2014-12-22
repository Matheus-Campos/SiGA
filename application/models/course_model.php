<?php 
require_once(APPPATH."/exception/CourseNameException.php");
require_once(APPPATH."/exception/CourseException.php");
class Course_model extends CI_Model {

	public function getCommonAttributesForThisCourse($courseId){
		
		$idExists = $this->checkExistingId($courseId);

		if($idExists){
			$searchResult = $this->db->get_where('course', array('id_course' => $courseId));
			$foundCourse = $searchResult->row_array();
		}else{
			$foundCourse = FALSE;
		}
	
		return $foundCourse;		
	}

	public function getCourseIdByCourseName($courseName){

		$courseId =$this->getCourseIdForThisCourseName($courseName);

		return $courseId;
	}

	private function getCourseIdForThisCourseName($courseName){
		$this->db->select('id_course');
		$searchResult = $this->db->get_where('course', array('course_name' => $courseName));
		$searchResult = $searchResult->row_array();

		$courseId = $searchResult['id_course'];

		return $courseId;
	}

		public function checkExistingCourseTypeId($course_type_id){

		$foundType = $this->getCourseTypeById($course_type_id);

		$idExists = FALSE;
		if(sizeof($foundType) === 0){
			$idExists = FALSE;
		}else{
			$idExists = TRUE;
		}

		return $idExists;
	}

	public function getCourseTypeById($courseId){
		$this->db->select('course_type');
		$searchResult = $this->db->get_where('course', array('id_course' => $courseId));
		$searchResult = $searchResult->row_array();

		$foundCourseType = $searchResult['course_type'];

		return $foundCourseType;
	}

	/**
	 * Get all courses registered on database
	 * @return An array with the courses. Each position is a tuple of the relation.
	 */
	public function getAllCourses(){
		$this->db->select('*');
		$this->db->from('course');
		$registeredCourses = $this->db->get()->result_array();

		return $registeredCourses;
	}

	/**
	 * Save a course on the database
	 * @param $courseToSave - Array with the attributes of the course
	 * @return TRUE if the insertion was made or FALSE if it does not
	 */
	public function saveCourse($courseToSave){
		
		$courseNameToSave = $courseToSave['course_name'];
		$courseNameAlreadyExists = $this->courseNameAlreadyExists($courseNameToSave);
	
		$insertionStatus = FALSE;

		if($courseNameAlreadyExists === FALSE){
			$this->db->insert("course", $courseToSave);
			$insertionStatus = TRUE;
		}else{
			$insertionStatus = FALSE;
		}

		return $insertionStatus;
	}
	
	public function saveSecretary($secretary, $courseName){
		$this->db->select('id_course');
		$courseId = $this->db->get_where('course',array('course_name'=> $courseName))->row_array();
		
		$saveSecretary = array_merge($secretary,$courseId);
		$save = $this->db->insert("secretary_course", $saveSecretary);
		
		if($save){
			$insertionStatus = TRUE;
		}else{
			$insertionStatus = FALSE;
		}

		return $insertionStatus;
		
	}
	
	/**
	 * Delete a course by its id
	 * @param int $id_course - The course id to be deleted
	 * @return TRUE if the exclusion was made right or FALSE if it does not
	 */
	public function deleteCourseById($id_course){
		$idExists = $this->checkExistingId($id_course);
		
		$courseWasDeleted = FALSE;
		if($idExists){
			$this->db->delete('course', array('id_course' => $id_course));
			$courseWasDeleted = TRUE;
		}else{
			$courseWasDeleted = FALSE;
		}

		return $courseWasDeleted;
	}

	/**
	 * Check if a given course id exists on the database
	 * @param $course_id
	 * @return TRUE if the id exists or FALSE if does not
	 */
	public function checkExistingId($course_id){
		$foundCourse = $this->getCourseById($course_id);

		$idExistis = FALSE;
		if(sizeof($foundCourse) === 0){
			$idExistis = FALSE;
		}else{
			$idExistis = TRUE;
		}

		return $idExistis;
	}
	
	/**
	 * Function to select one course by its unique id
	 * @param int $id
	 * @return object $courseAsked if it exists, boolean $courseAsked if not exists
	 */
	public function getCourseById($id){
		$this->db->where('id_course',$id);
		$this->db->from('course');
		$courseAsked = $this->db->get()->row();
		return $courseAsked;
	}
	
	/**
	 * Update course attributes
	 * @param String $id_course - The course id to be updated
	 * @param array $courseToUpdate - The new course data to replace the old one
	 * @return void
	 */
	public function updateCourse($id_course, $courseToUpdate){

		$idExists = $this->checkExistingId($id_course);

		if($idExists){
			$newCourseName = $courseToUpdate['course_name'];			
			$courseNameHasChange = $this->checkIfCourseNameHasChanged($id_course, $newCourseName);
			$courseNameAlreadyExists = $this->courseNameAlreadyExists($newCourseName);

			// The course name has to change and do not exists or already exists and do not change
			$courseNameIsOk = ($courseNameAlreadyExists && !$courseNameHasChange) || (!$courseNameAlreadyExists && $courseNameHasChange);

			if($courseNameIsOk){
				$this->updateCourseOnDb($id_course, $courseToUpdate);
			}else{
				throw new CourseNameException("O curso '".$newCourseName."' já existe.");
			}
		}else{
			throw new CourseException("Cannot update this course. The informed ID does not exists.");
		}
	}
	
	public function getSecretaryByCourseId($id_course){
		
		$this->db->select('id_secretary, id_group, id_user');
		$secretary_return = $this->db->get_where('secretary_course', array('id_course'=>$id_course))->row_array();
		
		return $secretary_return;
		
	}

	/**
	 * Update secretary atributes of a master degree course
	 * @param int $idCourseToUpdate - The course to update the secretary
	 * @param array $newSecretary - The new secretary to replace the old one
	 */
	public function updateCourseSecretary($idCourseToUpdate, $newSecretary){
		// Validate attributes
		$secretaryId = $this->getSecretaryIdByCourseId($idCourseToUpdate);
		$this->updateSecretary($secretaryId, $newSecretary);
	}

	private function getSecretaryIdByCourseId($courseId){
		$this->db->select('id_secretary');
		$searchResult = $this->db->get_where('secretary_course', array('id_course' => $courseId));

		$searchResult = $searchResult->row_array();

		$foundSecretary = $searchResult['id_secretary'];

		return $foundSecretary;
	}

	private function updateSecretary($secretaryId, $newSecretary){
		$this->db->where('id_secretary', $secretaryId);
		$this->db->update('secretary_course', $newSecretary);
	}
	
	/**
	 * Update a course on DB
	 * @param String $id_course - The course id to be updated
	 * @param array $courseToUpdate - The new course data to replace the old one
	 * @return void
	 */
	private function updateCourseOnDb($id_course, $newCourseData){
		$this->db->where('id_course',$id_course);
		$this->db->update("course", $newCourseData);
	}

	/**
	 * Check if the course name from a given course id has changed in comparison with the new course name
	 * @param $course_id - The course id to get the old course name
	 * @param $newCourseName - The new course name that will replace the old one
	 * @return TRUE if the course name has changed or FALSE if does not
	 */
	private function checkIfCourseNameHasChanged($course_id, $newCourseName){
		$oldCourseName = $this->getCourseNameForThisCourseId($course_id);

		if($oldCourseName == $newCourseName){
			$hasChanged = FALSE;
		}else{
			$hasChanged = TRUE;
		}

		return $hasChanged;
	}

	/**
	 * Get the course name registered for a given course id
	 * @param $id_course - The course id to look for the course name 
	 * @return a String with the registered course name for the given course id if found, or FALSE if does not
	 */
	private function getCourseNameForThisCourseId($id_course){
		$this->db->select('course_name');
		$searchResult = $this->db->get_where('course', array('id_course' => $id_course));

		$foundCourseName = $searchResult->row_array();

		if(sizeof($foundCourseName) > 0){
			$foundCourseName = $foundCourseName['course_name'];
		}else{
			$foundCourseName = FALSE;
		}

		return $foundCourseName;
	}

	/**
	 * Check if the course type from a given course id has changed in comparison with the new course type id
	 * @param $course_id - The course id to get the old course type id
	 * @param $newCourseTypeId - The new course type id that will replace the old one
	 * @return TRUE if the course type has changed or FALSE if does not
	 */
	private function checkIfCourseTypeHasChanged($course_id, $newCourseTypeId){
		$oldCourseTypeId = $this->getCourseTypeForThisCourseId($course_id);

		if($oldCourseTypeId == $newCourseTypeId){
			$hasChanged = FALSE;
		}else{
			$hasChanged = TRUE;
		}

		return $hasChanged;
	}

	/**
	 * Get the course type id registered for a given course id
	 * @param $id_course - The course id to look for the course type 
	 * @return a String with the registered course type for the given course id if found, or FALSE if does not
	 */
	private function getCourseTypeForThisCourseId($id_course){
		$this->db->select('course_type_id');
		$searchResult = $this->db->get_where('course', array('id_course' => $id_course));

		$foundCourseType = $searchResult->row_array();

		if(sizeof($foundCourseType) > 0){
			$foundCourseType = $foundCourseType['course_type_id'];
		}else{
			$foundCourseType = FALSE;
		}

		return $foundCourseType;
	}
	
	/**
	 * Check if the given course name already exists on database
	 * @param $courseName - The course name to check
	 * @return TRUE if already exists or FALSE if does not exists
	 */
	private function courseNameAlreadyExists($courseName){

		$this->db->select('course_name');
		$this->db->from('course');
		$this->db->where('course_name', $courseName);
		$searchResult = $this->db->get();
		
		$courseNameAlreadyExists = FALSE;

		if($searchResult->num_rows() > 0){
			$courseNameAlreadyExists = TRUE;
		}else{
			$courseNameAlreadyExists = FALSE;
		}

		return $courseNameAlreadyExists;
	}
	
}