$(document).ready(function () {
    $('#formSelector').change(function () {
      const formId = $(this).val();
      if (!formId) return;
  
      $.post(base_url + 'ClinicalNotes/get_form_fields', { form_id: formId }, function (res) {
        const { fields } = res;
        let html = '';
        fields.forEach(field => {
          html += `
            <div class="form-group">
              <label>${field.field_name}</label>
              <input type="${field.field_type === 'datetime' ? 'datetime-local' : (field.field_type === 'number' ? 'number' : 'text')}"
                     name="${field.field_name}"
                     maxlength="${field.field_length || ''}" class="form-control" required>
            </div>`;
        });
        $('#dynamicFields').html(html);
      }, 'json');
    });
  
    $('#clinicalNotesForm').submit(function (e) {
      e.preventDefault();
      const formId = $('#formSelector').val();
      const patientId = $('input[name="patient_id"]').val();
  
      const fieldData = {};
      $('#dynamicFields').find('input').each(function () {
        const name = $(this).attr('name');
        const value = $(this).val();
        fieldData[name] = value;
      });
  
      $.post(base_url + 'ClinicalNotes/save_patient_note', {
        form_id: formId,
        patient_id: patientId,
        field_data: JSON.stringify(fieldData),
        csrf_name: csrf_hash
      }, function (res) {
        if (res.success) {
          $('#saveStatus').html('<div class="alert alert-success">Note saved successfully.</div>');
          setTimeout(() => location.reload(), 3000);
        } else {
          $('#saveStatus').html('<div class="alert alert-danger">Failed to save note.</div>');
        }
      }, 'json');
    });
  });
  