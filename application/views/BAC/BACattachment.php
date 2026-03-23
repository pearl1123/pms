<div class="modal fade" id="bacAttachmentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Attachment</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("BAC/saveAttachment"); ?>" method="post" class="form-horizontal" id="formBACAdd" enctype="multipart/form-data">
                <div class="modal-body" style="max-height:70vh; overflow-y:auto;">
                    <input type="hidden" name="bac_id" id="bac_id">

                    <div id="bacAttachmentContainer">

                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        $('#bacAttachmentModal').on('shown.bs.modal', function() {
            var bac_id = $('#bac_id').val();
            var $container = $('#bacAttachmentContainer');
            $container.empty();

            if (!bac_id) {
                console.log('BAC ID missing');
                return;
            }

            $.ajax({
                url: "<?php echo base_url('BAC/getBACAttachments'); ?>",
                type: "POST",
                data: {
                    bac_id: bac_id
                },
                dataType: "json",
                success: function(res) {
                    if (res.length > 0) {
                        var table = `
                        <table class="table table-bordered table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="35%">Attachment Name</th>
                                    <th width="25%">Uploaded File</th>
                                    <th width="25%">Select File</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tblBACAttachmentBody"></tbody>
                        </table>
                    `;
                        $container.append(table);

                        res.forEach(function(a) {
                            var uploadedFile = (a.file_name && a.original_file_name) ?
                                `<a href="<?php echo base_url('assets/uploads/bac_attachments/'); ?>${a.file_name}" 
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
                                            class="form-control bac-file-input"
                                            data-attachment="${a.attachment_id}"
                                            accept=".pdf,.doc,.docx,.jpg,.png">
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                                class="btn btn-success btnBACUpload"
                                                data-attachment="${a.attachment_id}">
                                            Upload
                                        </button>
                                    </td>
                                </tr>
                            `;
                            $('#tblBACAttachmentBody').append(row);
                        });

                    } else {
                        $container.append('<p class="text-center">No attachments required for this BAC.</p>');
                    }
                },
                error: function(err) {
                    console.error(err);
                }
            });
        });

        $(document).on('click', '.btnBACUpload', function() {

            var attachment_id = $(this).data('attachment');
            var bac_id = $('#bac_id').val();
            var fileInput = $('.bac-file-input[data-attachment="' + attachment_id + '"]')[0];

            if (!fileInput.files.length) {
                alert('Please select a file first.');
                return;
            }

            var formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('bac_id', bac_id);
            formData.append('attachment_id', attachment_id);

            $.ajax({
                url: "<?php echo base_url('BAC/uploadAttachment'); ?>",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    var data = typeof res === 'string' ? JSON.parse(res) : res;

                    if (data.success) {
                        alert('Upload successful: ' + data.message);

                        var rowTd = $('.bac-file-input[data-attachment="' + attachment_id + '"]')
                            .closest('tr').find('td:nth-child(2)');

                        var originalName = (data.original_file_name || fileInput.files[0].name).replace(/_/g, ' ');
                        var fileLink = `<a href="<?php echo base_url('assets/uploads/bac_attachments/'); ?>${data.file_name}" 
                                        target="_blank" class="text-success">
                                        ${data.original_file_name.replace(/_/g, ' ')}
                                        </a>`;
                        rowTd.html(fileLink);
                        rowTd.html('<span class="text-success">' + originalName + '</span>');

                        fileInput.value = '';
                    } else {
                        alert('Upload failed: ' + data.message);
                    }
                },
                error: function(err) {
                    console.error(err);
                    alert('Upload failed: See console for details.');
                }
            });

        });

    });
</script>