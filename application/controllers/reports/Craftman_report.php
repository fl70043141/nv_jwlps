<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Craftman_report extends CI_Controller {
  
        function __construct() {
            parent::__construct();
            $this->load->model('Reports_all_model');
        }

        public function index(){ 
              //I'm just using rand() function for data example 
            $this->view_search_report();
	}
        public function view_search_report(){
            
            $this->load->model('Consignee_receive_model');
            $data['search_list'] = $this->Consignee_receive_model->search_result();
            $data['main_content']='reports_all/craftman_report/search_craftman_report'; 
            $data['craftman_list'] = get_dropdown_data(CRAFTMANS,'craftman_name','id','Craftman');
            $this->load->view('includes/template',$data); 
        }
        
        function fl_ajax(){
            
//            echo '<pre>';            print_r($this->input->post()); die;
            $func = $this->input->post('function_name');
            $param = $this->input->post();
            
            if(method_exists($this, $func)){ 
                (!empty($param))?$this->$func($param):$this->$func();
            }else{
                return false;
            }
        }
        
        public function search(){ //view the report
            $invoices = $this->load_data(); 
            $this->load->view('reports_all/craftman_report/search_craftman_report_result',$invoices);
	} 
        
        public function print_report(){ 
//            $this->input->post() = 'aa';
            $crf_data = $this->load_data(); 
//            echo '<pre>';            print_r($_GET); die; 
            $this->load->library('Pdf'); 
            $this->load->model('Items_model');
            
            // create new PDF document
            $pdf = new Pdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->fl_header='header_jewel';//invice bg
            $pdf->fl_header_title='Report';//invice bg
            $pdf->fl_header_title_RTOP='Craftman Summary';//invice bg
            
            
            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('Fahry Lafir');
            $pdf->SetTitle('PDF AM Invoice');
            $pdf->SetSubject('AM Invoice');
            $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
            
            // set default header data
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

            // set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                    
            // set font
            $pdf->SetFont('times', '', 10);
        
        
            $pdf->AddPage();   
            $pdf->SetTextColor(32,32,32);     
            $html = '<table border="0">
                        <tr>
                            <td>Dates Result: '.$this->input->get('date_from').' - '.$this->input->get('date_to').'</td>
                            <td align="right">Printed on : '.date(SYS_DATE_FORMAT).'</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td align="right">Printed by : '.$this->session->userdata(SYSTEM_CODE)['user_first_name'].' '.$this->session->userdata(SYSTEM_CODE)['user_last_name'].'</td>
                        </tr>
                    </table> ';
            $i=1;
            $g_tot_settled = $g_inv_total = $g_tot_balance=0; 
//            echo '<pre>';            print_r($cust_dets); die;
            $html .= '<table  class="table-line" border="0" padding="">
                        <thead> 
                            <tr class="colored_bg">
                                <th width="20%" align="center">Receive No</th> 
                                <th width="25%" align="left">Craftman</th> 
                                <th width="20%" align="right">Date Received</th> 
                                <th width="20%" align="right">Total Weight</th> 
                                <th width="15%" align="center">Tota Articles</th>
                            </tr>
                        </thead>
                        <tbody>';
                        $tot_units = $tot_units2 = 0;
                        $unit_abr1 = $unit_abr2 = '';
                        if(isset($crf_data['search_list']) &&  count($crf_data['search_list'])>0){
                            foreach($crf_data['search_list'] as $crf_item){
                                $tot_units += $crf_item['total_units'];
                                $tot_units2 += $crf_item['total_units_2'];
                                $unit_abr1 = $crf_item['unit_abbreviation'];
                                $unit_abr2 = $crf_item['unit_abbreviation_2'];
//                                echo '<pre>';                            print_r($crf_data); die;
                                $html .= '<tr>
                                                <td width="20%" align="center">'.$crf_item['cm_receival_no'].'</td>
                                                <td width="25%" align="left">'.$crf_item['craftman_name'].'</td>
                                                <td width="20%" align="center">'.date(SYS_DATE_FORMAT,$crf_item['receival_date']).'</td>
                                                <td width="20%" align="right">'.number_format($crf_item['total_units'],3).' '.$crf_item['unit_abbreviation'].'</td> 
                                                <td width="15%" align="center">'.number_format($crf_item['total_units_2']).' '.$crf_item['unit_abbreviation_2'].'</td> 
                                            </tr>';
                            }
                        }
                            
            $html.= '</tbody> 
                    </table> 
                ';                
            $html .= '
                    <table>
                        
                        <tfoot> 
                            <tr class="">
                                <td align="left" colspan="6"></td>
                            </tr> 
                            <tr>
                                <th width="20%" align="center"></th>
                                <th width="25%" align="center"></th>
                                <th width="20%" align="right"></th>
                                <th width="20%" align="right"><b>'.number_format($tot_units,3).' '.$unit_abr1.'</b></th>
                                <th width="15%" align="center"><b>'.$tot_units2.' '.$unit_abr2.'</b></th>
                            </tr>
                        </tfoot>
                    </table>
                ';
            
            
            $html .= '
                    <style>
                    .colored_bg{
                        background-color:#E0E0E0;
                    }
                    .table-line th, .table-line td {
                        padding-bottom: 2px;
                        padding-left: 2px;
                        border-bottom: 1px solid #ddd;
                        text-align:center; 
                    }
                    .text-right,.table-line.text-right{
                        text-align:right;
                    }
                    .table-line tr{
                        line-height: 20px;
                    }
                    </style>';
            $pdf->writeHTMLCell(190,'',10,'',$html);
            
            $pdf->SetFont('times', '', 12.5, '', false);
            $pdf->SetTextColor(255,125,125);            
            // force print dialog
            $js = 'this.print();';
//            $js = 'print(true);';
            // set javascript
//            $pdf->IncludeJS($js);
            $pdf->Output('Sales_invoice_.pdf', 'I');
        }
        
        public function  load_data(){
            $invoices = array();
            $input = (empty($this->input->post()))? $this->input->get():$this->input->post(); 
            $search_data=array( 
                                'receival_no' => $this->input->post('receive_no'),
                                'craftman_id' => $input['craftman_id'],  
                                'order_no' => $input['order_no'],  
                                'date_from' => strtotime($input['date_from']),  
                                'date_to' => strtotime($input['date_to']),  
                                ); 
//            echo '<pre>';            print_r($search_data); die;
            $so_rec['search_list'] = $this->Reports_all_model->craftman_receive_data($search_data);
             
            return $so_rec;
        }
}
