<div class="modal fade" id="permissionSetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<!-- MODAL HEADER
			//====================================================================================================================================== -->
    		<div class="modal-header">
        		<span class="modal-title" id="permissionSetModalHeader" style="font-weight: bold;font-size: 16px;">Set Permission</span>
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          			<span aria-hidden="true">&times;</span>
        		</button>
     		</div>
      
    		<form action="<?php ?>" method="post" class="form-horizontal" id="frmPermissionSet">
    		<?php echo $csrf;?>

				<!-- MODAL BODY
				//====================================================================================================================================== -->
        		<div class="modal-body">

					<table class="table table-responsive">
						<thead class="bg-primary">
							<tr>
								<th style="display: none;">ID</th>
								<th>Name</th>
								<th>Definition</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td id="rowPermissionId" style="display: none;"></td>
								<td id="rowPermissionName"></td>
								<td id="rowPermissionDefinition"></td>
							</tr>
						</tbody>
					</table>

					<hr/>

					<ul class="list-group">
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_um"><b> SELECT ALL</b></li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_um"><b> User Management</b></li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_um_u" value="1001" style="margin-left: 10px;"> Users</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_um_gl" value="1002" style="margin-left: 10px;"> Group Library</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_um_pl" value="1003" style="margin-left: 10px;"> Permission Library</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_cn"><b> Clinical Notes</b></li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_cn_cf" value="2001" style="margin-left: 10px;"> Create Form</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl"><b> System Libraries</b></li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_ad" value="3001" style="margin-left: 10px;"> Area Demographics</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_as" value="3002" style="margin-left: 10px;"> Appointment Status</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_at" value="3003" style="margin-left: 10px;"> Appointment Type</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_bt" value="3003" style="margin-left: 10px;"> Bill Type</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_c" value="3004" style="margin-left: 10px;"> Category</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_cc" value="3005" style="margin-left: 10px;"> Chief Complaint</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_dc" value="3006" style="margin-left: 10px;"> Death Certificate</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_doct" value="3007" style="margin-left: 10px;"> Doctor</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_docu" value="3008" style="margin-left: 10px;"> Documents</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_en" value="3009" style="margin-left: 10px;"> Extension Name</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_im" value="3010" style="margin-left: 10px;"> Immunization</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_ms" value="3011" style="margin-left: 10px;"> Marital Status</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_med" value="3012" style="margin-left: 10px;"> Medication</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_nat" value="3013" style="margin-left: 10px;"> Nationality</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_occ" value="3014" style="margin-left: 10px;"> Occupation</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_off" value="3015" style="margin-left: 10px;"> Office</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_rel" value="3016" style="margin-left: 10px;"> Religion</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_rp" value="3017" style="margin-left: 10px;"> Relationship to Patient</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_sc" value="3018" style="margin-left: 10px;"> Sub-Category</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_sl_t" value="3019" style="margin-left: 10px;"> Title</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_pl"><b> Patient List</b></li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_pl_pr" value="4001" style="margin-left: 10px;"> Patient Record</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_pl_er" value="4002" style="margin-left: 10px;"> Emergency Room</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_pl_ip" value="4003" style="margin-left: 10px;"> Inpatient</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_pl_op" value="4004" style="margin-left: 10px;"> Outpatient</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_pc"><b> Patient Chart</b></li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_pc_app" value="5001" style="margin-left: 10px;"> Appointment</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_pc_pv" value="5002" style="margin-left: 10px;"> Patient Visits</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_pc_vt" value="5003" style="margin-left: 10px;"> Vitals</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_pc_cn" value="5003" style="margin-left: 10px;"> Clinical Notes</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_pc_r" value="5004" style="margin-left: 10px;"> Radiology</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_pc_l" value="5005" style="margin-left: 10px;"> Laboratory</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_pc_mp" value="5006" style="margin-left: 10px;"> Medication/Prescriptions</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_pc_im" value="5007" style="margin-left: 10px;"> Immunization</li>
						<li class="list-group-item"><input class="form-check-input" type="checkbox" id="cbx_pc_docu" value="5008" style="margin-left: 10px;"> Documents</li>
					</ul>

        		</div>

				<!-- MODAL FOOTER
				//====================================================================================================================================== -->
        		<div class="modal-footer">
          			<button type="button" class="btn btn-danger btn-flat" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i> Cancel</button>
            		<button type="submit" class="btn btn-primary btn-flat"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
        		</div>
     		</form>
    	</div>
  	</div>
</div>