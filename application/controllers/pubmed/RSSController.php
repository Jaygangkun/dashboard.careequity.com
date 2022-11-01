<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/vendor/autoload.php';

use Dompdf\Dompdf;

class RSSController extends CI_Controller
{

	public function __construct()
	{

		parent::__construct();

		$this->load->library('LibPubmedDB');
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
		$reports = $this->libpubmeddb->reports_get_by_id($_GET['report_id']);
		if (count($reports) == 0) {
			echo ("Not find report");
			die();
		}

		$report = $reports[0];

		//original search text
		if ($report['terms'] != "") {
			if ($report['study'] == "All Fields") {
				$origin_search_str = "(" . $report['conditions'] . ")" . " " . $report['country'] . " (" . $report['terms'] . ")";
			} else {
				$origin_search_str = "(" . $report['conditions'] . "[" . $report['study'] . "])" . " " . $report['country'] . " (" . $report['terms'] . ")";
			}
		} else {
			if ($report['study'] == "All Fields") {
				$origin_search_str = $report['conditions'];
			} else {
				$origin_search_str = "(" . $report['conditions'] . "[" . $report['study'] . "])";
			}
		}


		$encode_search_str = urlencode($origin_search_str);

		//echo $encode_search_str;



		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://pubmed.ncbi.nlm.nih.gov/?term=' . $encode_search_str,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Cookie: pm-csrf=JC0Dxz9RiYe8HS2ABzKGJmaH4c0PCu1qrCdKIhCYOmICpcLyQcpjfqt9Yju2XR5j; ncbi_sid=0B4081E50BEBF073_6576SID; pm-sid=2BUVbBZqGAn5YC8AlF89AA:99ebd61350f6e9c860fadd860b152d9f; pm-adjnav-sid=LBoYi1uNq4WefkTMc2C7sg:99ebd61350f6e9c860fadd860b152d9f; pm-sessionid=fqnxq8494p4uvlsqzjpfxska6npsf01g'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$cut_str = '<h1 class="usa-sr-only">Search Page</h1>';
		$s_list = explode($cut_str, $response);

		$cut_str1 = '<form action="/" method="get" autocomplete="off"';
		$rea_val = explode($cut_str1, $s_list[1]);



		$cut_str2 = '<input type="hidden" name="csrfmiddlewaretoken" value="';
		$rea_val2 = explode($cut_str2, $rea_val[0]);
		// echo $rea_val2[1];


		$csr_key = substr($rea_val2[1], 0, -3);

		//echo $csr_key;
		// echo  $response;

		//return;
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://pubmed.ncbi.nlm.nih.gov/create-rss-feed-url/',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => array('name' => '' . $origin_search_str . '', 'term' => '' . $origin_search_str . '', 'limit' => '100', 'csrfmiddlewaretoken' => '' . $csr_key . ''),
			CURLOPT_HTTPHEADER => array(
				'referer: https://pubmed.ncbi.nlm.nih.gov/?term=' . $encode_search_str . '',
				'Cookie: pm-csrf=JC0Dxz9RiYe8HS2ABzKGJmaH4c0PCu1qrCdKIhCYOmICpcLyQcpjfqt9Yju2XR5j; ncbi_sid=0B4081E50BEBF073_6576SID; pm-sid=2BUVbBZqGAn5YC8AlF89AA:99ebd61350f6e9c860fadd860b152d9f; pm-adjnav-sid=LBoYi1uNq4WefkTMc2C7sg:99ebd61350f6e9c860fadd860b152d9f; pm-sessionid=fqnxq8494p4uvlsqzjpfxska6npsf01g'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		//echo $response;
		$cut_str3 = '{"rss_feed_url": "';
		$s_list1 = explode($cut_str3, $response);

		$cut_str4 = '"';
		$rea_val3 = explode($cut_str4, $s_list1[1]);





		$rss_url = $rea_val3[0];
		//echo $csr_key;



		//	echo $rss_url;

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => '' . $rss_url . '',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Cookie: pm-csrf=JC0Dxz9RiYe8HS2ABzKGJmaH4c0PCu1qrCdKIhCYOmICpcLyQcpjfqt9Yju2XR5j; pm-sid=2BUVbBZqGAn5YC8AlF89AA:99ebd61350f6e9c860fadd860b152d9f; pm-adjnav-sid=LBoYi1uNq4WefkTMc2C7sg:99ebd61350f6e9c860fadd860b152d9f; ncbi_sid=0B4081E50BEBF073_6576SID'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;

		$xml = new SimpleXMLElement($response);
		//echo $xml;

		$data = array(
			'clinics' => array(),
			'title' => $xml->channel->title
		);


		//$xml = new SimpleXmlElement("doc1.xml",null,true);
		foreach ($xml->channel->item as $item) {


			/*
				$cd = $entry->children('http://purl.org/dc/elements/1.2');
				echo "\n".$cd->identifier;
				echo "\n".$cd->title;
				echo "\n";
			*/
			$namespaces = $item->getNameSpaces(true);
			$cds = $item->children($namespaces['dc']);

			$details['creator'] = "";
			$details['identifier'] = "";

			foreach ($cds as $name => $c) {
				//echo $name, '=', $c, "\n";

				if ($name == "creator") {

					if ($details['creator'] == "") {
						$details['creator'] .= $c;
					} else {
						$details['creator'] .= "<br>" . $c;
					}
				}


				if ($name == "identifier") {

					if ($details['identifier'] == "") {
						$details['identifier'] .= $c;
					} else {
						$details['identifier'] .= "<br>" . $c;
					}
				}
			}




			$details['link'] = $item->link;
			$details['title'] = $item->title;
			$details['description'] = $item->description;
			$details['guid'] = $item->guid;
			$details['pubdate'] = $item->pubDate;


			$data['clinics'][] = $details;

			/*

			$namespaces = $entry->getNameSpaces(true);
    		$cd = $entry->children($namespaces['cd']);
    		echo "\n".$cd->publisher;
    		echo "\n".$cd->author;

				$namespaces = $entry->getNameSpaces(true);
				$cd = $entry->children($namespaces['ef']);
				echo "\n".$cd->publisher;
				echo "\n".$cd->author;
				*/
		}


		$dompdf = new Dompdf();

		$clinic_html = $this->load->view('pubmed/template/clinic-table', $data, TRUE);
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


		// $reports = $this->Reports->getByID($_GET['report_id']);
		$reports = $this->libpubmeddb->reports_get_by_id($_GET['report_id']);

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


		// $saved_reports = $this->Reports->allSavedReports($_POST['id']);
		$saved_reports = $this->libpubmeddb->reports_all_saved($_POST['id']);
		

		$data = array(
			'clinics' => array(),
			'title' => ""
		);
		$guids =  array();

		foreach ($saved_reports as $saved_report) {






			//writeLog('old:' . $report['guids']);
			//writeLog('---current:' . $saved_report['guid']);
			//WriteLog('Pos count:' . $pos);


			$details['creator'] =  $saved_report['author'];
			$details['identifier'] =  $saved_report['identifier'];

			$details['link'] =  $saved_report['link'];
			$details['title'] =  $saved_report['title'];
			$details['description'] =  $saved_report['description'];
			$details['guid'] =  $saved_report['guid'];
			$details['pubdate'] =  $saved_report['pubdate'];


			$data['clinics'][] = $details;



			$guids[] =  $saved_report['guid'];


			//writeLog('--current saved report:' . $saved_report['guid']);

			$data['title'] =  $saved_report['report_title'];
		}

		echo json_encode($data);
	}

	//dateDifferent
	public function dateDiff($date1, $date2)
	{
		$date1_ts = strtotime($date1);
		$date2_ts = strtotime($date2);
		$diff = $date2_ts - $date1_ts;
		return round($diff / 86400, PHP_ROUND_HALF_DOWN);
	}

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
		$reports = $this->libpubmeddb->reports_get_by_id($_GET['report_id']);

		if ($reports) {
			$response['success'] = true;

			if (count($reports)) {
				$report = $reports[0];
			} else {
				$report = null;
			}

			$week_list = $report['week_list'];
			$week_reports = $report['week_reports'];

			//$filename = $_SESSION['username'] . '_' . $report['title'] . '_' . date('m-d-Y') . '.csv';
			$filename = $report['title'] . '_' . date('m-d-Y') . '.csv';

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

	public function reportUpdates()
	{
		$resp = array(
			'success' => true,
		);

		if(!isset($_POST['report_ids'])) {
			$resp['success'] = false;
			echo json_encode($resp); die();
		}

		set_time_limit(0);

		$reports_count = 0;
		foreach($_POST['report_ids'] as $report_id) {
			$saved_reports = $this->libpubmeddb->reports_all_saved($report_id);
					

			$data = array(
				'clinics' => array(),
				'title' => ""
			);
			$guids =  array();

			foreach ($saved_reports as $saved_report) {






				//writeLog('old:' . $report['guids']);
				//writeLog('---current:' . $saved_report['guid']);
				//WriteLog('Pos count:' . $pos);


				$details['creator'] =  $saved_report['author'];
				$details['identifier'] =  $saved_report['identifier'];

				$details['link'] =  $saved_report['link'];
				$details['title'] =  $saved_report['title'];
				$details['description'] =  $saved_report['description'];
				$details['guid'] =  $saved_report['guid'];
				$details['pubdate'] =  $saved_report['pubdate'];


				$data['clinics'][] = $details;



				$guids[] =  $saved_report['guid'];


				//writeLog('--current saved report:' . $saved_report['guid']);

				$data['title'] =  $saved_report['report_title'];
			}

			if(count($data['clinics']) > 0) {
				$reports_count ++;
			}
		}

		$resp['count'] = $reports_count;

		echo json_encode($resp); 
	}
}
