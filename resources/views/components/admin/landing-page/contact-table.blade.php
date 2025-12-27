@props([
  'contact_infos' => [],
])
<div class="table-responsive">
<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Title</th>
      <th scope="col">Type</th>
      <th scope="col">Content</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
  @if($contact_infos->count() > 0)
    @foreach($contact_infos as $key => $contact_info)
    <tr>
      <td>
      <div class="fw-semibold">#{{ $key + 1 }}</div>
      </td>
      <td>{{ $contact_info->title }}</td>
      <td>{{ $contact_info->type }}</td>
      <td>
      <div style="width: 200px;">{!! $contact_info->content !!}</div>
      </td>
      <td>
        <div class="d-flex align-items-center gap-1">
          <button type="button" class="btn btn-sm btn-outline-primary content-edits btn-content-edit pt-2 pb-1 rounded-3" data-id="{{ $contact_info->id }}" data-url="{{ route('admin.landing.page.contact.info.update', $contact_info->id) }}"><i class="fa-solid fa-pen-to-square fs-5"></i></button>
          <form action="{{ route('admin.landing.page.contact.info.delete', $contact_info->id) }}" method="GET">
            <button type="submit" class="btn btn-sm btn-outline-danger pt-2 pb-1 rounded-3"><i class="fa-solid fa-trash fs-5"></i></button>
          </form>
        </div>
      </td>
    </tr>
    @endforeach
  @else
    <tr>
      <th colspan="5" class="text-center">data not found</th>
    </tr>
  @endif
  </tbody>
</table>
</div>