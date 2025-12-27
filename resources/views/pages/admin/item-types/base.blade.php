@extends('layouts.admin-layout')
@section('title', '- Item Types')

@push('style')
<link rel="stylesheet" href="{{ asset('assets/css/datatable.css') }}" />
@endpush

@section('content')
<x-admin.breadcrumb :breadcrumbs="$breadcrumbs" :create_btn_name="'Create'" :btn_route="'admin.item.type.create'" />
<x-admin.page-title title="Item Type" />

<div class="row" style="margin-top: -0.75rem;">
    <div class="col-12">
        <div class="card rounded-4">
            <div class="">
                <div class="row">
                    <div class="col-12">

                        <div class="datatable-wrapper">
                            <!-- Header -->
                            <x-admin.datatable-header />

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
    
    $(document).ready(function() {
        let datas = [];
        let loading = true;
        let links = [];
        let page = 1;
        let perPage = 5;
        let totalPage = 0;
        let search = '';
        let filter = '';
        let sortColumn = '';
        let sort = '';
        let pageItems = 0;
        let lastPage = 0;
        const baseUrl = window.location.origin;

        function getData(searchVal, filterVal, sortCol, sortType, currentPage, perPageVal) {
            //loading = true;
            renderTable();

            $.ajax({
                url: `${baseUrl}/admin/item-type/get`,
                method: 'GET',
                data: {
                    search: searchVal,
                    filter: filterVal,
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
                    loading = false;
                    renderTable();
                }
            });
        }

        function renderTable() {
            let html = '';
            if (loading) {
                html = `<tr><td colspan="5" class="text-center fs-4 py-3 fw-semibold">Loading...</td></tr>`;
            } else if (datas.length > 0) {
                datas.forEach((data, index) => {
                    html += `<tr>
                        <td>#${index + 1}</td>
                        <td><div style="width: 100px;">${data.name}</div></td>
                        <td>${data.status === 'active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Deactive</span>'}</td>
                        <td>
                            <div class="d-flex gap-1 align-items-center">
                                <a href="/admin/item-type/edit/${data.id}" class="btn btn-sm btn-outline-primary pt-2 pb-1 rounded-3">
                                    <i class="fa-solid fa-pen-to-square fs-5"></i>
                                </a>
                                <button type="button" data-url="/admin/item-type/delete/${data.id}" class="btn btn-sm btn-outline-danger delete-btn pt-2 pb-1 rounded-3 ${deleteOptionIsLock ? 'disabled' : ''}">
                                    <i class="fa-solid fa-trash fs-5"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                });
            } else {
                html = `<tr><td colspan="5" class="text-center fs-4">No Data found.</td></tr>`;
            }
            $('#table-tbody').html(html);
        }

        function renderPagination() {
            const $pagination = $('#pagination');
            $pagination.empty();
            links.forEach(link => {
                let liClass = 'page-item';
                let btnClass = 'page-link';
                if (link.active) liClass += ' active';
                if (link.url === null) liClass += ' disabled';

                if (link.label === '&laquo; Previous') {
                    $pagination.append(`<li class="${liClass}"><button class="${btnClass} prev-btn" style="cursor:pointer;">${link.label}</button></li>`);
                } else if (link.label === 'Next &raquo;') {
                    $pagination.append(`<li class="${liClass}"><button class="${btnClass} next-btn" style="cursor:pointer;">${link.label}</button></li>`);
                } else if (link.label === '...') {
                    $pagination.append(`<li class="${liClass}"><button class="${btnClass}" style="cursor:pointer;">...</button></li>`);
                } else {
                    $pagination.append(`<li class="${liClass}"><button class="${btnClass} page_btn" data-page="${link.label}" style="cursor:pointer;">${link.label}</button></li>`);
                }
            });
        }

        function updateStats() {
            $('#page').text(page);
            $('#per-page').text(perPage);
            $('#total-page').text(totalPage);
        }

        // Initial load
        $('#filter-select-field').val('');
        $('.search-input').val('');
        getData(search, filter, sortColumn, sort, page, perPage);

        // Event listeners
        $(document).on('click', '.page_btn', function() {
            const pageNum = Number($(this).data('page'));
            page = pageNum;
            getData(search, filter, sortColumn, sort, pageNum, perPage);
        });

        $(document).on('click', '.prev-btn', function() {
            if (page > 1) {
                page -= 1;
                getData(search, filter, sortColumn, sort, page, perPage);
            }
        });

        $(document).on('click', '.next-btn', function() {
            if (page < lastPage) {
                page += 1;
                getData(search, filter, sortColumn, sort, page, perPage);
            }
        });

        $('#parPage').on('change', function() {
            perPage = Number($(this).val());
            page = 1;
            getData(search, filter, sortColumn, sort, page, perPage);
        });

        $('.search-btn').on('click', function() {
            page = 1;
            getData(search, filter, sortColumn, sort, page, perPage);
        });


        $('.search-input').on('input', function() {
            search = $(this).val();
        }).on('keypress', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13 || e.which === 13) {
                e.preventDefault();
                page = 1;
                getData(search, filter, sortColumn, sort, page, perPage);
            }
        });

        $('#filter-select-field').on('change', function() {
            filter = $(this).val();
            page = 1;
            getData(search, filter, sortColumn, sort, page, perPage);
        });

        $(document).on('click', '.sortable', function() {
            const $this = $(this);
            const colName = $this.data('colName');
            let colSort = $this.data('colNameSortType');

            $('.sortable').not($this).removeClass('up-sort down-sort');

            sortColumn = colName;
            sort = colSort;
            getData(search, filter, sortColumn, sort, 1, perPage);

            if (colSort === 'desc') {
                $this.removeClass('up-sort').addClass('down-sort');
                $this.data('colNameSortType', 'asc');
            } else {
                $this.removeClass('down-sort').addClass('up-sort');
                $this.data('colNameSortType', 'desc');
            }
        });

        $('#reset-dt-btn').on('click', function() {
            const $btn = $(this);
            $btn.addClass('rotate-360').prop('disabled', true);
            $('.search-input').val('');
            $('#filter-select-field').val('');
            $('#parPage').val(5);
            $('.sortable').each(function() {
                if ($(this).data('colNameSortType') !== 'desc') $(this).data('colNameSortType', 'desc');
            });
            search = '';
            filter = '';
            sortColumn = '';
            sort = '';
            page = 1;
            perPage = 5;
            getData('', '', '', '', page, perPage);
            setTimeout(() => {
                $btn.removeClass('rotate-360').prop('disabled', false);
            }, 901);
        });

        $(document).on('click', '.delete-btn', function() {
            const url = $(this).data('url');
            $.get(baseUrl + url)
                .done(function(res) {
                    if (res.status === 'success') {
                        let p = 1;
                        if (page !== 1 && pageItems === 1) p = page - 1;
                        else if (page !== 1 && pageItems > 1) p = page;
                        processNotify(res.msg, res.status)
                        page = p;
                        getData(search, filter, sortColumn, sort, page, perPage);
                    } else {
                        alert(res.msg); // replace with your notify function
                    }
                })
                .fail(function(err) {
                    console.error(err);
                    processNotify("Something went wrong! Please try again later.", "warning");
                });
        });
    });
</script>
@endpush
