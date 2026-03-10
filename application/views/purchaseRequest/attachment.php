<div class="modal fade" id="prAttachmentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Attachment</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="<?php echo base_url("PurchaseRequest/saveAttachment"); ?>" method="post" class="form-horizontal" id="formPRAdd" enctype="multipart/form-data">
                <div class="modal-body" style="max-height:70vh; overflow-y:auto;">
                    <input type="hidden" name="pr_id" id="pr_id">

                    <div id="attachmentContainer">

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

        // Load attachments when modal opens
        $('#prAttachmentModal').on('shown.bs.modal', function() {
            var pr_id = $('#pr_id').val();
            var $container = $('#attachmentContainer');
            $container.empty();

            if (!pr_id) {
                console.log('PR ID missing');
                return;
            }

            $.ajax({
                url: "<?php echo base_url('PurchaseRequest/getPRAttachments'); ?>",
                type: "POST",
                data: {
                    pr_id: pr_id
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
                            <tbody id="tblAttachmentBody"></tbody>
                        </table>
                    `;
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
                                    <td class="text-center">
                                        <button type="button"
                                                class="btn btn-success btnUpload"
                                                data-attachment="${a.attachment_id}">
                                            Upload
                                        </button>
                                    </td>
                                </tr>
                            `;
                            $('#tblAttachmentBody').append(row);
                        });

                    } else {
                        $container.append('<p class="text-center">No attachments required for this PR.</p>');
                    }
                },
                error: function(err) {
                    console.error(err);
                }
            });
        });

        // Upload button per row
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
                        alert('Upload successful: ' + data.message);

                        // Update uploaded filename in the table without reloading
                        var rowTd = $('.file-input[data-attachment="' + attachment_id + '"]')
                            .closest('tr').find('td:nth-child(2)');

                        // Replace underscores with spaces
                        var originalName = (data.original_file_name || fileInput.files[0].name).replace(/_/g, ' ');
                        var fileLink = `<a href="<?php echo base_url('assets/uploads/pr_attachments/'); ?>${data.file_name}" 
                                        target="_blank" class="text-success">
                                        ${data.original_file_name.replace(/_/g, ' ')}
                                        </a>`;
                        rowTd.html(fileLink);
                        rowTd.html('<span class="text-success">' + originalName + '</span>');

                        // Clear the file input
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

<!-- <script>
    $(document).ready(function() {

        $('#prAttachmentModal').on('shown.bs.modal', function() {

            var pr_id = $('#pr_id').val();
            var $container = $('#attachmentContainer');

            $container.empty();

            if (!pr_id) {
                console.log('PR ID missing');
                return;
            }

            $.ajax({
                url: "<?php echo base_url('PurchaseRequest/getPRAttachments'); ?>",
                type: "POST",
                data: {
                    pr_id: pr_id
                },
                dataType: "json",
                success: function(res) {

                    if (res.length > 0) {

                        var table = `
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50%">Attachment</th>
                                    <th width="15%">Required</th>
                                    <th width="35%">Upload File</th>
                                </tr>
                            </thead>
                            <tbody id="tblAttachments">
                            </tbody>
                        </table>
                    `;

                        $container.append(table);

                        res.forEach(function(a) {

                            var requiredBadge = a.required == 1 ?
                                '<span class="badge badge-danger">Required</span>' :
                                '<span class="badge badge-secondary">Optional</span>';

                            var row = `
                            <tr>
                                <td>${a.attachment_name}</td>
                                <td>${requiredBadge}</td>
                                <td>
                                    <input type="file"
                                           class="form-control-file"
                                           name="attachments[${a.attachment_id}]"
                                           accept=".pdf,.doc,.docx,.jpg,.png">
                                </td>
                            </tr>
                        `;

                            $('#tblAttachments').append(row);

                        });

                    } else {

                        $container.append('<p class="text-center">No attachments required for this PR.</p>');

                    }

                },
                error: function(err) {
                    console.error(err);
                }
            });

        });

    });
</script> -->