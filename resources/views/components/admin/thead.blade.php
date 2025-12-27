@props([
  'theadColumns' => [],
])
<thead>
  <tr>
    @if(count($theadColumns) > 0)
      @foreach($theadColumns as $key => $column)
        @if($column['sortable'] !== false)
          <th class="sortable" data-col-name="{{ $column['sortable_col'] ?? '' }}" data-col-name-sort-type="desc"
            data-column="{{ $key + 1 }}">{{ $column['name'] }}</th>
        @else
          <th data-column="{{ $key + 1 }}">{{ $column['name'] }}</th>
        @endif
      @endforeach
    @endif
  </tr>
</thead>
