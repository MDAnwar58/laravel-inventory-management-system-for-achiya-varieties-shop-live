@props([
'setting' => null,
'lStockAlert' => null,
])
<script>
    const setting = @json($setting);
    const lStockAlert = @json($lStockAlert);
    let times = setting.schedules.length > 0 ?
        setting.schedules.map((t, i) => ({
            id: t.id
            , i: i + 1
            , time: t.time
        })) : [];
    
    let alertMsg = setting.low_stock_alert_msg

    document.addEventListener('DOMContentLoaded', function() {
        const isAuthSystemCheck = document.getElementById('is-auth-system');
        const lowStockAlertCheck = document.getElementById('low-stock-alert');
        const deleteOptionCheck = document.getElementById('delete-option');
        const lowStockAlertMsgTextarea = document.getElementById('low-stock-alert-msg-textarea');
        const countDisplayParent = document.getElementById('count-parent');
        const countDisplay = document.getElementById('count');
        const optionDisplay = document.getElementById('option');
        const isAuthSystemStatus = document.getElementById('is-auth-system-status');
        const stockAlertStatus = document.getElementById('stock-alert-status');
        let limitText = ''

        setTimes(times)
        if (times.length > 0) setInputTimeAndRemove()

        if (Number(setting.is_auth_system)) {
            isAuthSystemCheck.checked =  Number(setting.is_auth_system);
            isAuthSystemCheck.dataset.id = setting.id;
        } else {
            isAuthSystemCheck.checked = false
            isAuthSystemCheck.dataset.id = setting.id;
        }
        if (Number(lStockAlert.is_alert)){
            lowStockAlertCheck.checked =  Number(lStockAlert.is_alert);
            stockAlertStatus.innerText = lStockAlert.is_alert ? "On" : "Off";
        }else {
            lowStockAlertCheck.checked = false
            stockAlertStatus.innerText = "Off";
        }

        if (Number(setting.delete_options)) {
            deleteOptionCheck.checked = Number(setting.delete_options);
            deleteOptionCheck.dataset.id = setting.id;
        } else {
            deleteOptionCheck.checked = false
            deleteOptionCheck.dataset.id = setting.id;
        }
        if (setting)optionDisplay.innerText = Number(setting.delete_options) ? "Lock" : "Unlock";
        if (setting)isAuthSystemStatus.innerText = Number(setting.is_auth_system) ? "On" : "Off";
        countDisplay.innerText = 0;


        isAuthSystemCheck.addEventListener('change', async function(e) {
            const id = this.dataset.id;
            const is_auth_system_checked = e.currentTarget.checked;
            try {
                const res = await axios.post(`/admin/settings/auth-off-or-on/${id}`, {
                    is_auth_system: is_auth_system_checked
                })
                if (res.status === 200) {
                    processNotify(res.data.msg);
                    isAuthSystemStatus.innerText = res.data.is_auth_system
                }
            } catch (error) {
                console.error('error:', error);
            }
        });
        lowStockAlertCheck.addEventListener('change', async function(e) {
            const id = this.dataset.id;
            const low_stock_alert_checked = e.currentTarget.checked;
            try {
                const res = await axios.post(`/admin/settings/low-stock-alert-off-or-on/${id}`, {
                    is_alert: low_stock_alert_checked
                })
                if (res.status === 200) {
                    processNotify(res.data.msg);
                    stockAlertStatus.innerText = res.data.auth_status;
                }
            } catch (error) {
                console.error('error:', error);
            }
        });
        deleteOptionCheck.addEventListener('change', async function(e) {
            const id = this.dataset.id;
            const delete_option_checked = e.currentTarget.checked;

            try {
                const res = await axios.post(`/admin/settings/delete-option/${id}`, {
                    delete_option: delete_option_checked
                })
                if (res.status === 200) {
                    processNotify(res.data.msg);
                    optionDisplay.innerText = res.data.option;
                }
            } catch (error) {
                console.error('error:', error);
            }
        });

        //console.log(setting);
        lowStockAlertMsgTextarea.addEventListener('input', function(e) {
            let text = e.target.value;
            let count = text.length; // letter count
            if (count <= 300) {
                limitText = text
                countDisplay.innerText = count;
                countDisplayParent.classList.remove('text-danger')
            } else {
                countDisplayParent.classList.add('text-danger')
                e.target.value = limitText
            }
        });
    })


    document.addEventListener("DOMContentLoaded", () => {
        const modalEl = document.getElementById('alertMsgAddModal');
        const alertMsgModal = new bootstrap.Modal(modalEl, {
            backdrop: false
        }); // disable default backdrop
        const alertMsgModalBackdrop = document.querySelector('.add-alert-msg-modal-backdrop')
        const btnCloses = document.querySelectorAll('.alert-msg-modal-close-btn')
        const alertMsgSubmitBtn = document.getElementById('alert-msg-submit-btn')
        const lowStockAlertMsgTextarea = document.getElementById('low-stock-alert-msg-textarea');
        const countDisplayParent = document.getElementById('count-parent');
        const lowStockAlertMsgError = document.getElementById('low-stock-alert-msg-error');
        const cDisplay = document.getElementById('count');

        alertMsgSubmitBtn.dataset.id = setting.id;
        if (setting.low_stock_alert_msg) {
            lowStockAlertMsgTextarea.value = setting.low_stock_alert_msg;
            cDisplay.innerText = setting.low_stock_alert_msg.length;
        } else {
            lowStockAlertMsgTextarea.value = "";
            cDisplay.innerText = 0
        }

        document.getElementById('low-stock-msg-set-btn').addEventListener('click', () => {
            alertMsgModal.show();
            alertMsgModalBackdrop.classList.add("show")
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

                alertMsgModalBackdrop.classList.remove("show")
                lowStockAlertMsgTextarea.value = alertMsg ? alertMsg : ''
                countDisplayParent.classList.remove('text-danger')
                lowStockAlertMsgError.innerText = ''
            })
        })

        alertMsgSubmitBtn.addEventListener('click', async function(e) {
            const id = e.currentTarget.dataset.id;
            const lowStockAlertMsg = lowStockAlertMsgTextarea.value;
            if (lowStockAlertMsg) {
                lowStockAlertMsgError.innerText = ''
                try {
                    const res = await axios.post(`/admin/settings/low-stock-alert-msg-store/${id}`, {
                        low_stock_alert_msg: lowStockAlertMsg
                    })
                    if (res.status === 200) {
                        document.activeElement.blur();
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();

                        alertMsgModalBackdrop.classList.remove("show")
                        countDisplayParent.classList.remove('text-danger')
                        processNotify(res.data.msg);
                    }
                } catch (error) {
                    processNotify("Something went wrong, please try again later.", "error");
                    console.error('error:', error);
                }
            } else lowStockAlertMsgError.innerText = "Low stock alert field is required."
        })
    });


    document.addEventListener("DOMContentLoaded", () => {
        const modalTimesEl = document.getElementById('timesAddModal');
        const timesModal = new bootstrap.Modal(modalTimesEl, {
            backdrop: false
        }); // disable default backdrop
        const timesModalBackdrop = document.querySelector('.add-times-modal-backdrop'); // Get the backdrop element
        const btnCloses = document.querySelectorAll('.times-modal-close-btn')
        const timesSubmitBtn = document.getElementById('times-submit-btn')
        const timesAddBtn = document.getElementById('times-add-btn')
        const timesInputList = document.getElementById('times-input-list')
        const timesForSubmit = document.getElementById('times-for-submit')

        timesSubmitBtn.dataset.id = setting.id;
        timesForSubmit.value = JSON.stringify(times);

        document.getElementById('times-set-btn').addEventListener('click', () => {
            timesModal.show();
            timesModalBackdrop.classList.add("show")
        });

        // Remove backdrop when modal hides
        modalTimesEl.addEventListener('hidden.bs.modal', () => {
            const existingBackdrop = document.querySelector('.modal-backdrop');
            if (existingBackdrop) existingBackdrop.remove();
        });

        btnCloses.forEach((btn) => {
            btn.addEventListener("click", () => {
                document.activeElement.blur();
                const modal = bootstrap.Modal.getInstance(modalTimesEl);
                if (modal) modal.hide();

                timesModalBackdrop.classList.remove("show")
            })
        })

        timesAddBtn.addEventListener('click', async function(e) {
            timesAddBtn.insertAdjacentHTML("beforebegin", `
                    <div class="pb-3 input-group">
                        <input type="time" data-id="" class="form-control fs-4 times-inputs" placeholder="Times">
                        <button type="button" class="btn btn-sm btn-danger remove-input-btns"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                `);

            setInputTimeAndRemove()
        })
        timesInputList.addEventListener('click', function(e) {
            const timesInputs = document.querySelectorAll('.times-inputs')
            timesInputs.forEach((timesInput, i) => {
                timesInput.dataset.index = i + 1;
                timesInput.addEventListener('input', function(e) {
                    const id = e.currentTarget.dataset.id
                    const timeValue = e.currentTarget.value;

                    // check if index already exists
                    const existing = times.find(t => t.i === i + 1);

                    if (existing) existing.time = timeValue;
                    else times.push({
                        id
                        , i: i + 1
                        , time: timeValue
                    }); // add new

                    timesForSubmit.value = JSON.stringify(times);
                });
            });
        })
    });



    function setTimes(times) {
        const timesAddBtn = document.getElementById('times-add-btn')
        const timesForSubmit = document.getElementById('times-for-submit')
        const firstTimeInput = document.getElementById('first-time-input');
        if (times.length > 0) {
            firstTimeInput.remove();
            timesForSubmit.value = times;
            times.forEach((t, i) => {
                timesAddBtn.insertAdjacentHTML("beforebegin", `
                    <div class="pb-3 input-group">
                        <input type="time" data-id="" data-index="${i+1}" class="form-control fs-4 times-inputs" placeholder="Times" value="${t.time}">
                        <button type="button" class="btn btn-sm btn-danger remove-input-btns"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                `);
            })
        }
    }

    function setInputTimeAndRemove() {
        const timesForSubmit = document.getElementById('times-for-submit')
        const removeInputBtns = document.querySelectorAll(".remove-input-btns");
        if (removeInputBtns) {
            removeInputBtns.forEach(btn => {
                btn.addEventListener("click", (e) => {
                    const index = Number(e.currentTarget.parentElement.firstElementChild.dataset.index);
                    // properly filter out only the one with this index
                    tms = times.filter(t => {
                        if (Number(t.i) !== index) return true;
                        else return false; // remove
                    });
                    times = tms;
                    timesForSubmit.value = JSON.stringify(tms);
                    e.currentTarget.parentElement.remove();
                })


            })
        }
    }

</script>
