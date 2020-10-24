window.displayErrorToast = function(errorMap, errorList) {
    if (Object.keys(errorMap).length > 0) {
        let $toastError = $('#templates').find('#templateToastError').clone();
        let $toastContainer = $('#toastContainer');
    
        for (const [key, value] of Object.entries(errorMap)) {
            let errorFormatted = `<p class="mb-0 text-danger">${key} - ${value}</p>`
            $toastError.find('.toast-body').first().append(errorFormatted);
        }
    
        $toastError.toast('show');
        $toastContainer.append($toastError);
    }    
}