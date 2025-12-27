<style>
    .add-alert-msg-modal-backdrop {
        --bs-backdrop-zindex: 1050;
        --bs-backdrop-bg: rgb(0, 0, 0, .5);
        position: fixed;
        top: 0;
        left: 0;
        z-index: var(--bs-backdrop-zindex);
        width: 100vw;
        backdrop-filter: blur(9px);
        height: 100vh;
        background-color: var(--bs-backdrop-bg);
        opacity: 0;
        visibility: hidden;
        transition: opacity .4s ease-in-out, visibility .4s ease-in-out;
    }

    .add-alert-msg-modal-backdrop.show {
        opacity: 1;
        visibility: visible;
    }

    .add-times-modal-backdrop {
        --bs-backdrop-zindex: 1050;
        --bs-backdrop-bg: rgb(0, 0, 0, .5);
        position: fixed;
        top: 0;
        left: 0;
        z-index: var(--bs-backdrop-zindex);
        width: 100vw;
        backdrop-filter: blur(9px);
        height: 100vh;
        background-color: var(--bs-backdrop-bg);
        opacity: 0;
        visibility: hidden;
        overflow: auto;
        max-height: 100vh;
        transition: opacity .4s ease-in-out, visibility .4s ease-in-out;
    }

    .add-times-modal-backdrop.show {
        opacity: 1;
        visibility: visible;
    }

</style>
