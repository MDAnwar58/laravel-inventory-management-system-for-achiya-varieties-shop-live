@extends('layouts.admin-layout')
@section('title', '- Users')

@push('style')
  <link rel="stylesheet" href="{{ asset('assets/css/datatable.css') }}" />
@endpush

@section('content')
  <x-admin.breadcrumb :breadcrumbs="$breadcrumbs" :create_btn_name="'Create User'" :btn_route="'admin.user.create'" />
  <x-admin.page-title title="User" />

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
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script>
    let deleteOptionsStr = "{{ $setting->delete_options }}";
    let authUser = @json(auth()->user());
    let authUserRole = authUser.role;
    let deleteOptions = Number(deleteOptionsStr);
    let deleteOptionIsLock = deleteOptions !== 1 ? false : true;
    //let deleteOptionIsLock = authUser.role === "owner" || deleteOptions !== 1 ? false : true;
    
    /* ---------- Demo ---------- */
    function dedent(str) {
      const lines = str.split('\n');
      const minIndent = lines.reduce((min, line) => {
        if (line.trim() === '') return min;
        const match = line.match(/^(\s*)/);
        return Math.min(min, match[1].length);
      }, Infinity);
      return lines.map(line => line.slice(minIndent)).join('\n');
    }

    // Example component converted to jQuery
    $(document).ready(function() {
        let staffs = [];
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
        let csrfToken = '';
        const baseUrl = window.location.origin;

        function getData(search, filter, sortColumn, sort, page, perPage) {
          loading = true;
          updateTable();
          
          $.ajax({
            url: `${baseUrl}/admin/user/get`,
            method: 'GET',
            data: {
              search: search,
              filter: filter,
              sort_column: sortColumn,
              sort: sort,
              page: page,
              per_page: perPage
            },
            success: function(res) {
              staffs = res.data;
              loading = false;
              links = res.links;
              page = res.current_page;
              perPage = res.per_page;
              totalPage = res.total;
              pageItems = res.data.length;
              lastPage = res.last_page;
              
              updateTable();
              updatePagination();
              updateStats();
            },
            error: function(err) {
              console.error('error:', err);
              loading = false;
              updateTable();
            }
          });
        }

        function updateTable() {
          let htmlEleacts = '';
          
          if (loading) {
            htmlEleacts = `<tr>
              <td colspan="9" class="text-center fs-4 py-3 fs-5 fw-semibold">Loading...</td>
            </tr>`;
          } else if (staffs.length > 0) {
            htmlEleacts = staffs.map((staff, index) => {
              return `<tr>
                <td class="text-center">${index + 1}</td>
                <td>${staff.avatar ? `<img src="${staff.avatar}" alt="..." class="rounded mb-1" style="width: 50px; height: 50px;" />` : 'N/A'}</td>
                <td><div style="width: 150px;">${staff.name}</div></td>
                <td>${staff.email}</td>
                <td>${staff.phone_number ?? 'N/A'}</td>
                <td>${toNormalText(staff.role) ?? 'N/A'}</td>
                <td>
                  ${Number(staff.is_active) ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Deactive</span>'}
                </td>
                <td>
                  <div class="d-flex gap-1 align-items-center">
                    <a href="/admin/user/show/${staff.id}" class="btn btn-sm btn-outline-info pt-2 pb-1 rounded-3"><i class="fa-solid fa-eye fs-5"></i></a>
                    <a href="/admin/user/edit/${staff.id}" class="btn btn-sm btn-outline-primary pt-2 pb-1 rounded-3"><i class="fa-solid fa-pen-to-square fs-5"></i></a>
                    <button type="button" data-url="/admin/user/delete/${staff.id}" class="btn btn-sm btn-outline-danger delete-btn pt-2 pb-1 rounded-3 ${deleteOptionIsLock === true ? 'disabled' : ''}"><i class="fa-solid fa-trash fs-5"></i></button>
                  </div>
                </td>
              </tr>`;
            }).join('');
          } else {
            htmlEleacts = `<tr>
              <td colspan="9" class="text-center fs-4">No staffs found.</td>
            </tr>`;
          }
          
          $('#table-tbody').html(htmlEleacts);
        }

        function updatePagination() {
          const pagination = $('#pagination');
          pagination.empty();
          
          if (links.length > 0) {
            links.forEach((link, i) => {
              if (link.label === "&laquo; Previous") {
                pagination.append(`<li class="page-item prev ${link.url === null ? 'disabled' : ''}">
                  <button type="button" class="page-link prev-btn" style="cursor: pointer;">${link.label}</button>
                </li>`);
              }
              
              if (link.url !== null && link.label !== 'Next &raquo;' && link.label !== '&laquo; Previous') {
                pagination.append(`<li class="page-item ${link.active === true ? 'active' : ''}">
                  <button type="button" class="page-link page_btn" data-page="${link.label}" style="cursor: pointer;">${link.label}</button>
                </li>`);
              }
              
              if (link.label === "...") {
                pagination.append(`<li class="page-item ${link.url === null ? 'disabled' : ''}">
                  <button type="button" class="page-link">...</button>
                </li>`);
              }
              
              if (link.label === "Next &raquo;") {
                pagination.append(`<li class="page-item next ${link.url === null ? 'disabled' : ''}">
                  <button type="button" class="page-link next-btn" data-page="" style="cursor: pointer;">${link.label}</button>
                </li>`);
              }
            });
          }
        }

        function updateStats() {
          $('#page').text(page);
          $('#per-page').text(perPage);
          $('#total-page').text(totalPage);
        }

        function toNormalText(str) {
          return str ? str.replace(/_/g, ' ').replace(/\w\S*/g, function(txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
          }) : '';
        }

        // Event handlers
        $(document).on('click', '.page_btn', function(e) {
          const pageNum = Number($(this).data('page'));
          page = pageNum;
          getData(search, filter, sortColumn, sort, pageNum, perPage);
        });

        $(document).on('click', '.prev-btn', function(e) {
          if (page !== 1) {
            let p = page - 1;
            page = p;
            getData(search, filter, sortColumn, sort, p, perPage);
          }
        });

        $(document).on('click', '.next-btn', function(e) {
          if (page !== lastPage) {
            let p = page + 1;
            page = p;
            getData(search, filter, sortColumn, sort, p, perPage);
          }
        });

        $(document).on('change', '#parPage', function(e) {
          const perPageValue = Number($(this).val());
          perPage = perPageValue;
          page = 1;
          getData(search, filter, sortColumn, sort, 1, perPageValue);
        });

        $(document).on('click', '.search-btn', function(e) {
          page = 1;
          getData(search, filter, sortColumn, sort, 1, perPage);
        });

        $(document).on('input', '.search-input', function(e) {
          search = $(this).val();
        });

        $(document).on('change', '#filter-select-field', function(e) {
          const filterValue = $(this).val();
          filter = filterValue;
          page = 1;
          getData(search, filterValue, sortColumn, sort, 1, perPage);
        });

        $(document).on('click', '.sortable', function(e) {
          const $this = $(this);
          const columnNameValue = $this.data('col-name');
          let sortValue = $this.data('col-name-sort-type');

          $('.sortable').not(this).removeClass('up-sort down-sort');

          sortColumn = columnNameValue;
          sort = sortValue;
          page = 1;
          getData(search, filter, columnNameValue, sortValue, 1, perPage);

          if (sortValue === "desc") {
            $this.removeClass("up-sort").addClass("down-sort");
          } else {
            $this.addClass("up-sort").removeClass("down-sort");
          }

          if (sortValue === "desc") {
            $this.data('col-name-sort-type', 'asc');
          } else {
            $this.data('col-name-sort-type', 'desc');
          }
        });

        $(document).on('click', '#reset-dt-btn', function() {
          const $btn = $(this);
          $btn.addClass("rotate-360").prop('disabled', true);
          
          $('.search-input').val('');
          $('#filter-select-field').val('');
          $('#parPage').val(5);
          
          $('.sortable').each(function() {
            if ($(this).data('col-name-sort-type') !== "desc") {
              $(this).data('col-name-sort-type', 'desc');
            }
          });
          
          search = '';
          filter = '';
          sortColumn = '';
          sort = '';
          page = 1;
          perPage = 5;
          
          getData('', '', '', '', 1, 5);
          
          setTimeout(() => {
            $btn.removeClass("rotate-360").prop('disabled', false);
          }, 901);
        });

        $(document).on('click', '.delete-btn', async function(e) {
          const url = $(this).data('url');
          
          try {
            const response = await $.ajax({
              url: baseUrl + url,
              method: 'GET'
            });
            
            if (response.status === "success") {
              let p = 1;
              if (page !== 1 && pageItems === 1) p = page - 1;
              if (page !== 1 && pageItems > 1) p = page;
              
              processNotify(response.msg, response.status);
              page = p;
              getData(search, filter, sortColumn, sort, p, perPage);
            } else {
              processNotify(response.msg, response.status);
            }
          } catch (err) {
            console.error("error:", err);
            processNotify("Something went wrong! Please try again later.", "error");
          }
        });

        // Initial setup
        $('.search-input').val('');
        $('#filter-select-field').val('');
        getData(search, filter, sortColumn, sort, page, perPage);
    });
  </script>
@endpush
