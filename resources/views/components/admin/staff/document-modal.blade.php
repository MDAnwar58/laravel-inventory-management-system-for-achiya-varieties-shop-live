<!-- Modal -->
<div class="modal fade" id="documentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
  aria-labelledby="documentModalBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl  modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="documentModalBackdropLabel">NID/Smart Card <span id="card-side">Front</span>
          Side</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-0">
        <div class="img-container">
          <img id="mainImage" class="zoomable" style="max-width: 100%;" alt="">
          <!-- <img src="{{ auth()->user()->profile->card_front_side }}" class="zoomable" style="max-width: 100%;" alt=""> -->

          <div id="zoomResult" class="zoom-result"></div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fa-solid fa-print"></i>
        </button>
        <button type="button" class="btn btn-primary">
          <i class="fa-solid fa-download"></i>
        </button>
      </div>
    </div>
  </div>
</div>
