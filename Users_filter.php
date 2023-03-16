<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Team_connect extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Auth_model');
		// $this->load->model('user_model');
		// $this->load->model('Dashboard_model', 'Dashboard');
		$this->load->model('Team_connect_model');      
		
		if(!(($_SESSION['user_id']>0 || $_SESSION['core_mentor']['user_id']>0)  && ($_SESSION['user_type'] == 1 || $_SESSION['core_mentor']['user_type'] == 2))){
			redirect('auth/login');
		}

		$domain_name = array_shift((explode('.', $_SERVER['HTTP_HOST'])));
		$domain_details = $this->Auth_model->get_domain_details_by_name($domain_name);
		if ($domain_details->status < 1) {
			redirect('Deactive_account');
		}
	}

    public function index()
	{	
		// search filter form all data recieve
			if ($this->input->post()) {
				$post_data = $this->input->post();			
				$this->session->set_userdata('user_filter', $post_data);
			}
		// search filter form all data recieve       
	
		// $data['menu_active'] = $this->uri->segment(1);
		$this->load->view('include/header_new');
		$this->load->view('team_connect/team_connect_list');
		$this->load->view('include/footer_new');
	}

	public function reset_user_filter()
	{
		$_SESSION['user_filter'] = null;
		redirect('team_connect');
	}

	function ajax_user_list()
	{		

		$sEcho				=	$this->input->post('sEcho');
		$iDisplayStart		=	$this->input->post('iDisplayStart');
		$iDisplayLength		=	$this->input->post('iDisplayLength');
		$sSearch			=	$this->input->post('sSearch');
		$iSortingCols		=	$this->input->post('iSortingCols');
		$aColumns			=	$this->input->post('iSortingCols');
		$this->session->set_userdata("iDisplayLength", $iDisplayLength);
		$bSearchable		=	array();

		for ($i = 0; true; $i++) {
			$temp =	$this->input->post('bSearchable_' . $i);
			if ($temp == '')
				break;
			$bSearchable[$i]	=	$this->input->post('bSearchable_' . $i);
		}

		$sorting	=	array();
		for ($i = 0; $i < $iSortingCols; $i++) {

			$iSortCol =	$this->input->post('iSortCol_' . $i);

			$sSortDir =	$this->input->post('sSortDir_' . $i);

			$sorting[$iSortCol]	=	$sSortDir;
		}

		$output		=	$this->Team_connect_model->ajax_user_list($iDisplayStart, $iDisplayLength, $sorting, $sSearch, $sEcho, $bSearchable, $this->session->userdata("courseSearch"));
		$output		=	json_encode($output);
		echo $output;
	}

}

?>