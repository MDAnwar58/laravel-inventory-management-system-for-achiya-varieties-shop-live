
<style>
    .sales-report-area,
    .profit-report-area {
        width: 0;
        height: 0;
        opacity: 0;
        visibility: hidden;
        transition: height 0.5s ease-in-out, opacity 0.5s ease-in-out, visibility 0.5s ease-in-out;
    }

    .sales-report-area.show,
    .profit-report-area.show {
        width: 100%;
        height: auto;
        opacity: 1;
        visibility: visible;
    }

    .total-cards-body {
        height: 13em;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nav-pills .nav-link.active,
    .nav-pills .show>.nav-link Specificity: (0, 3, 0) {
        color: var(--bs-nav-pills-link-active-color);
        background-color: var(--bs-nav-pills-link-active-bg);
    }

    .nav-pills .nav-link {
        color: rgb(91, 91, 91);
        background-color: transparent;
        font-weight: 600;
        transition: background-color 0.4s ease-in-out, color 0.4s ease-in-out;
    }

    .nav-pills .nav-link:hover {
        color: white;
        background-color: #3b7ddd;
        font-weight: 600;
    }

    .report-loading {
        opacity: 1;
        visibility: visible;
        height: auto;
        transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
    }
    .report-loading.hide {
        opacity: 0;
        visibility: hidden;
        height: 0;
    }
    .report-loading.show {
        opacity: 1;
        visibility: visible;
        height: auto;
    }

.lds-facebook,
.lds-facebook div {
  box-sizing: border-box;
}
.lds-facebook {
  display: inline-block;
  position: relative;
  width: 80px;
  height: 80px;
}
.lds-facebook div {
  display: inline-block;
  position: absolute;
  left: 8px;
  width: 16px;
  background: currentColor;
  animation: lds-facebook 1.2s cubic-bezier(0, 0.5, 0.5, 1) infinite;
}
.lds-facebook div:nth-child(1) {
  left: 8px;
  animation-delay: -0.24s;
}
.lds-facebook div:nth-child(2) {
  left: 32px;
  animation-delay: -0.12s;
}
.lds-facebook div:nth-child(3) {
  left: 56px;
  animation-delay: 0s;
}
@keyframes lds-facebook {
  0% {
    top: 8px;
    height: 64px;
  }
  50%, 100% {
    top: 24px;
    height: 32px;
  }
}


</style>