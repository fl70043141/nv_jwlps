<?php 

class Sales_invoices_model extends CI_Model
{
	function __construct(){	
            parent::__construct(); 
 	}
	 
        public function search_result($data='',$where=''){ 
//            echo '<pre>';            print_r($data); die;
            $this->db->select('i.*');
            $this->db->select('(select concat(first_name," ",last_name) from '.USER.' where auth_id = i.added_by) as sales_person');
            $this->db->select('c.customer_name,c.address,c.city,c.phone,pt.payment_term_name,pt.days_after');
            $this->db->from(INVOICES.' i'); 
            $this->db->join(CUSTOMERS.' c','c.id = i.customer_id'); 
            $this->db->join(PAYMENT_TERMS.' pt','pt.id = i.payement_term_id');  
            $this->db->where('i.deleted',0); 
            
            if($where!='') $this->db->where($where);
            
            if(isset($data['invoice_no'])) $this->db->like('i.invoice_no',$data['invoice_no']);
            if(isset($data['customer_id']) && $data['customer_id']!='') $this->db->where('i.customer_id',$data['customer_id']);
            
            if($this->session->userdata(SYSTEM_CODE)['user_group_id']!=0) $this->db->where('i.inv_group_id',$this->session->userdata(SYSTEM_CODE)['user_group_id']); 
            
            $this->db->order_by('i.id','desc'); 
            $result = $this->db->get()->result_array();  
//            echo $this->db->last_query();die;
//            echo '<pre>';            print_r($result); die;
            return $result;
	}
	
         public function get_single_row($id,$where=''){
            $this->db->select('i.*');
            $this->db->select('(select invoice_type_name from '.INVOICE_TYPE.' where id = i.invoice_type_id) as invoice_type');
            $this->db->select('(select concat(first_name," ",last_name) from '.USER.' where auth_id = i.added_by) as sales_person');
            $this->db->select('c.customer_name,c.short_name,c.address,c.city,c.phone,pt.payment_term_name,pt.days_after');
            $this->db->select('so.sales_order_no,so.order_date');
            $this->db->from(INVOICES.' i'); 
            $this->db->join(CUSTOMERS.' c','c.id = i.customer_id'); 
            $this->db->join(PAYMENT_TERMS.' pt','pt.id = i.payement_term_id'); 
            $this->db->join(SALES_ORDERS.' so','so.id = i.so_id','left'); 
            $this->db->where('i.id',$id);
            $this->db->where('i.deleted',0);
            if($where!='') $this->db->where($where);
            $result = $this->db->get()->result_array();  
            return $result[0];
	}               
         public function get_invc_desc($id){
            $this->db->select('id.*, ((id.unit_price*id.item_quantity*(100-id.discount_persent)*0.01) - id.discount_fixed) as sub_total');
            $this->db->select('ic.category_name as item_cat_name,ic.is_gem');
            $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = id.item_quantity_uom_id)  as unit_abbreviation');
            $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = id.item_quantity_uom_id_2)  as unit_abbreviation_2');
            $this->db->select('itm.item_code,itm.item_category_id as item_category');
            $this->db->join(ITEMS.' itm', 'itm.id = id.item_id'); 
            $this->db->join(ITEM_CAT." ic","ic.id = itm.item_category_id");  
            $this->db->from(INVOICE_DESC.' id'); 
            $this->db->where('id.invoice_id',$id);
            $this->db->where('id.deleted',0);
            $result = $this->db->get()->result_array();  
//        echo '<pre>';        print_r($result);die;
            return $result;
	} 
        public function get_invc_desc_for_so($order_id,$where='',$group_by='i.so_id'){ 
            $this->db->select('id.*, sum((id.unit_price*id.item_quantity*(100-id.discount_persent)*0.01) - id.discount_fixed) as sub_total');
            $this->db->select('(select ic.id from '.ITEM_CAT.' ic LEFT JOIN '.ITEMS.' itm ON itm.item_category_id = ic.id where itm.id = id.item_id) as item_category');
            $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = id.item_quantity_uom_id)  as unit_abbreviation');
            $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = id.item_quantity_uom_id_2)  as unit_abbreviation_2');
            $this->db->from(INVOICE_DESC.' id'); 
            $this->db->join(INVOICES.' i','i.id = id.invoice_id','left'); 
            $this->db->where('i.so_id',$order_id);
            $this->db->where('id.deleted',0);
            $this->db->group_by($group_by);
            if($where!='') $this->db->where($where);
            $result = $this->db->get()->result_array();  
//        echo '<pre>';        print_r($result);die;
            return $result;
	}  
         public function get_transections($inv_id,$where=''){ 
            $this->db->select('tr.*');
            $this->db->select('trt.name,trt.calculation');
            $this->db->from(TRANSECTION.' tr'); 
            $this->db->join(TRANSECTION_TYPES.' trt', 'trt.id = tr.transection_type_id'); 
            $this->db->where('tr.trans_reference',$inv_id);
            $this->db->where('tr.status',1);
            $this->db->where('tr.person_type',20);//garage cust
            $this->db->where('tr.deleted',0);
            if($where!='') $this->db->where('tr.deleted',0);
            $result = $this->db->get()->result_array();  
            return $result;
	}
        public function get_single_item($item_code,$sales_type_id='',$where=''){ 
//            $this->db->select('i.*,is.uom_id,is.units_available,is.uom_id_2,is.units_available_2');
            $this->db->select('i.*');
            $this->db->select('ip.price_amount,ip.currency_code,ip.currency_value');
            $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = i.item_uom_id)  as unit_abbreviation');
            $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = i.item_uom_id_2)  as unit_abbreviation_2');
            $this->db->from(ITEMS.' i'); 
//            $this->db->join(ITEM_STOCK.' is','is.item_id = i.id'); 
            $this->db->join(ITEM_PRICES.' ip','ip.item_id = i.id'); 
            $this->db->where('i.status',1);
            $this->db->where('i.deleted',0);
            $this->db->where('ip.deleted',0);
            $this->db->where('ip.item_price_type',2); 
            $this->db->where('i.item_code',$item_code); 
            if($sales_type_id!='') $this->db->where('ip.sales_type_id',$sales_type_id);
            if($where!='')$this->db->where($where);
            $result = $this->db->get()->result_array();  
//            echo  $this->db->last_query(); die;
//        echo '<pre>';        print_r($result);die;
            if(!empty($result)){
                return $result[0];
            }else{
                return $this->get_single_item_for_code($item_code, $where);
            }
            return $result;
	}
         public function get_single_item_for_code($item_code,$where=''){ 
            $this->db->select('i.*');
            $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = i.item_uom_id)  as unit_abbreviation');
            $this->db->select('(select unit_abbreviation from '.ITEM_UOM.' where id = i.item_uom_id_2)  as unit_abbreviation_2');
            $this->db->from(ITEMS.' i'); 
            $this->db->where('i.status',1);
            $this->db->where('i.deleted',0);
            $this->db->where('i.item_code',$item_code);
//            $this->db->where('ip.sales_type_id',$sales_type);
            if($where!='')$this->db->where($where);
            $result = $this->db->get()->result_array();  
//            echo  $this->db->last_query(); die;
//        echo '<pre>';        print_r($result);die;
            if(!empty($result))
                return $result[0];
            return $result;
	}
         
         public function get_currency_for_code($code='LKR',$where=''){ 
            $this->db->select('*');
            $this->db->from(CURRENCY);
            if($code!='')$this->db->where('code',$code);
            if($where!='')$this->db->where($where);
            $result = $this->db->get()->result_array();  
//            echo  $this->db->last_query(); die;
//        echo '<pre>';        print_r($result);die;
            if(!empty($result))
                return $result[0];
            return $result;
	}
         
                        
        public function add_db($data){    
//            echo '<pre>';            print_r($data); die; 
                $this->db->trans_start();
                
		if(!empty($data['inv_tbl']))$this->db->insert(INVOICES, $data['inv_tbl']);  
		if(!empty($data['inv_desc']))$this->db->insert_batch(INVOICE_DESC, $data['inv_desc']);   
		if(!empty($data['item_stock_transection']))$this->db->insert_batch(ITEM_STOCK_TRANS, $data['item_stock_transection']);   
                
                if(!empty($data['item_stock'])){
                    foreach ($data['item_stock'] as $stock){
                        $this->db->where('location_id', $stock['location_id']);
                        $this->db->where('item_id', $stock['item_id']);
                        $this->db->update(ITEM_STOCK, array('units_available'=>$stock['new_units_available'],'units_available_2'=>$stock['new_units_available_2']));
                    }
                }
                
//                if($data['so_ref']!=""){//update so after invoice created 
//                    $this->db->where('id',$data['so_ref']);
//                    $this->db->update(SALES_ORDERS,array('invoiced'=>1));
//                } 
                if(!empty($data['so_desc_tbl'])){
                    foreach ($data['so_desc_tbl'] as $so_desc_itm){
                        $this->db->where('new_item_id', $so_desc_itm['new_item_id']);
                        $this->db->where('sales_order_id', $data['so_ref']);
                        $this->db->update(SALES_ORDER_DESC,array('invoiced'=>1)); 
                    }
                }
		if(!empty($data['consignee_commish_tbl']))$this->db->insert(CONSIGNEE_COMMISH, $data['consignee_commish_tbl']);  //consigne  commish 
                 
		if(!empty($data['payment_transection']))$this->db->insert(TRANSECTION, $data['payment_transection']);  
		if(!empty($data['payment_transection_ref']))$this->db->insert(TRANSECTION_REF, $data['payment_transection_ref']);  
		 
		if(!empty($data['addons']))$this->db->insert_batch(INVOICES_ADDONS, $data['addons']); 
		if(!empty($data['gl_trans']))$this->db->insert_batch(GL_TRANS, $data['gl_trans']); 
                
                $status[0]=$this->db->trans_complete();
		$status[1]=$data['inv_tbl']['id']; 
		return $status;
	}
        
        public function edit_db($id,$data){
		$this->db->trans_start();
                
		$this->db->where('id', $id);
                $this->db->where('deleted',0);
		$this->db->update(VEHICLE_RATES, $data);
                        
		$status=$this->db->trans_complete();
		return $status;
	}
        
        public function delete_db($id,$data){ 
            
//            echo '<pre>';            print_r($data); die;
		$this->db->trans_start();
                
		$this->db->where('id', $id); 
                $this->db->where('deleted',0);
		$this->db->update(INVOICES, $data['tbl_data']);
		
                $this->db->where('invoice_id', $id); 
                $this->db->where('deleted',0);
		$this->db->update(INVOICE_DESC, array('deleted'=>1));
                
		$this->db->where('invoice_id', $id); 
                $this->db->where('deleted',0);
		$this->db->update(INVOICES_ADDONS, array('deleted'=>1));
                
                $this->db->where('reference_id', $id); 
                $this->db->where('person_type', 10); // cust
                $this->db->where('deleted',0);
		$this->db->update(TRANSECTION_REF, array('deleted'=>1,'deleted_on' => date('Y-m-d'),'deleted_by' => $this->session->userdata(SYSTEM_CODE)['ID']));
		
                if(!empty($data['item_stock'])){
                    foreach ($data['item_stock'] as $stock){
                        $this->db->where('location_id', $stock['location_id']);
                        $this->db->where('item_id', $stock['item_id']);
                        $this->db->update(ITEM_STOCK, array('units_available'=>$stock['new_units_available'],'units_available_2'=>$stock['new_units_available_2']));
                    }
                }
                
                
//		if(!empty($data['gl_trans']))$this->db->insert_batch(GL_TRANS, $data['gl_trans']); 
                $this->db->where('trans_ref', $id); 
                $this->db->where('person_type', 10); // cust
                $this->db->where('deleted',0);
		$this->db->update(GL_TRANS, array('deleted'=>1,'deleted_on' => date('Y-m-d'),'deleted_by' => $this->session->userdata(SYSTEM_CODE)['ID']));
		
                
                $this->db->where('trans_ref', $id); 
                $this->db->where('transection_type', 2); 
                $this->db->delete(ITEM_STOCK_TRANS); 
                
                $status=$this->db->trans_complete();
		return $status;
	}
        
        function delete_db2($id){
                $this->db->trans_start();
                $this->db->delete(VEHICLE_RATES, array('id' => $id));     
                $status = $this->db->trans_complete();
                return $status;	
	} 
        
        function get_sales_invoices_for_order($order_id){
            $this->db->select('*');
            $this->db->from(INVOICES);
            $this->db->where('so_id',$order_id);
            $result = $this->db->get()->result_array();  
            return $result;
        }
        
        public function get_item_standard_prices($item_id,$where=''){ 
            $this->db->select('ip.*');  
            $this->db->from(ITEM_PRICES." ip");    
            $this->db->where('ip.item_id',$item_id);
            $this->db->where('ip.deleted',0);
            $this->db->where('ip.status',1);
            $this->db->where('ip.item_price_type',3); //3 for standard price
            if($where!='') $this->db->where($where);
            $result = $this->db->get()->result_array();  
//            echo $this->db->last_query(); die;
            return $result;
	}
 
}
?>