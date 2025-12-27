@extends('layouts.admin-layout')
@section('title', '- Products')

@push('style')
  <link rel="stylesheet" href="{{ asset('assets/css/datatable.css') }}" />
  <style>
  
    .dropdown-menu {
        min-width: 200px;
        max-height: 45vh;
        overflow-y: auto;
    }
    .bdt {
        font-family: 'Noto Sans Bengali', sans-serif;
        margin-right: 0.1rem;
    }
  </style>
@endpush

@section('content')
  <x-admin.breadcrumb :breadcrumbs="$breadcrumbs" :create_btn_name="'Create'" :btn_route="'admin.product.create'" />
  <x-admin.page-title title="Product" />

  <div class="row" style="margin-top: -0.75rem;">
    <div class="col-12">
      <div class="card rounded-4">
        <div class="">
          <div class="row">
            <div class="col-12">

              <div class="datatable-wrapper">
                <!-- Header -->
                <x-admin.products-datatable-header :item_types="$item_types" :brands="$brands" :categories="$categories" :sub_categories="$sub_categories" />

                <!-- Table -->
                <div class="table-responsive">
                  <table class="table datatable-table" id="dataTable">
                    <x-admin.thead :theadColumns="$theadColumns" />
                    <tbody id="table-tbody">

                    </tbody>
                  </table>
                </div>
                <form id="csrf-form" style="display: none;">
                  @csrf
                </form>
                <!-- Footer -->
                <x-admin.datatable-footer />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@push('script')
  @if (Session::has('status'))
    <x-alert :msg="Session::get('msg')" :status="Session::get('status')" />
  @endif
  {{-- <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script> --}}
  <script>
    let deleteOptionsStr = "{{ $setting->delete_options }}";
    let authUser = @json(auth()->user());
    let authUserRole = authUser.role;
    let deleteOptions = Number(deleteOptionsStr);
    let deleteOptionIsLock = deleteOptions !== 1 ? false : true;
    //let deleteOptionIsLock = authUser.role === "owner" || deleteOptions !== 1 ? false : true;
    
    function dedent(str) {
      const lines = str.split('\n');
      const minIndent = lines.reduce((min, line) => {
        if (line.trim() === '') return min;
        const match = line.match(/^(\s*)/);
        return Math.min(min, match[1].length);
      }, Infinity);
      return lines.map(line => line.slice(minIndent)).join('\n');
    }

    function formatNumber(num, type = '') {
        // Convert input to a number safely
        num = parseFloat(num);
        if (type === 'ft' || type === 'yard' || type === 'm') {
            if (num < 1) return parseFloat((num * 100).toFixed(2)).toString();
            else return parseFloat((num).toFixed(2)).toString();
            //else return (num / 100).toString().replace(/\.0+$/, '');
        } else {
            // If value is less than 1 (like 0.99), return multiplied by 1000
            if (num < 1) {
                return (num * 1000).toString().replace(/\.0+$/, '');
            }
            
            // Otherwise, remove unnecessary decimals
            return parseFloat(num.toFixed(3)).toString();
        }
    }
    function kgOrGmOrFitOrYard(num, type = 'kg') {
        if (type === 'kg') {
            num = parseFloat(num);
            if (num < 1)return 'gm';
            else return 'kg'
        } else if (type === 'ft') {
            num = parseFloat(num);
            if (num < 1)return ' inchi';
            else return 'ft';
        } else if (type === 'yard') {
            num = parseFloat(num);
            if (num < 1)return ' inchi';
            else return 'yard';
        } else if (type === 'm') {
            num = parseFloat(num);
            if (num < 1)return ' inchi';
            else return 'm';
        } else return "nothings";
    }
    function stockType(data){
        if (data?.purchase_limit) {
            if (data?.stock_w_type !== 'none') {
                if (data?.stock_w_type === 'kg')return 'kg';
                if (data?.stock_w_type === 'ft')return 'ft';
                else return 'yard';
            } else return ' pcs';
        } else return '';
    }



    $(document).ready(function() {
    let datas = [];
    let loading = true;
    let links = [];
    let page = 1;
    let perPage = 5;
    let totalPage = 0;
    let search = '';
    let filter = '';
    let stockFilter = '';
    let itemTypeFilter = '';
    let brandFilter = '';
    let categoryFilter = '';
    let subCategoryFilter = '';
    let sortColumn = '';
    let sort = '';
    let pageItems = 0;
    let lastPage = 0;
    const baseUrl = window.location.origin;

    // Read URL param for stock filter
    const params = new URLSearchParams(window.location.search);
    stockFilter = params.get('sf') || '';

    function getData(searchVal, filterVal, stockVal, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortCol, sortType, currentPage, perPageVal) {
        //loading = true;
        renderTable();

        $.ajax({
            url: `${baseUrl}/admin/products/get`,
            method: 'GET',
            data: {
                search: searchVal,
                filter: filterVal,
                stock_filter: stockVal,
                item_type_filter: itemTypeFilter,
                brand_filter: brandFilter,
                category_filter: categoryFilter,
                sub_category_filter: subCategoryFilter,
                sort_column: sortCol,
                sort: sortType,
                page: currentPage,
                per_page: perPageVal
            },
            success: function(res) {
                datas = res.data;
                links = res.links;
                page = res.current_page;
                perPage = res.per_page;
                totalPage = res.total;
                pageItems = res.data.length;
                lastPage = res.last_page;
                loading = false;
                renderTable();
                renderPagination();
                updateStats();
            },
            error: function(err) {
                console.error(err);
                processNotify("Server processing failed!.", "error");
                loading = false;
                renderTable();
            }
        });
    }

    function renderTable() {
        let html = '';
        if (loading) {
            html = `<tr><td colspan="15" class="text-center fs-4 py-3 fw-semibold">Loading...</td></tr>`;
        } else if (datas.length > 0) {
            datas.forEach((data) => {
                let stock = data.stock_w_type === 'none' ? data.stock : data.stock_w;
                
                html += `<tr>
                    <td><div style="width: 200px;">#${data.sku}</div></td>
                    <td>
                        <div class="position-relative">
                            ${data.image ? `<img src="${data.image}" alt="${data.name}" style="width:50px;height:50px;object-fit:cover;border-radius:5px;">` : 'N/A'}
                            ${stock === 0 ? `<div class="position-absolute top-0 start-0 w-100 rounded-3 d-flex justify-content-center align-items-center" style="background-color: rgba(255,255,255,0.65);height:100%;"><i class="fa-solid fa-ban text-muted" style="font-size:1.7rem;"></i></div>` : ''}
                        </div>
                    </td>
                    <td><div style="width:200px;">${data.name}</div></td>
                    <td>${data?.item_type?.name || 'N/A'}</td>
                    <td>${data?.brand?.name || 'N/A'}</td>
                    <td>${data?.category?.name || 'N/A'}</td>
                    <td>${data?.sub_category?.name || 'N/A'}</td>
                    <td><div style="width:70px;"><span class="bdt">৳</span>${data?.price || 'N/A'}</div></td>
                    <td><div style="width:120px;"><span class="bdt">৳</span>${data?.retail_price || 'N/A'}</div></td>
                    <td><div style="width:100px;"><span class="bdt">৳</span>${data?.cost_price || 'N/A'}</div></td>
                    <td>${data?.purchase_limit || 'N/A'}${stockType(data)}</td>
                    <td>${data?.stock_w_type !== 'none' ? formatNumber(data?.stock_w, data?.stock_w_type)+kgOrGmOrFitOrYard(data?.stock_w, data?.stock_w_type) : 'N/A'}</td>
                    <td><div style="width:60px;">${data?.stock_w_type === 'none' ? data?.stock : 'N/A'} ${data.stock ? 'pcs' : ''}</div></td>
                    <td><div style="width:120px;">${stock > 0 && stock > Number(data?.low_stock_level) ? '<span class="badge bg-success">In Stock</span>' : stock > 0 && stock <= Number(data?.low_stock_level) ? '<span class="badge bg-warning">Low In Stock</span>' : '<span class="badge bg-danger">Out of Stock</span>'}</div></td>
                    <td>${data.status === 'active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Deactive</span>'}</td>
                    <td>
                        <div class="d-flex gap-1 align-items-center">
                            <a href="/admin/product/show/${data.id}" class="btn btn-sm btn-outline-info pt-2 pb-1 rounded-3"><i class="fa-solid fa-eye fs-5"></i></a>
                            <a href="/admin/product/edit/${data.id}" class="btn btn-sm btn-outline-primary pt-2 pb-1 rounded-3"><i class="fa-solid fa-pen-to-square fs-5"></i></a>
                            <button type="button" data-url="/admin/product/delete/${data.id}" class="btn btn-sm btn-outline-danger delete-btn pt-2 pb-1 rounded-3 ${deleteOptionIsLock ? 'disabled' : ''}"><i class="fa-solid fa-trash fs-5"></i></button>
                        </div>
                    </td>
                </tr>`;
            });
        } else {
            html = `<tr><td colspan="15" class="text-center fs-4">Data Not found.</td></tr>`;
        }
        $('#table-tbody').html(html);
    }

    function renderPagination() {
        const $pagination = $('#pagination');
        $pagination.empty();
        links.forEach(link => {
            let liClass = 'page-item';
            if (link.active) liClass += ' active';
            if (link.url === null) liClass += ' disabled';

            if (link.label === '&laquo; Previous') {
                $pagination.append(`<li class="${liClass}"><button class="page-link prev-btn">${link.label}</button></li>`);
            } else if (link.label === 'Next &raquo;') {
                $pagination.append(`<li class="${liClass}"><button class="page-link next-btn">${link.label}</button></li>`);
            } else if (link.label === '...') {
                $pagination.append(`<li class="${liClass}"><button class="page-link">...</button></li>`);
            } else {
                $pagination.append(`<li class="${liClass}"><button class="page-link page_btn" data-page="${link.label}">${link.label}</button></li>`);
            }
        });
    }

    function updateStats() {
        $('#page').text(page);
        $('#per-page').text(perPage);
        $('#total-page').text(totalPage);
    }

    // Initial render
    $('.search-input').val('');
    $('#filter-select-field').val('');
    $('#filter-stock-select-field').val(stockFilter);
    getData(search, filter, stockFilter, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortColumn, sort, page, perPage);
    history.replaceState(null, "", window.location.pathname);

    // Event handlers
    $(document).on('click', '.page_btn', function() {
        page = Number($(this).data('page'));
        getData(search, filter, stockFilter, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortColumn, sort, page, perPage);
    });
    $(document).on('click', '.prev-btn', function() {
        if (page > 1) getData(search, filter, stockFilter, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortColumn, sort, --page, perPage);
    });
    $(document).on('click', '.next-btn', function() {
        if (page < lastPage) getData(search, filter, stockFilter, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortColumn, sort, ++page, perPage);
    });

    $('#parPage').on('change', function() {
        perPage = Number($(this).val());
        page = 1;
        getData(search, filter, stockFilter, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortColumn, sort, page, perPage);
    });

    $('.search-btn').on('click', function() {
        page = 1;
        getData(search, filter, stockFilter, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortColumn, sort, page, perPage);
    });
    $('.search-input').on('input', function() {
        search = $(this).val();
    }).on('keypress', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13 || e.which === 13) {
                e.preventDefault();
                page = 1;
                getData(search, filter, stockFilter, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortColumn, sort, page, perPage);
            }
        });

    $('#filter-select-field').on('change', function() {
        filter = $(this).val();
        page = 1;
        getData(search, filter, stockFilter, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortColumn, sort, page, perPage);
    });

    $('#filter-stock-select-field').on('change', function() {
        stockFilter = $(this).val();
        page = 1;
        getData(search, filter, stockFilter, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortColumn, sort, page, perPage);
    });

    $("#item-type-select-field").on("change", function() {
        itemTypeFilter = $(this).val();
        page = 1;
        getData(search, filter, stockFilter, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortColumn, sort, page, perPage);
    });

    $("#brand-select-field").on("change", function() {
        brandFilter = $(this).val();
        page = 1;
        getData(search, filter, stockFilter, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortColumn, sort, page, perPage);
    });

    $("#category-select-field").on("change", function() {
        categoryFilter = $(this).val();
        page = 1;
        getData(search, filter, stockFilter, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortColumn, sort, page, perPage);
    });

    $("#sub-category-select-field").on("change", function() {
        subCategoryFilter = $(this).val();
        page = 1;
        getData(search, filter, stockFilter, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortColumn, sort, page, perPage);
    });

    $(document).on('click', '.sortable', function() {
        const $this = $(this);
        const colName = $this.data('colName');
        let colSort = $this.data('colNameSortType');

        $('.sortable').not($this).removeClass('up-sort down-sort');

        sortColumn = colName;
        sort = colSort;
        getData(search, filter, stockFilter, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortColumn, sort, 1, perPage);

        if (colSort === 'desc') $this.removeClass('up-sort').addClass('down-sort').data('colNameSortType', 'asc');
        else $this.removeClass('down-sort').addClass('up-sort').data('colNameSortType', 'desc');
    });

    $('#reset-dt-btn').on('click', function() {
        const $btn = $(this);
        $btn.addClass('rotate-360').prop('disabled', true);
        $('.search-input').val('');
        $('#filter-select-field').val('');
        $('#filter-stock-select-field').val('');
        $('#parPage').val(5);
        $('.sortable').each(function() { $(this).data('colNameSortType', 'desc'); });
        search = '';
        filter = '';
        stockFilter = '';
        itemTypeFilter = '';
        brandFilter = '';
        sortColumn = '';
        sort = '';
        page = 1;
        perPage = 5;
        getData('', '', '', '', '', '', '', '', '', page, perPage);
        setTimeout(() => $btn.removeClass('rotate-360').prop('disabled', false), 901);
    });

    $(document).on('click', '.delete-btn', function() {
        const url = $(this).data('url');
        $.get(baseUrl + url)
            .done(function(res) {
                if (res.status === 'success') {
                    let p = 1;
                    if (page !== 1 && pageItems === 1) p = page - 1;
                    else if (page !== 1 && pageItems > 1) p = page;
                    processNotify(res.msg, res.status);// replace with processNotify if available
                    page = p;
                    getData(search, filter, stockFilter, itemTypeFilter, brandFilter, categoryFilter, subCategoryFilter, sortColumn, sort, page, perPage);
                } else alert(res.msg);
            })
            .fail(function(err) {
                console.error(err);
                 processNotify ("Something went wrong! Please try again later.", "warning");
            });
    });
});
  </script>
@endpush
