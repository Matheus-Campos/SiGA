<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Adiciona_colunas_data_inicial_e_final_tabela_offer extends CI_Migration {

    public function up() {

        $this->dbforge->add_column('offer', array(
            'start_date' => array('type' => 'date', 'NULL' => TRUE),
            'end_date' => array('type' => 'date', 'NULL' => TRUE)
        ));

        $this->db->where('offer_status', "approved");
        $today = new Datetime();
        $today = $today->format("Y/m/d");
        $this->db->update('offer', array('start_date' => $today));
    }

    public function down(){
        $this->dbforge->drop_column('offer', "start_date");
        $this->dbforge->drop_column('offer', "end_date");
    }
}