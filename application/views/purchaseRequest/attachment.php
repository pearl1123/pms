<style>
    #overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.7);
        z-index: 9999;
        display: none;
        text-align: center;
        padding-top: 20%;
    }
</style>
<div class="modal fade" id="prAttachmentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Attachment</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body" style="max-height:70vh; overflow-y:auto;">
                <input type="hidden" id="pr_id">

                <div id="attachmentContainer"></div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div id="overlay">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Loading...</span>
    </div>
    <p>Loading...</p>
</div>

<script>
    $(document).ready(function() {
        $('#tblPR').on('click', '.btnAttachment', function() {
            var pr_id = $(this).data('prid');
            $('#pr_id').val(pr_id);
            var $container = $('#attachmentContainer');
            $container.empty();

            // Show overlay loader inside modal
            $('#overlay').show();

            $.ajax({
                url: "<?php echo base_url('PurchaseRequest/getPRAttachments'); ?>",
                type: "POST",
                data: {
                    pr_id: pr_id
                },
                dataType: "json",
                success: function(res) {
                    $container.empty();

                    if (res.length > 0) {
                        var table = `
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th width="25%">Attachment Name</th>
                                <th width="20%">Uploaded File</th>
                                <th width="20%">Select File</th>
                                <th width="20%">Remarks</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tblAttachmentBody"></tbody>
                    </table>`;
                        $container.append(table);

                        res.forEach(function(a) {
                            var uploadedFile = (a.file_name && a.original_file_name) ?
                                `<a href="<?php echo base_url('assets/uploads/pr_attachments/'); ?>${a.file_name}" 
                                target="_blank" class="text-success">
                                ${a.original_file_name.replace(/_/g, ' ')}
                            </a>` :
                                `<span class="text-muted">No file uploaded</span>`;

                            var row = `
                            <tr>
                                <td>${a.attachment_name}</td>
                                <td class="text-center">${uploadedFile}</td>
                                <td>
                                    <input type="file"
                                        class="form-control file-input"
                                        data-attachment="${a.attachment_id}"
                                        accept=".pdf,.doc,.docx,.jpg,.png">
                                </td>
                                <td>
                                    <input type="text"
                                        class="form-control remarks-input"
                                        data-attachment="${a.attachment_id}"
                                        value="${a.remarks ?? ''}" readonly>
                                </td>
                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-success btnUpload"
                                            data-attachment="${a.attachment_id}">
                                        Upload
                                    </button>
                                </td>
                            </tr>`;
                            $('#tblAttachmentBody').append(row);
                        });

                    } else {
                        $container.append('<p class="text-center">No attachments required for this PR.</p>');
                    }

                    $('#prAttachmentModal').modal('show');
                    $('#overlay').hide();
                },
                error: function(err) {
                    console.error(err);
                    $('#overlay').hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load attachments.'
                    });
                }
            });

        });

        // UPLOAD button — handles file only
        $(document).on('click', '.btnUpload', function() {
            var attachment_id = $(this).data('attachment');
            var pr_id = $('#pr_id').val();
            var fileInput = $('.file-input[data-attachment="' + attachment_id + '"]')[0];

            if (!fileInput.files.length) {
                alert('Please select a file first.');
                return;
            }

            var formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('pr_id', pr_id);
            formData.append('attachment_id', attachment_id);

            $.ajax({
                url: "<?php echo base_url('PurchaseRequest/uploadAttachment'); ?>",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {

                    var data = typeof res === 'string' ? JSON.parse(res) : res;

                    if (data.success) {

                        Swal.fire({
                            type: 'success',
                            title: 'Upload Successful',
                            text: data.message,
                            confirmButtonColor: '#3085d6'
                        });

                        var row = $('.file-input[data-attachment="' + attachment_id + '"]').closest('tr');

                        var originalName = (data.original_file_name || fileInput.files[0].name).replace(/_/g, ' ');

                        var fileLink = `<a href="<?php echo base_url('assets/uploads/pr_attachments/'); ?>${data.file_name}" 
                            target="_blank" class="text-success">${originalName}</a>`;

                        row.find('td:nth-child(2)').html(fileLink);
                        fileInput.value = '';

                    } else {

                        Swal.fire({
                            type: 'error',
                            title: 'Upload Failed',
                            text: data.message
                        });

                    }
                },
                error: function(err) {

                    console.error(err);

                    Swal.fire({
                        type: 'error',
                        title: 'Upload Failed',
                        text: 'See console for details.'
                    });

                }
            });
        });

        $(document).on('click', '.btnAttachment', function() {
            var pr_id = $(this).data('prid');
            $('#pr_id').val(pr_id);
            $('#prAttachmentModal').modal('show');
        });

    });
</script>