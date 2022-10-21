<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/vendor/autoload.php';

use Dompdf\Dompdf;

class RSSController extends CI_Controller
{

	public function __construct()
	{

		parent::__construct();

		$this->load->library('LibBiorxivDB');
	}


	public function rssTest()
	{
	}

	public function rssDownload()
	{
		set_time_limit(0);

		if (!isset($_GET['report_id'])) {
			echo ("Not find report id");
			die();
		}

		// $reports = $this->Reports->getByID($_GET['report_id']);
		$reports = $this->libbiorxivdb->reports_get_by_id($_GET['report_id']);
		if (count($reports) == 0) {
			echo ("Not find report");
			die();
		}

		$report = $reports[0];



		$current_date = date("Y-m-d");
		$yesterday_date = date('Y-m-d', strtotime("-2 days"));
		$date_sentence = " limit_from:" . $yesterday_date . " limit_to:" . $current_date . " numresults:75 sort:relevance-rank format_result:standard";


		$total_title = "";

		//original search text
		if ($report['terms'] != "") {
			if ($report['study'] == "-- All Collections --") {
				$origin_search_str = $report['conditions'] . " " . $report['country'] . " " . $report['terms'] . " " . "jcode:biorxiv" . $date_sentence;
			} else {
				$origin_search_str = $report['conditions'] . " " . $report['country'] . " " . $report['terms'] . " " . "jcode:biorxiv" . " " . "subject_collection_code:" . $report['study'] . " " . $date_sentence;
			}

			$total_title =  $report['conditions'] . " " . $report['country'] . " " . $report['terms'];
		} else {
			if ($report['study'] == "-- All Collections --") {
				$origin_search_str = $report['conditions'] . " " . "jcode:biorxiv" . $date_sentence;
			} else {
				$origin_search_str = $report['conditions'] . " " . "jcode:biorxiv" . " " . "subject_collection_code:" . $report['study'] . " " . $date_sentence;
			}

			$total_title = $report['conditions'];
		}


		$encode_search_str = urlencode($origin_search_str);



		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://www.biorxiv.org/search/' . $encode_search_str,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;

		// Create a DOM object from a HTML file
		//$html = file_get_html('test.htm');

		// Create a DOM object from a string
		$html = str_get_html($response);




		$data = array(
			'clinics' => array(),
			'title' => $total_title
		);



		$page_counts = 0;


		foreach ($html->find('.pager-items') as $ul) {
			foreach ($ul->find('li.last a') as $li) {
				$page_counts =  $li->plaintext;
			}
		}


		if ($page_counts > 0) {

			//First page
			foreach ($html->find('.highwire-cite-highwire-article') as $ul) {



				foreach ($ul->find('.highwire-cite-linked-title') as $ele_title) {
					$details['title'] = $ele_title->plaintext;
					$details['link'] = 'https://www.biorxiv.org' . $ele_title->href;
				}

				foreach ($ul->find('.highwire-citation-authors') as $ele_author) {
					$details['creator'] = $ele_author->plaintext;
				}

				foreach ($ul->find('.highwire-cite-metadata-doi') as $newid) {
					//echo $newid->plaintext;
					$details['identifier'] = $newid->plaintext;
				}


				$data['clinics'][] = $details;
			}


			//Second page to last page
			for ($i = 1; $i < $page_counts; $i++) {
				$next_page_encodeurl = $encode_search_str . "?page=" . $i;
				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://www.biorxiv.org/search/' . $next_page_encodeurl,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
				));

				$response = curl_exec($curl);

				curl_close($curl);
				//echo $response;

				// Create a DOM object from a string
				$html = str_get_html($response);

				foreach ($html->find('.highwire-cite-highwire-article') as $ul) {



					foreach ($ul->find('.highwire-cite-linked-title') as $ele_title) {
						$details['title'] = $ele_title->plaintext;
						$details['link'] = 'https://www.biorxiv.org' . $ele_title->href;
					}

					foreach ($ul->find('.highwire-citation-authors') as $ele_author) {
						$details['creator'] = $ele_author->plaintext;
					}

					foreach ($ul->find('.highwire-cite-metadata-doi') as $newid) {
						//echo $newid->plaintext;
						$details['identifier'] = $newid->plaintext;
					}


					$data['clinics'][] = $details;
				}
			}
		} else {
			// Only one page
			foreach ($html->find('.highwire-cite-highwire-article') as $ul) {



				foreach ($ul->find('.highwire-cite-linked-title') as $ele_title) {
					$details['title'] = $ele_title->plaintext;
					$details['link'] = 'https://www.biorxiv.org' . $ele_title->href;
				}

				foreach ($ul->find('.highwire-citation-authors') as $ele_author) {
					$details['creator'] = $ele_author->plaintext;
				}

				foreach ($ul->find('.highwire-cite-metadata-doi') as $newid) {
					//echo $newid->plaintext;
					$details['identifier'] = $newid->plaintext;
				}


				$data['clinics'][] = $details;
			}
		}










		$dompdf = new Dompdf();

		$clinic_html = $this->load->view('biorxiv/template/clinic-table', $data, TRUE);
		// echo $clinic_html;die();
		$dompdf->loadHtml($clinic_html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A3', 'landscape');

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream("SearchResults.pdf");

		echo "PDF Downloading...";
		die();




		/*
		// create rss url
		$rss_url = "https://clinicaltrials.gov/ct2/results/rss.xml?rcv_d=&lup_d=7&sel_rss=mod7&term=".str_replace(" ", "+", $report['terms'])."&type=".$report['study']."&cond=".str_replace(" ", "+", $report['conditions'])."&cntry=".$report['country']."&count=10";

		$days = 7;
		if($report['status'] == 'new'){
			$days = 7;
		}
		else if($report['status'] == 'recent'){
			$days = 31;
		}
		else if($report['status'] == 'old'){
			$days = 31 * 3;
		}

		$rss_url = getRssLink(array(
			'days' => $days,
			'terms' => $report['terms'],
			'study' => $report['study'],
			'conditions' => $report['conditions'],
			'country' => $report['country'],
			'count' => 10
		));
		// echo $rss_url; die();
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => $rss_url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
			'Cookie: CTOpts=Qihzm6CLC74Psi1HjyUgzw-R98Fz3R4gQC-w; Psid=vihzm6CLC74Psi1Hjyz3FQ7V9gCkkKC8-BC8Eg0jF64VSgzqSB78SB0gCD8V'
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		
		$xml = new SimpleXMLElement($response);
		
		$data = array(
			'clinics' => array(),
			'title' => $xml->channel->title
		);

		foreach ($xml->channel->item as $item) {
			$details = getStudyDetails($item->link);
			$details['link'] = $item->link;
			$details['title'] = $item->title;
			$details['description'] = $item->description;

			$data['clinics'][] = $details;
		}

		$dompdf = new Dompdf();
		
		$clinic_html = $this->load->view('admin/template/clinic-table', $data, TRUE);
		// echo $clinic_html;die();
		$dompdf->loadHtml($clinic_html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A3', 'landscape');

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream("SearchResults.pdf");

		echo "PDF Downloading...";
		die();

		*/
	}



	public function downloadListCsv()
	{


		$response = array(
			'success' => false
		);


		$reports = $this->libbiorxivdb->reports_get_by_id($_GET['report_id']);
		// $reports = $this->Reports->getByID($_GET['report_id']);

		if ($reports) {
			$response['success'] = true;

			if (count($reports)) {
				$report = $reports[0];
			} else {
				$report = null;
			}

			$week_list = $report['week_list'];
			$week_reports = $report['week_reports'];

			// $filename = $_SESSION['username'] . '_' . $report['title'] . '_' . date('m-d-Y') . '.csv';
			$filename = $report['title'] . '_' . date('m-d-Y') . '.csv';

			header("Content-Description: File Transfer");
			header('Content-Type: text/csv');
			header("Content-Disposition: attachment; filename=$filename");

			$data = array();

			$week_list_arr = explode(",", $week_list);
			$week_report_arr = explode(",", $week_reports);

			$show_date = date('m-d-Y');

			$week_report_arr = array_reverse($week_report_arr);

			$week_val = "";

			$every_week = 0;
			$every_week_val = 0;
			$week_cumul_val = 0;

			$every_week = intval(count($week_list_arr) / 7);


			for ($i = 0; $i < count($week_list_arr); $i++) {

				if ($i == 0) {
					$week_list_arr[$i] = "this week";
				} else if ($i == 1) {
					$week_list_arr[$i] = "previous week";
				} else {
					$week_list_arr[$i] = $week_list_arr[$i] . " weeks ago";
				}

				//$show_date = date('m-d-Y', strtotime("-$i week"));
				//$data[] = array($week_list_arr[$i], $show_date, $week_report_arr[$i]);


				$week_cumul_val += intval($week_report_arr[$i]);
				$every_week_val += 1;

				if ($every_week_val == 8 || $every_week == 1) {

					$week_val = $i / 8;
					$show_date = date('m-d-Y', strtotime("-$i day"));
					$data[] = array($week_list_arr[$week_val], $show_date, $week_cumul_val);

					$every_week -= 1;
					$every_week_val = 1;
					$week_cumul_val = 0;
				}
			}



			$fp = fopen('php://output', 'w');

			$header = array("WEEK", "DATE", "COUNT");
			fputcsv($fp, $header);

			foreach ($data as $key => $line) {
				//$val = explode(",", $line);
				fputcsv($fp, $line);
			}
			fclose($fp);

			exit;
		}

		//exit;
	}










	public function rssPopup()
	{
		set_time_limit(0);

		if (!isset($_POST['id'])) {
			echo ("Not find report id");
			die();
		}

		$reports = $this->libbiorxivdb->reports_get_by_id($_POST['id']);
		if (count($reports) == 0) {
			echo ("Not find report");
			die();
		}

		$report = $reports[0];



		$current_date = date("Y-m-d");
		$yesterday_date = date('Y-m-d', strtotime("-2 days"));
		$date_sentence = " limit_from:" . $yesterday_date . " limit_to:" . $current_date . " numresults:75 sort:relevance-rank format_result:standard";


		$total_title = "";

		//original search text
		if ($report['terms'] != "") {
			if ($report['study'] == "-- All Collections --") {
				$origin_search_str = $report['conditions'] . " " . $report['country'] . " " . $report['terms'] . " " . "jcode:biorxiv" . $date_sentence;
			} else {
				$origin_search_str = $report['conditions'] . " " . $report['country'] . " " . $report['terms'] . " " . "jcode:biorxiv" . " " . "subject_collection_code:" . $report['study'] . " " . $date_sentence;
			}

			$total_title =  $report['conditions'] . " " . $report['country'] . " " . $report['terms'];
		} else {
			if ($report['study'] == "-- All Collections --") {
				$origin_search_str = $report['conditions'] . " " . "jcode:biorxiv" . $date_sentence;
			} else {
				$origin_search_str = $report['conditions'] . " " . "jcode:biorxiv" . " " . "subject_collection_code:" . $report['study'] . " " . $date_sentence;
			}

			$total_title = $report['conditions'];
		}


		$encode_search_str = urlencode($origin_search_str);



		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://www.biorxiv.org/search/' . $encode_search_str,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;

		// Create a DOM object from a HTML file
		//$html = file_get_html('test.htm');

		// Create a DOM object from a string
		$html = str_get_html($response);




		$data = array(
			'clinics' => array(),
			'title' => $total_title
		);



		$page_counts = 0;


		foreach ($html->find('.pager-items') as $ul) {
			foreach ($ul->find('li.last a') as $li) {
				$page_counts =  $li->plaintext;
			}
		}


		if ($page_counts > 0) {

			//First page
			foreach ($html->find('.highwire-cite-highwire-article') as $ul) {



				foreach ($ul->find('.highwire-cite-linked-title') as $ele_title) {
					$details['title'] = $ele_title->plaintext;
					$details['link'] = 'https://www.biorxiv.org' . $ele_title->href;
				}

				foreach ($ul->find('.highwire-citation-authors') as $ele_author) {
					$details['creator'] = $ele_author->plaintext;
				}

				foreach ($ul->find('.highwire-cite-metadata-doi') as $newid) {
					//echo $newid->plaintext;
					$details['identifier'] = $newid->plaintext;
				}


				$data['clinics'][] = $details;
			}


			//Second page to last page
			for ($i = 1; $i < $page_counts; $i++) {
				$next_page_encodeurl = $encode_search_str . "?page=" . $i;
				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://www.biorxiv.org/search/' . $next_page_encodeurl,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
				));

				$response = curl_exec($curl);

				curl_close($curl);
				//echo $response;

				// Create a DOM object from a string
				$html = str_get_html($response);

				foreach ($html->find('.highwire-cite-highwire-article') as $ul) {



					foreach ($ul->find('.highwire-cite-linked-title') as $ele_title) {
						$details['title'] = $ele_title->plaintext;
						$details['link'] = 'https://www.biorxiv.org' . $ele_title->href;
					}

					foreach ($ul->find('.highwire-citation-authors') as $ele_author) {
						$details['creator'] = $ele_author->plaintext;
					}

					foreach ($ul->find('.highwire-cite-metadata-doi') as $newid) {
						//echo $newid->plaintext;
						$details['identifier'] = $newid->plaintext;
					}


					$data['clinics'][] = $details;
				}
			}
		} else {
			// Only one page
			foreach ($html->find('.highwire-cite-highwire-article') as $ul) {



				foreach ($ul->find('.highwire-cite-linked-title') as $ele_title) {
					$details['title'] = $ele_title->plaintext;
					$details['link'] = 'https://www.biorxiv.org' . $ele_title->href;
				}

				foreach ($ul->find('.highwire-citation-authors') as $ele_author) {
					$details['creator'] = $ele_author->plaintext;
				}

				foreach ($ul->find('.highwire-cite-metadata-doi') as $newid) {
					//echo $newid->plaintext;
					$details['identifier'] = $newid->plaintext;
				}


				$data['clinics'][] = $details;
			}
		}




		echo json_encode($data);


	
	}

	//download csv on modal
	public function downloadDatesListCsv()
	{



		$response = array(
			'success' => false
		);


		
		$select_last_date = $_GET['last_date_day'];
		$select_start_date = $_GET['start_date_day'];

		date_default_timezone_set('US/Eastern');

		$today = date("m/d/Y");

		$date1_ts = strtotime($select_last_date);
		$date2_ts = strtotime($today);
		$last_days = $date2_ts - $date1_ts;
		$last_days= round($last_days / 86400, PHP_ROUND_HALF_DOWN);

		$date1_ts = strtotime($select_start_date);
		$date2_ts = strtotime($today);
		$start_days = $date2_ts - $date1_ts;
		$start_days= round($start_days / 86400, PHP_ROUND_HALF_DOWN);

		$date1_ts = strtotime($select_start_date);
		$date2_ts = strtotime($select_last_date);
		$total_days = $date2_ts - $date1_ts;
		$total_days= round($total_days / 86400, PHP_ROUND_HALF_DOWN);

		

		// $reports = $this->Reports->getByID($_GET['report_id']);
		$reports = $this->libbiorxivdb->reports_get_by_id($_GET['report_id']);

		if ($reports) {
			$response['success'] = true;

			if (count($reports)) {
				$report = $reports[0];
			} else {
				$report = null;
			}

			$week_list = $report['week_list'];
			$week_reports = $report['week_reports'];

			$filename = $_SESSION['username'] . '_' . $report['title'] . '_' . date('m-d-Y') . '.csv';

			header("Content-Description: File Transfer");
			header('Content-Type: text/csv');
			header("Content-Disposition: attachment; filename=$filename");

			$data = array();

			$week_list_arr = explode(",", $week_list);
			$week_report_arr = explode(",", $week_reports);

			$total_counts = 0;

			$show_date = date('m-d-Y');

			$week_report_arr = array_reverse($week_report_arr);


			$selected_week_report_arr = array();
			$report_count = intval($total_days) + 1;
			$selected_week_report_arr = array_slice($week_report_arr, intval($last_days),  intval($report_count));



			for ($i = 0; $i < count($selected_week_report_arr) ; $i++) {

				$total_counts +=  $selected_week_report_arr[$i];

				$show_date = date('m-d-Y', strtotime("-$i day", strtotime($select_last_date)));
				$data[] = array($show_date, $selected_week_report_arr[$i], $total_counts);
			}

			$total_counts = 0;

			$fp = fopen('php://output', 'w');

			$header = array("DATE", "COUNTS", "TOTAL COUNTS");
			fputcsv($fp, $header);

			foreach ($data as $key => $line) {
				//$val = explode(",", $line);
				fputcsv($fp, $line);
			}
			fclose($fp);

			exit;
		}



		//exit;
	}

}
