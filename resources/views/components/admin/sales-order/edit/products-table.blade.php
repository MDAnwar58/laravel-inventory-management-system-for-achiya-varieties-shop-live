<div class="col-xl-6 col-lg-12 px-lg-3 ps-md-3 mt-md-0 mt-3">
    <div class="card">
        <div id="card-title" class=" pt-3 px-4 d-flex flex-row justify-content-between align-items-center">
            <div class="text-secondary fw-bold fs-4">Product List</div>
         
            <div class="d-flex gap-1">
                <input type="text" class="form-control fs-5" id="product-search" placeholder="Search Products...">
                <button type="button" id="reset-product-table-btn"
                    class="btn btn-sm btn-light rounded shadow-sm fs-5 fw-semibold" 
                    data-bs-toggle="tooltip" data-bs-title="Reset Products List."
                     >
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
                
                <button type="button" id="clean-selected-products-btn" class="btn btn-sm btn-outline-danger rounded shadow-sm fs-5 fw-semibold" data-bs-toggle="tooltip" data-bs-title="Clean All Selected Products.">
                    <i class="fa-solid fa-trash"></i>
                </button>

                <button type="button" id="old-selected-products-btn" class="btn btn-sm btn-outline-primary rounded shadow-sm fs-5 fw-semibold d-none">
                    <i class="fa-solid fa-check"></i>
                </button>
            </div>
        </div>
        <div class=" card-body pt-0">
            <div class="table-responsive">
               <table class="table table-hover" id="productTable">
                <thead>
                    <tr>
                        <th scope="col" class="text-muted fw-normal">Product</th>
                        <th scope="col" class="text-muted fw-normal">Retail Price Enable</th>
                        <th scope="col" class="text-muted fw-normal">Pick</th>
                    </tr>
                </thead>
                <tbody id="productList"></tbody>
            </table>
            </div>
            <x-admin.sales-order.product-table-footer />
        </div>
    </div>
</div>
@push('script')
    <script>
        function logWindowWidth() {
            const cardTitle = document.getElementById('card-title');
            const width = window.innerWidth
            if (width >= 1200 && width < 1275) {
                cardTitle.classList.remove('flex-row');
                cardTitle.classList.remove('justify-content-between');
                cardTitle.classList.add('flex-column');
                cardTitle.classList.add('justify-content-center');
                cardTitle.firstElementChild.classList.add('pb-2');
            } else if (width >= 475 && width < 1200) {
                cardTitle.classList.remove('flex-column');
                cardTitle.classList.remove('justify-content-center');
                cardTitle.firstElementChild.classList.remove('pb-2');
                cardTitle.classList.add('flex-row');
                cardTitle.classList.add('justify-content-between');
            } else if (width < 475) {
                cardTitle.classList.remove('flex-row');
                cardTitle.classList.remove('justify-content-between');
                cardTitle.classList.add('flex-column');
                cardTitle.classList.add('justify-content-center');
                cardTitle.firstElementChild.classList.add('pb-2');
            }
        }

        // log initial width
        logWindowWidth();

        // listen for window resize
        window.addEventListener("resize", logWindowWidth);

    </script>
@endpush
