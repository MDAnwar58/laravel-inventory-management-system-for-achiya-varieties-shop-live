@extends('layouts.admin-layout')
@section('title', '- Sales Order')

@push('style')
  <link rel="stylesheet" href="{{ asset('assets/css/datatable.css') }}" />
  <style>
    .flex {
      display: flex;
    }

    .space-x-4> :not([hidden])~ :not([hidden]) {
      margin-left: 1rem;
      /* 4 = 1rem in Tailwind */
    }

    .-space-x-4> :not([hidden])~ :not([hidden]) {
      margin-left: -1rem;
      /* negative spacing */
    }

    .rtl\:space-x-reverse {
      direction: rtl;
    }

    .rtl\:space-x-reverse> :not([hidden])~ :not([hidden]) {
      margin-left: 0;
      margin-right: 1rem;
    }
    .bdt {
        font-family: 'Noto Sans Bengali', sans-serif;
        margin-right: 0.1rem;
    }
  </style>
@endpush

@section('content')
  <x-admin.breadcrumb :breadcrumbs="$breadcrumbs" :create_btn_name="'Create'" :btn_route="'admin.sales.order.create'" />
  <x-admin.page-title title="Sales Order" />

  <div class="row" style="margin-top: -0.75rem;">
    <div class="col-12">
      <div class="card rounded-4">
        <div class="">
          <div class="row">
            <div class="col-12">

              <div class="datatable-wrapper">
                <!-- Header -->
                <x-admin.sales-order.edit.datatable-header />

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
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="{{ asset('assets/js/react-hook.js') }}"></script>
  <script>
    let deleteOptionsStr = "{{ $setting->delete_options }}";
    let trLenth = "{{ count($theadColumns) }}";
    let authUser = @json(auth()->user());
    let authUserRole = authUser.role;
    let deleteOptions = Number(deleteOptionsStr);
    let deleteOptionIsLock = deleteOptions !== 1 ? false : true;
    //let deleteOptionIsLock = authUser.role === "owner" || deleteOptions !== 1 ? false : true;

    let datas = [], loading = true, links = [], page = 1, perPage = 5, totalPage = 0, search = '', filter = '', paymentStatusFilter = '', sortColumn = '', sort = '', pageItems = 0, lastPage = 0;
    let baseUrl = window.location.origin

    function dedent(str) {
      const lines = str.split('\n');
      const minIndent = lines.reduce((min, line) => {
        if (line.trim() === '') return min;
        const match = line.match(/^(\s*)/);
        return Math.min(min, match[1].length);
      }, Infinity);
      return lines.map(line => line.slice(minIndent)).join('\n');
    }
    
    function formatNumber(num) {
        // Convert input to a number safely
        num = parseFloat(num);

        // If value is less than 1 (like 0.99), return multiplied by 1000
        if (num < 1) {
            return (num * 1000).toString().replace(/\.0+$/, '');
        }

        // Otherwise, remove unnecessary decimals
        return parseFloat(num.toFixed(3)).toString();
    }
    function kgOrGm(num) {
        // Convert input to a number safely
        num = parseFloat(num);

        // If value is less than 1 (like 0.99), return multiplied by 1000
        if (num < 1) {
            return 'gm';
        }

        // Otherwise, remove unnecessary decimals
        return 'kg'
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
    let paymentStatusFilter = '';
    let sortColumn = '';
    let sort = '';
    let pageItems = 0;
    let lastPage = 0;
    const baseUrl = window.location.origin;

    // ============================
    // Fetch data
    // ============================
    function getData(searchVal, filterVal, pStatusFilterVal, sortCol, sortType, currentPage, perPageVal) {
        //loading = true;
        if (loading)setTableTbodyLoading();

        $.ajax({
            url: `${baseUrl}/admin/sales/orders/get`,
            method: 'GET',
            data: {
                search: searchVal,
                filter: filterVal,
                payment_status_filter: pStatusFilterVal,
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

                setTableBody(datas);
                setPagination(links);
                setShowingAndEntries(page, perPage, totalPage);
            },
            error: function(err) {
                console.error(err);
                processNotify("Server processing failed!.", "error");
                loading = false;
                setTableTbodyLoading();
            }
        });
    }

    // ============================
    // Set loading
    // ============================
    function setTableTbodyLoading() {
        $("#table-tbody").html(`
            <tr>
                <td colspan="${trLenth || 15}" class="text-center fs-4 py-3 fw-semibold">Loading...</td>
            </tr>
        `);
    }

    // ============================
    // Set table body
    // ============================
    function setTableBody(datas) {
        const $tbody = $("#table-tbody");
        $tbody.empty();

        if (datas.length === 0) {
            $tbody.html(`<tr><td colspan="${trLenth || 15}" class="text-center fs-4">Data Not found.</td></tr>`);
            return;
        }

        datas.forEach((data, index) => {
            let productImgs = "";
            if (data.sales_order_products && data.sales_order_products.length > 0) {
                productImgs = data.sales_order_products.slice(0, 4).map((orderItem) => {
                    if (orderItem.product?.image) {
                        return `<img src="${orderItem.product.image}" class="rounded-circle border border-white" width="40" height="40" alt="">`;
                    } else {
                        return `<div class="border shadow rounded-circle d-flex justify-content-center align-items-center" style="width:40px;height:40px;">
                                    <i class="fa-solid fa-image fs-3"></i>
                                </div>`;
                    }
                }).join('');
            }

            const htmlRow = `
                <tr>
                    <td>#${index + 1}</td>
                    <td><div style="width:170px;">#${data?.order_number}</div></td>
                    <td><div style="width:65px;">${data?.memo_no ?? '...'}</div></td>
                    <td><div style="width:150px;">${data?.customer?.name}</div></td>
                    <td><div style="width:110px;">${data?.customer?.phone}</div></td>
                    <td><div style="width:150px;">${data?.customer?.address ?? 'N/A'}</div></td>
                    <td><div style="width:115px;"><div class="d-flex align-items-center">
                        ${data.currency === 'BDT' ? '<span class="bdt">৳</span>' : ''}
                        ${data?.paid_amount}
                    </div></div></td>
                    <td><div style="width:115px;"><div class="d-flex align-items-center">
                        ${data.currency === 'BDT' ? '<span class="bdt">৳</span>' : ''}
                        ${data?.due_amount}
                    </div></div></td>
                    <td><div style="width:115px;"><div class="d-flex align-items-center">
                        ${data.currency === 'BDT' ? '<span class="bdt">৳</span>' : ''}
                        ${data?.total}
                    </div></div></td>
                    <td><div style="width:115px;">${date_format(data?.order_date)}</div></td>
                    <td><div style="width:115px;">${data?.due_date ? date_format(data?.due_date) : 'N/A'}</div></td>
                    <td><div style="width:115px;">${data?.cancelled_date ? date_format(data?.cancelled_date) : 'N/A'}</div></td>
                    <td>
                        <div style="width:195px;">
                            <div class="flex -space-x-4 rtl-space-x-reverse">
                                ${productImgs}
                                <span class="d-flex align-items-center justify-content-center rounded-circle border border-white bg-secondary text-white fw-medium text-decoration-none" style="width:40px;height:40px;font-size:12px;margin-left:-10px;">
                                    ${data.sales_order_products.length}+
                                </span>
                            </div>
                        </div>
                    </td>
                    <td><div style="width:95px;">
                        ${data?.payment_status === "paid" ? '<span class="badge bg-success-subtle text-success">Paid</span>' :
                            data?.payment_status === "partial due" ? '<span class="badge bg-info-subtle text-info">Partial Due</span>' :
                            data?.payment_status === "cancel" ? '<span class="badge bg-danger-subtle text-danger">Cancel</span>' :
                            '<span class="badge bg-warning-subtle text-warning">Full Due</span>'}
                    </div></td>
                    <td><div style="width:95px;">
                        ${data?.status === "pending" ? '<span class="badge bg-warning">Pending</span>' :
                            data?.status === "confirmed" ? '<span class="badge bg-success">Confirmed</span>' :
                            data?.status === "cancelled" ? '<span class="badge bg-danger">Cancelled</span>' :
                            data?.status === "returned" ? '<span class="badge bg-warning">Returned</span>' :
                            '<span class="badge bg-danger">Due</span>'}
                    </div></td>
                    <td>
                        <div class="d-flex gap-1 align-items-center">
                            <a href="/admin/invoice/${data.id}/print" target="_blank" class="btn btn-sm btn-success pt-2 pb-1 rounded-3">
                                <i class="fa-solid fa-print text-white fs-5"></i>
                            </a>
                            <a href="/admin/invoice/${data.id}/excel" class="btn btn-sm btn-info pt-2 pb-1 rounded-3">
                                <i class="fa-solid fa-file-excel text-white fs-5"></i>
                            </a>
                            <a href="/admin/sales/order/invoice/${data.id}" class="btn btn-sm btn-outline-info pt-2 pb-1 rounded-3">
                                <i class="fa-solid fa-eye fs-5"></i>
                            </a>
                            <a href="/admin/sales/order/edit/${data.id}" class="btn btn-sm btn-outline-primary pt-2 pb-1 rounded-3">
                                <i class="fa-solid fa-pen-to-square fs-5"></i>
                            </a>
                            <button type="button" data-url="/admin/sales/order/delete/${data.id}" class="btn btn-sm btn-outline-danger delete-btn pt-2 pb-1 rounded-3 ${deleteOptionIsLock ? 'disabled' : ''}">
                                <i class="fa-solid fa-trash fs-5"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            $tbody.append(htmlRow);
        });
    }

    // ============================
    // Set pagination
    // ============================
    function setPagination(links) {
        const $pagination = $("#pagination");
        $pagination.empty();

        if (!links || links.length === 0) return;

        links.forEach(link => {
            let liClass = "page-item";
            if (link.active) liClass += " active";
            if (link.url === null) liClass += " disabled";

            let btnHtml = '';
            if (link.label === "&laquo; Previous") {
                btnHtml = `<button class="page-link prev-btn">${link.label}</button>`;
            } else if (link.label === "Next &raquo;") {
                btnHtml = `<button class="page-link next-btn">${link.label}</button>`;
            } else if (link.label === "...") {
                btnHtml = `<button class="page-link">...</button>`;
            } else {
                btnHtml = `<button class="page-link page_btn" data-page="${link.label}">${link.label}</button>`;
            }

            $pagination.append(`<li class="${liClass}">${btnHtml}</li>`);
        });
    }

    // ============================
    // Showing & entries info
    // ============================
    function setShowingAndEntries(page, perPage, totalPage) {
        $('#page').text(page);
        $('#per-page').text(perPage);
        $('#total-page').text(totalPage);
    }

    // ============================
    // Event bindings
    // ============================
    $(".search-input").val('');
    $("#filter-select-field").val('');
    $("#filter-payment-status-select-field").val('');

    // Per page change
    $('#parPage').on('change', function() {
        perPage = Number($(this).val());
        page = 1;
        getData(search, filter, paymentStatusFilter, sortColumn, sort, page, perPage);
    });

    // Search input
    $('.search-input').on('input', function() {
        search = $(this).val();
    }).on('keypress', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13 || e.which === 13) {
                e.preventDefault();
                page = 1;
                getData(search, filter, paymentStatusFilter, sortColumn, sort, page, perPage);
            }
        });

    // Search button
    $('.search-btn').on('click', function() {
        page = 1;
        getData(search, filter, paymentStatusFilter, sortColumn, sort, page, perPage);
    });

    // Filter select
    $('#filter-select-field').on('change', function() {
        filter = $(this).val();
        page = 1;
        getData(search, filter, paymentStatusFilter, sortColumn, sort, page, perPage);
    });

    // Payment status filter
    $('#filter-payment-status-select-field').on('change', function() {
        paymentStatusFilter = $(this).val();
        page = 1;
        getData(search, filter, paymentStatusFilter, sortColumn, sort, page, perPage);
    });

    // Sorting
    $(document).on('click', '.sortable', function() {
        const $this = $(this);
        const columnName = $this.data('colName');
        const sortValue = $this.data('colNameSortType');

        $(".sortable").not($this).removeClass("up-sort down-sort");

        sortColumn = columnName;
        sort = sortValue;
        page = 1;
        getData(search, filter, paymentStatusFilter, sortColumn, sort, page, perPage);

        if (sortValue === "desc") {
            $this.removeClass("up-sort").addClass("down-sort").data("colNameSortType", "asc");
        } else {
            $this.removeClass("down-sort").addClass("up-sort").data("colNameSortType", "desc");
        }
    });

    // Reset button
    $('#reset-dt-btn').on('click', function() {
        const $btn = $(this);
        $btn.addClass("rotate-360").prop("disabled", true);
        $(".search-input").val('');
        $("#filter-select-field").val('');
        $("#filter-payment-status-select-field").val('');
        $("#parPage").val(5);
        $(".sortable").each(function() { $(this).data("colNameSortType", "desc"); });

        search = '';
        filter = '';
        paymentStatusFilter = '';
        sortColumn = '';
        sort = '';
        page = 1;
        perPage = 5;
        getData('', '', '', '', '', page, perPage);

        setTimeout(() => {
            $btn.removeClass("rotate-360").prop("disabled", false);
        }, 901);
    });

    // Pagination events
    $(document).on('click', '.page_btn', function() {
        page = Number($(this).data("page"));
        getData(search, filter, paymentStatusFilter, sortColumn, sort, page, perPage);
    });

    $(document).on('click', '.prev-btn', function() {
        if (page > 1) getData(search, filter, paymentStatusFilter, sortColumn, sort, --page, perPage);
    });

    $(document).on('click', '.next-btn', function() {
        if (page < lastPage) getData(search, filter, paymentStatusFilter, sortColumn, sort, ++page, perPage);
    });

    // Delete row
    $(document).on('click', '.delete-btn', function() {
        const url = $(this).data("url");
        $.get(baseUrl + url)
            .done(function(res) {
                if (res.status === "success") {
                    let p = 1;
                    if (page !== 1 && pageItems === 1) p = page - 1;
                    else if (page !== 1 && pageItems > 1) p = page;
                    processNotify(res.msg, res.status);
                    page = p;
                    getData(search, filter, paymentStatusFilter, sortColumn, sort, page, perPage);
                } else {
                    processNotify(res.msg, res.status);
                }
            })
            .fail(function(err) {
                console.error(err);
                processNotify("Something went wrong! Please try again later.", "error");
            });
    });

    // ============================
    // Initial data load
    // ============================
    getData(search, filter, paymentStatusFilter, sortColumn, sort, page, perPage);
});
  </script>
@endpush
