<table id="example1" class="table dataTable table-bordered table-striped">
         <thead>
            <tr>
                <th>#</th>
                <th>Receive No</th> 
                <th>Craftman</th> 
                <th>Date Received</th> 
                <th>Total Weight</th> 
                <th>Total Articles</th>    
            </tr>
        </thead>
        <tbody>
              <?php
                  $i = $tot_units1 = $tot_units2 = 0; 
                   foreach ($search_list as $search){ 
//                       echo '<pre>';                       print_r($search); die;
                       $tot_units1 +=$search['total_units']; 
                       $tot_units2 +=$search['total_units_2']; 
                       echo '
                           <tr>
                               <td>'.($i+1).'</td> 
                               <td>'.$search['cm_receival_no'].'</td>
                               <td>'.$search['craftman_name'].'</td>
                               <td>'.(($search['receival_date']>0)?date('d M Y',$search['receival_date']):'').'</td>  
                               <td>'. number_format($search['total_units'],3).' '.$search['unit_abbreviation'].'</td>
                               <td>'.$search['total_units_2'].' '.$search['unit_abbreviation_2'].'</td> ';
                       $i++;
                   }
              ?>   
        </tbody>
           <tfoot>
            <tr>
                <th>#</th>
                <th></th> 
                <th></th> 
                <th style="text-align: right;">TOTAL: </th> 
                <th><?php echo number_format($tot_units1,3).' '.$search['unit_abbreviation'];?></th> 
                <th><?php echo number_format($tot_units2).' '.$search['unit_abbreviation_2'];?></th>  
            </tr>
           </tfoot>
         </table>