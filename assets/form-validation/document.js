$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#frmCreateDoc').formValidation({
    	framework: 'bootstrap',
        excluded: ':disabled',
        icon: {
            feedback: 'form-control-feedback '
        },

        fields: {

            subject: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    },
                    regexp: {
                        // regexp: /^[a-zA-Z._/0-9:, -]+$/,
                        regexp: /^[a-zA-Z0-9()_;:,./ -]*$/,
                        message: 'The Subject can only consist of both letters and numbers including selected special characters.'
                    },
                }
            },

            document_date: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    },
                    date: {
                        format: 'DD/MM/YYYY',
                        message: 'The value is not a valid date.'
                    },
                    callback: {
                        message: 'The document date cannot be in the future.',
                        callback: function(value, validator, $field) {
                            const selectedDate = new Date(value);
                            const currentDate = new Date();
                            currentDate.setHours(0, 0, 0, 0); 
                            selectedDate.setHours(0, 0, 0, 0);
                            return selectedDate <= currentDate;
                        }
                    }
                }
            },

            date_received: {
                row: '.col-lg-8',
                validators: {
                    date: {
                        format: 'DD/MM/YYYY',
                        message: 'The value is not a valid date.'
                    },
                    callback: {
                        message: 'The received date cannot be in the future.',
                        callback: function(value, validator, $field) {
                            const selectedDate = new Date(value);
                            const currentDate = new Date();
                            currentDate.setHours(0, 0, 0, 0); // Set the time to 00:00:00 to compare only the date           
                            selectedDate.setHours(0, 0, 0, 0);
                            return selectedDate <= currentDate;
                        }
                    }
                }
            },

            receiver_office: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            },

            // receiver_name: {
            //     row: '.col-lg-8',
            //     validators: {
            //         notEmpty: {
            //             message: 'This field is required.'
            //         },
            //         // regexp: {
            //         //     regexp: /^[a-zA-Z._/ -]+$/,
            //         //     message: 'The Receiver name can only consist of characters including hyphen (-) and dot(.).'
            //         // },
            //     }
            // },
            addressee: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    },
                    regexp: {
                        regexp: /^[a-zA-ZñÑ._/ -]+$/,
                        message: 'The Addressee can only consist of characters including hyphen (-) and dot(.).'
                    },
                }
            },
            // external_name: {
            //     row: '.col-lg-8',
            //     validators: {
            //         notEmpty: {
            //             message: 'This field is required.'
            //         },
            //         regexp: {
            //             regexp: /^[a-zA-Z._/ -]+$/,
            //             message: 'The External Name can only consist of characters including hyphen (-) and dot(.).'
            //         },
            //     }
            // },
            document_category: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            },
            
            sender_office: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            },

            sender_name: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    },
                    regexp: {
                        regexp: /^[a-zA-ZñÑ._/ -]+$/,
                        message: 'The Sender Name can only consist of characters including hyphen (-) and dot(.).'
                    },
                }
            },

            document_signatory: {
                row: '.col-lg-8',
                validators: {
                    regexp: {
                        regexp: /^[a-zA-Z._/ -]+$/,
                        message: 'The Document Signatory can only consist of characters including hyphen (-) and dot(.).'
                    },
                }
            },

            document_duration: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            },    

            remarks: {
                row: '.col-lg-8',
                validators: {
                    // notEmpty: {
                    //     message: 'This field is required.'
                    // },
                    regexp: {
                        // regexp: /^[a-zA-Z._/0-9:, -]+$/,
                        regexp: /^[a-zA-Z0-9()_;:,./ -]*$/,
                        message: 'The Remarks can only consist of both letters and numbers including selected special characters.'
                    },
                    stringLength: {
                        max: 255,
                        message: 'Your remarks must contain 255 characters only.'
                    },
                }
            },

            'uploadz[]': {
                row: 'td',
                validators: {
                    file: {
                        extension: 'pdf',
                        type: 'application/pdf',
                        maxSize: 100000000, //100000000 bytes = 100 mb 
                        message: 'Please choose pdf file only with a max size of 100 MB.'
                    }
                },
            }, 

        }

        
    });

    $('#frmEditDoc').formValidation({
        framework: 'bootstrap',
        excluded: ':disabled',
        icon: {
            feedback: 'form-control-feedback '
        },

        fields: {

            subject_edit: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    },
                    regexp: {
                        // regexp: /^[a-zA-Z._/0-9:, -]+$/,
                        regexp: /^[a-zA-Z0-9()_;:,./ -]*$/,
                        message: 'The Subject can only consist of both letters and numbers including selected special characters.'
                    },
                }
            },

            document_date_edit: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    },
                    date: {
                        format: 'DD/MM/YYYY',
                        message: 'The value is not a valid date.'
                    },                    
                    callback: {
                        message: 'The document date cannot be in the future.',
                        callback: function(value, validator, $field) {
                            const selectedDate = new Date(value);
                            const currentDate = new Date();
                            currentDate.setHours(0, 0, 0, 0); 
                            selectedDate.setHours(0, 0, 0, 0);
                            return selectedDate <= currentDate;
                        }
                    }
                }
            },

            date_received_edit: {
                row: '.col-lg-8',
                validators: {
                    date: {
                        format: 'DD/MM/YYYY',
                        message: 'The value is not a valid date.'
                    },
                    callback: {
                        message: 'The received date cannot be in the future.',
                        callback: function(value, validator, $field) {
                            const selectedDate = new Date(value);
                            const currentDate = new Date();
                            currentDate.setHours(0, 0, 0, 0); 
                            selectedDate.setHours(0, 0, 0, 0);
                            return selectedDate <= currentDate;
                        }
                    }
                }
            },

            receiver_office_edit: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            },
            addressee_edit: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    },
                    regexp: {
                        regexp: /^[a-zA-ZñÑ._/ -]+$/,
                        message: 'The Addressee can only consist of characters including hyphen (-) and dot(.).'
                    },
                }
            },
            // receiver_name_edit: {
            //     row: '.col-lg-8',
            //     validators: {
            //         notEmpty: {
            //             message: 'This field is required.'
            //         },
            //         // regexp: {
            //         //     regexp: /^[a-zA-Z._/ -]+$/,
            //         //     message: 'The Receiver name can only consist of characters including hyphen (-) and dot(.).'
            //         // },
            //     }
            // },
            // external_name_edit: {
            //     row: '.col-lg-8',
            //     validators: {
            //         notEmpty: {
            //             message: 'This field is required.'
            //         },
            //         regexp: {
            //             regexp: /^[a-zA-Z._/ -]+$/,
            //             message: 'The External Name can only consist of characters including hyphen (-) and dot(.).'
            //         },
            //     }
            // },
            document_category_edit: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            },
            
            sender_office_edit: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            },

            sender_name_edit: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    },
                    regexp: {
                        regexp: /^[a-zA-ZñÑ._/ -]+$/,
                        message: 'The Sender Name can only consist of characters including hyphen (-) and dot(.).'
                    },
                }
            },

            document_signatory_edit: {
                row: '.col-lg-8',
                validators: {
                    regexp: {
                        regexp: /^[a-zA-Z._/ -]+$/,
                        message: 'The Document Signatory can only consist of characters including hyphen (-) and dot(.).'
                    },
                }
            },

            document_duration_edit: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            },

            remarks_edit: {
                row: '.col-lg-8',
                validators: {
                    // notEmpty: {
                    //     message: 'This field is required.'
                    // },
                    regexp: {
                        // regexp: /^[a-zA-Z._/0-9:, -]+$/,
                        regexp: /^[a-zA-Z0-9()_;:,./ -]*$/,
                        message: 'The Remarks can only consist of both letters and numbers including selected special characters.'
                    },
                    stringLength: {
                        max: 255,
                        message: 'Your remarks must contain 255 characters only.'
                    },
                }
            },

            'uploadz[]': {
                row: 'td',
                validators: {                    
                    file: {
                        extension: 'pdf',
                        type: 'application/pdf',
                        maxSize: 100000000, //100000000 bytes = 100 mb 
                        message: 'Please choose pdf file only with a max size of 100 MB.'
                    }
                },
            },

        }

        
    });


    $('#frmTrackForward').formValidation({
        framework: 'bootstrap',
        excluded: ':disabled',
        icon: {
            feedback: 'form-control-feedback '
        },

        fields: {

            // 'receiver_office_tracker[]': {
            //     row: 'td',
            //     validators: {
            //         callback: {
            //           message: 'This field is required',
            //           callback: function(value, validator, $field) {

            //             var name = $('[name="assigned_to[]"]').val();

            //             return (name == '' ) ? (value!=='') : true;
                     
            //           }
            //         },
            //     }
            // },


            'assigned_to[]': {
                row: 'td',
                validators: {
                    callback: {
                        message: 'Please choose only ONE (1) either Receiver Office or Employee.',
                        callback: function(value, validator, $field) {
                            var group_name = $field.closest('tr').find('[name="group_name[]"]').val();
                            return (group_name !== '') ? (value === '') : (value !== '');
                        }
                    }
                }
            },
            'group_name[]': {
                row: 'td',
                validators: {
                    callback: {
                        message: 'Please choose only ONE (1) either Receiver Office or Employee.',
                        callback: function(value, validator, $field) {
                            var assigned_to = $field.closest('tr').find('[name="assigned_to[]"]').val();
                            return (assigned_to !== '') ? (value === '') : (value !== '');
                        }
                    }
                }
            },
            remarks_tracker: {
                row: '.col-sm-12',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    },
                    // regexp: {
                    //     regexp: /^[a-zA-Z._/ -]+$/,
                    //     message: 'The remarks can only consist of characters including hyphen (-) and dot(.).'
                    // },
                }
            },



        }

        
    }).on('success.form.fv', function(e) {
        $('[name="group_name[]"]').attr('disabled',false);
    });

   // Add change event listeners to initial fields
    $('select[name="group_name[]"]').on('change', function() {
        $('#frmTrackForward').formValidation('revalidateField', 'group_name[]');
        $('#frmTrackForward').formValidation('revalidateField', 'assigned_to[]');
    });

    $('select[name="assigned_to[]"]').on('change', function() {
        $('#frmTrackForward').formValidation('revalidateField', 'group_name[]');
        $('#frmTrackForward').formValidation('revalidateField', 'assigned_to[]');
    });


    $('#frmComment').formValidation({
        framework: 'bootstrap',
        excluded: ':disabled',
        icon: {
            feedback: 'form-control-feedback '
        },

        fields: {

            comments: {
                row: '.col-sm-9',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    },
                }
            },

        }

        
    });

//     $('#frmTransmittal').formValidation({
//         framework: 'bootstrap',
//         excluded: ':disabled',
//         icon: {
//             feedback: 'form-control-feedback '
//         },

//         fields: {

//             agency: {
//                 row: '.col-lg-8',
//                 validators: {
//                     notEmpty: {
//                         message: 'This field is required.'
//                     },
//                     regexp: {
//                         regexp: /^[a-zA-Z._/ -]+$/,
//                         message: 'The Name of Agency can only consist of characters including hyphen (-) and dot(.).'
//                     },
//                 }
//             },

//             transmittal_date: {
//                 row: '.col-lg-8',
//                 validators: {
//                     notEmpty: {
//                         message: 'This field is required.'
//                     },                    
//                     date: {
//                         format: 'DD/MM/YYYY',
//                         message: 'The value is not a valid date.'
//                     }
//                 }
//             },
// /*
//             transmittal_type: {
//                 row: '.col-lg-8',
//                 validators: {
//                     notEmpty: {
//                         message: 'This field is required.'
//                     }
//                 }
//             },*/



//         }

        
//     });



    $('#frmTrackForwardEdit').formValidation({
        framework: 'bootstrap',
        excluded: ':disabled',
        icon: {
            feedback: 'form-control-feedback '
        },

        fields: {

            receiver_office_edit_tracker: {
                row: 'td',
                validators: {
                    callback: {
                      message: 'Please choose only ONE (1) either Receiver Office or Employee.',
                      callback: function(value, validator, $field) {

                        var assigned_to = $('#assigned_to_edit').val();

                        return (assigned_to != '' ) ? (value==='') : (value!=='') ;

                      }
                    }, 
                }
            },
            assigned_to_edit: {
                row: 'td',
                validators: {
                    callback: {
                      message: 'Please choose only ONE (1) either Receiver Office or Employee.',
                      callback: function(value, validator, $field) {

                        var group_name = $('#receiver_office_edit_tracker').val();

                        return (group_name != '' ) ? (value==='') : (value!=='');


                      }
                    }, 
                }
            },
            remarks_edit_tracker: {
                row: '.col-md-12',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    },
                }
            },

        }

        
    }).on('success.form.fv', function(e) {
        $('#receiver_office_edit_tracker').attr('disabled',false);
    });


});


 $('#frmDuplicateDoc').formValidation({
        framework: 'bootstrap',
        excluded: ':disabled',
        icon: {
            feedback: 'form-control-feedback '
        },

        fields: {

            subject_dup: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    },
                    regexp: {
                        // regexp: /^[a-zA-Z._/0-9:, -]+$/,
                        regexp: /^[a-zA-Z0-9()_;:,./ -]*$/,
                        message: 'The Subject can only consist of both letters and numbers including selected special characters.'
                    },
                }
            },

            document_date_dup: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    },
                    date: {
                        format: 'DD/MM/YYYY',
                        message: 'The value is not a valid date.'
                    }
                }
            },

            date_received_dup: {
                row: '.col-lg-8',
                validators: {
                    date: {
                        format: 'DD/MM/YYYY',
                        message: 'The value is not a valid date.'
                    }
                }
            },

            receiver_office_dup: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            },
            addressee_dup: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    },
                    regexp: {
                        regexp: /^[a-zA-ZñÑ._/ -]+$/,
                        message: 'The Addressee can only consist of characters including hyphen (-) and dot(.).'
                    },
                }
            },
            // receiver_name_dup: {
            //     row: '.col-lg-8',
            //     validators: {
            //         notEmpty: {
            //             message: 'This field is required.'
            //         },
            //         // regexp: {
            //         //     regexp: /^[a-zA-Z._/ -]+$/,
            //         //     message: 'The Receiver name can only consist of characters including hyphen (-) and dot(.).'
            //         // },
            //     }
            // },
            // external_name_dup: {
            //     row: '.col-lg-8',
            //     validators: {
            //         notEmpty: {
            //             message: 'This field is required.'
            //         },
            //         regexp: {
            //             regexp: /^[a-zA-Z._/ -]+$/,
            //             message: 'The External Name can only consist of characters including hyphen (-) and dot(.).'
            //         },
            //     }
            // },
            document_category_dup: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            },
            
            sender_office_dup: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            },

            sender_name_dup: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    },
                    regexp: {
                        regexp: /^[a-zA-ZñÑ._/ -]+$/,
                        message: 'The Sender Name can only consist of characters including hyphen (-) and dot(.).'
                    },
                }
            },

            document_signatory_dup: {
                row: '.col-lg-8',
                validators: {
                    regexp: {
                        regexp: /^[a-zA-Z._/ -]+$/,
                        message: 'The Document Signatory can only consist of characters including hyphen (-) and dot(.).'
                    },
                }
            },

            document_duration_dup: {
                row: '.col-lg-8',
                validators: {
                    notEmpty: {
                        message: 'This field is required.'
                    }
                }
            },

            remarks_dup: {
                row: '.col-lg-8',
                validators: {
                    // notEmpty: {
                    //     message: 'This field is required.'
                    // },
                    regexp: {
                        // regexp: /^[a-zA-Z._/0-9:, -]+$/,
                        regexp: /^[a-zA-Z0-9()_;:,./ -]*$/,
                        message: 'The Remarks can only consist of both letters and numbers including selected special characters.'
                    },
                    stringLength: {
                        max: 255,
                        message: 'Your remarks must contain 255 characters only.'
                    },
                }
            },

            'uploadz[]': {
                row: 'td',
                validators: {                    
                    file: {
                        extension: 'pdf',
                        type: 'application/pdf',
                        maxSize: 100000000, //100000000 bytes = 100 mb 
                        message: 'Please choose pdf file only with a max size of 100 MB.'
                    }
                },
            },

        }

        
    });
