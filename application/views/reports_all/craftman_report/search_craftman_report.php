
<script>
    
$(document).ready(function(){  
	get_results();
    $("#receive_no").keyup(function(){ 
		event.preventDefault();
		get_results();
    });
	 
    $("#search_btn").click(function(){
		event.preventDefault();
		get_results();
    });
    $("#received_date").change(function(){
		event.preventDefault();
		get_results();
    }); 
//    $("#status").change(function(){
//		event.preventDefault();
//		get_results();
//    });
    $("#print_btn").click(function(){
        var post_data = jQuery('#form_search').serialize(); 
//        var json_data = JSON.stringify(post_data)
        window.open('<?php echo $this->router->fetch_class()."/print_report?";?>'+post_data,'ZV VINDOW',width=600,height=300)
    });
	
	
	function get_results(){
        $("#result_search").html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Retrieving Data..');    
        var post_data = jQuery('#form_search').serializeArray(); 
        post_data.push({name:"function_name",value:'search'});
        $.ajax({
			url: "<?php echo site_url($this->router->directory.$this->router->fetch_class().'/fl_ajax');?>",
			type: 'post',
			data : post_data,
			success: function(result){
                             $("#result_search").html(result);
                             $(".dataTable").DataTable();
        }
		});
	}
});
</script>
 
<?php // echo '<pre>'; print_r($facility_list); die;?>

<div class="row">
<div class="col-md-12">
    <br>   
    <div class="col-md-12">

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
           <?php echo ($this->user_default_model->check_authority($this->session->userdata(SYSTEM_CODE)['user_role_ID'], $this->router->class, 'add'))?'<a href="'.base_url($this->router->fetch_class().'/add').'" class=" btn btn-app "><i class="fa fa-plus"></i>Create New</a>':''; ?> 
             
        </div>
    </div>
    
 <br><hr>
    <section  class="content"> 
        <div class="">
              <!-- general form elements -->
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Search </h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
              
            <?php echo form_open("", 'id="form_search" class="form-horizontal"')?>  
   
                    <div class="box-body">
                        <div class="row"> 
                            <div class="col-md-4">  
                                    <div class="form-group pad  no-pad-top">
                                        <label for="order_no">Order No</label>
                                         <?php echo form_input('order_no',set_value('order_no',''),' class="form-control" placeholder="Search by Order No" id="order_no"');?>
                                    </div> 
                                </div>    
                                <div class="col-md-4">  
                                    <div class="form-group pad  no-pad-top">
                                        <label for="receive_no">Order No</label>
                                         <?php echo form_input('receive_no',set_value('receive_no',''),' class="form-control" placeholder="Search by receive No" id="receive_no"');?>
                                    </div> 
                                </div>    
                                <div class="col-md-4">  
                                        <div class="form-group pad  no-pad-top">
                                            <label for="craftman_id">Craftman.</label>
                                             <?php echo form_dropdown('craftman_id',$craftman_list,set_value('craftman_id'),' class="form-control select2" id="craftman_id"');?>

                                        </div> 
                                </div>    
                                <div class="col-md-4">  
                                        <div class="form-group pad">
                                            <label for="date_from">Received From</label>
                                            <?php  echo form_input('date_from',set_value('date_from', date(SYS_DATE_FORMAT, strtotime("-1 month"))),' class="form-control datepicker" readonly  id="date_from"');?>
                                        </div> 
                                </div>  
                                <div class="col-md-4">  
                                        <div class="form-group pad">
                                            <label for="date_to">Received To</label>
                                            <?php  echo form_input('date_to',set_value('date_to',date(SYS_DATE_FORMAT)),' class="form-control datepicker" readonly  id="date_to"');?>
                                        </div> 
                                </div>   
                        </div>
                    </div>
                <div class="panel-footer">
                    <button type="reset" class="btn btn-default">Clear Form</button>                                    
                                    <a id="print_btn" class="btn btn-info margin-r-5 pull-right"><span class="fa fa-print"></span> Print</a>
                                     <a id="search_btn" class="btn btn-primary pull-right"><span class="fa fa-search"></span>Search</a>
                                </div>
              </div>
    </section>
                            <?php echo form_close(); ?>               
                                
                         
                            
                        </div>
     <div class="col-md-12">
    <div class="box">
            <div class="box-header">
              <h3 class="box-title">item receive history</h3>
            </div>
            <!-- /.box-header -->
            <div  id="result_search" class="box-body"> </div>
            <!-- /.box-body -->
          </div>
       
     </div>
</div> 