<?php
class Team_connect_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

    public function ajax_user_list($iDisplayStart, $iDisplayLength, $sorting, $sSearch, $sEcho, $bSearchable, $custom_search = array())
	{       

		$user_filters = $this->session->userdata('user_filter');
        // print_r($user_filters); die;

		$this->db->select('count(users.id) as total');
		$this->db->from('users');
		$this->db->join('users_product_map', 'users.id=users_product_map.user_id');
		$this->db->where('users_product_map.domain_id', $_SESSION['core_mentor']['domain_registration_id']);

		// 1st place:
            if ($user_filters) {
                if ($user_filters['email_phone']) {
                    $this->db->group_start();
                    $this->db->where('users.email', trim($user_filters['email_phone']));
                    $this->db->or_where('users.phone', trim($user_filters['email_phone']));
                    $this->db->or_where('users.id', trim($user_filters['email_phone']));
                    $this->db->group_end();
                }
                if ($user_filters['user_type']) {
                    $this->db->where('users.user_type', $user_filters['user_type']);
                }
                if ($user_filters['status']) {
                    $this->db->where('users_product_map.status', ($user_filters['status'] - 1 ));
                }
		if ($user_filters['start_date']) {
			$this->db->where('DATE_FORMAT(schedule.start_date_time, "%Y-%m-%d") >=', date('Y-m-d', strtotime($customSearch['start_date'])));
		}

		if ($user_filters['end_date']) {
			$this->db->where('DATE_FORMAT(schedule.start_date_time, "%Y-%m-%d") <=', date('Y-m-d', strtotime($customSearch['end_date'])));
		}
            }
        // 1st place:

		$query = $this->db->get();

		$result = $query->row();
		$iTotalRecords = $result->total;
		$this->db->select('count(users.id) as total');
		$this->db->from('users');
		$this->db->join('users_product_map', 'users.id=users_product_map.user_id');
		$this->db->where('users_product_map.domain_id', $_SESSION['core_mentor']['domain_registration_id']);

        // 2nd place:
            if ($user_filters) {
                if ($user_filters['email_phone']) {
                    $this->db->group_start();
                    $this->db->where('users.email', trim($user_filters['email_phone']));
                    $this->db->or_where('users.phone', trim($user_filters['email_phone']));
                    $this->db->or_where('users.id', trim($user_filters['email_phone']));
                    $this->db->group_end();
                }
                if ($user_filters['user_type']) {
                    $this->db->where('users.user_type', $user_filters['user_type']);
                }
                if ($user_filters['status']) {
                    $this->db->where('users_product_map.status', ($user_filters['status'] - 1));
                }
		if ($user_filters['start_date']) {
			$this->db->where('DATE_FORMAT(schedule.start_date_time, "%Y-%m-%d") >=', date('Y-m-d', strtotime($customSearch['start_date'])));
		}

		if ($user_filters['end_date']) {
			$this->db->where('DATE_FORMAT(schedule.start_date_time, "%Y-%m-%d") <=', date('Y-m-d', strtotime($customSearch['end_date'])));
		}
            }
        // 2nd place:

		if ($sSearch) {
			$this->db->group_start();
			$this->db->like('users.email', $sSearch);
			$this->db->or_like('users.name', $sSearch);
			$this->db->or_like('users.phone', $sSearch);
			$this->db->group_end();
		}

		$query = $this->db->get();

		$result = $query->row();
		$iTotalDisplayRecords = $result->total;

		$this->db->select('users.*,users_product_map.status as map_status');
		$this->db->from('users');
		$this->db->join('users_product_map', 'users.id=users_product_map.user_id');
		$this->db->where('users_product_map.domain_id', $_SESSION['core_mentor']['domain_registration_id']);

        // 3rd place:
            if ($user_filters) {
                if ($user_filters['email_phone']) {
                    $this->db->group_start();
                    $this->db->where('users.email', trim($user_filters['email_phone']));
                    $this->db->or_where('users.phone', trim($user_filters['email_phone']));
                    $this->db->or_where('users.id', trim($user_filters['email_phone']));
                    $this->db->group_end();
                }
                if ($user_filters['user_type']) {
                    $this->db->where('users.user_type', $user_filters['user_type']);
                }
                if ($user_filters['status']) {
                    $this->db->where('users_product_map.status', ($user_filters['status'] - 1));
                }
		if ($user_filters['start_date']) {
			$this->db->where('DATE_FORMAT(schedule.start_date_time, "%Y-%m-%d") >=', date('Y-m-d', strtotime($customSearch['start_date'])));
		}

		if ($user_filters['end_date']) {
			$this->db->where('DATE_FORMAT(schedule.start_date_time, "%Y-%m-%d") <=', date('Y-m-d', strtotime($customSearch['end_date'])));
		}
            }
        // 3rd place:

		if ($sSearch) {
			$this->db->group_start();
			$this->db->like('users.email', $sSearch);
			$this->db->or_like('users.name', $sSearch);
			$this->db->or_like('users.phone', $sSearch);
			$this->db->group_end();
		}

		foreach ($sorting as $key => $sort) {
			if ($key == 1) {
				$this->db->order_by('users.id',	$sort);
			} else {
				$this->db->order_by('users.id', 'DESC');
			}
		}
		$this->db->order_by('users.id', 'desc');

		$this->db->limit($iDisplayLength, $iDisplayStart);
		$query = $this->db->get();

		$result = $query->result();
        // echo '<pre>';
		// print_r($result);

		$aaData	=	array();

		$key = $iDisplayStart;
		foreach ($result as $key => $row) {
			$status = '';
			$userType = '';			

			if ($row->map_status == 1) {				
                $status =  'Active';
			} else {				
                $status =  'Deactive';
			}

			// userType condition
			if ($row->user_type == 1) {
				$userType =  "Admin";
			} else if ($row->user_type == 2) {
				$userType = "Core Mentor";
			} else if ($row->user_type == 3) {
				$userType = "Mentor";
			} else if ($row->user_type == 4) {
				$userType = "Student";
			} else {
				$userType = "Staff Counsellor";
			}		
			

      if ($row->profile_image) {
          $image_url = '<img src="'.$row->profile_image.'" alt="" width="80" height="80" >';
      }else{
          $image_url = '<i class="fa fa-user fa-5x ml-3 text-danger" ></i>';
      }
			

			$aaData[]	=	array(
				$key + 1,			
				$row->name . ' ' . $row->last_name,
				$row->email,
				$row->phone,
				$status,
				$userType,
        $image_url,				

			);
		}

		$output	= array(
			"sEcho"		=> 	$sEcho,
			"iTotalRecords"	=>	$iTotalRecords,
			"iTotalDisplayRecords" 	=>	$iTotalDisplayRecords,
			"aaData"	=>	$aaData
		);

		return	$output;
	}


}
?>
