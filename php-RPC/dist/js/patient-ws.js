$(function () {
			/** Patient Info add/update/delete **/		
			$("#patientInfoAddForm").validate({
				onfocusout: false,//function(element) {$(element).valid();},
				// Specify the validation rules
				rules: {
					recordedOn: {required: true},
					patientHeight: {required: true, number: true},
					patientWeight: {required: true, number: true},
				}
			});
			$('#user').change(function() {
				var id = this.value;
				//$("#patientInfoId").val(pid);
				//$( "#messagebx" ).html('');
				var wbUrl = $("#wbUrl").val();
				
				$.ajax({
				   type: "GET",
				   url: wbUrl,
				   dataType: "json",
				   data: {id:id, action: "getuserData"},
				   success: function (data, status) {
					if (status =="success") {
						if(data.response !="") {
							var response = data.response;
							$("#email").val(data.response.email);
							$("#firstname").val(data.response.firstName);
							$("#lastname").val(data.response.lastName);
							$("#dob").val(data.response.userInfo.dob);
							$('#gender').val([data.response.userInfo.gender]);
							$("#phone").val(data.response.userInfo.phone);
							$("#title").val(data.response.userInfo.title);
							var userList = "<table disabled id='userList' class='table table-bordered table-striped'>" +
											"<thead><tr><th>First name</th><th>Last name</th><th>Email </th><th>Phone</th><th>Dob</th><th>Gender</th></tr></thead>"+
											"<tr>"+
											"<td>" + data.response.firstName + "</td>" +
											"<td>" + data.response.lastName + "</td>" +
											"<td>" + data.response.email + "</td>" +
											"<td>" + data.response.userInfo.phone + "</td>" +
											"<td>" + data.response.userInfo.dob + "</td>" +
											"<td>" + data.response.userInfo.gender + "</td>" + 
											"</tr></table>";
							$("#userListTable").html(userList);
						}
					}
				   }
			   });
			});
			$( "#patientInfoSubmit" ).click(function() {
				if($("#patientInfoAddForm").valid()) {
					var patientInfoId = $("#patientInfoId").val();
					if(patientInfoId !="") {
						var formAction = "updatePatientInfo";
					} else {
						var formAction = "addPatientInfo";
					}
					var wbUrl = $("#wbUrl").val();
					var sendInfo = {
						height: $("#patientHeight").val(),
						recordedOn: $(".recordedOn").val(),			
						weight: $("#patientWeight").val(),
						patientId: $( "#patientId").val(),
						action: formAction,
						id: patientInfoId
				   };
				   var encoded = JSON.stringify( sendInfo );
				   $.ajax({
					   type: "PUT",
					   url: wbUrl,
					   dataType: "json",
					   data: encoded,
					   success: function (data, status) {
						   if (status =="success") {
							if(formAction =="updatePatientInfo") {
									$( "#pi1-"+patientInfoId).html($(".recordedOn").val());
									$( "#pi2-"+patientInfoId).html($("#patientHeight").val());
									$( "#pi3-"+patientInfoId).html($("#patientWeight").val());
							   } else {
									var add_info = 'tr:last';
									if(infoCount() == 0) {
										add_info  = 'tbody:last';
										$('#norecInfo').remove();
									}
									$('#patientInfo-list ' + add_info).after(
										'<tr id="pi-'+ data.response +'">' +'<td>'+ data.response +'</td>' +
													'<td id="pi1-'+data.response+'">'+ $(".recordedOn").val() +'</td>'+
													'<td id="pi2-'+data.response+'">' + $("#patientHeight").val() + '</td>' + 
													'<td id="pi3-'+data.response+'">'+ $("#patientWeight").val() +'</td>' +
													'<td><a href="#" class="editInfoLnk" data-id="'+ data.response + '" id="'+ data.response + '" data-toggle="modal" data-target="#patientInfoForm">EDIT</a> | <a href="javascript:void(0);" class="pinfodel" data-id="'+ wbUrl+'#'+data.response+'" title="Are you sure you want to delete the Patient Info record?">DELETE</a></td>' +
												  '</tr>'
									);
							   }
							   $( "#messagebx" ).html('<p style="color:green">' + data.text + '</p>');
							   getPatientInfo();
							   delPatientInfo();
							   setTimeout(function(){
								  $( "#messagebx" ).html('');
								  $('#patientInfoForm').modal('hide');
								}, 1000);
						   } else {
							   //alert("Cannot add to list !");
						   }
					   }
				   });
			   }
			});
			$("#patientInfoFormLnk").click(function () {
				//$("#recordedOn").val('');
				$("#patientInfoId").val('');
				$("#patientHeight").val('');
				$("#patientWeight").val('');
			});
			function infoCount() {
				return $('#patientInfo-list >tbody >tr').length;
			}
			function getPatientInfo() {
				$(".editInfoLnk").click(function () {
					var pid = $(this).attr("id");
					$("#patientInfoId").val(pid);
					$( "#messagebx" ).html('');
					var wbUrl = $("#wbUrl").val();
					$.ajax({
					   type: "GET",
					   url: wbUrl,
					   dataType: "json",
					   data: {id:pid, action: "getPatientInfoById"},
					   success: function (data, status) {
						if (status =="success") {
							if(data.response !="") {
								var response = data.response;
								$("#datetimepicker1").val(response.recordedOn);
								$("#patientHeight").val(response.height);
								$("#patientWeight").val(response.weight);
							}
						}
					   }
				   });
				});
			}
			getPatientInfo();
			delPatientInfo();
			function delPatientInfo(){
				//Patient Info Delete
				$(".pinfodel").click(function () {
					var recId = $(this).attr("data-id");
					var res = recId.split("#"); 
					var wbUrl=res[0];
					var psid = res[1];
					$.ajax({
					   type: "POST",
					   url: wbUrl,
					   dataType: "json",
					   data: {id:psid, action: "patientInfoDel"},
					   success: function (data, status) {
						if (status =="success") {
							if(data.response !="") {
								var response = data.response;
							}
						}
					   }
				   });
					$('table#patientInfo-list tr#pi-'+psid).remove();
					if(infoCount() == 0) {
						$( "table#patientInfo-list" ).after('<p style="color:red;padding:5px;" id="norecInfo">No Records Found!	</p>');
					}
				});
			}
			/**** Patient Problems code Start ****/
			$("#patientProblemFormLnk").click(function () {
				$("#patientProblemId").val('');
				$("#problemOcAge").val('');
				$("#problemChronicity").val('');
				$("#problemDetails").val('');
			});
			$("#patientProblemAddForm").validate({
				onfocusout: false,//function(element) {$(element).valid();},
				// Specify the validation rules
				rules: {
					problemOcAge: {required: true, number: true},
					problemChronicity: {required: true},
					problemDetails: {required: true},
				}
			});
			$("#patientCircleAddForm").validate({
				onfocusout: false,//function(element) {$(element).valid();},
				// Specify the validation rules
				rules: {
					phone: {required: true, number: true}
				}
			});
			$( "#patientProblemSubmit" ).click(function() {
				if($("#patientProblemAddForm").valid()) {
				var patientProblemId = $("#patientProblemId").val();
				if(patientProblemId !="") {
					var formAction = "updatePatientProblem";
				} else {
					var formAction = "addPatientProblem";
				}

				var wbUrl = $("#wbUrl").val();
				var sendInfo = {
					patientId: $( "#patientId").val(),
					ageofoccurence: $("#problemOcAge").val(),
					chronicity: $("#problemChronicity").val(),			
					problemdetails: $("#problemDetails").val(),
					action: formAction,
					id: patientProblemId,
			   };
			   $.ajax({
				   type: "PUT",
				   url: wbUrl,
				   dataType: "json",
				   data: sendInfo,
				   success: function (data, status) {
					   if (status =="success") {
						if(formAction =="updatePatientProblem") {
								$( "#pp1-"+patientProblemId).html($("#problemOcAge").val());
								$( "#pp2-"+patientProblemId).html($("#problemChronicity").val());
								$( "#pp3-"+patientProblemId).html($("#problemDetails").val());
						   } else {
								var pro_info = 'tr:last';
								if(problemCount() == 0) {
									pro_info  = 'tbody:last';
									$('#noprobInfo').remove();
								}
								$('#patientProblem-list ' + pro_info).after(
									'<tr id="pp-'+ data.response +'">' +'<td>'+ data.response +'</td>' +
												'<td id="pp1-'+data.response+'">'+ $("#problemOcAge").val() +'</td>'+
												'<td id="pp2-'+data.response+'">' + $("#problemChronicity").val() + '</td>' + 
												'<td id="pp3-'+data.response+'">'+ $("#problemDetails").val() +'</td>' +
												'<td><a href="#" class="editPrLnk" data-id="'+ data.response + '" id="'+ data.response +'" data-toggle="modal" data-target="#patientProblemForm">EDIT</a> | <a href="javascript:void(0);" class="pPblmdel" data-id="'+ wbUrl+'#'+data.response+'" title="Are you sure you want to delete the Patient Problem record?">DELETE</a></td>' +
											  '</tr>'
								);
						   }
						   $( "#messageprbx" ).html('<p style="color:green">' + data.text + '</p>');
						   getPatientProblem();
						   delPatientProblem();
						   setTimeout(function(){
							  $( "#messageprbx" ).html('');
							  $('#patientProblemForm').modal('hide');
							}, 1000);
					   } else {
						   //alert("Cannot add to list !");
					   }
				   }
			   });
			   }
			});
			function problemCount() {
				return $('#patientProblem-list >tbody >tr').length;
			}
			function getPatientProblem() {
				$(".editPrLnk").click(function () {
					var value = $(this).attr("id");
					$("#patientProblemId").val(value);
					$( "#messageprbx" ).html('');
					var wbUrl = $("#wbUrl").val();
					$.ajax({
					   type: "POST",
					   url: wbUrl,
					   dataType: "json",
					   data: {id:value, action: "getPatientProblemById"},
					   success: function (data, status) {
						if (status =="success") {
							if(data.response !="") {
								var response = data.response;
								console.log(response);
								$("#problemOcAge").val(response.ageofoccurence);
								$("#problemChronicity").val(response.chronicity);
								$("#problemDetails").val(response.problemdetails);
							}
						}
					   }
				   });
				});
			}
			getPatientProblem();
			delPatientProblem();
			function delPatientProblem(){
			//Patient Problem Delete
				$(".pPblmdel").click(function () {
					var recId = $(this).attr("data-id");
					var res = recId.split("#"); 
					var wbUrl=res[0];
					var psid = res[1];
					$.ajax({
					   type: "POST",
					   url: wbUrl,
					   dataType: "json",
					   data: {id:psid, action: "patientPblmDel"},
					   success: function (data, status) {
						if (status =="success") {
							if(data.response !="") {
								var response = data.response;
								$('#patientProblem-list tr#pp-'+psid).remove();
								//$( "#messagepsbx" ).html('<p style="color:green">' + data.text + '</p>');
							}
						}
					   }
				   });
					$('table#patientProblem-list tr#pp-'+psid).remove();
					if(problemCount() == 0) {
						$( "table#patientProblem-list" ).after('<p style="color:red;padding:5px;" id="noprobInfo">No Records Found!	</p>');
					}
				});
			}
			/**** Patient History code Start ****/
			$("#patientHistoryFormLnk").click(function () {
				$("#patientHistoryId").val('');
				$("#historyDetails").val('');
				$("#visitedDate").val('');
			});
			$("#patientHistoryAddForm").validate({
				onfocusout: false,//function(element) {$(element).valid();},
				// Specify the validation rules
				rules: {
					historyDetails: {required: true},
					visitedDate: {required: true},
				}
			});
			
			$( "#patientHistorySubmit" ).click(function() {
				if($("#patientHistoryAddForm").valid()) { 
				var patientHistoryId = $("#patientHistoryId").val();
				if(patientHistoryId !="") {
					var formAction = "updatePatientHistory";
				} else {
					var formAction = "addPatientHistory";
				}

				var wbUrl = $("#wbUrl").val();
				var sendInfo = {
					patientId: $( "#patientId").val(),
					historyDetails: $("#historyDetails").val(),
					visitedDate: $("#visitedDate").val(),			
					action: formAction,
					id: patientHistoryId,
			   };
			   $.ajax({
				   type: "POST",
				   url: wbUrl,
				   dataType: "json",
				   data: sendInfo,
				   success: function (data, status) {
					   if (status =="success") {
						if(formAction =="updatePatientHistory") {
								$( "#ph1-"+patientHistoryId).html($("#historyDetails").val());
								$( "#ph2-"+patientHistoryId).html($("#visitedDate").val());
						   } else {
								var his_info = 'tr:last';
								if(historyCount() == 0) {
									his_info  = 'tbody:last';
									$('#nohisInfo').remove();
								}
								$('#patientHistory-list ' + his_info).after(
									'<tr id="ph-'+ data.response +'">' +'<td>'+ data.response +'</td>' +
										'<td id="ph1-'+data.response+'">'+ $("#visitedDate").val() +'</td>'+
										'<td id="ph2-'+data.response+'">' + $("#historyDetails").val() + '</td>' + 
										'<td><a href="#" class="editPhLnk" data-id="'+ data.response + '" id="'+ data.response +'" data-toggle="modal" data-target="#patientHistoryForm">EDIT</a> | <a href="javascript:void(0);" class="ptdel" data-id="'+ wbUrl+'#'+data.response+'" title="Are you sure you want to delete the Patient History record?">DELETE</a></td>' +
									'</tr>'
								);
						   }
						   $( "#messagehsbx" ).html('<p style="color:green">' + data.text + '</p>');
						   getPatientHistory();
						   delPatientHistory();
						   setTimeout(function(){
							  $( "#messagehsbx" ).html('');
							  $('#patientHistoryForm').modal('hide');
							}, 1000);
					   } else {
						   //alert("Cannot add to list !");
					   }
				   }
			   });
			   }
			});
			function historyCount() {
				return $('#patientHistory-list >tbody >tr').length;
			}
			function  getPatientHistory() {
				$(".editPhLnk").click(function () {
					var value = $(this).attr("id");
					$("#patientHistoryId").val(value);
					$( "#messagehsbx" ).html('');
					var wbUrl = $("#wbUrl").val();
					$.ajax({
					   type: "POST",
					   url: wbUrl,
					   dataType: "json",
					   data: {id:value, action: "getPatientHistoryById"},
					   success: function (data, status) {
						if (status =="success") {
							if(data.response !="") {
								var response = data.response;
								$("#historyDetails").val(response.details);
								$("#visitedDate").val(response.visitedDate);
							}
						}
					   }
				   });
				});
			}
			getPatientHistory();
			delPatientHistory();
			function delPatientHistory() {
				//Patient history Delete
				$(".pHisdel").click(function () {
					var recId = $(this).attr("data-id");
					var res = recId.split("#"); 
					var wbUrl=res[0];
					var psid = res[1];
					$.ajax({ 
					   type: "POST",
					   url: wbUrl,
					   dataType: "json",
					   data: {id:psid, action: "patientHisDel"},
					   success: function (data, status) {
						if (status =="success") {
							if(data.response !="") {
								var response = data.response;
								$('#patientHistory-list tr#ph-'+psid).remove();
								//$( "#messagepsbx" ).html('<p style="color:green">' + data.text + '</p>');
							}
						}
					   }
				   });
				   $('table#patientHistory-list tr#ph-'+psid).remove();
					if(historyCount() == 0) {
						$( "table#patientHistory-list" ).after('<p style="color:red;padding:5px;" id="nohisInfo">No Records Found!	</p>');
					}
				});
			}
			/**** Patient Allergy code Start ****/
			$("#patientAllergyFormLnk").click(function () {
				$("#patientAllergyId").val('');
				$("#allergy").val('');
				$("#reaction").val('');
				$("#severity").val('');
				$("#source").val('');
			});
			$("#patientAllergyAddForm").validate({
				onfocusout: false,//function(element) {$(element).valid();},
				// Specify the validation rules
				rules: {
					allergy: {required: true},
					reaction: {required: true},
					severity: {required: true},
					source: {required: true},
				}
			});
			
			$( "#patientAllergySubmit" ).click(function() {
				if($("#patientAllergyAddForm").valid()) { 
				var patientAllergyId = $("#patientAllergyId").val();
				if(patientAllergyId !="") {
					var formAction = "updatePatientAllergy";
				} else {
					var formAction = "addPatientAllergy";
				}

				var wbUrl = $("#wbUrl").val();
				var sendInfo = {
					patientId: $( "#patientId").val(),
					allergy: $("#allergy").val(),
					reaction: $("#reaction").val(),			
					severity: $("#severity").val(),			
					source: $("#source").val(),			
					action: formAction,
					id: patientAllergyId,
			   };
			   $.ajax({
				   type: "POST",
				   url: wbUrl,
				   dataType: "json",
				   data: sendInfo,
				   success: function (data, status) {
					   if (status =="success") {
						if(formAction =="updatePatientAllergy") {
								$( "#pa1-"+patientAllergyId).html($("#allergy").val());
								$( "#pa2-"+patientAllergyId).html($("#reaction").val());
								$( "#pa3-"+patientAllergyId).html($("#severity").val());
								$( "#pa4-"+patientAllergyId).html($("#source").val());
						   } else {
								var allery_info = 'tr:last';
								if(historyCount() == 0) {
									allery_info  = 'tbody:last';
									$('#noallergyInfo').remove();
								}
								$('#patientAllergy-list ' + allery_info).after(
									'<tr id="pa-'+ data.response +'">' +'<td>'+ data.response +'</td>' +
										'<td id="pa1-'+data.response+'">'+ $("#allergy").val() +'</td>'+
										'<td id="pa2-'+data.response+'">' + $("#reaction").val() + '</td>' + 
										'<td id="pa3-'+data.response+'">' + $("#severity").val() + '</td>' + 
										'<td id="pa4-'+data.response+'">' + $("#source").val() + '</td>' + 
										'<td><a href="#" class="editPaLnk" data-id="'+ data.response + '" id="'+ data.response +'" data-toggle="modal" data-target="#patientAllergyForm">EDIT</a> | <a href="javascript:void(0);" class="paldel" data-id="'+ wbUrl+'#'+data.response+'" title="Are you sure you want to delete the Patient Allergy record?">DELETE</a></td>' +
									'</tr>'
								);
						   }
						   $( "#messagealbx" ).html('<p style="color:green">' + data.text + '</p>');
						   getPatientAllergy();
						   delPatientAllergy();
						   setTimeout(function(){
							  $( "#messagealbx" ).html('');
							  $('#patientAllergyForm').modal('hide');
							}, 1000);
					   } else {
						   //alert("Cannot add to list !");
					   }
				   }
			   });
			   }
			});
			function allergyCount() {
				return $('#patientAllergy-list >tbody >tr').length;
			}
			function  getPatientAllergy() {
				$(".editPaLnk").click(function () {
					var value = $(this).attr("id");
					$("#patientAllergyId").val(value);
					$( "#messagealbx" ).html('');
					var wbUrl = $("#wbUrl").val();
					$.ajax({
					   type: "POST",
					   url: wbUrl,
					   dataType: "json",
					   data: {id:value, action: "getPatientAllergyById"},
					   success: function (data, status) {
						if (status =="success") {
							if(data.response !="") {
								var response = data.response;
								$("#allergy").val(response.allergy);
								$("#reaction").val(response.reaction);
								$("#severity").val(response.severity);
								$("#source").val(response.source);
							}
						}
					   }
				   });
				});
			}
			getPatientAllergy();
			delPatientAllergy();
			function delPatientAllergy() {
				//Patient history Delete
				$(".paldel").click(function () {
					var recId = $(this).attr("data-id");
					var res = recId.split("#"); 
					var wbUrl=res[0];
					var psid = res[1];
					$.ajax({ 
					   type: "POST",
					   url: wbUrl,
					   dataType: "json",
					   data: {id:psid, action: "delPatientAllergy"},
					   success: function (data, status) {
						if (status =="success") {
							if(data.response !="") {
								var response = data.response;
								$('#patientAllergy-list tr#pa-'+psid).remove();
								//$( "#messagepsbx" ).html('<p style="color:green">' + data.text + '</p>');
							}
						}
					   }
				   });
				   $('table#patientAllergy-list tr#pa-'+psid).remove();
					if(allergyCount() == 0) {
						$( "table#patientAllergy-list" ).after('<p style="color:red;padding:5px;" id="noallergyInfo">No Records Found!	</p>');
					}
				});
			}

			/**** Patient Surgery code Start ****/
			$("#patientSurgeryFormLnk").click(function () {
				$("#patientSurgeryId").val('');
				$("#datePerformed").val('');
				$("#surgerydetails").val('');
			});
						
			$("#patientSurgeryAddForm").validate({
				onfocusout: false,//function(element) {$(element).valid();},
				// Specify the validation rules
				rules: {
					datePerformed: {required: true},
					surgerydetails: {required: true},
				}
			});
			$( "#patientSurgerySubmit" ).click(function() {
				if($("#patientSurgeryAddForm").valid()) {
				var patientSurgeryId = $("#patientSurgeryId").val();
				if(patientSurgeryId !="") {
					var formAction = "updatePatientSurgery";
				} else {
					var formAction = "addPatientSurgery";
				}

				var wbUrl = $("#wbUrl").val();
				var sendInfo = {
					patientId: $( "#patientId").val(),
					datePerformed: $("#datePerformed").val(),
					surgerydetails: $("#surgerydetails").val(),			
					action: formAction,
					id: patientSurgeryId,
			   };
			   $.ajax({
				   type: "POST",
				   url: wbUrl,
				   dataType: "json",
				   data: sendInfo,
				   success: function (data, status) {
					   if (status =="success") {
						if(formAction =="updatePatientSurgery") {
								$( "#ps1-"+patientSurgeryId).html($("#datePerformed").val());
								$( "#ps2-"+patientSurgeryId).html($("#surgerydetails").val());
						   } else {
								var sur_info = 'tr:last';
								if(historyCount() == 0) {
									sur_info  = 'tbody:last';
									$('#nohisInfo').remove();
								}
								$('#patientSurgery-list ' + sur_info).after(
									'<tr id="ps-'+ data.response +'">' +'<td>'+ data.response +'</td>' +
										'<td id="ps1-'+data.response+'">'+ $("#datePerformed").val() +'</td>'+
										'<td id="ps2-'+data.response+'">' + $("#surgerydetails").val() + '</td>' + 
										'<td><a href="#" class="editPsLnk" data-id="'+ data.response + '" id="'+ data.response +'" data-toggle="modal" data-target="#patientSurgeryForm">EDIT</a> | <a href="javascript:void(0);" class="ptdel" data-id="'+ wbUrl+'#'+data.response+'" title="Are you sure you want to delete the Patient History record?">DELETE</a></td>' +
									'</tr>'
								);
						   }
						   $( "#messagepsbx" ).html('<p style="color:green">' + data.text + '</p>');
						   getPatientSurgery();
						   delPatientSurgery();
						   setTimeout(function(){
							  $( "#messagepsbx" ).html('');
							  $('#patientSurgeryForm').modal('hide');
							}, 1000);
					   } else {
						   //alert("Cannot add to list !");
					   }
				   }
			   });
			   }
			});
			function surgeryCount() {
				return $('#patientSurgery-list >tbody >tr').length;
			}
			function getPatientSurgery() {
				$(".editPsLnk").click(function () {
					var value = $(this).attr("id");
					$("#patientSurgeryId").val(value);
					$( "#messagepsbx" ).html('');
					var wbUrl = $("#wbUrl").val();
					$.ajax({
					   type: "POST",
					   url: wbUrl,
					   dataType: "json",
					   data: {id:value, action: "getPatientSurgeryById"},
					   success: function (data, status) {
						if (status =="success") {
							if(data.response !="") {
								var response = data.response;
								$("#datePerformed").val(response.datePerformed);
								$("#surgerydetails").val(response.surgerydetails);
							}
						}
					   }
				   });
				});
			}
			getPatientSurgery();
			delPatientSurgery();
			function delPatientSurgery(){
			//Patient Surgery Delete
				$(".pSurdel").click(function () {
					var recId = $(this).attr("data-id");
					var res = recId.split("#"); 
					var wbUrl=res[0];
					var psid = res[1];
					$.ajax({ 
					   type: "POST",
					   url: wbUrl,
					   dataType: "json",
					   data: {id:psid, action: "patientSurDel"},
					   success: function (data, status) {
						if (status =="success") {
							if(data.response !="") {
								var response = data.response;
								$('#patientSurgery-list tr#ps-'+psid).remove();
								//$( "#messagepsbx" ).html('<p style="color:green">' + data.text + '</p>');
							}
						}
					   }
				   });
				   $('table#patientSurgery-list tr#ps-'+psid).remove();
					if(surgeryCount() == 0) {
						$( "table#patientSurgery-list" ).after('<p style="color:red;padding:5px;" id="nosurInfo">No Records Found!	</p>');
					}
				});
			}
			
			
			/**** Patient Medication code Start ****/
			$("#patientMedicineFormLnk").click(function () {
				$("#patientMedicineId").val('');
				$("#medicine").val('');
				$("#frequency").val('');
				$("#refills").val('');
				$("#route").val('');
			});
						
			$("#patientMedicineAddForm").validate({
				onfocusout: false,//function(element) {$(element).valid();},
				// Specify the validation rules
				rules: {
					medicine: {required: true},
					frequency: {required: true},
					refills: {required: true},
					route: {required: true},
				}
			});
			$( "#patientMedicineSubmit" ).click(function() {
				if($("#patientMedicineAddForm").valid()) {
				var patientMedicineId = $("#patientMedicineId").val();
				if(patientMedicineId !="") {
					var formAction = "updatePatientMedication";
				} else {
					var formAction = "addPatientMedication";
				}

				var wbUrl = $("#wbUrl").val();
				var sendInfo = {
					patientId: $( "#patientId").val(),
					medicine: $("#medicine").val(),
					frequency: $("#frequency").val(),			
					refills: $("#refills").val(),			
					route: $("#route").val(),			
					action: formAction,
					id: patientMedicineId,
			   };
			   $.ajax({
				   type: "POST",
				   url: wbUrl,
				   dataType: "json",
				   data: sendInfo,
				   success: function (data, status) {
					   if (status =="success") {
						if(formAction =="updatePatientMedication") {
								$( "#pm1-"+patientMedicineId).html($("#medicine").val());
								$( "#pm2-"+patientMedicineId).html($("#frequency").val());
								$( "#pm2-"+patientMedicineId).html($("#refills").val());
								$( "#pm2-"+patientMedicineId).html($("#route").val());
						   } else {
								var med_info = 'tr:last';
								if(medicineCount() == 0) {
									med_info  = 'tbody:last';
									$('#nohisInfo').remove();
								}
								$('#patientMedicine-list ' + med_info).after(
									'<tr id="pm-'+ data.response +'">' +'<td>'+ data.response +'</td>' +
										'<td id="pm1-'+data.response+'">'+ $("#medicine").val() +'</td>'+
										'<td id="pm2-'+data.response+'">' + $("#frequency").val() + '</td>' + 
										'<td id="pm3-'+data.response+'">' + $("#refills").val() + '</td>' + 
										'<td id="pm4-'+data.response+'">' + $("#route").val() + '</td>' + 
										'<td><a href="#" class="editPmLnk" data-id="'+ data.response + '" id="'+ data.response +'" data-toggle="modal" data-target="#patientMedicineForm">EDIT</a> | <a href="javascript:void(0);" class="pMedicinedel" data-id="'+ wbUrl+'#'+data.response+'" title="Are you sure you want to delete the Patient History record?">DELETE</a></td>' +
									'</tr>'
								);
						   }
						   $( "#messagepmbx" ).html('<p style="color:green">' + data.text + '</p>');
						   getPatientMedicine();
						   delPatientMedicine();
						   setTimeout(function(){
							  $( "#messagepsbx" ).html('');
							  $('#patientMedicineForm').modal('hide');
							}, 1000);
					   } else {
						   //alert("Cannot add to list !");
					   }
				   }
			   });
			   }
			});
			function medicineCount() {
				return $('#patientMedicine-list >tbody >tr').length;
			}
			function getPatientMedicine() {
				$(".editPmLnk").click(function () {
					var value = $(this).attr("id");
					$("#patientMedicineId").val(value);
					$( "#messagepmbx" ).html('');
					var wbUrl = $("#wbUrl").val();
					$.ajax({
					   type: "POST",
					   url: wbUrl,
					   dataType: "json",
					   data: {id:value, action: "getPatientMedicationById"},
					   success: function (data, status) {
						if (status =="success") {
							if(data.response !="") {
								var response = data.response;
								$("#medicine").val(response.medicine);
								$("#frequency").val(response.frequency);
								$("#refills").val(response.refills);
								$("#route").val(response.route);
							}
						}
					   }
				   });
				});
			}
			getPatientMedicine();
			delPatientMedicine();
			function delPatientMedicine(){
			//Patient Surgery Delete
				$(".pMedicinedel").click(function () {
					var recId = $(this).attr("data-id");
					var res = recId.split("#"); 
					var wbUrl=res[0];
					var psid = res[1];
					$.ajax({ 
					   type: "POST",
					   url: wbUrl,
					   dataType: "json",
					   data: {id:psid, action: "delPatientMedication"},
					   success: function (data, status) {
						if (status =="success") {
							if(data.response !="") {
								var response = data.response;
								$('#patientMedicine-list tr#ps-'+psid).remove();
								//$( "#messagepsbx" ).html('<p style="color:green">' + data.text + '</p>');
							}
						}
					   }
				   });
				   $('table#patientMedicine-list tr#pm-'+psid).remove();
					if(surgeryCount() == 0) {
						$( "table#patientMedicine-list" ).after('<p style="color:red;padding:5px;" id="nomedInfo">No Records Found!	</p>');
					}
				});
			}
			//Patient List Delete
			$(".pLidel").click(function () {
				var recId = $(this).attr("data-id");
				var res = recId.split("#"); 
				var wbUrl=res[0];
				var psid = res[1];
				$.ajax({
				   type: "POST",
				   url: wbUrl,
				   dataType: "json",
				   data: {id:psid, action: "patientDelete"},
				   success: function (data, status) {
					if (status =="success") {
						if(data.response !="") {
							var response = data.response;
							$('#patients-list tr#pei-'+psid).remove();
							//$( "#messagepsbx" ).html('<p style="color:green">' + data.text + '</p>');
						}
					}
				   }
			   });
			});
			
			//Patient Circle Delete
			$(".pCircledel").click(function () {
				var recId = $(this).attr("data-id");
				var res = recId.split("#"); 
				var wbUrl=res[0];
				var psid = res[1];
				$.ajax({ 
				   type: "POST",
				   url: wbUrl,
				   dataType: "json",
				   data: {id:psid, action: "patientCircleDel"},
				   success: function (data, status) {
					if (status =="success") {
						if(data.response !="") {
							var response = data.response;
							$('#patientCircle-list tr#pc-'+psid).remove();
							//$( "#messagepsbx" ).html('<p style="color:green">' + data.text + '</p>');
						}
					}
				   }
			   });
			});
			function circleCount() {
				return $('#patientCircle-list >tbody >tr').length;
			}
			$( "#patientCircleSubmit" ).click(function() {
				if($("#patientCircleAddForm").valid()) { 
				var patientCircleId = $("#patientCircleId").val();
				if(patientCircleId !="") {
					var formAction = "updatePatientCircle";
				} else {
					var formAction = "addPatientCircle";
					
				}
				var wbUrl = $("#wbUrl").val();
				var sendInfo = {
					id:patientCircleId,
					patientId: $("#patientId").val(),
					dob: $("#dob").val(),
					email: $("#email").val(),
					firstname: $("#firstname").val(),
					gender: $('#gender').val(),
					lastname: $("#lastname").val(),
					phone: $("#phone").val(),
					title: $("#title").val(),
					relation: $("#relation").val(),
					user: $("#user").val(),
					action: formAction
				};
			   $.ajax({
				   type: "POST",
				   url: wbUrl,
				   dataType: "json",
				   data: sendInfo,
				   success: function (data, status) {
					   if (status =="success") {
						if(formAction =="updatePatientCircle") {
								//$( "#ph1-"+patientHistoryId).html($("#historyDetails").val());
								//$( "#ph2-"+patientHistoryId).html($("#visitedDate").val());
								$( "#pc2-"+patientCircleId).html($("#title").val() + $("#firstname").val() + $("#lastname").val());
								/*if ($('input[name=optionsGender]:checked').val() == 'M') { oValue = 'Male'; } else if($('input[name=optionsGender]:checked').val() == 'F') { oValue = 'Female'; } else if($('input[name=optionsGender]:checked').val() == 'T') {oValue = 'Transgender';}*/
								
								$( "#pc3-"+patientCircleId).html($("#gender").val());
								$( "#pc4-"+patientCircleId).html($("#dob").val());
								$( "#pc5-"+patientCircleId).html($("#email").val());
								$( "#pc6-"+patientCircleId).html($("#phone").val());
						   } else {
								var cir_info = 'tr:last';
								if(circleCount() == 0) {
									cir_info  = 'tbody:last';
									$('#nocirInfo').remove();
								}
								/*if ($('input[name=optionsGender]:checked').val() == 'M') { oValue = 'Male'; } else if($('input[name=optionsGender]:checked').val() == 'F') { oValue = 'Female'; } else if($('input[name=optionsGender]:checked').val() == 'T') {oValue = 'Transgender';}*/
								//$( "#pc3-"+patientCircleId).html($("#title").val());
								$('#patientCircle-list ' + cir_info).after(
									'<tr id="pc-'+ data.response +'">' +'<td>'+ data.response +'</td>' +
										'<td id="pc2-'+data.response+'">' +  $("#firstname").val() + $("#lastname").val() +'</td>' + 
										'<td id="pc3-'+data.response+'">' + $("#gender").val() + '</td>' + 
										'<td id="pc4-'+data.response+'">' + $("#dob").val() + '</td>' + 
										'<td id="pc5-'+data.response+'">' + $("#email").val() + '</td>' + 
										'<td id="pc6-'+data.response+'">' + $("#phone").val() + '</td>' + 
										'<td><a href="#" class="editPcLnk" data-id="'+ data.response + '" id="'+ data.response +'" data-toggle="modal" data-target="#patientCircleForm">EDIT</a> | <a href="javascript:void(0);" class="pCircledel" data-id="'+ wbUrl+'#'+data.response+'" title="Are you sure you want to delete the Patient Circle record?">DELETE</a></td>' +
									'</tr>'
								);
						   }
						   $( "#messagehsbx" ).html('<p style="color:green">' + data.text + '</p>');
						   getPatientCircle();
						   delPatientCircle();
						   setTimeout(function(){
							  $( "#messagehsbx" ).html('');
							  $('#patientCircleForm').modal('hide');
							}, 1000);
					   } else {
						   //alert("Cannot add to list !");
					   }
				   }
			   });
			   }
			});
			function  getPatientCircle() {
				$(".editPcLnk").click(function () {
					var value = $(this).attr("id");
					$("#patientCircleId").val(value);
					$( "#messagehsbx" ).html('');
					var wbUrl = $("#wbUrl").val();
					$.ajax({
					   type: "GET",
					   url: wbUrl,
					   dataType: "json",
					   data: {id:value, action: "getPatientCircleById"},
					   success: function (data, status) {
						if (status =="success") {
							if(data.response !="") {
								var response = data.response;
								$("#user").val(response.user);
								$("#dob").val(response.dob);
								$("#email").val(response.email)
								$("#firstname").val(response.firstname)
								$("#gender").val(response.gender)
								$("#lastname").val(response.lastname)
								$("#phone").val(response.phone)
								$("#title").val(response.title)
								$("#relation").val(response.relation)
								
							}
						}
					   }
				   });
				});
			}
			getPatientCircle();
			delPatientCircle();
			function delPatientCircle() {
				//Patient history Delete
				$(".pCircledel").click(function () {
					var recId = $(this).attr("data-id");
					var res = recId.split("#"); 
					var wbUrl=res[0];
					var psid = res[1];
					$.ajax({ 
					   type: "POST",
					   url: wbUrl,
					   dataType: "json",
					   data: {id:psid, action: "patientCircleDel"},
					   success: function (data, status) {
						if (status =="success") {
							if(data.response !="") {
								var response = data.response;
								$('#patientCircle-list tr#pc-'+psid).remove();
								//$( "#messagepsbx" ).html('<p style="color:green">' + data.text + '</p>');
							}
						}
					   }
				   });
				   $('table#patientCircle-list tr#pc-'+psid).remove();
					if(circleCount() == 0) {
						$( "table#patientCircle-list" ).after('<p style="color:red;padding:5px;" id="nocirInfo">No Records Found!	</p>');
					}
				});
			}
			$('#userList').dataTable({});
    });