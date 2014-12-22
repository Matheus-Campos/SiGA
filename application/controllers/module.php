<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Module extends CI_Controller {

	// CRUD

	/**
	  * Check the modules registered to an user
	  * @param $user_id - User id to check the modules
	  * @return 
	  */
	public function checkModules($user_id){

		$this->load->model('module_model');
		$registered_modules = $this->module_model->getUserModulesNames($user_id);

		return $registered_modules;
	}
	
	/**
	 * Check existing modules (groups) in the database
	 * @return array with the modules (groups) names
	 */
	public function getExistingModules(){
		
		$this->load->model('module_model');
		$existing_modules = $this->module_model->getAllModules();
		$existing_modules_form = $this->turnCourseTypesToArray($existing_modules);
		
		return $existing_modules_form;
	}
	
	/**
	 * Join the id's and names of modules (groups) into an array as key => value.
	 * Used to the update course form
	 * @param $modules - The array that contains the tuples of modules
	 * @return An array with the id's and modules names as id => module_name
	 */
	private function turnCourseTypesToArray($modules){
		// Quantity of course types registered
		$quantity_of_course_types = sizeof($modules);
	
		for($cont = 0; $cont < $quantity_of_course_types; $cont++){
			$keys[$cont] = $modules[$cont]['id_module'];
			$values[$cont] = ucfirst($modules[$cont]['module_name']);
		}
	
		$form_modules = array_combine($keys, $values);
	
		return $form_modules;
	}

}