<script>
document.addEventListener("DOMContentLoaded", () => {
        const modalEl = document.getElementById('contactAddModal');
        const contactModal = new bootstrap.Modal(modalEl, {
            backdrop: false
        }); // disable default backdrop
        const contactModalBackdrop = document.querySelector('.add-contact-modal-backdrop')
        const btnCloses = document.querySelectorAll('.contact-modal-close-btn')
        
        document.getElementById('add-contact-info-btn').addEventListener('click', () => {
            contactModal.show();
            contactModalBackdrop.classList.add("show")
        });

        // Remove backdrop when modal hides
        modalEl.addEventListener('hidden.bs.modal', () => {
            const existingBackdrop = document.querySelector('.modal-backdrop');
            if (existingBackdrop) existingBackdrop.remove();
        });

        btnCloses.forEach((btn) => {
            btn.addEventListener("click", () => {
                document.activeElement.blur();
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                contactModalBackdrop.classList.remove("show")
            })
        })
        
    });
    document.addEventListener("DOMContentLoaded", () => {
        const contentEdits = document.querySelectorAll('.content-edits')
        const modalEl = document.getElementById('contactEditModal');
        const contactModal = new bootstrap.Modal(modalEl, {
            backdrop: false
        }); // disable default backdrop
        const contactModalBackdrop = document.querySelector('.edit-contact-modal-backdrop')
        const btnCloses = document.querySelectorAll('.edit-contact-modal-close-btn')
        const editTitleInput = document.getElementById('edit-title')
        const editTypeSelect = document.getElementById('edit-type')
        const editContactForm = document.getElementById('edit-contact-form')

        const baseUrl = window.location.origin

        contentEdits.forEach((edit) => {
            edit.addEventListener('click', async function () {
                const id = edit.dataset.id
                const url = edit.dataset.url
                try {
                    const res = await axios.get(`/admin/landing-page/contact-info-edit/${id}`);
                    if (res.status === 200) {
                        console.log(res.data);
                        contactModal.show();
                        editContactForm.setAttribute('action', url)
                        editContactForm.setAttribute('method', 'POST')
                        contactModalBackdrop.classList.add("show")
                        editTitleInput.value = res.data.title
                        editTypeSelect.value = res.data.type
                        $('#edit-contact-content').summernote('code', res.data.content ?? '');
                        return;
                    } else {
                        editTitleInput.value = ''
                        editTypeSelect.value = ''
                        $('#edit-contact-content').summernote('code', '');
                        return;
                    }
                } catch (error) {
                    editTitleInput.value = ''
                    console.error('error:', error);   
                }
            })
      })

        // Remove backdrop when modal hides
        modalEl.addEventListener('hidden.bs.modal', () => {
            const existingBackdrop = document.querySelector('.modal-backdrop');
            if (existingBackdrop) existingBackdrop.remove();
        });

        btnCloses.forEach((btn) => {
            btn.addEventListener("click", () => {
                document.activeElement.blur();
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                contactModalBackdrop.classList.remove("show")
            })
        })
    });

    
    document.addEventListener("DOMContentLoaded", () => {
        const modalEl = document.getElementById('featureAddModal');
        const featureModal = new bootstrap.Modal(modalEl, {
            backdrop: false
        }); // disable default backdrop
        const featureModalBackdrop = document.querySelector('.add-feature-modal-backdrop')
        const btnCloses = document.querySelectorAll('.feature-modal-close-btn')
        
        document.getElementById('add-feature-btn').addEventListener('click', () => {
            featureModal.show();
            featureModalBackdrop.classList.add("show")
        });

        // Remove backdrop when modal hides
        modalEl.addEventListener('hidden.bs.modal', () => {
            const existingBackdrop = document.querySelector('.modal-backdrop');
            if (existingBackdrop) existingBackdrop.remove();
        });

        btnCloses.forEach((btn) => {
            btn.addEventListener("click", () => {
                document.activeElement.blur();
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                featureModalBackdrop.classList.remove("show")
            })
        })
    });

    document.addEventListener("DOMContentLoaded", () => {
        const featureEdits = document.querySelectorAll('.feature-edits')
        const modalEl = document.getElementById('featureEditModal');
        const featureModal = new bootstrap.Modal(modalEl, {
            backdrop: false
        }); // disable default backdrop
        const featureModalBackdrop = document.querySelector('.edit-feature-modal-backdrop')
        const btnCloses = document.querySelectorAll('.edit-feature-modal-close-btn')
        const editFeatureTitleInput = document.getElementById('edit-feature-title')
        const editFeatureTypeSelect = document.getElementById('edit-feature-type')
        const editFeatureContentTextArea = document.getElementById('edit-feature-content')
        const editFeatureForm = document.getElementById('edit-feature-form')

        featureEdits.forEach((edit) => {
            edit.addEventListener('click', async function () {
                const id = edit.dataset.id
                const url = edit.dataset.url
                try {
                    const res = await axios.get(`/admin/landing-page/feature-edit/${id}`);
                    if (res.status === 200) {
                        featureModal.show();
                        featureModalBackdrop.classList.add("show")
                        editFeatureForm.setAttribute('action', url)
                        editFeatureForm.setAttribute('method', 'POST')
                        editFeatureTitleInput.value = res.data.title
                        editFeatureTypeSelect.value = res.data.type
                        editFeatureContentTextArea.value = res.data.content ?? '';
                    } else {
                        editFeatureTitleInput.value = ''
                        editFeatureTypeSelect.value = ''
                        editFeatureContentTextArea.value = ''
                    }
                } catch (error) {
                    editFeatureTitleInput.value = ''
                    editFeatureTypeSelect.value = ''
                    editFeatureContentTextArea.value = ''
                    console.error('error:', error);   
                }
            })
      })

        // Remove backdrop when modal hides
        modalEl.addEventListener('hidden.bs.modal', () => {
            const existingBackdrop = document.querySelector('.modal-backdrop');
            if (existingBackdrop) existingBackdrop.remove();
        });

        btnCloses.forEach((btn) => {
            btn.addEventListener("click", () => {
                document.activeElement.blur();
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                featureModalBackdrop.classList.remove("show")
            })
        })
    });
</script>