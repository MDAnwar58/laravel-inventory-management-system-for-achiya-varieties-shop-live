@extends('layouts.admin-layout')
@section('title', '- Sales Order Edit')

@push('style')
  <link rel="stylesheet" href="{{ asset('assets/css/pagination.css') }}">

  <style>
  .gradient-app-logo {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .add-customer-modal-backdrop {
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

    .add-customer-modal-backdrop.show {
      opacity: 1;
      visibility: visible;
    }
    .retail-checkboxs {
        transform: scale(1.5); /* Increase size (1.5x) */
    }


    .custom-tooltip {
      --bs-tooltip-bg: var(--bd-violet-bg);
      --bs-tooltip-color: var(--bs-white);
    }
    .bdt {
        font-family: 'Noto Sans Bengali', sans-serif;
        margin-right: 0.1rem;
    }


  </style>
@endpush

@section('content')
  <x-admin.breadcrumb :breadcrumbs="$breadcrumbs" />
  <form action="{{ route('admin.sales.order.update', $data->id) }}" method="POST">
    @csrf
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="row main_row pb-5">
      <x-admin.sales-order.edit.products-table />
      <x-admin.sales-order.edit.customers-table />
      <x-admin.sales-order.edit.order-info />
      <x-admin.sales-order.edit.billed-card :printing_content="$printing_content" :data="$data" />
    </div>
  </form>

  <x-admin.sales-order.add-new-customer-modal />
@endsection

@push('script')
  <x-admin.tool-tip />
  @if (Session::has('status'))
    <x-alert :msg="Session::get('msg')" :status="Session::get('status')" />
  @endif
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="{{ asset('assets/js/react-hook.js') }}"></script>
  <script>
    // products variables
    let products = []
    let product_search = ""
    let product_page = 1
    let product_per_page = 5
    let product_loading = true

    // customers variables
    let customers = []
    let customer_search = ""
    let customer_page = 1
    let customer_per_page = 5
    let customer_loading = true
    let customer = JSONParse("{{ $data->customer }}");
    let salesOrderProducts = @json($data->sales_order_products);
    let orderJson = @json($data);
    let order = {
      order_date: orderJson?.order_date,
      due_date: orderJson?.due_date,
      payment_status: orderJson?.payment_status,
      notes: orderJson?.notes,
    }

    // products for billed card variables
    let productsForBilledCard = []
    let pList = salesOrderProducts.length > 0 ? salesOrderProducts.map(p => {
      return {
        id: p?.product?.id,
        image: p?.product?.image,
        name: p?.product?.name,
        price: p?.price,
        discount_price: p?.discount_price,
        purchase_limit: p.qty,
        qty: p.qty,
        stock: p?.product.stock_w_type !== "none" ? p?.product?.stock_w : p?.product?.stock,
        tax: p?.tax,
        total_price: p?.total,
        retail_price_status: p?.retail_price_status,
        stock_w_type: p?.stock_w_type,
        order_status: true
      }
    }) : []; // keep it outside so it persists
    let payment_status = "{{ $data->payment_status }}";
    let status = "{{ $data->status }}";
    let due_amount = "{{ $data->due_amount }}";
    let paid_amount = "{{ $data->paid_amount }}";
    let memo_no = "{{ $data->memo_no }}";

    let ids = []
    let IdsWithOthers = true;
    let loadOldSelectedProducts = false;
    let subtotal = 0;
    let totalVat = 0;
    let total = 0;

    let stockProducts = [];
    let pStockQty = 0;
    // for selected products and customer
    let sPIds = "";
    let cmId = "";

    function strNumToBool(str) {
        if (!str)return false
        const num = parseInt(str);
        if (num === 1) return true
        else return false
    }
    function strToBool(value) {
      if (!value)return false
      return value === "true";
    }
    function isFloat(n) {
      return Number(n) === n && !Number.isInteger(n);
    }
    function formatNumber(num, type = 'none') {
      if (type !== 'none') {
        if (type === 'ft' || type === 'yard' || type === 'm') {
            num = parseFloat(num);
            if (num < 1) return parseFloat((num * 100).toFixed(2)).toString();
            return parseFloat(num.toFixed(2)).toString();
        } else {
            num = parseFloat(num);
            if (num < 1) {
                return (num * 1000).toString().replace(/\.0+$/, '');
            }
            return parseFloat(num.toFixed(3)).toString();
        }
      } else {
          num = parseFloat(num);
          if (num < 1) {
              return (num * 1000).toString().replace(/\.0+$/, '');
          }
          return parseFloat(num.toFixed(3)).toString();
      }
    }
    function kgOrGmOrFtOrYardOrMtOrInchi(num, type = 'kg') {
      if (type === 'kg') {
        num = parseFloat(num);
        if (num < 1) {
            return 'gm';
        }
        return 'kg'
      } else if (type === 'ft') {
        num = parseFloat(num);
        if (num < 1)return 'inchi';
        return 'ft'
      } else if (type === 'yard') {
        num = parseFloat(num);
        if (num < 1)return 'inchi';
        return 'yard'
      } else if (type === 'm') {
        num = parseFloat(num);
        if (num < 1)return 'inchi';
        return 'm'
      }
    }
   // write number and decimals and decimals 3
    const writeOnlyNumberAndDecimalInput = (input, decimals_length = 3) => {
            const val = input.value;
        // 1. strip every character that is not a digit or dot
            let cleaned = val.replace(/[^0-9.]/g, '');

            // 2. keep only the first dot
            const parts = cleaned.split('.');
            if (parts.length > 2) cleaned = parts[0] + '.' + parts.slice(1).join('');

            // 3. allow at most 3 decimals
            if (parts[1] && parts[1].length > decimals_length) {
                cleaned = parts[0] + '.' + parts[1].slice(0, decimals_length);
            }

            // 4. put the cleaned value back (no cursor jump)
            if (cleaned !== val) {
                input.value = cleaned;
            }
    }


    const writeOnlyNumberAndDecimalWithFtOrInchiCalCulateInput = (input, decimals_length = 2, maxDecimal = 11) => {
        let val = input.value;

        // 1. strip every character that is not a digit or dot
        let cleaned = val.replace(/[^0-9.]/g, '');

        // 2. keep only the first dot
        const parts = cleaned.split('.');
        if (parts.length > 2) cleaned = parts[0] + '.' + parts.slice(1).join('');

        // 3. limit decimals to decimals_length
        if (parts[1]) {
            let decimalPart = parts[1].slice(0, decimals_length);

            // 4. enforce max decimal number (e.g., .11)
            if (parseInt(decimalPart) > maxDecimal) {
                decimalPart = maxDecimal.toString().padStart(decimals_length, '0');
            }

            cleaned = parts[0] + '.' + decimalPart;
        }

        // 5. put the cleaned value back
        if (cleaned !== val) input.value = cleaned;
    };


    function calculatePriceFromAnyToInches(ftInchesInput, pricePerFt, inchPerFt = 12) {
        const feet = Math.floor(ftInchesInput);
        const inchesDecimal = ftInchesInput - feet;

        // Treat .XX as inches directly
        const inches = Math.round(inchesDecimal * 100);

        const totalInches = feet * inchPerFt + inches;
        const pricePerInch = pricePerFt / inchPerFt;
        return Math.round(totalInches * pricePerInch);
    }
    //console.log(calculatePriceFromFtInches(3.06, 50));

    const incrementInputQty = (input) => {
        let value = parseFloat(input.value);
        let decimal = (value % 1).toFixed(2);
        let integer = Math.floor(value);
        value = parseFloat((value + 0.01).toFixed(2));
        if (decimal >= 0.11)value = (integer + 1).toFixed(2);
        input.value = value;
    }

    const incrementInputQtyUsingNextBtn = (value, beyond = 0.11) => {
         // Get the decimal part
        let decimal = (value % 1).toFixed(2);
        let integer = Math.floor(value);

        // Increment by 0.01
        value = parseFloat((value + 0.01).toFixed(2));

        // If decimal part goes beyond 0.11
        if (decimal >= beyond) {
            value = (integer + 1).toFixed(2);
        }
        return value;
    }

    const decrementInputQtyUsingPrevBtn = (value, inch = 11) => {
        let integer = Math.floor(value);                 // foot part
        let decimal = Math.round((value % 1) * 100);     // inch part

        decimal -= 1; // decrement 1 inch

        // If inch part goes below 0
        if (decimal < 0) {
            if (integer > 0) {
            integer -= 1;
            decimal = inch; // reset inch to 11
            } else {
            decimal = 0; // prevent going below 0
            }
        }

        // format inch part always as two digits
        const result = parseFloat(`${integer}.${decimal.toString().padStart(2, "0")}`);
        return result;
    }

    function addLastInch(a, b, last_inchi = 12) {
      // Convert 8.09 → 8 ft, 9 in
      const [ft1, inch1] = a.toString().split('.').map(Number);
      const [ft2, inch2] = b.toString().split('.').map(Number);

      let totalInch = (inch1 || 0) + (inch2 || 0);
      let totalFt = ft1 + ft2;

      // Convert 12 inches → 1 foot
      if (totalInch >= last_inchi) {
        totalFt += Math.floor(totalInch / 12);
        totalInch = totalInch % 12;
      }

      // Format as 10.06 (two digits for inches)
      const result = `${totalFt}.${totalInch.toString().padStart(2, '0')}`;
      return result;
    }




    // console.log(pList);
    document.addEventListener('DOMContentLoaded', () => {
      const { useEffect } = ReactHook
      useEffect(() => {
        if(pList.length > 0){
          //pIds = pList.map(p => p.id)
          //ids = JSON.stringify(pIds)
          //sPIds = ids
          ids = pList.map(p => p.id)
          IdsWithOthers = false
          loadOldSelectedProducts = true
          getProducts(product_search, product_page, product_per_page, ids, loadOldSelectedProducts, IdsWithOthers)
        } else getProducts(product_search, product_page, product_per_page, ids, loadOldSelectedProducts, IdsWithOthers)
        if (customer?.id){
          cmId = customer.id
          getCustomers(customer_search, customer_page, customer_per_page, customer.id)
        }
        else getCustomers(customer_search, customer_page, customer_per_page, cmId)

       // getProductsForBilledCard()
        setProductsInput([])
        setSalesOrderInfo(order)
        setForDueAndPartialDueAmount(pList, status, payment_status, due_amount, paid_amount)
      }, [])

      const productSearchInput = document.getElementById('product-search')
      const productResetBtn = document.getElementById('reset-product-table-btn')
      const customerSearchInput = document.getElementById('customer-search')
      const customerResetBtn = document.getElementById('reset-customer-table-btn')
      const CId = document.getElementById("CId");
      const paidAmountDiv = document.getElementById("paid-amount-div");
      const paymentStatusSelectField = document.getElementById("payment_status");
      const statusSelectField = document.getElementById("status");
      const paidAmountEl = document.getElementById("paid-amount-el");
      const dueAmountEl = document.getElementById("due-amount-el");
      //const resetAllBtn = document.getElementById("reset-all-btn");
      const dueDateInput = document.getElementById("due-date")
      const memoNoInput = document.getElementById("memo_no")
      const notesTextArea = document.getElementById('notes')
      const paidAmountInput = document.getElementById("paid-amount-input");
      const dueAmountInput = document.getElementById("due-amount-input");
      const spinner = document.getElementById('spinner-border')
      const submitBtn = document.querySelector('.submit-btn')
      const cleanSelectedProductsBtn = document.getElementById('clean-selected-products-btn')
      const oldSelectedProductsBtn = document.getElementById("old-selected-products-btn")

      productSearchInput.value = ""
      customerSearchInput.value = ""
      CId.value = "";
      paymentStatusSelectField.value = payment_status;
      statusSelectField.value = status;
      if(memo_no)memoNoInput.value = memo_no;
      if (paid_amount === "") paidAmountInput.value = ""
      if (due_amount === "" || status === "cancelled") dueAmountInput.value = ""

      productSearchInput.addEventListener('keyup', (e) => {
        product_search = e.target.value
        product_page = 1
        getProducts(product_search, product_page, product_per_page, [], true)
      })

      productResetBtn.addEventListener('click', () => {
        productSearchInput.value = ""
        product_search = ""
        product_page = 1
        sPIds = ""
        IdsWithOthers = true;
        if(loadOldSelectedProducts === false && ids?.length > 0)loadOldSelectedProducts = true
        getProducts(product_search, product_page, product_per_page, [], loadOldSelectedProducts, IdsWithOthers)
      })


      customerSearchInput.addEventListener('keyup', (e) => {
        customer_search = e.target.value
        customer_page = 1
        getCustomers(customer_search, customer_page, customer_per_page, cmId)
      })

      customerResetBtn.addEventListener('click', () => {
        customerSearchInput.value = ""
        customer_search = ""
        customer_page = 1
        cmId = ""
        getCustomers(product_search, product_page, product_per_page, cmId)
      })

      paymentStatusSelectField.addEventListener('change', (e) => {
        if (pList.length > 0) {
          const dueAmountSpan = document.getElementById("due-amount");
          const paidAmount = document.getElementById("paid-amount");
          const totalAInput = document.getElementById("total-amount-input");
          const totalAmount = totalAInput.value
          if (e.currentTarget.value === 'partial due' || e.currentTarget.value === 'due') {
            dueDateInput.disabled = false
            paidAmountDiv.classList.remove('d-none')
            paidAmountEl.classList.remove('d-none')
            dueAmountEl.classList.remove('d-none')
            if (e.currentTarget.value === 'partial due') {
                paidAmountInput.readOnly = false
            }
            if (e.currentTarget.value === 'due' && totalAmount) {
              paidAmount.innerHTML = 0.00
              paidAmountInput.value = parseFloat(0).toFixed(2)
              paidAmountInput.readOnly = true
              dueAmountInput.value = totalAmount
              dueAmountSpan.innerHTML = totalAmount
            }
          } else {
            if (e.currentTarget.value === 'paid')notesTextArea.value = ''
            paidAmountInput.readOnly = false
            dueDateInput.disabled = true
            dueDateInput.value = ''
            paidAmountInput.value = 0.00
            dueAmountInput.value = 0.00
            dueAmountSpan.innerHTML = 0.00
            paidAmount.innerHTML = 0.00
            paidAmountDiv.classList.add('d-none')
            paidAmountEl.classList.add('d-none')
            dueAmountEl.classList.add('d-none')
          }
        }
      })

      statusSelectField.addEventListener('change', (e) => {
        let status = e.currentTarget.value
        const dueAmountSpan = document.getElementById("due-amount");
        const paidAmount = document.getElementById("paid-amount");
        if (status === "cancelled") {
          paymentStatusSelectField.value = "cancel"
          dueDateInput.disabled = true
          dueDateInput.value = ''
          notesTextArea.value = ''
          paidAmountInput.value = 0.00
          dueAmountInput.value = 0.00
          dueAmountSpan.innerHTML = 0.00
          paidAmount.innerHTML = 0.00
          paidAmountDiv.classList.add('d-none')
          paidAmountEl.classList.add('d-none')
          dueAmountEl.classList.add('d-none')
        }else{
          paymentStatusSelectField.value = "paid"
        }
      })

      cleanSelectedProductsBtn.addEventListener('click', () => {
        pList = []
        ids = []
        sPIds = ""
        billedCardProductList(pList);
        showingTotalPrice([])
        resetProductAddBtnInSelectedProductsTable()
      })

      oldSelectedProductsBtn.addEventListener('click', () => {
          productSearchInput.value = ""
          product_search = ""
          product_page = 1
          loadOldSelectedProducts = true
          IdsWithOthers = false
          getProducts(product_search, product_page, product_per_page, ids, loadOldSelectedProducts, IdsWithOthers)
      })
      //resetAllBtn.addEventListener('click', () => {
      //  pList = []
      //  billedCardProductList(pList);
      //  showingTotalPrice([])
      //  resetProductAddBtnInSelectedProductsTable()
      //  resetCustomerAddBtnInSelectedCustomersTable()
      //})


      submitBtn.addEventListener('click', async () => {
        submitBtn.classList.add('disabled')
        spinner.classList.remove('d-none')
      })


    })

    async function getProducts(search, page, perPage, pIds, load_old_selected_products = false, ids_with_others = true) {
      const productList = document.getElementById('productList');
      if (!productList) return;
      if (product_loading) productList.innerHTML = `<tr class="text-center"><td colspan="3">Loading...</td></tr>`;
      try {
        let baseUrl = window.location.origin
        const res = await axios.get(`${baseUrl}/admin/products/get-for-sales?search=${search}&page=${page}&per_page=${perPage}&ids=${pIds}&ids_with_others=${ids_with_others}`)
        console.log(res);
        products = res.data.data;
        links = res.data.links;
        let last_page = res.data.last_page;
        let current_page = res.data.current_page;
        let per_page = res.data.per_page;
        let data_entries = res.data.total;
        product_loading = false

        if (products.length > 0) {
          productList.innerHTML = '';
          products.forEach(product => {
            if (!productList) return; // safety check
            let stock = product.stock_w_type === "none" ? product.stock : product.stock_w;

            productList.innerHTML += `<tr>`
              + `<td>`
              + `<div class="d-flex gap-2" style="width: 331px;">`
              +`${product.image ? `<div class="position-relative" style="width:70px; height:70px;">`
              +`<img src="${product.image}" alt="${product.name}" width="70" height="70" class="rounded-3">`
              +`${stock > 0 ? '' : `<div class="position-absolute top-0 start-0 w-100 h-100 rounded-3" style="background-color: rgba(213, 213, 213, 0.5);"></div>`}`
              +`</div>` : `<div class="border shadow rounded-3 d-flex justify-content-center align-items-center" style="width: 70px; height: 70px;">`
              +`<i class="fa-solid fa-image  ${stock > 0 ? ' text-secondary-emphasis' : 'text-body-tertiary'} fs-1"></i>`
              +`</div>`}`
              + `<div>`
              + `<div class="fw-semibold text-dark-emphasis">${product.name}</div>`
              +`<div class="fs-6 main-price" data-id="${product.id}">Wholesale Price:- <span class="bdt">৳</span>${product.price}</div>`
              //+` ${product.discount_price ? `<span class="text-muted text-decoration-line-through" style="font-size: .67rem;">${product.price}</span>` : ''}`
              +`${product.retail_price ? `<div class="fs-6 retail-price d-none" data-id="${product.id}">Retail Price:- <span class="bdt">৳</span>${product.retail_price}</div>` : ''}`
              +`<div style="font-size: .80rem;">Stock:- ${stock > 0 ? formatNumber(stock, product.stock_w_type) : 'Out of Stock'}${stock > 0 && product.stock_w_type !== "none" ? kgOrGmOrFtOrYardOrMtOrInchi(stock, product.stock_w_type) : ' pcs'}</div>`
              +`<div style="font-size: .70rem;" class="min-purchase" data-id="${product.id}">Min. Purchase:- ${product.purchase_limit}${product.stock_w_type !== "none" ? kgOrGmOrFtOrYardOrMtOrInchi(product.purchase_limit, product.stock_w_type) : ' pcs'}</div>`
              + `</div>`
              + `</div>`
              + `</td>`
              +`<td>`
              +`${product.retail_price ? `<div class="form-check d-flex justify-content-center">`
              +`<input type="checkbox"  name="retail_checkbox" class="form-check-input retail-checkboxs" data-id="${product.id}" data-retail-price="${product.retail_price}" data-price="${product.price}" data-stock-w-type="${product.stock_w_type}" />`
              +`</div>` : '<div class="text-center">N/A</div>'}`
              +`</td>`
              + `<td>`
              + `<div class="d-flex gap-1">`
              + `<button type="button" data-product-id="${product.id}" data-product-stock-w-type="${product.stock_w_type}" data-stock="${product.stock}" class="btn btn-sm btn-light product-add-btn rounded-3 border shadow-sm fs-5 fw-semibold ${stock <= 0 ? 'disabled' : ''}">`
              +`<i class="fa-solid fa-plus"></i>`
              +`<div class="spinner-border spinner-border-sm d-none" role="status"><span class="visually-hidden">Loading...</span></div>`
              +`</button>`
              + `<button type="button" data-product-id="${product.id}" class="btn btn-sm btn-danger add-product-remove-btn rounded-3 border shadow-sm fs-5 fw-semibold d-none"><i class="fa-solid fa-xmark"></i></button>`
              + `</div>`
              + `</td>`
              + `</tr>`;
          });

          const productPage = document.getElementById('product-page');
          const productPerPage = document.getElementById('product-per-page');
          const productTotalPage = document.getElementById('product-total-page');
          productPage.innerText = current_page;
          productPerPage.innerText = per_page;
          productTotalPage.innerText = data_entries;
          const productPagination = document.getElementById('product-pagination');
          productPagination.innerHTML = '';
          links.forEach((link, i) => {
            if (link.label === "&laquo; Previous") {
              productPagination.innerHTML += `<li class="page-item prev ${link.url === null ? 'disabled' : ''}">`
                + `<button type="button" class="page-link product-prev-btn" style="cursor: pointer;">&laquo;</button>`
                + `</li>`
            }
            if (link.url !== null) {
              if (link.label !== 'Next &raquo;' && link.label !== '&laquo; Previous') {
                //console.log(link)
                productPagination.innerHTML += `<li class="page-item ${link.active === true ? 'active' : ''}">`
                  + `<button type="button" class="page-link product-page-btn" data-page="${link.label}" style="cursor: pointer;">${link.label}</button>`
                  + `</li>`
              }
            }
            if (link.label === "...") {
              productPagination.innerHTML += `<li class="page-item ${link.url === null ? 'disabled' : ''}">`
                + `<button type="button" class="page-link" >...</button>`
                + `</li>`
            }
            if (link.label === "Next &raquo;") {
              productPagination.innerHTML += `<li class="page-item next ${link.url === null ? 'disabled' : ''}" >`
                + `<button type="button" class="page-link product-next-btn" data-page="" style="cursor: pointer;">&raquo;</button>`
                + `</li>`
            }
          })

          const nextBtn = document.querySelector('.product-next-btn')
          const prevBtn = document.querySelector('.product-prev-btn')
          const pageBtn = document.querySelectorAll('.product-page-btn')
          const retailCheckboxs = document.querySelectorAll('.retail-checkboxs')
          const productAddBtns = document.querySelectorAll('.product-add-btn')
          const addProductRemoveBtns = document.querySelectorAll('.add-product-remove-btn')

          if (retailCheckboxs.length > 0) {
                retailCheckboxs.forEach((retailCheckbox) => {
                    retailCheckbox.checked = false
                })
            }

          pageBtn.forEach((btn) => {
            btn.addEventListener('click', (e) => {
              const pageNum = Number(e.currentTarget.dataset.page)
              product_page = pageNum
              // that for selected products
              let pro_ids = IdsWithOthers === false ? ids : []
              if(loadOldSelectedProducts === false)loadOldSelectedProducts = true
              getProducts(product_search, pageNum, product_per_page, pro_ids, loadOldSelectedProducts, IdsWithOthers)
            })
          })
          nextBtn.addEventListener('click', () => {
            if (product_page !== last_page) {
              product_page++
              // that for selected products
              let pro_ids = loadOldSelectedProducts ? ids : []
              if(loadOldSelectedProducts === false)loadOldSelectedProducts = true
              getProducts(product_search, product_page, product_per_page, pro_ids, loadOldSelectedProducts, IdsWithOthers)
            }
          })
          prevBtn.addEventListener('click', () => {
            if (product_page > 0) {
              product_page--
              // that for selected products
              let pro_ids = loadOldSelectedProducts ? ids : []
              if(loadOldSelectedProducts === false)loadOldSelectedProducts = true
              getProducts(product_search, product_page, product_per_page, pro_ids, loadOldSelectedProducts, IdsWithOthers)
            }
          })

          retailCheckboxs.forEach((checkbox) => {
              checkbox.addEventListener('change', (e) => {
                  const productId = parseInt(e.currentTarget.dataset.id);
                  const productStockWType = e.currentTarget.dataset.stockWType;
                  const productRetailPrice = parseFloat(e.currentTarget.dataset.retailPrice); // numeric
                  const productPrice = parseFloat(e.currentTarget.dataset.price); // numeric

                  const retailPriceEl = document.querySelector(`.retail-price[data-id='${productId}']`)
                  const mainPriceEl = document.querySelector(`.main-price[data-id='${productId}']`)
                  const minPurchaseEl = document.querySelector(`.min-purchase[data-id='${productId}']`)
                  const productAddBtn = document.querySelector(`.product-add-btn[data-product-id='${productId}']`)

                  if (e.currentTarget.checked) {
                      retailPriceEl.classList.remove('d-none')
                      mainPriceEl.classList.add('d-none')
                      minPurchaseEl.classList.add('d-none')
                      productAddBtn.dataset.retailPrice = 1

                      let pListFind = pList.find(p => p.id === Number(productId))
                      if (pListFind) {
                        pList = pList.map(p => {
                            if (p.id === Number(productId)) {
                                let sp = null;
                                if (stockProducts.length > 0)sp = stockProducts.find(sp => sp.id === Number(productId));
                                p.price = productRetailPrice
                                p.total_price = sp && sp.qty ? sp.qty * productRetailPrice : p.qty * productRetailPrice
                                p.retail_price_status = 1
                                if (sp && sp.qty) p.qty = sp.qty
                            }
                            return p
                        })
                        if (pList) getRemainingProducts(pList)
                      }
                  } else {
                      retailPriceEl.classList.add('d-none')
                      mainPriceEl.classList.remove('d-none')
                      minPurchaseEl.classList.remove('d-none')
                      productAddBtn.dataset.retailPrice = 0
                      let pListFind = pList.find(p => p.id === Number(productId))
                      if (pListFind) {
                        pList = pList.map(p => {
                            if (p.id === Number(productId)) {
                                let sp = null;
                                if (stockProducts.length > 0)sp = stockProducts.find(sp => sp.id === Number(productId));
                                p.price = productPrice
                                p.total_price = sp && sp.qty ? sp.qty * productPrice : p.qty * productPrice
                                p.retail_price_status = 0
                                if (sp && sp.qty) p.qty = sp.qty
                            }
                            return p
                        })
                        if (pList) getRemainingProducts(pList)
                      }
                  }
              })
          })

          // set user buy products
          if (load_old_selected_products && pList.length > 0) setSalesOrderProducts(pList)
          productAddBtns.forEach((btn) => {
            btn.addEventListener("click", async (e) => {
              let el = e.currentTarget;
              const productId = e.currentTarget.dataset.productId;
              const addStatus = pList.some(p => p.id === Number(productId));

              if (!addStatus) {
                el.firstElementChild.classList.add('d-none')
                const productStockWType = e.currentTarget.dataset.productStockWType;
                const productRetailPriceStatus = e.currentTarget.dataset.retailPrice;
                //const product = productsForBilledCard.find(
                //  (p) => p.id === Number(productId)
                //);
                el.lastElementChild.classList.remove('d-none')
                let product = null;
                const res = await axios.get(`/admin/products/for-order-billed/get?id=${Number(productId)}`)
                product = res.data
                el.lastElementChild.classList.add('d-none')

                if (product && !pList.some((p) => p.id === product.id)) {
                  let qty = getQtyForProductAdd(product, productRetailPriceStatus)

                  pList.push({
                    ...product,
                    qty: qty,
                    //price: strNumToBool(productRetailPriceStatus) ? product.retail_price : product.discount_price ? product.discount_price : product.price,
                    price: strNumToBool(productRetailPriceStatus) ? product.retail_price : product.price,
                    //total_price: strNumToBool(productRetailPriceStatus) ? (product.retail_price * product.purchase_limit).toFixed(2) : product.discount_price ? (product.discount_price * product.purchase_limit).toFixed(2) : (product.price * product.purchase_limit).toFixed(2),
                    total_price: strNumToBool(productRetailPriceStatus) ? (product.retail_price * qty).toFixed(2) : (product.price * qty).toFixed(2),
                    stock: productStockWType !== "none" ? product.stock_w : product.stock,
                    stock_w_type: productStockWType,
                    retail_price_status: parseInt(productRetailPriceStatus)
                  });
                  ids = pList.map(p => p.id)
                  setSelectedProducts(ids)
                  el.firstElementChild.classList.remove('d-none')
                  billedCardProductList(pList);
                }
              }
            });
          });

          if (addProductRemoveBtns) {
              addProductRemoveBtns.forEach(addProductRemoveBtn => {
                  addProductRemoveBtn.addEventListener("click", (e) => {
                      const productId = e.currentTarget.dataset.productId;
                      const retailCheckbox = document.querySelector(`.retail-checkboxs[data-id="${productId}"]`);
                      const productAddBtn = document.querySelector(`.product-add-btn[data-product-id='${productId}']`)
                      if (retailCheckbox) {
                          retailCheckbox.checked = false
                          productAddBtn.dataset.retailPrice = false
                      }
                      // remove product from pList
                      pList = pList.filter(p => p.id !== parseInt(productId));
                      ids = ids.filter(id => id !== parseInt(productId))
                      // remaining means - অবশিষ্ট
                      removeSelectedProducts(Number(productId))
                      getRemainingProducts(pList)
                      e.currentTarget.classList.add('d-none')
                  })
              })
          }

        } else {
          productList.innerHTML = '';
          productList.innerHTML += `<tr class="text-center"><td colspan="3">Not found products.</td></tr>`;
        }
      } catch (err) {
        console.error('error:', err)
      }
    }

    function getQtyForProductAdd(p, p_retail_price_status)
    {
      let stock = p.stock_w_type === "none" ? parseInt(p.stock) : Number(parseFloat(p.stock_w).toFixed(3));
      return p.purchase_limit > stock
      ? p_retail_price_status ? stock > 1 ? 1 : stock : stock
      : p.stock_w_type === "none"
      ? p_retail_price_status ? 1 : parseInt(p.purchase_limit)
      : p_retail_price_status ? 1 : Number(parseFloat(p.purchase_limit).toFixed(3));
    }

    function setSalesOrderProducts(products) {
      ids = products.map(p => p.id)
      setSelectedProducts(ids)
      setElementForSelectdedProductsWhenLoadThePage(products)
      billedCardProductList(products)
    }

    async function getCustomers(search, page, perPage, customerId) {
      const customerList = document.getElementById('customerList');
      if (!customerList) return;
      if (customer_loading) customerList.innerHTML = `<tr class="text-center"><td colspan="2">Loading...</td></tr>`;

        let baseUrl = window.location.origin
        const res = await axios.get(`${baseUrl}/admin/customers/get?search=${search}&page=${page}&per_page=${perPage}&id=${customerId}`)
        customers = res.data.data;
        links = res.data.links;
        let last_page = res.data.last_page;
        let current_page = res.data.current_page;
        let per_page = res.data.per_page;
        let data_entries = res.data.total;
        customer_loading = false

        if (customers.length > 0) {
          customerList.innerHTML = '';
          customers.forEach(customer => {
            if (!customerList) return; // safety check
            customerList.innerHTML += `<tr>`
              + `<td>`
              + `<div class="d-flex gap-2" style="width: 250px;">`
              + `<div class="border shadow rounded-circle" style="padding: 0.65rem;">`
              + `<i class="fa-solid fa-user-tie fs-3"></i>`
              + `</div>`
              + `<div>`
              + `<div class="fw-semibold text-dark-emphasis" style="padding-top: 0.15rem;">${customer.name}</div>`
              + `<div class="text-muted">Phone No. ${customer.phone}</div>`
              + `</div>`
              + `</div>`
              + `</td>`
              + `<td>`
              + `<button type="button" data-customer-id="${customer.id}" data-customer-name="${customer.name}" data-customer-phone="${customer.phone}" data-customer-address="${customer.address}" class="btn btn-sm btn-light rounded-3 border shadow-sm fs-5 fw-semibold customer-add-btn"><i class="fa-solid fa-plus"></i></button>`
              + `</td>`
              + `</tr>`;
          });

          const customerPage = document.getElementById('customer-page');
          const customerPerPage = document.getElementById('customer-per-page');
          const customerTotalPage = document.getElementById('customer-total-page');
          customerPage.innerText = current_page;
          customerPerPage.innerText = per_page;
          customerTotalPage.innerText = data_entries;
          const customerPagination = document.getElementById('customer-pagination');
          customerPagination.innerHTML = '';
          links.forEach((link, i) => {
            if (link.label === "&laquo; Previous") {
              customerPagination.innerHTML += `<li class="page-item prev ${link.url === null ? 'disabled' : ''}">`
                + `<button type="button" class="page-link customer-prev-btn" style="cursor: pointer;">&laquo;</button>`
                + `</li>`
            }
            if (link.url !== null) {
              if (link.label !== 'Next &raquo;' && link.label !== '&laquo; Previous') {
                //console.log(link)
                customerPagination.innerHTML += `<li class="page-item ${link.active === true ? 'active' : ''}">`
                  + `<button type="button" class="page-link customer-page-btn" data-page="${link.label}" style="cursor: pointer;">${link.label}</button>`
                  + `</li>`
              }
            }
            if (link.label === "...") {
              customerPagination.innerHTML += `<li class="page-item ${link.url === null ? 'disabled' : ''}">`
                + `<button type="button" class="page-link" >...</button>`
                + `</li>`
            }
            if (link.label === "Next &raquo;") {
              customerPagination.innerHTML += `<li class="page-item next ${link.url === null ? 'disabled' : ''}" >`
                + `<button type="button" class="page-link customer-next-btn" data-page="" style="cursor: pointer;">&raquo;</button>`
                + `</li>`
            }
          })
          const nextBtn = document.querySelector('.customer-next-btn')
          const prevBtn = document.querySelector('.customer-prev-btn')
          const pageBtn = document.querySelectorAll('.customer-page-btn')

          pageBtn.forEach((btn) => {
            btn.addEventListener('click', (e) => {
              const pageNum = Number(e.currentTarget.dataset.page)
              customer_page = pageNum
              getCustomers(customer_search, pageNum, customer_per_page, cmId)
            })
          })
          nextBtn.addEventListener('click', () => {
            if (customer_page !== last_page) {
              customer_page++
              getCustomers(customer_search, customer_page, customer_per_page, cmId)
            }
          })
          prevBtn.addEventListener('click', () => {
            if (customer_page > 0) {
              customer_page--
              getCustomers(customer_search, customer_page, customer_per_page, cmId)
            }
          })

          // add customer in billed card
          if (customer) setCustomer(customer)
          const customerAddBtns = document.querySelectorAll(".customer-add-btn")
          customerAddBtns.forEach((btn) => {
            btn.addEventListener("click", (e) => {
              const customerId = e.currentTarget.dataset.customerId;
              const customerName = e.currentTarget.dataset.customerName;
              const customerPhone = e.currentTarget.dataset.customerPhone;
              const customerAddress = e.currentTarget.dataset.customerAddress;
              const customer = {
                id: customerId,
                name: customerName,
                phone: customerPhone,
                address: customerAddress
              }
              setCustomer(customer)
            })
          })
        } else {
          customerList.innerHTML = '';
          customerList.innerHTML += `<tr class="text-center"><td colspan="2">Not found customers.</td></tr>`;
        }
    }

    function setCustomer(customer) {
      const customerAddBtns = document.querySelectorAll(".customer-add-btn")
      const CId = document.getElementById("CId");
      const CName = document.getElementById("CName");
      const CPhone = document.getElementById("CPhone");
      const CAddress = document.getElementById("CAddress");
      CName.innerText = customer.name;
      CPhone.innerText = customer.phone;
      if (customer.address) CAddress.innerText = customer.address;
      else CAddress.innerText = "...";
      CId.value = customer.id;
      if (customerAddBtns.length > 0) {
        customerAddBtns.forEach((btn) => {
          const btnCustomerId = Number(btn.dataset.customerId)
          const cId = Number(customer.id)
          if (btnCustomerId === cId) {
            btn.classList.remove("btn-light")
            btn.classList.add("btn-primary")
            btn.firstElementChild.remove();
            btn.innerHTML = `<i class="fa-solid fa-check"></i>`
          } else {
            btn.classList.remove("btn-primary")
            btn.classList.add("btn-light")
            btn.lastElementChild.remove();
            btn.innerHTML = `<i class="fa-solid fa-plus"></i>`
          }
        })
      }
    }

    async function getProductsForBilledCard() {
      const res = await axios.get("/admin/products/for-order-billed/get")
      productsForBilledCard = res.data;
    }

    document.addEventListener("DOMContentLoaded", () => {
      const modalEl = document.getElementById('customerAddModal');
      const customerModal = new bootstrap.Modal(modalEl, { backdrop: false }); // disable default backdrop
      const customerModalBackdrop = document.querySelector('.add-customer-modal-backdrop')
      const btnCloses = document.querySelectorAll('.add-customer-modal-close-btn')
      const customerSubmitBtn = document.getElementById('customer-submit-btn')
      const customerBtnSpinnerBorder = document.getElementById('customer-btn-spinner-border')
      // inputs
      const nameInput = document.getElementById('name')
      const phoneInput = document.getElementById('phone')
      const addressInput = document.getElementById('address')
      const nameError = document.getElementById('name-error')
      const phoneError = document.getElementById('phone-error')
      const addressError = document.getElementById('address-error')

      document.getElementById('new-customer-add-btn').addEventListener('click', () => {
        customerModal.show();
        customerModalBackdrop.classList.add("show")
      });

      // Remove backdrop when modal hides
      modalEl.addEventListener('hidden.bs.modal', () => {
        const existingBackdrop = document.querySelector('.modal-backdrop');
        if (existingBackdrop) existingBackdrop.remove();
      });

      btnCloses.forEach((btn) => {
        btn.addEventListener("click", () => {
          customerModal.hide();
          customerModalBackdrop.classList.remove("show")
        })
      })

      customerSubmitBtn.addEventListener('click', async () => {
        customerBtnSpinnerBorder.classList.remove('d-none')
        customerSubmitBtn.classList.add('disabled')
        try {
          const res = await axios.post("/admin/sales/order/add/new/customer", {
            name: nameInput?.value || "",
            phone: phoneInput?.value || "",
            address: addressInput?.value || "",
          })
          if (res.data.status === "success") {
            getCustomers(customer_search, customer_page, customer_per_page, cmId)
            processNotify(res.data.msg)
            customerModal.hide();
            customerModalBackdrop.classList.remove("show")
            customerBtnSpinnerBorder.classList.add('d-none')
            customerSubmitBtn.classList.remove('disabled')
          }
        } catch (error) {
          if (error.response.data.errors.name) nameError.innerText = error.response.data.errors.name
          if (error.response.data.errors.phone) phoneError.innerText = error.response.data.errors.phone
          if (error.response.data.errors.address) addressError.innerText = error.response.data.errors.address
          if (!error.response.data.errors.name && !error.response.data.errors.phone && !error.response.data.errors.address) processNotify("Something went wrong! Please try again later.", "error")

          customerBtnSpinnerBorder.classList.add('d-none')
          customerSubmitBtn.classList.remove('disabled')
        }
      })
    });

    document.addEventListener("DOMContentLoaded", () => {
      const orderDateInput = document.getElementById('order-date')
      const dueDateInput = document.getElementById('due-date')
      const notesTextArea = document.getElementById('notes')
      const invoiceDate = document.getElementById('invoiceDate')
      const paidAmountInput = document.getElementById("paid-amount-input")
      const paidAmountEditIcon = document.getElementById("paid-amount-edit-icon")
      const paidAmountCloseIcon = document.getElementById("paid-amount-close-icon")

      let current_date = new Date().toISOString().split('T')[0]
      orderDateInput.value = current_date;
      invoiceDate.innerText = current_date;
      if (!order?.notes) notesTextArea.value = '';

      dueDateInput.addEventListener('change', (e) => {
        const dueData = formatDateToReadable(e.currentTarget.value);
        notesTextArea.value = `আমি বাকী টাকা টা এই তারিখে: ${dueData} পরিশোধ করবো। إِنْ شَاءَ ٱللّٰهُ “ইনশাআল্লাহ”`;
      })

      paidAmountInput.addEventListener('input', (e) => {
        let value = e.currentTarget.value;
        // Allow only numbers and one decimal
        value = value.replace(/[^0-9.]/g, '');
        value = value.replace(/(\..*)\./g, '$1');
        // Limit to 2 decimal places
        if (value.includes('.')) {
          const [intPart, decPart] = value.split('.');
          value = intPart + '.' + decPart.slice(0, 2);
        }
        e.currentTarget.value = value;
        setPaidAndDueAmount(value);
      });
    })

    function setPaidAndDueAmount(value) {
      const paidAmount = document.getElementById("paid-amount");
      const dueAmount = document.getElementById("due-amount");
      const dueAmountInput = document.getElementById("due-amount-input");
      if (paidAmount) {
        const num = parseFloat(value);
        if (!isNaN(num)) {
          d_amount = total - num;
          // console.log(d_amount);
          paidAmount.innerText = num.toFixed(2);
          dueAmount.innerText = d_amount.toFixed(2);
          dueAmountInput.value = d_amount.toFixed(2);
        } else {
          paidAmount.innerText = "0.00"; // fallback if input is empty
          dueAmount.innerText = "0.00";
          dueAmountInput.value = "0.00";
        }
      }
    }

    function billedCardProductList(list) {
      const invoiceList = document.getElementById('invoiceList');
      const orderDateInput = document.getElementById('order-date')
      const dueDateInput = document.getElementById('due-date')
      const paymentStatusInput = document.getElementById('payment_status')
      const statusInput = document.getElementById('status')
      const memoNoInput = document.getElementById('memo_no')
      const notesTextArea = document.getElementById('notes')
      const oldSelectedProductsBtn = document.getElementById('old-selected-products-btn')

      if (list.length > 0) {
        invoiceList.innerHTML = '';
        showingTotalPrice(list);
        // order information fields enable
        orderDateInput.disabled = false;
        paymentStatusInput.disabled = false;
        statusInput.disabled = false;
        memoNoInput.disabled = false;
        notesTextArea.disabled = false;
        oldSelectedProductsBtn.classList.remove('d-none')

        list.forEach(product => {
          if (Number(product.qty) > product.stock || product.purchase_limit > product.stock) {
            stockProducts.push({
              id: product.id,
              qty: product.qty
            });
          }

          invoiceList.innerHTML += `<tr class="item-row">`
            + `<td>`
            + `<div class="d-flex gap-2 align-items-center" style="width: 265px;">`
            + `${product.image ? `<img src="${product.image}" alt="${product.name}" width="30" height="30" class="rounded-circle">`  : `<div class="border shadow rounded-circle d-flex justify-content-center align-items-center" style="width: 30px; height: 30px;">`
            + `<i class="fa-solid fa-image fs-5"></i>`
            + `</div>`}`
            + product.name
            + `</div>`
            + `</td>`
            + `<td><div class="d-flex align-items-center" style="width: 165px;"><span class="bdt">৳</span>${product.discount_price ? product.discount_price : product.price} ${product.discount_price ? `<span class="ms-1 text-muted text-decoration-line-through" style="font-size: .67rem;">${product.price}</span>` : ''}</div></td>`
            + `<td>`
            + `<div class="input-group" style="width: 165px;">`
            + `<button type="button" class="btn btn-light border prev-btn ${product.purchase_limit <= 0.01 ? 'disabled' : ''}" data-id="${product.id}"><i class="fa-solid fa-minus"></i></button>`
            + `<input type="text" class="form-control price-input text-center qty-increase-and-decrease" placeholder="1" data-id="${product.id}" data-price="${product.price}" data-discount-price="${product.discount_price}" data-purchase-limit="${product.purchase_limit}" data-stock="${product.stock}" data-stock-w-type="${product.stock_w_type}" data-retail-price-status="${product.retail_price_status}" data-product-qty="${product?.qty}" data-stock-low="${Number(product.qty) > product.stock ? true : false}" data-order-status="${product.order_status ? true : false}" value="${product.stock_w_type !== 'none'
           ? product.stock_w_type === 'ft' || product.stock_w_type === 'yard' || product.stock_w_type === 'm'
           ? parseFloat(product.qty).toFixed(2)
           : product.qty
           : parseInt(product.qty)}" />`
            + `<button type="button" class="btn btn-light border next-btn ${nextBtnDisabled(product.stock, product.purchase_limit, product.qty)}" data-id="${product.id}"><i class="fa-solid fa-plus"></i></button>`
            + `</div>`
            + `</td>`
            + `<td><div class="d-flex align-items-center"><span class="bdt">৳</span><span class="item-total" data-id="${product.id}">${product?.total_price ? product?.total_price : product.discount_price ? (product.discount_price * product.purchase_limit).toFixed(2) : (product.price * product.purchase_limit).toFixed(2)}</span></div></td>`
            + `<td><button type="button" class="btn btn-sm btn-outline-danger product-remove-btn" data-id="${product.id}"><i class="fas fa-trash"></i></button></td>`
            + `</tr>`;
        });

        const qtyInputs = document.querySelectorAll('.qty-increase-and-decrease');
        qtyInputs.forEach(input => {
          input.addEventListener("input", (e) => {
            writeOnlyNumberAndDecimalInput(e.currentTarget);
            let stockWType = e.currentTarget.dataset.stockWType;
            if (stockWType === 'ft') writeOnlyNumberAndDecimalWithFtOrInchiCalCulateInput(e.currentTarget, 2, 11)
            if (stockWType === 'yard') writeOnlyNumberAndDecimalWithFtOrInchiCalCulateInput(e.currentTarget, 2, 35)
            if (stockWType === 'm') writeOnlyNumberAndDecimalWithFtOrInchiCalCulateInput(e.currentTarget, 2, 38)
            let stock = 0;
            let qtyValue = 0;
            let inputValue = 0;
            if (stockWType === "none")inputValue = inputWritingValueInt(e.currentTarget);
            else inputValue = Number(e.currentTarget.value);
            if (Number.isInteger(inputValue))qtyValue = parseInt(e.currentTarget.value);
            else qtyValue = parseFloat(e.currentTarget.value);
            let inputStock = Number(e.currentTarget.dataset.stock);
            if (Number.isInteger(inputStock))stock = parseInt(inputStock);
            else stock = parseFloat(inputStock);
            const stockLow = e.currentTarget.dataset.stockLow === "true" ? true : false;
            const orderStaus = e.currentTarget.dataset.orderStatus === "true" ? true : false;
            const productQty = Number(e.currentTarget.dataset.productQty);
            //console.log(stockWType !== "none");
            if (orderStaus) {
              if (stockWType === "ft")stock = addLastInch(stock, productQty)
              if (stockWType === "yard")stock = addLastInch(stock, productQty, 36)
              if (stockWType === "m")stock = addLastInch(stock, productQty, 39)
              else stock += productQty
              //if (stockWType !== "none")stock = Number(stock.toFixed(3))
              if (stockWType === "kg")stock = Number(stock.toFixed(3))
              else if (stockWType === "ft" || stockWType === "yard" || stockWType === "m")stock = Number(parseFloat(stock).toFixed(2))
            }


            let el = e.currentTarget;
            let retailPriceStatus = e.currentTarget.dataset.retailPriceStatus;
            let purchaseLimitStr = e.currentTarget.dataset.purchaseLimit;
            let purchaseLimit = Number(purchaseLimitStr) || 0;
            let priceStr = e.currentTarget.dataset.price;
            let priceFloat = Number(priceStr).toFixed(2);
            let price = priceFloat ? Number(priceFloat) : 0;
            let discountPrice = parseInt(e.currentTarget.dataset.discountPrice) || 0;
            let id = e.currentTarget.dataset.id;
            let stopCountValue = 0;

            const nextBtn = document.querySelector(`.next-btn[data-id="${id}"]`);
            const prevBtn = document.querySelector(`.prev-btn[data-id="${id}"]`);
            if (!stockLow && stockWType === "none" && qtyValue <= 1) {
                qtyValue = 1;
                el.value = 1;
                prevBtn.classList.add('disabled');
            } else if (!stockLow && stockWType === "kg" && qtyValue <= 0.001) {
                qtyValue = 0.001;
                el.value = 0.001;
                prevBtn.classList.add('disabled');
            } else if (!stockLow && stockWType === "ft" && qtyValue <= 0.01) {
                qtyValue = 0.01;
                el.value = 0.01;
                prevBtn.classList.add('disabled');
            } else if (stockLow === true) {
                  if (stockWType === "none" && qtyValue <= 1) {
                    qtyValue = 1;
                    el.value = 1;
                    prevBtn.classList.add('disabled');
                } else if (stockWType === "kg" && qtyValue <= 0.001) {
                    qtyValue = 0.001;
                    el.value = 0.001;
                    prevBtn.classList.add('disabled');
                } else if ((stockWType === "ft" || stockWType === "yard") && qtyValue <= 0.01) {
                    qtyValue = 0.01;
                    el.value = 0.01;
                    prevBtn.classList.add('disabled');
                }else prevBtn.classList.remove('disabled');
            } else prevBtn.classList.remove('disabled');

            // else if (!stockLow && stockWType !== "none" && qtyValue <= 0.001) {
            //    qtyValue = 0.001;
            //    el.value = 0.001;
            //    prevBtn.classList.add('disabled');
            //}
            //else if (!strNumToBool(retailPriceStatus) && !stockLow && qtyValue <= purchaseLimit) {
            //   if (stock > purchaseLimit) {
            //        qtyValue = purchaseLimit;
            //        el.value = purchaseLimit;
            //        prevBtn.classList.add('disabled');
            //   } else {
            //        qtyValue = stock;
            //        el.value = stock;
            //        e.currentTarget.dataset.stockLow = true
            //        nextBtn.classList.add('disabled');
            //   }
            //}

            //console.log(qtyValue, stock);
            if (qtyValue > stock) {
                qtyValue = stock;
                el.value = stock;
                nextBtn.classList.add('disabled');
            }else nextBtn.classList.remove('disabled');

            // * item total calculation
            if (qtyValue > 0 && qtyValue <= stock) {
                //let pPrice = discountPrice ? discountPrice : price;
                let pPrice = price;
                let stock_w_type = '';
                qtyValue = stockWType === "none" ? parseInt(qtyValue) : parseFloat(qtyValue);
                if (stockWType === 'ft' || stockWType === 'yard' || stockWType === 'm')stock_w_type = stockWType

                addProductTotalPrice(id, qtyValue, pPrice, stock_w_type)
            }

          });
        });

        const nextBtns = document.querySelectorAll(`.next-btn`);
        const prevBtns = document.querySelectorAll(`.prev-btn`);
        nextBtns.forEach(nextBtn => {
          nextBtn.addEventListener('click', (e) => {
            const el = e.currentTarget;
            const id = e.currentTarget.dataset.id;
            let qtyValue = 0;
            let stock = 0;
            const qtyInput = document.querySelector(`.qty-increase-and-decrease[data-id="${id}"]`);
            let qtyInputValue = Number(qtyInput);
            qtyValue = Number.isInteger(qtyInputValue) ? parseInt(qtyInput.value) : parseFloat(qtyInput.value);
            let stockWType = qtyInput.dataset.stockWType;
            let inputStock = Number(qtyInput.dataset.stock);
            if (stockWType === "none")stock = parseInt(inputStock);
            else stock = parseFloat(inputStock);
            const stockLow = qtyInput.dataset.stockLow === "true" ? true : false;
            const orderStaus = qtyInput.dataset.orderStatus === "true" ? true : false;
            const productQty = Number(qtyInput.dataset.productQty);

            if (orderStaus) {
              if (stockWType === "ft")stock = addLastInch(stock, productQty)
              else if (stockWType === "yard")stock = addLastInch(stock, productQty, 36)
              else if (stockWType === "m")stock = addLastInch(stock, productQty, 39)
              else stock += productQty
              if (stockWType === "kg")stock = Number(stock.toFixed(3))
              else if (stockWType === "ft" || stockWType === "yard" || stockWType === "m")stock = Number(parseFloat(stock).toFixed(2))
            }


            let priceStr = qtyInput.dataset.price;
            let priceFloat = Number(priceStr).toFixed(2);
            let price = priceFloat ? Number(priceFloat) : 0;
            //let discountPrice = parseInt(qtyInput.dataset.discountPrice) || 0;
            let pId = qtyInput.dataset.id;
            //let pPrice = discountPrice ? discountPrice : price;
            let pPrice = price;

            if (qtyValue >= stock) {
                qtyValue = stock;
                qtyInput.value = stock;
                el.classList.add('disabled');
                return;
            } else {
                if (stockWType !== "none"){
                  if (stockWType === "kg")qtyValue = parseFloat((qtyValue + 0.001).toFixed(3))
                  else if(stockWType === "ft")qtyValue = incrementInputQtyUsingNextBtn(qtyValue);
                  else if(stockWType === "yard")qtyValue = incrementInputQtyUsingNextBtn(qtyValue, 0.35);
                  else if(stockWType === "m")qtyValue = incrementInputQtyUsingNextBtn(qtyValue, 0.38);
                }
                else qtyValue++;
                qtyInput.value = qtyValue;
                const prevBtn = document.querySelector(`.prev-btn[data-id="${pId}"]`);
                // console.log(prevBtn);
                prevBtn.classList.remove('disabled');
            }

            let stock_w_type = ''
            if (stockWType === 'ft' || stockWType === 'yard' || stockWType === 'm')stock_w_type = stockWType
            if (qtyValue <= stock) addProductTotalPrice(pId, qtyValue, pPrice, stock_w_type)
          })
        })
        prevBtns.forEach(prevBtn => {
          prevBtn.addEventListener("click", (e) => {
            const el = e.currentTarget;
            const id = el.dataset.id;
            let qtyValue = 0;
            let stock = 0;
            const qtyInput = document.querySelector(`.qty-increase-and-decrease[data-id="${id}"]`);
            let qtyInputValue = Number(qtyInput);
            let stockWType = qtyInput.dataset.stockWType;
            qtyValue = Number.isInteger(qtyInputValue) ? parseInt(qtyInput.value) : parseFloat(qtyInput.value);
            let inputStock = Number(qtyInput.dataset.stock);
            if (stockWType === "none")stock = parseInt(inputStock);
            else stock = parseFloat(inputStock);
            const stockLow = qtyInput.dataset.stockLow === "true" ? true : false;
            const productQty = Number(qtyInput.dataset.productQty);
            //console.log(stockLow);
            if (stockLow && productQty > stock) {
              stock += productQty
              if (stockWType !== "none")stock = Number(stock.toFixed(3))
            }


            let retailPriceStatus = qtyInput.dataset.retailPriceStatus;
            let purchaseLimitStr = qtyInput.dataset.purchaseLimit;
            let purchaseLimit = Number(purchaseLimitStr) || 0;
            let priceStr = qtyInput.dataset.price;
            let priceFloat = Number(priceStr).toFixed(2);
            let price = priceFloat ? Number(priceFloat) : 0;
            let discountPrice = parseInt(qtyInput.dataset.discountPrice) || 0;
            let pId = qtyInput.dataset.id;

            if (stockWType === "none" && qtyValue <= 1) {
                qtyValue = 1;
                el.value = 1;
                el.classList.add('disabled');
                return;
            } else if (stockWType !== "none" && qtyValue <= 0.001) {
                qtyValue = 0.001;
                el.value = 0.001;
                el.classList.add('disabled');
                return;
            } else if (!strNumToBool(retailPriceStatus) && !stockLow && qtyValue <= purchaseLimit) {
                qtyValue = purchaseLimit;
                el.value = purchaseLimit;
                qtyInput.value = purchaseLimit;
                el.classList.add('disabled');
                return;
            } else {
                const nextBtn = document.querySelector(`.next-btn[data-id="${id}"]`);
                nextBtn.classList.remove('disabled');
                if (stockWType ==="none") {
                    qtyValue--;
                    qtyInput.value = qtyValue;
                    if (qtyValue === 1) el.classList.add('disabled')
                    else el.classList.remove('disabled')
                }else{
                    if (stockWType === 'ft' || stockWType === 'yard' || stockWType === 'm') {
                        if(stockWType === 'ft')qtyValue = decrementInputQtyUsingPrevBtn(qtyValue);
                        if(stockWType === 'yard')qtyValue = decrementInputQtyUsingPrevBtn(qtyValue, 35);
                        if(stockWType === 'm')qtyValue = decrementInputQtyUsingPrevBtn(qtyValue, 38);
                        qtyInput.value = qtyValue;
                        if (qtyValue === 0.01) el.classList.add('disabled')
                        else el.classList.remove('disabled')
                    } else {
                        qtyValue = parseFloat((qtyValue - 0.001).toFixed(3))
                        qtyInput.value = qtyValue;
                        if (qtyValue === 0.001) el.classList.add('disabled')
                        else el.classList.remove('disabled')
                    }
                }
            }

            //let pPrice = discountPrice ? discountPrice : price;
            let pPrice = price;
            let qtyValueCondition = 0;
            let stock_w_type = '';
            if (stockWType === "none")qtyValueCondition = qtyValue >= 1
            else {
              if (stockWType === 'ft' || stockWType === 'yard' || stockWType === 'm')qtyValueCondition = qtyValue >= 0.01
              else qtyValueCondition = qtyValue >= 0.001
            }
            if (stockWType === "ft" || stockWType === "yard" || stockWType === "m")stock_w_type = stockWType
            if (qtyValueCondition) addProductTotalPrice(pId, qtyValue, pPrice, stock_w_type)
          })
        })

        const productRemoveBtns = document.querySelectorAll(".product-remove-btn")
        productRemoveBtns.forEach(productRemoveBtn => {
          productRemoveBtn.addEventListener("click", (e) => {
            const productId = e.currentTarget.dataset.id;
            // remove product from pList
            pList = pList.filter(p => p.id !== parseInt(productId));
            ids = ids.filter(id => id !== parseInt(productId));
            // remaining means - অবশিষ্ট
            removeSelectedProducts(Number(productId))
            getRemainingProducts(pList)
          })
        })
      } else {
        setProductsInput([]);
        invoiceList.innerHTML = '';
        dueDateInput.value = ''
        paymentStatusInput.value = ''
        statusInput.value = ''
        memoNoInput.value = ''
        notesTextArea.value = ''
        orderDateInput.disabled = true;
        dueDateInput.disabled = true;
        paymentStatusInput.disabled = true;
        statusInput.disabled = true;
        memoNoInput.disabled = true;
        notesTextArea.disabled = true;
        oldSelectedProductsBtn.classList.add('d-none')
      }
    }

    function inputWritingValueInt(input)
    {
        let v = input.value.replace(/[^0-9]/g, '');
        // 2. remove leading zeros (keep single 0)
        v = v.replace(/^0+/, '') || '0';
        input.value = v;
        return parseInt(v);
    }

    function nextBtnDisabled(stock, purchase_limit, qty)
    {
        if (purchase_limit > stock) {
            if (stock === qty)return "disabled"
            else return ""
        } else {
            if (purchase_limit >= stock)return "disabled"
            else return ""
        }
    }

    function setElementForSelectdedProductsWhenLoadThePage(sProducts)
    {
          if (sProducts.length > 0) {
            sProducts.forEach((product) => {
                let retailPriceStatus = Boolean(product.retail_price_status);
                const retailCheckbox = document.querySelector(`.retail-checkboxs[data-id="${product.id}"]`);
                const RetailPrice = document.querySelector(`.retail-price[data-id="${product.id}"]`);
                const MainPrice = document.querySelector(`.main-price[data-id="${product.id}"]`);
                const minPurchase = document.querySelector(`.min-purchase[data-id="${product.id}"]`);

                if (retailPriceStatus) {
                    if (retailCheckbox)retailCheckbox.checked = retailPriceStatus;
                    if(RetailPrice)RetailPrice.classList.remove('d-none')
                    if(MainPrice)MainPrice.classList.add('d-none')
                    if(minPurchase)minPurchase.classList.add('d-none')
                }
            })
          }
    }
    function setSelectedProducts(productIds) {
      if (productIds.length > 0) {
        productIds.forEach(id => {
          const productAddBtn = document.querySelectorAll(`.product-add-btn[data-product-id='${id}']`);
          const addProductRemoveBtn = document.querySelector(`.add-product-remove-btn[data-product-id='${id}']`);
          if (addProductRemoveBtn)addProductRemoveBtn.classList.remove('d-none')

          productAddBtn.forEach(btn => {
            if (btn) {
              btn.classList.remove("btn-light")
              btn.classList.add("btn-primary")
              btn.firstElementChild.remove()
              btn.innerHTML = `<i class="fa-solid fa-check"></i>`
            }
          })
        });
      }
    }
    function removeSelectedProducts(productId) {
      const productAddBtn = document.querySelector(`.product-add-btn[data-product-id='${productId}']`);
      const retailPriceEl = document.querySelector(`.retail-price[data-id='${parseInt(productId)}']`)
      const mainPriceEl = document.querySelector(`.main-price[data-id='${parseInt(productId)}']`)
      const minPurchaseEl = document.querySelector(`.min-purchase[data-id="${parseInt(productId)}"]`);
      const retailCheckbox = document.querySelector(`.retail-checkboxs[data-id="${parseInt(productId)}"]`);
      const addProductRemoveBtn = document.querySelector(`.add-product-remove-btn[data-product-id='${productId}']`)

      if (productAddBtn) {
        productAddBtn.classList.remove("btn-primary")
        productAddBtn.classList.add("btn-light")
        productAddBtn.lastElementChild.remove()
        productAddBtn.innerHTML = `<i class="fa-solid fa-plus"></i>`
        if(retailCheckbox)retailCheckbox.checked = false
        if (retailPriceEl)retailPriceEl.classList.add('d-none')
        if (mainPriceEl)mainPriceEl.classList.remove('d-none')
        if (minPurchaseEl)minPurchaseEl.classList.remove('d-none')
        if (addProductRemoveBtn)addProductRemoveBtn.classList.add('d-none')
      }
    }


    function setProductsInput(list) {
      const productsInput = document.getElementById("products-input");
      productsInput.value = JSON.stringify(list);
    }

    function getRemainingProducts(list) {
      billedCardProductList(list)
    }

    function addProductTotalPrice(id, qty, price, type = '') {
       let total_price = 0;
      if (type === '')total_price = qty * price;
      else if (type === 'ft')total_price = calculatePriceFromAnyToInches(qty, price)
      else if (type === 'yard')total_price = calculatePriceFromAnyToInches(qty, price, 36)
      else if (type === 'm')total_price = calculatePriceFromAnyToInches(qty, price, 39)

      let itemTotal = document.querySelector(`.item-total[data-id="${id}"]`);
      if (itemTotal) itemTotal.innerText = total_price.toFixed(2);

      const product = pList.find(p => p.id === parseInt(id));
      if (product) {
        product.total_price = total_price.toFixed(2);
        product.qty = qty;
      }
      showingTotalPrice(pList);
    }

    function showingTotalPrice(selectProducts) {
      const t = document.getElementById("total");
      const tAInput = document.getElementById("total-amount-input");
      const stAInput = document.getElementById("sub-total-amount-input");
      const paidAmountInput = document.getElementById("paid-amount-input")
      if (selectProducts.length > 0) {
        setProductsInput(selectProducts.map(p => ({
          id: p.id,
          price: p.price,
          discount_price: p.discount_price,
          qty: p.qty,
          total_price: p.total_price,
          stock_w_type: p.stock_w_type,
          retail_price_status: p.retail_price_status,
          tax: p.tax
        })));
      }else{
        setProductsInput([])
      }

      const subTotal = selectProducts.reduce((acc, product) => {
        return acc + (parseFloat(product.total_price) || 0);
      }, 0);
      subtotal = subTotal
      total = subTotal

      t.innerText = total.toFixed(2);
      tAInput.value = total.toFixed(2);
      stAInput.value = subTotal.toFixed(2);
      if (total < Number(paidAmountInput.value))setPartialDue(total);
      setDueAmount(total);

      if (selectProducts.length <= 0) {
          t.innerText = "0.00";
          tAInput.value = "";
          stAInput.value = "";
      };
    }
    function setDueAmount(total_price) {
      const paidAmountInput = document.getElementById("paid-amount-input");
      const dueAmountInput = document.getElementById("due-amount-input");
      const dueAmountSpan = document.getElementById("due-amount");
      if (status !== "cancelled") {
        let total = total_price - paidAmountInput.value
        dueAmountSpan.innerText = total.toFixed(2);
        dueAmountInput.value = total.toFixed(2);
      }
    }

    function setPartialDue(amount)
    {
        const paidAmountInput = document.getElementById("paid-amount-input");
        const paidAmountSpan = document.getElementById("paid-amount");
        paidAmountInput.value = amount.toFixed(2);
        paidAmountSpan.innerText = amount.toFixed(2);
    }

    function resetProductAddBtnInSelectedProductsTable() {
      const productAddBtns = document.querySelectorAll(".product-add-btn")
      if (productAddBtns.length > 0) {
        productAddBtns.forEach(btn => {
          let productId = parseInt(btn.dataset.productId);
          const retailPriceEl = document.querySelector(`.retail-price[data-id='${parseInt(productId)}']`)
          const mainPriceEl = document.querySelector(`.main-price[data-id='${parseInt(productId)}']`)
          const minPurchaseEl = document.querySelector(`.min-purchase[data-id="${parseInt(productId)}"]`);
          const retailCheckbox = document.querySelector(`.retail-checkboxs[data-id="${parseInt(productId)}"]`);
          const addProductRemoveBtn = document.querySelector(`.add-product-remove-btn[data-product-id='${productId}']`)

          if (retailPriceEl)retailPriceEl.classList.add('d-none')
          if (mainPriceEl)mainPriceEl.classList.remove('d-none')
          if (minPurchaseEl)minPurchaseEl.classList.remove('d-none')
          if (retailCheckbox)retailCheckbox.checked = false
          if (addProductRemoveBtn)addProductRemoveBtn.classList.add('d-none')

          btn.classList.remove("btn-primary")
          btn.classList.add("btn-light")
          btn.lastElementChild.remove()
          btn.innerHTML = `<i class="fa-solid fa-plus"></i>`
        })
      }
    }
    function resetCustomerAddBtnInSelectedCustomersTable() {
      const customerAddBtns = document.querySelectorAll(".customer-add-btn")
      const CId = document.getElementById("CId");
      const CName = document.getElementById("CName");
      const CPhone = document.getElementById("CPhone");
      const CAddress = document.getElementById("CAddress");
      CId.value = "";
      CName.innerText = "";
      CPhone.innerText = "";
      CAddress.innerText = "";
      if (customerAddBtns.length > 0) {
        customerAddBtns.forEach(btn => {
          btn.classList.remove("btn-primary")
          btn.classList.add("btn-light")
          btn.lastElementChild.remove()
          btn.innerHTML = `<i class="fa-solid fa-plus"></i>`
        })
      }
    }

    function setSalesOrderInfo(order) {
      const orderDateInput = document.getElementById('order-date')
      const dueDateInput = document.getElementById('due-date')
      const notesTextArea = document.getElementById('notes')
      const paymentStatusSelectField = document.getElementById("payment_status");
      if (order) {
        let oDate = formatted_date_for_edit(order?.order_date);
        orderDateInput.value = oDate;
        if (order.due_date && order.payment_status === "due" || order.payment_status === "partial due") {
          dueDateInput.disabled = false;
          dueDateInput.value = formatted_date(order.due_date);
        }
        if (order.notes) notesTextArea.value = order.notes;
        paymentStatusSelectField.value = order.payment_status;
      }
    }

    function setForDueAndPartialDueAmount(list, status, payment_status, due_amount, paid_amount) {
      const paidAmountDiv = document.getElementById("paid-amount-div");
      const dueAmountEl = document.getElementById("due-amount-el");
      const paidAmountEl = document.getElementById("paid-amount-el");
      const dueDateInput = document.getElementById("due-date")
      const paidAmountInput = document.getElementById("paid-amount-input");
      const dueAmountInput = document.getElementById("due-amount-input");
      const paidAmountSpan = document.getElementById("paid-amount");
      const dueAmountSpan = document.getElementById("due-amount");

      if (list.length > 0) {
        if (payment_status === 'partial due' || payment_status === 'due') {
          dueDateInput.disabled = false
          paidAmountDiv.classList.remove('d-none')
          paidAmountEl.classList.remove('d-none')
          dueAmountEl.classList.remove('d-none')
          if (payment_status === "due")paidAmountInput.readOnly = true
          paidAmountInput.value = paid_amount;
          dueAmountInput.value = due_amount;
          paidAmountSpan.innerText = paid_amount;
          dueAmountSpan.innerText = due_amount;
        } else {
          dueDateInput.disabled = true
          paidAmountDiv.classList.add('d-none')
          paidAmountEl.classList.add('d-none')
          dueAmountEl.classList.add('d-none')
          paidAmountInput.value = "";
          dueAmountInput.value = "";
          paidAmountSpan.innerText = "0.00";
          dueAmountSpan.innerText = "0.00";
        }
      }
    }
  </script>
@endpush
