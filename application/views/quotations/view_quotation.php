<?php
$inv_dets = $inv_data['invoice_dets'];
$inv_desc = $inv_data['invoice_desc'];
$inv_trans = array();


//echo '<pre>';print_r($inv_dets); die;
?>
<style>
    .colored_bg{
        background-color:#E0E0E0;
    }
    .table-line th, .table-line td {
        padding-bottom: 2px;
        border-bottom: 1px solid #ddd;
        text-align:center; 
    }
    .text-right,.table-line.text-right{
        text-align:right;
    }
    .table-line tr{
        line-height: 30px;
    }
    </style>
<div class="row">
<div class="col-md-12">
    <br>   
    <div class="col-md-12">

    
    
        <div class="">
            <a href="<?php echo base_url($this->router->fetch_class().'/add');?>" class="btn btn-app "><i class="fa fa-plus"></i>Create New</a>
            <a href="<?php echo base_url($this->router->fetch_class());?>" class="btn btn-app "><i class="fa fa-search"></i>Search</a> 
            <?php echo ($this->user_default_model->check_authority($this->session->userdata(SYSTEM_CODE)['user_role_ID'], $this->router->class, 'quote_print'))?'<a target="_blank" href="'.base_url($this->router->fetch_class().'/quote_print/'.$inv_dets['id']).'" class="btn btn-app "><i class="fa fa-print"></i>Print Invoice</a>':''; ?>

        </div>
    </div>
    
 <br><hr>
    <section  class="content"> 
        <!--Flash Error Msg-->
        <?php  if($this->session->flashdata('error') != ''){ ?>
        <div class='alert alert-danger ' id="msg2">
        <a class="close" data-dismiss="alert" href="#">&times;</a>
        <i ></i>&nbsp;<?php echo $this->session->flashdata('error'); ?>
        <script>jQuery(document).ready(function(){jQuery('#msg2').delay(1500).slideUp(1000);});</script>
        </div>
        <?php } ?>

        <?php  if($this->session->flashdata('warn') != ''){ ?>
        <div class='alert alert-success ' id="msg2">
        <a class="close" data-dismiss="alert" href="#">&times;</a>
        <i ></i>&nbsp;<?php echo $this->session->flashdata('warn'); ?>
        <script>jQuery(document).ready(function(){jQuery('#msg2').delay(1500).slideUp(1000);});</script>
        </div>
        <?php } ?>  
        
        <div class="">
             <?php echo form_open_multipart("Quotations/validate"); ?> 
              <!-- general form elements -->
              <div class="box box-primary"> 
                  <div class="box-body">
                <!-- /.box-header -->
                <!-- form start -->
                      <div class="row header_form_sales"> 
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Type <span style="color: red">*</span></label>
                                    <div class="col-md-8">    
                                         <?php  echo form_dropdown('quotation_type',$quotation_list,set_value('quotation_type'),' class="form-control select2" data-live-search="true" id="quotation_type"');?>
                                         <!--<span class="help-block"><?php // echo form_error('customer_type_id');?>&nbsp;</span>-->
                                    </div> 
                                </div> 
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Customer <span style="color: red">*</span></label>
                                    <div class="col-md-8">    
                                         <?php  echo form_dropdown('customer_id',$customer_list,set_value('customer_id'),' class="form-control select2" data-live-search="true" id="customer_id"');?>
                                         <!--<span class="help-block"><?php // echo form_error('customer_type_id');?>&nbsp;</span>-->
                                    </div> 
                                </div>
                            </div>
                            <div class="col-md-5"> 
                                
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Date <span style="color: red">*</span></label>
                                    <div class="col-md-8">    
                                         <?php  echo form_input('quoted_date',set_value('quoted_date',date('m/d/Y',$inv_dets['quoted_date'])),' class="form-control datepicker" readonly id="quoted_date"');?>
                                         <span class="help-block"><?php echo form_error('quoted_date');?>&nbsp;</span>
                                    </div> 
                                </div>
                            </div>
                            <div  class="col-md-5">
                                <div hidden class="form-group">
                                    <label class="col-md-4 control-label">Payments<span style="color: red">*</span></label>
                                    <div class="col-md-8">    
                                         <?php  echo form_dropdown('payment_term_id',$payment_term_list,set_value('payment_term_id'),' class="form-control select2" data-live-search="true" id="payment_term_id"');?>
                                         <!--<span class="help-block"><?php // echo form_error('customer_type_id');?>&nbsp;</span>-->
                                    </div> 
                                </div>
                                <div hidden class="form-group">
                                    <label class="col-md-4 control-label">Sale Type <span style="color: red">*</span></label>
                                    <div class="col-md-8">    
                                         <?php  echo form_dropdown('sales_type_id',$sales_type_list,set_value('sales_type_id'),' class="form-control select2" data-live-search="true" id="sales_type_id"');?>
                                         <!--<span class="help-block"><?php // echo form_error('customer_type_id');?>&nbsp;</span>-->
                                    </div> 
                                </div>
                                
                            </div>
                             
                        </div>
                       
                    <div class="box-body">
                    <div class="col-md-10 col-md-offset-1">
                        <hr>
                        <table width="100%" border="1">
                            <tr><td>
                    <?php
                            
                    foreach ($inv_desc as $inv_itms){ 
                         echo '<table width="100%" id="example1" class="table-line" border="0">
                                    <thead>
                                        <tr class="colored_bg" style="background-color:#E0E0E0;">
                                             <th colspan="5">'.$inv_data['item_cats'][$inv_itms[0]['item_category']].'</th> 
                                         </tr>
                                        <tr style="">
                                             <th  width="10%"><u><b>Qty</b></u></th> 
                                             <th width="40%" style="text-align: left;"><u><b>Description</b></u></th>  
                                             <th width="15%" style="text-align: right;"><u><b>Rate</b></u></th> 
                                             <th width="15%" style="text-align: right;"><u><b>Discount</b></u></th> 
                                             <th width="19%" style="text-align: right;"><u><b>Total</b></u></th> 
                                         </tr>
                                    </thead>
                                <tbody>';

                     foreach ($inv_itms as $inv_itm){
                         echo     '<tr>
                                        <td width="10%">'.$inv_itm['quantity'].'</td> 
                                        <td width="40%" style="text-align: left;">'.$inv_itm['item_description'].'</td>  
                                        <td width="15%" style="text-align: right;">'. number_format($inv_itm['unit_price'],2).'</td> 
                                        <td width="15%" style="text-align: right;">'. number_format($inv_itm['discount_persent'],2).'</td> 
                                        <td width="19%" style="text-align: right;">'. number_format($inv_itm['sub_total'],2).'</td> 
                                    </tr> ';
                     }
                     echo       ' <tr><td  colspan="5"></td></tr></tbody></table>'; 
            }
            echo '
                    <table id="example1" width="100%" class="table-line" border="0">
                        
                       <tbody>

                                <tr class="td_ht">
                                    <td style="text-align: right;" colspan="4"><b> Total</b></td> 
                                    <td  width="19%"  style="text-align: right;"><b>'. number_format($inv_data['invoice_desc_total'],2).'</b></td> 
                                </tr>'; 
                        foreach ($inv_trans as $inv_tran){
                            echo '<tr>
                                            <td  style="text-align: right;" colspan="4">'.$inv_tran['name'].'</td> 
                                            <td  width="19%"  style="text-align: right;">'. number_format($inv_tran['transection_amount'],2).'</td> 
                                        </tr> ';

                        }
                        echo '<tr hidden>
                                    <td  style="text-align: right;" colspan="4"><b>Balance</b></td> 
                                    <td width="19%"  style="text-align: right;"><b>'. number_format($inv_data['invoice_total'],2).'</b></td> 
                                </tr> 
                        </tbody>
                    </table>
                                                               
                '; 
//             echo $html;
                       ?>
                                    </td></tr>
                        </table>
                    </div>
                    </div>
              </div>
              </div>
              <div class="box-footer">
                          <!--<butto style="z-index:1" n class="btn btn-default">Clear Form</button>-->                                    
                                    <!--<button class="btn btn-primary pull-right">Add</button>-->  
                                    <?php if($action != 'View'){?>
                                    <?php echo form_hidden('id', $inv_data['invoice_dets']['id']); ?>
                                    <?php echo form_hidden('action',$action); ?>
                                    <?php echo form_submit('submit',$action ,'class="btn btn-primary"'); ?>&nbsp;

                                    <?php echo anchor(site_url($this->router->fetch_class()),'Back','class="btn btn-info"');?>&nbsp;
                                    <?php echo form_reset('reset','Reset','class = "btn btn-default"'); ?>

                                 <?php }else{ 
                                        echo form_hidden('action',$action);
                                        echo anchor(site_url($this->router->fetch_class()),'OK','class="btn btn-primary"');
                                    } ?>
                      <!--<button type="submit" class="btn btn-primary">Submit</button>-->
                    </div
                  </form>
        </div>
    </section>
</div>
</div>
    
   
<script>
    
$(document).keypress(function(e) {
//    fl_alert('info',e.keyCode)
        if(e.keyCode == 80) {//80 for shit+p (print invoice)
           window.open('<?php echo base_url($this->router->fetch_class().'/quote_print/'.$inv_dets['id']);?>');
        }
        if(e.keyCode == 78) {//80 for shit+p (print invoice)
           window.location.replace('<?php echo base_url('Invoices/add/');?>');
        }
    });
</script>